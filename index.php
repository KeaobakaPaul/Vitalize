<?php
require_once __DIR__.'/functions.php';
$q = $_GET['q'] ?? null;
$skill = $_GET['skill'] ?? null;
$rows = programs_search($q, $skill);
include __DIR__.'/partials/header.php';
?>
<h1 class="text-3xl font-bold mb-4">Programs</h1>
<form method="get" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
  <input name="q" value="<?= h($q) ?>" placeholder="Search by Program or Coach" class="p-3 rounded-xl" />
  <select name="skill" class="p-3 rounded-xl">
    <option value="">All Levels</option>
    <?php foreach(['Beginner','Intermediate','Advanced'] as $lvl): ?>
      <option value="<?= $lvl ?>" <?= $skill===$lvl?'selected':'' ?>><?= $lvl ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn btn-primary">Filter</button>
</form>
<div class="card rounded-2xl p-4 overflow-x-auto">
  <table>
    <thead><tr>
      <th>Program Name</th><th>Coach</th><th>Duration</th><th>Skill Level</th><th>Enrolled</th><th>Actions</th>
    </tr></thead>
    <tbody>
    <?php if(!$rows): ?>
      <tr><td colspan="6" class="py-8 text-center text-slate-400">No programs yet.</td></tr>
    <?php endif; foreach($rows as $r): ?>
      <tr>
        <td><?= h($r['name']) ?></td>
        <td><?= h($r['coach']) ?> <span class="badge"><?= h($r['contact']) ?></span></td>
        <td><?= (int)$r['duration_weeks'] ?> weeks</td>
        <td><span class="badge"><?= h($r['skill_level']) ?></span></td>
        <td><?= (int)$r['enrolled_count'] ?></td>
        <td class="flex gap-2">
          <a class="btn btn-ghost" href="programs_edit.php?id=<?= (int)$r['id'] ?>">Edit</a>
          <a class="btn btn-danger" href="programs_delete.php?id=<?= (int)$r['id'] ?>" onclick="return confirm('Delete this program?')">Delete</a>
          <a class="btn btn-primary" href="enrol.php?program_id=<?= (int)$r['id'] ?>">Enrol</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__.'/partials/footer.php'; ?>
