<?php
session_start();
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
function goToStep($s) { header('Location: ?step=' . $s); exit; }
$steps = [
  1 => ['icon'=>'bi-file-earmark-text','label'=>'Ліцензія'],
  2 => ['icon'=>'bi-gear','label'=>'Перевірка'],
  3 => ['icon'=>'bi-database','label'=>'База даних'],
  4 => ['icon'=>'bi-palette','label'=>'Налаштування'],
];
?><!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Встановлення сайту</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/theme/css/installer.css">
</head>
<body class="installer-bg">
<div class="installer-card">
  <div class="p-4">
    <h2 class="mb-4 text-center" style="color:#3498db;font-weight:700;">Встановлення сайту</h2>
    <div class="installer-progress mb-4">
      <?php foreach($steps as $i=>$s): ?>
        <div class="installer-step<?= $i==$step?' active':'' ?>">
          <div class="circle"><i class="bi <?= $s['icon'] ?>"></i></div>
          <div class="label"><?= $s['label'] ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php
    switch($step) {
      case 1:
        include __DIR__ . '/step1_license.php';
        break;
      case 2:
        include __DIR__ . '/step2_check.php';
        break;
      case 3:
        include __DIR__ . '/step3_db.php';
        break;
      case 4:
        include __DIR__ . '/step4_settings.php';
        break;
      default:
        goToStep(1);
    }
    ?>
  </div>
</div>
</body>
</html>