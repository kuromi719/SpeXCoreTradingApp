<!DOCTYPE html>
<html lang="en">
<head class="crypt-light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo FetchWebSettings()['BaseName']; ?></title>
    <link rel="stylesheet" href="core/pages/assets/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="core/pages/assets/css/style.css">
    <link rel="stylesheet" href="core/pages/assets/css/responsive.css">
    <link rel="stylesheet" href="core/pages/assets/css/button.css">
    <link rel="stylesheet" href="core/pages/assets/css/gsap.css">
    <?php renderOptimizedThemeCSS(); ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo FetchWebSettings()['BaseLogo']; ?>">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="crypt-light">