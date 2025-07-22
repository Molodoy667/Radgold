<?php
require_once __DIR__ . '/../core/config.php';
?><!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?= SITE_NAME ?> - сучасний сайт оголошень">
    <meta name="keywords" content="оголошення, сайт, дошка, категорії">
    <meta name="author" content="<?= SITE_NAME ?>">
    <title><?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/theme/css/style.css">
    <link rel="stylesheet" href="/theme/css/gradients.css">
</head>
<body data-theme="light">
<?php include __DIR__ . '/navbar.php'; ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-2 d-none d-md-block">
      <?php include __DIR__ . '/sidebar.php'; ?>
    </div>
    <div class="col-md-10">