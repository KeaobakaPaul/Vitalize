<?php
require_once __DIR__.'/functions.php';
$enrolment_id = isset($_GET['enrolment_id']) ? (int)$_GET['enrolment_id'] : 0;
if ($enrolment_id <= 0) { flash('Invalid enrolment','error'); header('Location: enrolments.php'); exit; }

$st = db()->prepare("SELECT e.*, p.name AS program_name, p.duration_weeks, g.name AS gymnast_name
                     FROM enrolments e
                     JOIN programs p ON p.id=e.program_id
                     JOIN gymnasts g ON g.id=e.gymnast_id
                     WHERE e.id=?");
$st->execute([$enrolment_id]);
$en = $st->fetch();
if (!$en) { flash('Enrolment not found','error'); header('Location: enrolments.php'); exit; }

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $date = $_POST['session_date'] ?? date('Y-m-d');
    $present = isset($_POST['present']) && $_POST['present']=='1';
    $score = $_POST['score']!=='' ? (int)$_POST['score'] : null;
    $notes = trim($_POST['notes'] ?? '');
    if (!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $date)) { flash('Invalid date','error'); }
    else {
        if ($score !== null && ($score < 0 || $score > 100)) { flash('Score must be 0-100','error'); }
        else {
            if (attendance_upsert($enrolment_id, $date, $present, $score, $notes)) flash('Session saved.');
            else flash('Could not save session','error');
        }
    }
    header('Location: attendance.php?enrolment_id='.$enrolment_id); exit;
}

$rows = attendance_by_enrol($enrolment_id);
$prog = enrol_progress($enrolment_id, (int)$en['duration_weeks']);
include __DIR__.'/partials/header.php';
?>
<h1 class="text-3xl font-bold mb-4">Attendance & Progress – <?= h($en['gymnast_name']) ?> (<?= h($en['program_name']) ?>)</h1>

<form method="post" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
  <input class="p-3 rounded-xl" type="date" name="session_date" value="<?= h(date('Y-m-d')) ?>">
  <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-700">
    <input type="checkbox" name="present" value="1"> Present
  </label>
  <input class="p-3 rounded-xl" type="number" name="score" min="0" max="100" placeholder="Score (0-100)">
  <input class="p-3 rounded-xl md:col-span-4" name="notes" placeholder="Notes (optional)">
  <div class="md:col-span-4">
    <button class="btn btn-primary">Save Session</button>
    <a class="btn btn-ghost" href="enrolments.php">Back</a>
  </div>
</form>

<div class="card rounded-2xl p-4 mb-6">
  <div class="mb-2 text-slate-300">Completion</div>
  <div class="progress"><div style="width: <?= (int)$prog['completion'] ?>%"></div></div>
  <div class="mt-2 text-sm text-slate-400">
    <?= (int)$prog['sessions'] ?> sessions recorded.
    Present: <?= (int)$prog['present'] ?>.
    Avg Score: <?= $prog['avgScore']===null?'—':(int)$prog['avgScore'] ?>.
  </div>
</div>

<div class="card rounded-2xl p-4 overflow-x-auto">
  <table>
    <thead><tr><th>Date</th><th>Present</th><th>Score</th><th>Notes</th></tr></thead>
    <tbody>
      <?php if(!$rows): ?>
        <tr><td colspan="4" class="py-6 text-center text-slate-400">No sessions yet.</td></tr>
      <?php endif; foreach($rows as $r): ?>
        <tr>
          <td><?= h($r['session_date']) ?></td>
          <td><?= $r['present'] ? 'Yes' : 'No' ?></td>
          <td><?= $r['score']===null ? '—' : (int)$r['score'] ?></td>
          <td><?= h($r['notes']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__.'/partials/footer.php'; ?>
