<?php
/**
 * SpeX-Trading App Installation System
 * @package SpeX-Trading V3.5
 * @author SpeX Development
 * @instagram spexd
 */

session_start();
error_reporting(0);
ini_set('display_errors', 0);

if (file_exists('../core/system/.installed')) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../core/utils/SQLImporter.php';
require_once __DIR__ . '/../core/utils/ProxyManager.php';

class InstallRouter {
    private $stepsFile;
    private $stepsData;
    private $currentStep;
    
    public function __construct() {
        $this->stepsFile = __DIR__ . '/step.xml';
        $this->loadStepsData();
        $this->determineCurrentStep();
    }
    
    private function loadStepsData() {
        if (!file_exists($this->stepsFile)) {
            $this->createDefaultStepsFile();
        }
        
        $this->stepsData = simplexml_load_file($this->stepsFile);
    }
    
    private function createDefaultStepsFile() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<installation>
    <info>
        <version>3.5</version>
        <name>SpeX-Trading Platform</name>
        <author>SpeX Development</author>
        <created>' . date('Y-m-d H:i:s') . '</created>
    </info>
    <steps>
        <step name="WELCOME" value="0" title="Welcome" description="Welcome to SpeX Trading Platform" icon="fas fa-rocket" />
        <step name="REQUIREMENTS" value="0" title="System Check" description="Check system requirements" icon="fas fa-server" />
        <step name="DB_CONNECT" value="0" title="Database Setup" description="Configure database connection" icon="fas fa-database" />
        <step name="SQL_IMPORT" value="0" title="Import Database" description="Import database structure and data" icon="fas fa-download" />
        <step name="ADMIN_CREATE" value="0" title="Create Admin" description="Create administrator account" icon="fas fa-user-shield" />
        <step name="SETTINGS" value="0" title="Basic Settings" description="Configure basic system settings" icon="fas fa-cogs" />
        <step name="FINALIZE" value="0" title="Finalization" description="Complete installation process" icon="fas fa-check-circle" />
    </steps>
    <progress>
        <current_step>WELCOME</current_step>
        <total_steps>7</total_steps>
        <completed_steps>0</completed_steps>
        <installation_started>false</installation_started>
        <installation_completed>false</installation_completed>
    </progress>
</installation>';
        
        file_put_contents($this->stepsFile, $xml);
        $this->stepsData = simplexml_load_string($xml);
    }
    
    private function determineCurrentStep() {
        if (isset($_GET['step'])) {
            $requestedStep = strtoupper($_GET['step']);
            $validSteps = $this->getValidSteps();
            
            if (in_array($requestedStep, $validSteps)) {
                $this->currentStep = $requestedStep;
                return;
            }
        }
        
        $this->currentStep = (string)$this->stepsData->progress->current_step;
        
        if (empty($this->currentStep)) {
            $this->currentStep = 'WELCOME';
        }
    }
    
    private function getValidSteps() {
        $steps = [];
        foreach ($this->stepsData->steps->step as $step) {
            $steps[] = (string)$step['name'];
        }
        return $steps;
    }
    
    public function getCurrentStep() {
        return $this->currentStep;
    }
    
    public function getStepsData() {
        return $this->stepsData;
    }
    
    public function updateStep($stepName, $value) {
        foreach ($this->stepsData->steps->step as $step) {
            if ((string)$step['name'] === $stepName) {
                $step['value'] = $value;
                break;
            }
        }
        
        $completedSteps = 0;
        foreach ($this->stepsData->steps->step as $step) {
            if ((int)$step['value'] === 1) {
                $completedSteps++;
            }
        }
        
        $this->stepsData->progress->completed_steps = $completedSteps;
        $this->stepsData->progress->current_step = $stepName;
        
        if ($completedSteps === (int)$this->stepsData->progress->total_steps) {
            $this->stepsData->progress->installation_completed = 'true';
        }
        
        $this->stepsData->asXML($this->stepsFile);
    }
    
    public function getStepInfo($stepName) {
        foreach ($this->stepsData->steps->step as $step) {
            if ((string)$step['name'] === $stepName) {
                return [
                    'name' => (string)$step['name'],
                    'title' => (string)$step['title'],
                    'description' => (string)$step['description'],
                    'icon' => (string)$step['icon'],
                    'value' => (int)$step['value']
                ];
            }
        }
        return null;
    }
    
    public function getAllSteps() {
        $steps = [];
        foreach ($this->stepsData->steps->step as $step) {
            $steps[] = [
                'name' => (string)$step['name'],
                'title' => (string)$step['title'],
                'description' => (string)$step['description'],
                'icon' => (string)$step['icon'],
                'value' => (int)$step['value']
            ];
        }
        return $steps;
    }
}

$router = new InstallRouter();
$currentStep = $router->getCurrentStep();
$stepsData = $router->getStepsData();
$showLoading = !isset($_SESSION['install_loading_shown']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpeX-Core Base V3 - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --light-pink: #fef7f7;
            --soft-pink: #fce4ec;
            --blur-bg: rgba(255, 255, 255, 0.25);
            --text-primary: #2d3748;
            --text-secondary: #718096;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 25%, #fd79a8 50%, #a29bfe 75%, #74b9ff 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 1;
    transition: opacity 1s ease-out;
}

.loading-screen.fade-out {
    opacity: 0;
    pointer-events: none;
}

.loading-content {
    text-align: center;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
}

.brand-animation {
    position: relative;
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 100px;
}

.brand-text {
    position: absolute;
    font-family: 'Inter', sans-serif;
    font-weight: 800;
    color: #2c3e50;
    opacity: 0;
    text-shadow: 0 5px 30px rgba(44, 62, 80, 0.3);
}

.spex-core {
    font-size: 4.5rem;
    left: -300px;
    top: 50%;
    transform: translateY(-50%);
    animation: slideInFromLeft 1.5s ease-out 0.5s forwards;
}

.base-version {
    font-size: 2.5rem;
    right: -250px;
    top: 60%;
    transform: translateY(-50%);
    color: #667eea;
    animation: slideInFromRight 1.5s ease-out 1s forwards;
}

@keyframes slideInFromLeft {
    0% {
        left: -300px;
        opacity: 0;
        transform: translateY(-50%) scale(0.8);
    }
    100% {
        left: 50%;
        opacity: 1;
        transform: translateY(-50%) translateX(-80px) scale(1);
    }
}

@keyframes slideInFromRight {
    0% {
        right: -250px;
        opacity: 0;
        transform: translateY(-50%) scale(0.8);
    }
    100% {
        right: 50%;
        opacity: 1;
        transform: translateY(-50%) translateX(120px) scale(1);
    }
}

.loading-text-container {
    opacity: 0;
    animation: fadeInText 0.8s ease-out 3s forwards;
}

@keyframes fadeInText {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.loading-text {
    font-size: 1.1rem;
    color: #7f8c8d;
    font-weight: 500;
    margin: 0;
    padding: 15px 30px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 25px;
    border: 1px solid rgba(44, 62, 80, 0.1);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: all 0.5s ease;
}

.loading-text.changing {
    transform: scale(0.95);
    opacity: 0.7;
}

@media (max-width: 768px) {
    .spex-core {
        font-size: 3rem;
    }
    
    .base-version {
        font-size: 1.8rem;
    }
    
    @keyframes slideInFromLeft {
        100% {
            left: 50%;
            transform: translateY(-50%) translateX(-60px) scale(1);
        }
    }
    
    @keyframes slideInFromRight {
        100% {
            right: 50%;
            transform: translateY(-50%) translateX(80px) scale(1);
        }
    }
    
    .brand-animation {
        margin-bottom: 80px;
    }
}
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-15px) scale(1.02); }
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .loading-logo {
            font-size: 4.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            margin-bottom: 25px;
            animation: pulse-glow 2.5s ease-in-out infinite;
            position: relative;
        }
        
        .loading-logo::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.2), transparent);
            border-radius: 50%;
            animation: pulse-ring 2.5s ease-in-out infinite;
            z-index: -1;
        }
        
        @keyframes pulse-glow {
            0%, 100% { 
                transform: scale(1);
                filter: drop-shadow(0 0 25px rgba(102, 126, 234, 0.4));
            }
            50% { 
                transform: scale(1.08);
                filter: drop-shadow(0 0 40px rgba(102, 126, 234, 0.7));
            }
        }
        
        @keyframes pulse-ring {
            0%, 100% { 
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.3;
            }
            50% { 
                transform: translate(-50%, -50%) scale(1.3);
                opacity: 0.1;
            }
        }
        
        .loading-title {
            font-size: 3rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 15px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: slideInUp 0.8s ease-out;
        }
        
        .loading-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 50px;
            font-weight: 600;
            animation: slideInUp 0.8s ease-out 0.2s both;
        }
        
        .loading-progress {
            width: 350px;
            height: 12px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 25px;
            margin: 0 auto 25px;
            overflow: hidden;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .loading-bar {
            height: 100%;
            background: var(--accent-gradient);
            border-radius: 25px;
            width: 0%;
            animation: loading-animation 5s ease-out forwards;
            position: relative;
            box-shadow: 0 2px 10px rgba(79, 172, 254, 0.5);
        }
        
        .loading-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
            animation: shimmer 1.8s infinite;
        }
        
        @keyframes loading-animation {
            0% { width: 0%; }
            15% { width: 20%; }
            30% { width: 45%; }
            50% { width: 65%; }
            70% { width: 80%; }
            85% { width: 95%; }
            100% { width: 100%; }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .loading-text {
            font-size: 1rem;
            color: var(--text-secondary);
            font-weight: 600;
            animation: slideInUp 0.8s ease-out 0.4s both;
        }
        
        .install-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
            display: none;
        }
        
        .install-container.show {
            display: block;
            animation: slideInUp 0.8s ease-out;
        }
        
        .install-card {
            background: var(--blur-bg);
            backdrop-filter: blur(25px);
            border-radius: 30px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            margin-top: 20px;
        }
        
        .install-header {
            background: var(--primary-gradient);
            color: white;
            padding: 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .install-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.2;
        }
        
        .install-header h1 {
            font-size: 3.2rem;
            font-weight: 800;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .install-header p {
            font-size: 1.2rem;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            font-weight: 500;
        }
        
        .step-progress {
            padding: 40px;
            background: rgba(255, 255, 255, 0.6);
        }
        
        .progress-indicators {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .step-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            min-width: 120px;
            position: relative;
        }
        
        .step-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 12px;
            transition: all 0.4s ease;
            position: relative;
            z-index: 2;
        }
        
        .step-circle.completed {
            background: var(--accent-gradient);
            color: white;
            transform: scale(1.15);
            box-shadow: 0 15px 30px rgba(79, 172, 254, 0.4);
        }
        
        .step-circle.current {
            background: var(--secondary-gradient);
            color: white;
            animation: pulse-current 2.5s infinite;
            box-shadow: 0 15px 35px rgba(240, 147, 251, 0.5);
        }
        
        @keyframes pulse-current {
            0%, 100% { 
                transform: scale(1.1);
                box-shadow: 0 15px 35px rgba(240, 147, 251, 0.5);
            }
            50% { 
                transform: scale(1.2);
                box-shadow: 0 20px 45px rgba(240, 147, 251, 0.7);
            }
        }
        
        .step-circle.pending {
            background: rgba(255, 255, 255, 0.8);
            color: var(--text-secondary);
            border: 3px solid rgba(255, 255, 255, 0.6);
        }
        
        .step-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            text-align: center;
            line-height: 1.3;
        }
        
        .step-connector {
            position: absolute;
            top: 30px;
            left: 65%;
            right: -35%;
            height: 3px;
            background: rgba(255, 255, 255, 0.4);
            z-index: 1;
            border-radius: 5px;
        }
        
        .step-connector.completed {
            background: var(--accent-gradient);
            box-shadow: 0 2px 10px rgba(79, 172, 254, 0.3);
        }
        
        .progress-bar-container {
            background: rgba(255, 255, 255, 0.4);
            height: 12px;
            border-radius: 25px;
            overflow: hidden;
            margin-top: 25px;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .progress-bar-fill {
            height: 100%;
            background: var(--accent-gradient);
            border-radius: 25px;
            transition: width 0.8s ease;
            position: relative;
            box-shadow: 0 2px 10px rgba(79, 172, 254, 0.4);
        }
        
        .progress-bar-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 2.5s infinite;
        }
        
        .install-content {
            padding: 50px;
            background: rgba(255, 255, 255, 0.5);
        }
        
        @media (max-width: 768px) {
            .loading-content {
                padding: 50px 35px;
                margin: 20px;
            }
            
            .loading-title {
                font-size: 2.2rem;
            }
            
            .loading-logo {
                font-size: 3.5rem;
            }
            
            .progress-indicators {
                flex-direction: column;
                gap: 25px;
            }
            
            .step-indicator {
                flex-direction: row;
                min-width: auto;
                width: 100%;
                justify-content: flex-start;
            }
            
            .step-circle {
                margin-right: 20px;
                margin-bottom: 0;
            }
            
            .step-connector {
                display: none;
            }
        }
    </style>
</head>
<body>
    
    <?php if ($showLoading): ?>
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-content">
            <div class="loading-logo">
                <i class="fas fa-rocket"></i>
            </div>
            <h1 class="loading-title">SpeX-Core</h1>
            <p class="loading-subtitle">Base V3 Installation System</p>
            <div class="loading-progress">
                <div class="loading-bar"></div>
            </div>
            <p class="loading-text" id="loadingText">Initializing installation...</p>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="install-container" id="installContainer">
        <div class="install-card">
            <div class="install-header">
                <h1><i class="fas fa-cloud me-3"></i>SpeX-Core Installation</h1>
            </div>
            
            <div class="step-progress">
                <div class="progress-indicators">
                    <?php
                    $allSteps = $router->getAllSteps();
                    $totalSteps = count($allSteps);
                    $completedCount = 0;
                    
                    foreach ($allSteps as $index => $step) {
                        if ($step['value'] == 1) $completedCount++;
                        
                        $isCompleted = $step['value'] == 1;
                        $isCurrent = $step['name'] == $currentStep;
                        $isPending = !$isCompleted && !$isCurrent;
                        
                        $circleClass = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending');
                        $icon = $isCompleted ? '<i class="fas fa-check"></i>' : '<i class="' . $step['icon'] . '"></i>';
                        
                        echo '<div class="step-indicator">';
                        echo '<div class="step-circle ' . $circleClass . '">' . $icon . '</div>';
                        echo '<div class="step-label">' . $step['title'] . '</div>';
                        
                        if ($index < $totalSteps - 1) {
                            $connectorClass = $isCompleted ? 'completed' : '';
                            echo '<div class="step-connector ' . $connectorClass . '"></div>';
                        }
                        
                        echo '</div>';
                    }
                    
                    $progressPercentage = ($completedCount / $totalSteps) * 100;
                    ?>
                </div>
                
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: <?= $progressPercentage ?>%"></div>
                </div>
            </div>
            
            <div class="install-content">
                <?php include 'step.php'; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        <?php if ($showLoading): ?>
            const loadingTexts = [
                'Initializing SpeX-Core Base V3.5...',
                'Loading installation modules...',
                'Checking system compatibility...',
                'Preparing installation wizard...',
                'Configuring environment...',
                'Almost ready to begin...'
            ];
            
            let textIndex = 0;
            let textInterval;
            
            setTimeout(() => {
                textInterval = setInterval(() => {
                    $('#loadingText').addClass('changing');
                    
                    setTimeout(() => {
                        $('#loadingText').text(loadingTexts[textIndex]);
                        $('#loadingText').removeClass('changing');
                        textIndex = (textIndex + 1) % loadingTexts.length;
                    }, 250);
                    
                }, 1200);
            }, 4000);
            
            setTimeout(() => {
                if (textInterval) {
                    clearInterval(textInterval);
                }
                
                $('#loadingScreen').addClass('fade-out');
                setTimeout(() => {
                    $('#loadingScreen').remove();
                    $('#installContainer').addClass('show');
                }, 1000);
            }, 10000);
            <?php 
            $_SESSION['install_loading_shown'] = true;
            else: ?>
            $('#installContainer').addClass('show');
        <?php endif; ?>
        
        $.ajaxSetup({
            beforeSend: function() {
            },
            complete: function() {
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Please check your internet connection and try again.',
                    confirmButtonColor: '#667eea'
                });
            }
        });
    });
    
    function goToStep(stepName) {
        window.location.href = 'index.php?step=' + stepName;
    }
    
    function showSuccess(title, text, callback = null) {
        Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            confirmButtonColor: '#667eea',
            timer: 2000
        }).then(() => {
            if (callback) callback();
        });
    }
    
    function showError(title, text) {
        Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            confirmButtonColor: '#667eea'
        });
    }
    
    function showWarning(title, text) {
        Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            confirmButtonColor: '#667eea'
        });
    }
    </script>
</body>
</html>