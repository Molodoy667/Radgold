<?php
session_start();
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
function goToStep($s) { header('Location: ?step=' . $s); exit; }
?><!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Встановлення сайту</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
  <div class="card shadow">
    <div class="card-body">
      <h2 class="mb-4">Встановлення сайту</h2>
      <div class="progress mb-4" style="height: 20px;">
        <div class="progress-bar" role="progressbar" style="width: <?= $step*25 ?>%;" aria-valuenow="<?= $step ?>" aria-valuemin="1" aria-valuemax="4">Крок <?= $step ?> з 4</div>
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
</div>
</body>
</html>