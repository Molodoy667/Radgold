<h4>Крок 3: Налаштування бази даних</h4>
<?php
$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $host = $_POST['db_host'] ?? '';
  $name = $_POST['db_name'] ?? '';
  $user = $_POST['db_user'] ?? '';
  $pass = $_POST['db_pass'] ?? '';
  try {
    $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    // Очистка БД
    $pdo->exec('SET foreign_key_checks = 0;');
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach($tables as $t) $pdo->exec("DROP TABLE IF EXISTS `$t`;");
    $pdo->exec('SET foreign_key_checks = 1;');
    // Імпорт SQL
    $sql = file_get_contents(__DIR__.'/base.sql');
    $pdo->exec($sql);
    $_SESSION['db'] = compact('host','name','user','pass');
    goToStep(4);
  } catch(Exception $e) {
    $error = $e->getMessage();
  }
}
?>
<form method="post">
  <div class="mb-3"><label class="form-label">Хост</label><input type="text" name="db_host" class="form-control" required value="localhost"></div>
  <div class="mb-3"><label class="form-label">База даних</label><input type="text" name="db_name" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Користувач</label><input type="text" name="db_user" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Пароль</label><input type="password" name="db_pass" class="form-control"></div>
  <?php if($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <button class="btn btn-primary" type="submit">Далі</button>
</form>