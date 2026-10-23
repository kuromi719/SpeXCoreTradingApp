<?php
require __DIR__ . '/../../system/spex_v4_func.php';

try {
    header('Content-Type: application/json');
    $response = array();

    if(isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        
        $query = "SELECT * FROM admin WHERE user = '$username' AND pass = '$password'";
        $result = mysqli_query($spexcon, $query);
        
        if(mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['adminlog'] = 'appr';
            $_SESSION['adminID'] = $user['id'];
            $_SESSION['bool'] = '1';
            LogAdminLogin($username);
            $response['status'] = 'success';
            $response['message'] = 'Login Successfully';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Username or password invalid.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid Request';
    }

    echo json_encode($response);
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'System Error: ' . $e->getMessage();
    echo json_encode($response);
}
?>