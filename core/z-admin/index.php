<?php 

if (!isset($_SESSION['adminlog']) || isset($_SESSION['bool'])) {
    header('Location:login.php');
}

if ($_SESSION['adminlog']=='appr' || $_SESSION['bool']=='1') {
    header('Location:home.php');
}

?>