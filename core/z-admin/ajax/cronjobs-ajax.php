<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

switch($action) {
    case 'getCrons':
        $result = $spexcon->query("SELECT * FROM cron_jobs_params ORDER BY id ASC");
        $jobs = [];
        while($row = $result->fetch_assoc()) $jobs[] = $row;
        echo json_encode(['success' => true, 'crons' => $jobs]);
        break;
        
    case 'runCron':
        $id = (int)$_POST['id'];
        $data = $spexcon->query("SELECT * FROM cron_jobs_params WHERE id = $id");
        $fetc = mysqli_fetch_assoc($data);
        $CronPath = __DIR__ . '/../../crons/' . $fetc['job_file'];
        if (file_exists($CronPath)) {
            if (require $CronPath) {
                echo json_encode(['success' => true, 'message' => 'CronJob Executed!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to Execute Cron-Job.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'This job not found on CronJobs Path.']);
        }
        break;
}
?>