<?php
require_once __DIR__.'/db.php';
require_once __DIR__.'/helpers.php';

// ---------------- Programs ---------------- //
function programs_search(string $q=null, string $skill=null): array {
    $pdo = db();
    $where = []; $args = [];
    if ($q) { $where[] = "(name LIKE ? OR coach LIKE ?)"; $args[] = "%$q%"; $args[] = "%$q%"; }
    if ($skill && valid_skill($skill)) { $where[] = "skill_level = ?"; $args[] = $skill; }
    $sql = "SELECT p.*, 
               (SELECT COUNT(*) FROM enrolments e WHERE e.program_id = p.id) AS enrolled_count
            FROM programs p";
    if ($where) $sql .= " WHERE " . implode(" AND ", $where);
    $sql .= " ORDER BY created_at DESC";
    $st = $pdo->prepare($sql); $st->execute($args);
    return $st->fetchAll();
}

function program_get(int $id): ?array {
    $st = db()->prepare("SELECT * FROM programs WHERE id=?"); $st->execute([$id]);
    $row = $st->fetch(); return $row ?: null;
}

function program_create(array $in): bool {
    // validation
    $errors = program_validate($in);
    if ($errors) { flash(implode("\n", $errors), 'error'); return false; }
    $st = db()->prepare("INSERT INTO programs(name, description, coach, contact, duration_weeks, skill_level) VALUES(?,?,?,?,?,?)");
    $ok = $st->execute([trim($in['name']), trim($in['description']), trim($in['coach']), trim($in['contact']), (int)$in['duration_weeks'], $in['skill_level']]);
    if ($ok) flash("Program created successfully.");
    return $ok;
}

function program_update(int $id, array $in): bool {
    $errors = program_validate($in);
    if ($errors) { flash(implode("\n", $errors), 'error'); return false; }
    $st = db()->prepare("UPDATE programs SET name=?, description=?, coach=?, contact=?, duration_weeks=?, skill_level=? WHERE id=?");
    $ok = $st->execute([trim($in['name']), trim($in['description']), trim($in['coach']), trim($in['contact']), (int)$in['duration_weeks'], $in['skill_level'], $id]);
    if ($ok) flash("Program updated.");
    return $ok;
}

function program_delete(int $id): bool {
    $st = db()->prepare("DELETE FROM programs WHERE id=?");
    $ok = $st->execute([$id]);
    if ($ok) flash("Program deleted.");
    return $ok;
}

function program_validate(array $in): array {
    $err=[];
    if (empty(trim($in['name'] ?? ''))) $err[] = "Program name is required.";
    if (empty(trim($in['description'] ?? ''))) $err[] = "Description is required.";
    if (empty(trim($in['coach'] ?? ''))) $err[] = "Coach name is required.";
    $contact = trim($in['contact'] ?? '');
    if ($contact==='') $err[] = "Contact is required.";
    else if (!filter_var($contact, FILTER_VALIDATE_EMAIL) && !preg_match('/^[+\\d][\\d\\s().-]{5,}$/', $contact)) $err[] = "Contact must be email or phone.";
    $d = $in['duration_weeks'] ?? '';
    if ($d==='' || !is_numeric($d) || (int)$d<=0) $err[] = "Duration must be a positive number of weeks.";
    $lvl = $in['skill_level'] ?? '';
    if (!in_array($lvl, ['Beginner','Intermediate','Advanced'], true)) $err[] = "Skill level must be Beginner, Intermediate, or Advanced.";
    return $err;
}

// ---------------- Gymnasts & Enrolments ---------------- //
function gymnast_create_or_get(string $name, int $age, string $experience): int {
    // For demo simplicity we always create; could be enhanced to "find or create"
    $st = db()->prepare("INSERT INTO gymnasts(name, age, experience) VALUES(?,?,?)");
    $st->execute([$name, $age, $experience]);
    return (int)db()->lastInsertId();
}

function enrol_validate(array $in): array {
    $err=[];
    if (empty($in['program_id']) || !is_numeric($in['program_id'])) $err[]="Program selection is required.";
    if (empty(trim($in['name'] ?? ''))) $err[]="Gymnast name is required.";
    $age = $in['age'] ?? '';
    if ($age==='' || !is_numeric($age) || (int)$age < 4 || (int)$age > 80) $err[]="Age must be 4-80.";
    $exp = $in['experience'] ?? '';
    if (!in_array($exp, ['Beginner','Intermediate','Advanced'], true)) $err[]="Experience must be Beginner/Intermediate/Advanced.";
    return $err;
}

function enrol_create(array $in): bool {
    $errs = enrol_validate($in);
    if ($errs) { flash(implode("\n", $errs), 'error'); return false; }
    $gymnast_id = gymnast_create_or_get(trim($in['name']), (int)$in['age'], $in['experience']);
    $st = db()->prepare("INSERT INTO enrolments(program_id, gymnast_id) VALUES(?,?)");
    $ok = $st->execute([(int)$in['program_id'], $gymnast_id]);
    if ($ok) {
        // Notification hook (ready to wire up email)
        $prog = program_get((int)$in['program_id']);
        $msg = "Enrolment successful. Coach {$prog['coach']} will be notified at {$prog['contact']}.";
        flash($msg);
    }
    return $ok;
}

function enrolments_list(?int $program_id=null): array {
    $pdo = db();
    $sql = "SELECT e.id, e.enrolled_at, p.name AS program_name, p.duration_weeks, p.coach, p.skill_level,
                   g.name AS gymnast_name, g.age, g.experience
            FROM enrolments e
            JOIN programs p ON p.id = e.program_id
            JOIN gymnasts g ON g.id = e.gymnast_id";
    $args=[];
    if ($program_id) { $sql .= " WHERE e.program_id = ?"; $args[] = $program_id; }
    $sql .= " ORDER BY e.enrolled_at DESC";
    $st = $pdo->prepare($sql); $st->execute($args);
    $rows = $st->fetchAll();
    // attach metrics
    foreach($rows as &$r){
        $r['progress'] = enrol_progress((int)$r['id'], (int)$r['duration_weeks']);
    }
    return $rows;
}

// ---------------- Attendance & Progress ---------------- //
function attendance_upsert(int $enrolment_id, string $date, bool $present, ?int $score, ?string $notes): bool {
    $pdo = db();
    // insert or update (unique by enrolment + date)
    $st = $pdo->prepare("INSERT INTO attendance(enrolment_id, session_date, present, score, notes)
                         VALUES(?,?,?,?,?)
                         ON DUPLICATE KEY UPDATE present=VALUES(present), score=VALUES(score), notes=VALUES(notes)");
    return $st->execute([$enrolment_id, $date, $present?1:0, $score, $notes]);
}

function attendance_by_enrol(int $enrolment_id): array {
    $st = db()->prepare("SELECT * FROM attendance WHERE enrolment_id=? ORDER BY session_date DESC");
    $st->execute([$enrolment_id]);
    return $st->fetchAll();
}

function enrol_progress(int $enrolment_id, int $duration_weeks): array {
    $rows = attendance_by_enrol($enrolment_id);
    $sessions = count($rows);
    $present = array_sum(array_map(fn($r)=> (int)$r['present'], $rows));
    $avgScore = null;
    $scores = array_values(array_filter(array_map(fn($r)=> is_numeric($r['score']) ? (int)$r['score'] : null, $rows)));
    if ($scores) $avgScore = (int) floor(array_sum($scores)/count($scores));
    // completion = sessions / duration_weeks (cap 100)
    $completion = $duration_weeks > 0 ? min(100, (int)floor(($sessions / $duration_weeks) * 100)) : 0;
    return ['sessions'=>$sessions,'present'=>$present,'avgScore'=>$avgScore,'completion'=>$completion];
}
