<?php 

$UserUID   = getSession('UID');
$UserLogin = getSession('Login');
$UserMail  = getSession('EMail');

if (empty($UserUID) || empty($UserLogin) || empty($UserMail)) {
  Go('logout');
}

if (getSession('verifyMail')==1) {
  Go('verifyMail');
}

$UserData = FetchUserData($UserUID);
$WebsData = FetchWebSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head class="crypt-dark">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo FetchWebSettings()['BaseName']; ?></title>
  <meta name="description" content="<?php echo FetchWebSettings()['BaseDesc']; ?>">
  <link rel="stylesheet" href="core/pages/assets/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="core/pages/assets/css/style.css">
  <link rel="stylesheet" href="core/pages/assets/css/responsive.css">
  <link rel="stylesheet" href="core/pages/assets/css/button.css">
  <link rel="stylesheet" href="core/pages/assets/releases/8.13.0/css/anychart-ui.min.css?hcode=a0c21fc77e1449cc86299c5faa067dc4">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <?php renderOptimizedThemeCSS(); ?>
  <link rel="icon" type="image/png" href="<?php echo FetchWebSettings()['BaseLogo']; ?>">
</head>

<body class="crypt-light">
<section class="container-fluid d-sm-flex flex-row fixed-sidebar">

<?php require __DIR__ . '/assets_bar.php'; ?>

<br>