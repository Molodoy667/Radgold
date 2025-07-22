<?php
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/functions.php';
$site_name = get_site_name();
$theme = get_theme();
$gradient = get_gradient();
?><!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?= htmlspecialchars($site_name) ?> - сучасний сайт оголошень">
    <meta name="keywords" content="оголошення, сайт, дошка, категорії">
    <meta name="author" content="<?= htmlspecialchars($site_name) ?>">
    <title><?= htmlspecialchars($site_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/theme/css/style.css">
    <link rel="stylesheet" href="/theme/css/gradients.css">
</head>
<body data-theme="<?= htmlspecialchars($theme) ?>" class="<?= htmlspecialchars($gradient) ?>">
<?php include __DIR__ . '/navbar.php'; ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-2 d-none d-lg-block">
      <?php include __DIR__ . '/sidebar.php'; ?>
    </div>
    <div class="col-12 col-lg-10 pt-2 pt-lg-4">