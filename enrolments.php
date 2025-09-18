<?php
require_once __DIR__.'/functions.php';
$program_id = isset($_GET['program_id']) ? (int)$_GET['program_id'] : null;
$rows = enrolments_list($program_id);
include __DIR__.'/partials/header.php';
?>
<h1 class="text-3xl font-bold mb-4">Enrolments</h1>
<div class="card rounded-2xl p-4 overflow-x-auto">
  <table>
    <thead><tr>
      <th>Gymnast</th><th>Program</th><th>Level</th><th>Coach</th><th>Enrolled</th><th>Progress</th><th>Avg Score</th><th>Actions</th>
    </tr></thead>
    <tbody>
    <?php if(!$rows): ?>
      <tr><td colspan="8" class="py-8 text-center text-slate-400">No enrolments yet.</td></tr>
    <?php endif; foreach($rows as $r): ?>
      <tr>
        <td><?= h($r['gymnast_name']) ?> (<?= (int)$r['age'] ?>)</td>
        <td><?= h($r['program_name']) ?> (<?= (int)$r['duration_weeks'] ?>w)</td>
        <td><span class="badge"><?= h($r['skill_level']) ?></span></td>
        <td><?= h($r['coach']) ?></td>
        <td><?= h($r['enrolled_at']) ?></td>
        <td style="min-width:180px">
          <div class="progress"><div style="width: <?= (int)$r['progress']['completion'] ?>%"></div></div>
          <small><?= (int)$r['progress']['completion'] ?>% complete, <?= (int)$r['progress']['sessions'] ?> sessions</small>
        </td>
        <td><?= $r['progress']['avgScore']===null ? '—' : (int)$r['progress']['avgScore'] ?></td>
        <td><a class="btn btn-primary" href="attendance.php?enrolment_id=<?= (int)$r['id'] ?>">Update</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__.'/partials/footer.php'; ?>
