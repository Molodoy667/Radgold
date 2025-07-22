<h4>Крок 2: Перевірка системних вимог та прав</h4>
<ul class="list-group mb-3">
<?php
$ok = true;
// PHP version
$php_ok = version_compare(PHP_VERSION, '7.4.0', '>=');
echo '<li class="list-group-item">PHP 7.4+: <span class="badge '.($php_ok?'bg-success':'bg-danger').'">'.PHP_VERSION.'</span></li>';
if (!$php_ok) $ok = false;
// Права на папки
$folders = ['../images', '../upload'];
foreach ($folders as $f) {
  if (!is_dir($f)) mkdir($f, 0777, true);
  $w = is_writable($f);
  echo '<li class="list-group-item">'.htmlspecialchars($f).': <span class="badge '.($w?'bg-success':'bg-danger').'">'.($w?'OK':'Немає прав на запис').'</span></li>';
  if (!$w) $ok = false;
}
?>
</ul>
<form method="post">
  <button class="btn btn-primary" type="submit" <?= $ok?'':'disabled' ?>>Далі</button>
</form>
<?php
if ($_SERVER['REQUEST_METHOD']==='POST' && $ok) {
  goToStep(3);
}
?>