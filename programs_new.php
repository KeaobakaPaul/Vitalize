<?php
require_once __DIR__.'/functions.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (program_create($_POST)) { header('Location: index.php'); exit; }
}
include __DIR__.'/partials/header.php';
?>
<h1 class="text-3xl font-bold mb-4">Add Program</h1>
<form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <input class="p-3 rounded-xl" name="name" placeholder="Program name" required>
  <input class="p-3 rounded-xl" name="coach" placeholder="Coach name" required>
  <input class="p-3 rounded-xl" name="contact" placeholder="Coach contact (email or phone)" required>
  <input class="p-3 rounded-xl" name="duration_weeks" type="number" min="1" placeholder="Duration (weeks)" required>
  <select class="p-3 rounded-xl" name="skill_level" required>
    <option value="">Skill level</option>
    <option>Beginner</option><option>Intermediate</option><option>Advanced</option>
  </select>
  <textarea class="p-3 rounded-xl md:col-span-2" name="description" rows="5" placeholder="Description" required></textarea>
  <div class="md:col-span-2 flex gap-3">
    <button class="btn btn-primary" type="submit">Create Program</button>
    <a class="btn btn-ghost" href="index.php">Cancel</a>
  </div>
</form>
<?php include __DIR__.'/partials/footer.php'; ?>
