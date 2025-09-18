<?php
require_once __DIR__.'/functions.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$p = $id ? program_get($id) : null;
if (!$p) { flash('Program not found','error'); header('Location: index.php'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (program_update($id, $_POST)) { header('Location: index.php'); exit; }
}
include __DIR__.'/partials/header.php';
?>
<h1 class="text-3xl font-bold mb-4">Edit Program</h1>
<form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <input class="p-3 rounded-xl" name="name" value="<?= h($p['name']) ?>" required>
  <input class="p-3 rounded-xl" name="coach" value="<?= h($p['coach']) ?>" required>
  <input class="p-3 rounded-xl" name="contact" value="<?= h($p['contact']) ?>" required>
  <input class="p-3 rounded-xl" name="duration_weeks" type="number" min="1" value="<?= (int)$p['duration_weeks'] ?>" required>
  <select class="p-3 rounded-xl" name="skill_level" required>
    <?php foreach(['Beginner','Intermediate','Advanced'] as $lvl): ?>
      <option <?= $p['skill_level']===$lvl?'selected':'' ?>><?= $lvl ?></option>
    <?php endforeach; ?>
  </select>
  <textarea class="p-3 rounded-xl md:col-span-2" name="description" rows="5" required><?= h($p['description']) ?></textarea>
  <div class="md:col-span-2 flex gap-3">
    <button class="btn btn-primary" type="submit">Save Changes</button>
    <a class="btn btn-ghost" href="index.php">Cancel</a>
  </div>
</form>
<?php include __DIR__.'/partials/footer.php'; ?>
