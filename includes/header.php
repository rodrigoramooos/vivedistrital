<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Vive Distrital</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- Google Material Symbols -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <!-- CSS comum -->
  <link rel="stylesheet" href="<?php echo url('css/comum.css'); ?>">
  
  <?php if (isset($pageCSS)): ?>
  <!-- CSS específico da página -->
  <link rel="stylesheet" href="<?php echo url($pageCSS); ?>">
  <?php endif; ?>
  
  <?php if (isset($additionalStyles)): ?>
  <style>
    <?php echo $additionalStyles; ?>
  </style>
  <?php endif; ?>
</head>
<body>
