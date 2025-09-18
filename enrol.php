<?php
require_once __DIR__.'/functions.php';
$programs = programs_search();
$pref = isset($_GET['program_id']) ? (int)$_GET['program_id'] : 0;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (enrol_create($_POST)) { header('Location: enrolments.php'); exit; }
}
include __DIR__.'/partials/header.php';
?>
<h1 class="text-3xl font-bold mb-4">Enrol Gymnast</h1>
<form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <select class="p-3 rounded-xl md:col-span-2" name="program_id" required>
    <option value="">Select Program</option>
    <?php foreach($programs as $p): ?>
      <option value="<?= (int)$p['id'] ?>" <?= $pref===(int)$p['id']?'selected':'' ?>><?= h($p['name']) ?> (<?= h($p['skill_level']) ?>)</option>
    <?php endforeach; ?>
  </select>
  <input class="p-3 rounded-xl" name="name" placeholder="Gymnast name" required>
  <input class="p-3 rounded-xl" type="number" name="age" min="4" max="80" placeholder="Age" required>
  <select class="p-3 rounded-xl" name="experience" required>
    <option value="">Experience level</option>
    <option>Beginner</option><option>Intermediate</option><option>Advanced</option>
  </select>
  <div class="md:col-span-2 flex gap-3">
    <button class="btn btn-primary" type="submit">Enrol</button>
    <a class="btn btn-ghost" href="index.php">Cancel</a>
  </div>
</form>
<?php include __DIR__.'/partials/footer.php'; ?>
