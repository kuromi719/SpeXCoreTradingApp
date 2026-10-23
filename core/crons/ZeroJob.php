<?php
require_once __DIR__ . '/../system/spex_v4_func.php';

class ZeroJobManager {
    private $spexcon;
    private $cronBasePath;
    private $logPath;

    public function __construct($connection) {
        $this->spexcon = $connection;
        $this->cronBasePath = __DIR__ . '/';
        $this->logPath = __DIR__ . '/logs/zerojob.log';
        $this->ensureLogDirectory();
    }

    private function ensureLogDirectory() {
        $logDir = dirname($this->logPath);
        if (!is_dir($logDir)) mkdir($logDir, 0755, true);
    }

    private function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp][$level] $message\n";
        file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
        echo $logEntry;
    }

    private function executeJob($job) {
        $now = time();
        $nextRun = strtotime($job['next_run'] ?? '1970-01-01 00:00:00');

        if ($now < $nextRun) {
            $this->log("Skipping job: {$job['job_name']}, next_run at {$job['next_run']}", 'INFO');
            return null;
        }

        $startTime = microtime(true);
        $jobFile = $this->cronBasePath . $job['job_file'];

        if (!file_exists($jobFile)) {
            throw new Exception("Job file not found: {$jobFile}");
        }

        $this->updateJobStatus($job['id'], 'RUNNING');
        $this->log("Executing job: {$job['job_name']}", 'INFO');
        
        include $jobFile;

        $executionTime = microtime(true) - $startTime;

        $this->updateJobStatus($job['id'], 'SUCCESS', $executionTime);
        $newNextRun = date('Y-m-d H:i:s', $now + $job['interval_seconds']);
        $this->spexcon->query("UPDATE cron_jobs_params SET next_run = '$newNextRun' WHERE id = {$job['id']}");
    }

    private function updateJobStatus($jobId, $status, $executionTime = null, $errorMessage = null) {
        $query = "UPDATE cron_jobs_params SET last_status = ?, last_run = NOW()";
        $params = [$status];
        $types = "s";

        if ($executionTime !== null) {
            $query .= ", last_execution_time = ?, avg_execution_time = (avg_execution_time + ?) / 2";
            $params[] = $executionTime;
            $params[] = $executionTime;
            $types .= "dd";
        }

        if ($errorMessage !== null) {
            $query .= ", last_error_message = ?, error_count = error_count + 1";
            $params[] = $errorMessage;
            $types .= "s";
        } else if ($status === 'SUCCESS') {
            $query .= ", success_count = success_count + 1, retry_count = 0";
        }

        $query .= ", run_count = run_count + 1 WHERE id = ?";
        $params[] = $jobId;
        $types .= "i";

        $stmt = $this->spexcon->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    }

    private function calculateNextRun($intervalSeconds, $baseTime = null) {
        if ($baseTime) {
            $baseTimestamp = strtotime($baseTime);
        } else {
            $baseTimestamp = time();
        }
        return date('Y-m-d H:i:s', $baseTimestamp + $intervalSeconds);
    }

    public function run() {
        $this->log("ZeroJob Manager started", 'INFO');

        try {
            // Tüm aktif job’ları al, next_run kontrolü yapma → last_run üzerinden hesapla
            $stmt = $this->spexcon->prepare("SELECT * FROM cron_jobs_params WHERE is_active = 1 ORDER BY id ASC");
            $stmt->execute();
            $jobs = $stmt->get_result();

            $executedCount = 0;

            while ($job = $jobs->fetch_assoc()) {
                try {
                    $this->executeJob($job);
                    $executedCount++;
                    usleep(20000); // küçük gecikme
                } catch (Exception $e) {
                    $this->log("Job {$job['job_name']} failed: " . $e->getMessage(), 'ERROR');
                }
            }

            $this->log("ZeroJob Manager completed. Executed $executedCount jobs", 'INFO');

        } catch (Exception $e) {
            $this->log("ZeroJob Manager error: " . $e->getMessage(), 'FATAL');
        }
    }
}

if (!$spexcon) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed', 'timestamp' => date('Y-m-d H:i:s')]);
    exit;
}

$manager = new ZeroJobManager($spexcon);

if (php_sapi_name() === 'cli' || !empty($_GET['run'])) {
    $manager->run();
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'status' => $manager->getStatus(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>