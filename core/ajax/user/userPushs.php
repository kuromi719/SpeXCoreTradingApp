<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../utils/CRUDManager.php';

header('Content-Type: application/json');

global $spexcon;

try {
    $UID = getSession('UID');
    
    if (!$UID) {
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }
    
    $crud = new CRUDManager();
    $action = $_POST['action'] ?? $_GET['action'] ?? 'get';
    
    if ($action === 'close') {
        $notificationId = $_POST['id'] ?? 0;
        
        if ($notificationId > 0) {
            $result = $crud->update('user_pushs', 
                ['closed' => 1], 
                ['id' => $notificationId, 'uid' => $UID]
            );
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Failed to close notification']);
            }
        } else {
            echo json_encode(['error' => 'Invalid notification ID']);
        }
        exit;
    }
    
    $notifications = $crud->query("
        SELECT * FROM user_pushs 
        WHERE uid = ? AND closed = 0 
        ORDER BY date DESC 
        LIMIT 120
    ", [$UID]);
    
    $html = '';
    $count = count($notifications);
    $todayNotifications = [];
    $yesterdayNotifications = [];
    $olderNotifications = [];
    
    foreach ($notifications as $notification) {
        $notificationDate = new DateTime($notification['date']);
        $now = new DateTime();
        $yesterday = new DateTime('yesterday');
        
        if ($notificationDate->format('Y-m-d') === $now->format('Y-m-d')) {
            $todayNotifications[] = $notification;
        } elseif ($notificationDate->format('Y-m-d') === $yesterday->format('Y-m-d')) {
            $yesterdayNotifications[] = $notification;
        } else {
            $olderNotifications[] = $notification;
        }
    }
    
    function formatTimeAgo($date) {
        $now = new DateTime();
        $notificationDate = new DateTime($date);
        $diff = $now->diff($notificationDate);
        
        if ($diff->days > 0) {
            return $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' min' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Just now';
        }
    }
    
    function getAlertClass($type) {
        $types = [
            'login' => 'alert-warning',
            'security' => 'alert-danger',
            'trade' => 'alert-success',
            'announcement' => 'alert-primary',
            'coupon' => 'alert-info',
            'system' => 'alert-secondary',
            'profit' => 'alert-success',
            'loss' => 'alert-danger',
            'deposit' => 'alert-success',
            'withdrawal' => 'alert-warning'
        ];
        return $types[$type] ?? 'alert-primary';
    }
    
    function getNotificationIcon($type) {
        $icons = [
            'login' => '🔐',
            'security' => '🛡️',
            'trade' => '💰',
            'announcement' => '📢',
            'coupon' => '🔖',
            'system' => '⚙️',
            'profit' => '📈',
            'info'  => 'ℹ️',
            'danger' => '⛔',
            'warning' => '⚠️',
            'loss' => '📉',
            'deposit' => '💵',
            'withdrawal' => '💸'
        ];
        return $icons[$type] ?? '📋';
    }
    
    function generateNotificationHtml($notifications, $sectionTitle = '') {
        $html = '';
        
        if (!empty($notifications) && $sectionTitle) {
            $html .= '<h6 class="crypt-grayscale-400 mt-3 mb-2">' . $sectionTitle . '</h6>';
        }
        
        foreach ($notifications as $notification) {
            $alertClass = getAlertClass($notification['type']);
            $icon = getNotificationIcon($notification['type']);
            $timeAgo = formatTimeAgo($notification['date']);
            
            $html .= '
            <div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert" data-notification-id="' . $notification['id'] . '">
                <p class="crypt-grayscale-600 text-sm mb-0">' . $timeAgo . '</p>
                <a class="text-dark fs-6 fw-bold text-link" href="#!">' . $icon . ' ' . htmlspecialchars($notification['title']) . '</a>
                <p class="mt-2">' . htmlspecialchars($notification['descr']) . '</p>
                <button type="button" class="btn-close close-notification" data-notification-id="' . $notification['id'] . '" aria-label="Close"></button>
            </div>';
        }
        
        return $html;
    }
    
    if (!empty($todayNotifications)) {
        $html .= generateNotificationHtml($todayNotifications, 'Today');
    }
    
    if (!empty($yesterdayNotifications)) {
        $html .= generateNotificationHtml($yesterdayNotifications, 'Yesterday');
    }
    
    if (!empty($olderNotifications)) {
        $html .= generateNotificationHtml($olderNotifications, 'Earlier');
    }
    
    if (empty($notifications)) {
        $html = '
        <div class="text-center py-5">
            <div class="mb-3">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="crypt-grayscale-500 mb-0">No notifications</p>
            <small class="crypt-grayscale-600">You\'re all caught up!</small>
        </div>';
    }
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'count' => $count
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

?>