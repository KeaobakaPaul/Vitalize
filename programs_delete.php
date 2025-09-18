<?php
require_once __DIR__.'/functions.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) program_delete($id);
header('Location: index.php'); exit;
