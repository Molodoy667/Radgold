<form method="post" class="animate__animated animate__fadeIn">
  <h5 class="mb-3">Перевірка системних вимог та прав</h5>
  <ul class="list-group mb-3">
  <?php
  $ok = true;
  $php_ok = version_compare(PHP_VERSION, '7.4.0', '>=');
  echo '<li class="list-group-item">PHP 7.4+: <span class="badge '.($php_ok?'bg-success':'bg-danger').'">'.PHP_VERSION.'</span></li>';
  if (!$php_ok) $ok = false;
  $folders = ['../images', '../upload'];
  foreach ($folders as $f) {
    if (!is_dir($f)) mkdir($f, 0777, true);
    $w = is_writable($f);
    echo '<li class="list-group-item">'.htmlspecialchars($f).': <span class="badge '.($w?'bg-success':'bg-danger').'">'.($w?'OK':'Немає прав на запис').'</span></li>';
    if (!$w) $ok = false;
  }
  ?>
  </ul>
  <div class="d-flex justify-content-between">
    <a href="?step=1" class="btn btn-installer btn-outline-secondary"><i class="bi bi-arrow-left"></i>Назад</a>
    <button class="btn btn-installer btn-primary" type="submit" <?= $ok?'':'disabled' ?>><i class="bi bi-arrow-right"></i>Далі</button>
  </div>
</form>
<?php
if ($_SERVER['REQUEST_METHOD']==='POST' && $ok) {
  goToStep(3);
}
?>