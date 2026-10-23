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

if (getSession('LoginVia2Fa')==1) {
  Go('login-verify');
}

if (!empty(getSession('isAdminLogin'))) {
  $isAdminLogin = getSession('isAdminLogin');
  if ($isAdminLogin==1) {
    // :p act its so stupid control -spex
  } else {
    Go('logout');
  }
}

$UserData = FetchUserData($UserUID);
$WebsData = FetchWebSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head class="crypt-light">
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

<?php if (!empty(getSession('isAdminLogin')) && getSession('isAdminLogin') == 1): ?>
  <div class="alert alert-warning alert-dismissible fade show fixed-top d-flex align-items-center justify-content-between px-3" role="alert" style="height: 3.2rem; line-height: 1.5; z-index: 1099;">
    <div class="d-flex align-items-center flex-grow-1 justify-content-center text-truncate" style="font-size: 1rem; gap: 0.5rem;">
      <i class="fas fa-user-shield" style="font-size: 1.3rem;"></i>
      <span>Hi Admin, logged in as <b><?php echo htmlspecialchars($UserMail); ?></b></span>
    </div>
    <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close" style="flex-shrink: 0;"></button>
  </div>
  <style>
    body {
      padding-top: 3.2rem;
    }
  </style>
<?php endif; ?>

<section class="container-fluid d-sm-flex flex-row fixed-sidebar">

<?php require __DIR__ . '/app_bar.php'; ?>
<br>