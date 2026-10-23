<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    switch ($_POST['action']) {
        case 'get_themes':
            handleGetThemes();
            break;
        case 'get_theme':
            handleGetTheme();
            break;
        case 'create_theme':
            handleCreateTheme();
            break;
        case 'update_theme':
            handleUpdateTheme();
            break;
        case 'delete_theme':
            handleDeleteTheme();
            break;
        case 'activate_theme':
            handleActivateTheme();
            break;
        case 'duplicate_theme':
            handleDuplicateTheme();
            break;
        case 'set_default_theme':
            handleSetDefaultTheme();
            break;
        case 'export_theme':
            handleExportTheme();
            break;
        case 'import_theme':
            handleImportTheme();
            break;
        case 'reset_theme':
            handleResetTheme();
            break;
        case 'get_field_groups':
            handleGetFieldGroups();
            break;
        case 'get_theme_stats':
            handleGetThemeStats();
            break;
        case 'preview_theme':
            handlePreviewTheme();
            break;
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleGetThemes() {
    $orderBy = $_POST['order_by'] ?? 'theme_name';
    $orderDirection = $_POST['order_direction'] ?? 'ASC';
    
    $themes = getAllThemes($orderBy, $orderDirection);
    echo json_encode(['success' => true, 'themes' => $themes]);
}

function handleGetTheme() {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Theme ID is required');
    }
    
    $theme = getThemeById($id);
    if (!$theme) {
        throw new Exception('Theme not found');
    }
    
    echo json_encode(['success' => true, 'theme' => $theme]);
}

function handleCreateTheme() {
    $themeData = [];
    $fieldGroups = getThemeFieldGroups();
    
    foreach ($fieldGroups as $group) {
        foreach ($group['fields'] as $fieldName => $fieldConfig) {
            if (isset($_POST[$fieldName])) {
                $themeData[$fieldName] = Sanitize($_POST[$fieldName]);
            } elseif (isset($fieldConfig['default'])) {
                $themeData[$fieldName] = $fieldConfig['default'];
            }
        }
    }
    
    if (empty($themeData['theme_name'])) {
        throw new Exception('Theme name is required');
    }
    
    $themeId = createNewTheme($themeData);
    echo json_encode(['success' => true, 'message' => 'Theme created successfully', 'theme_id' => $themeId]);
}

function handleUpdateTheme() {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Theme ID is required');
    }
    
    $themeData = [];
    $fieldGroups = getThemeFieldGroups();
    
    foreach ($fieldGroups as $group) {
        foreach ($group['fields'] as $fieldName => $fieldConfig) {
            if (isset($_POST[$fieldName])) {
                $themeData[$fieldName] = Sanitize($_POST[$fieldName]);
            }
        }
    }
    
    updateTheme($id, $themeData);
    echo json_encode(['success' => true, 'message' => 'Theme updated successfully']);
}

function handleDeleteTheme() {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Theme ID is required');
    }
    
    deleteTheme($id);
    echo json_encode(['success' => true, 'message' => 'Theme deleted successfully']);
}

function handleActivateTheme() {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Theme ID is required');
    }
    
    activateTheme($id);
    echo json_encode(['success' => true, 'message' => 'Theme activated successfully']);
}

function handleDuplicateTheme() {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Theme ID is required');
    }
    
    $newName = $_POST['new_name'] ?? null;
    $newThemeId = duplicateTheme($id, $newName);
    
    echo json_encode(['success' => true, 'message' => 'Theme duplicated successfully', 'new_theme_id' => $newThemeId]);
}

function handleSetDefaultTheme() {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Theme ID is required');
    }
    
    setDefaultTheme($id);
    echo json_encode(['success' => true, 'message' => 'Default theme set successfully']);
}

function handleExportTheme() {
    $id = (int)($_POST['id'] ?? 0);
    $format = $_POST['format'] ?? 'json';
    
    if (!$id) {
        throw new Exception('Theme ID is required');
    }
    
    $theme = getThemeById($id);
    if (!$theme) {
        throw new Exception('Theme not found');
    }
    
    $exportData = exportTheme($id, $format);
    
    $filename = 'theme_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $theme['theme_name']) . '_' . date('Y-m-d_H-i-s');
    $filename .= ($format === 'css') ? '.css' : '.json';
    
    echo json_encode([
        'success' => true, 
        'data' => $exportData,
        'format' => $format,
        'filename' => $filename
    ]);
}

function handleImportTheme() {
    $data = $_POST['theme_data'] ?? '';
    $format = $_POST['format'] ?? 'json';
    
    if (empty($data)) {
        throw new Exception('Theme data is required');
    }
    
    $themeId = importTheme($data, $format);
    echo json_encode(['success' => true, 'message' => 'Theme imported successfully', 'theme_id' => $themeId]);
}

function handleResetTheme() {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Theme ID is required');
    }
    
    resetThemeToDefaults($id);
    echo json_encode(['success' => true, 'message' => 'Theme reset to defaults successfully']);
}

function handleGetFieldGroups() {
    $fieldGroups = getThemeFieldGroups();
    echo json_encode(['success' => true, 'field_groups' => $fieldGroups]);
}

function handleGetThemeStats() {
    $stats = getThemeStats();
    echo json_encode(['success' => true, 'stats' => $stats]);
}

function handlePreviewTheme() {
    $themeData = [];
    $fieldGroups = getThemeFieldGroups();

    foreach ($fieldGroups as $group) {
        foreach ($group['fields'] as $fieldName => $fieldConfig) {
            if (isset($_POST[$fieldName])) {
                $themeData[$fieldName] = $_POST[$fieldName];
            }
        }
    }
    
    $css = getThemePreviewCSS($themeData);
    echo json_encode(['success' => true, 'css' => $css]);
}
?>