<h4>Крок 4: Налаштування сайту та створення адміністратора</h4>
<?php
$error = '';
$theme = $_POST['theme'] ?? 'light';
$gradient = $_POST['gradient'] ?? 'gradient-1';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $site_name = $_POST['site_name'] ?? '';
  $admin_user = $_POST['admin_user'] ?? '';
  $admin_pass = $_POST['admin_pass'] ?? '';
  $admin_email = $_POST['admin_email'] ?? '';
  if ($site_name && $admin_user && $admin_pass && $admin_email) {
    // Зберігаємо налаштування у config.php
    $db = $_SESSION['db'];
    $config = "<?php\nif (!file_exists(__DIR__ . '/installed.lock')) { header('Location: /install/index.php'); exit; }\nconst DB_HOST = '".$db['host']."';\nconst DB_NAME = '".$db['name']."';\nconst DB_USER = '".$db['user']."';\nconst DB_PASS = '".$db['pass']."';\nconst SITE_NAME = '".addslashes($site_name)."';\n";
    file_put_contents(__DIR__.'/../core/config.php', $config);
    // Додаємо адміна і налаштування
    try {
      $pdo = new PDO("mysql:host={$db['host']};dbname={$db['name']}", $db['user'], $db['pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
      $hash = password_hash($admin_pass, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
      $stmt->execute([$admin_user, $hash, $admin_email]);
      // Оновлюємо налаштування
      $pdo->prepare("UPDATE settings SET value=? WHERE name='site_name'")->execute([$site_name]);
      $pdo->prepare("UPDATE settings SET value=? WHERE name='theme'")->execute([$theme]);
      $pdo->prepare("UPDATE settings SET value=? WHERE name='gradient'")->execute([$gradient]);
      // Створюємо installed.lock
      file_put_contents(__DIR__.'/../core/installed.lock', 'ok');
      echo '<div class="alert alert-success">Встановлення завершено! <a href="/">Перейти на сайт</a></div>';
      session_destroy();
      exit;
    } catch(Exception $e) {
      $error = $e->getMessage();
    }
  } else {
    $error = 'Заповніть всі поля!';
  }
}
?>
<form method="post">
  <div class="mb-3"><label class="form-label">Назва сайту</label><input type="text" name="site_name" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Тема сайту</label>
    <select name="theme" class="form-select">
      <option value="light" <?= $theme==='light'?'selected':'' ?>>Світла</option>
      <option value="dark" <?= $theme==='dark'?'selected':'' ?>>Темна</option>
    </select>
  </div>
  <div class="mb-3"><label class="form-label">Градієнт оформлення</label>
    <div class="d-flex flex-wrap gap-2">
      <?php for($i=1;$i<=30;$i++): ?>
        <label style="cursor:pointer;">
          <input type="radio" name="gradient" value="gradient-<?= $i ?>" <?= $gradient==="gradient-$i"?'checked':'' ?> hidden>
          <span class="gradient-<?= $i ?> d-inline-block" style="width:32px;height:32px;border-radius:50%;border:2px solid #fff;"></span>
        </label>
      <?php endfor; ?>
    </div>
  </div>
  <div class="mb-3"><label class="form-label">Логін адміністратора</label><input type="text" name="admin_user" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Пароль адміністратора</label><input type="password" name="admin_pass" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Email адміністратора</label><input type="email" name="admin_email" class="form-control" required></div>
  <?php if($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <button class="btn btn-success" type="submit">Завершити встановлення</button>
</form>