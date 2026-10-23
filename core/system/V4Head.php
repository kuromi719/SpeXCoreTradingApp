<?php
// ENTRY Protect-File's
ProtectFile(__DIR__ . '/SpeXSecV3.php', 'SpeXSecV3');
ProtectFile(__DIR__ . '/../observer-mode.php', 'ObServer-Mode');
ProtectFile(__DIR__ . '/ke-bug.php', 'keBug');
ProtectFile(__DIR__ . '/V4Head.php', 'V4Head');
ProtectFile(__DIR__ . '/web.html', 'Web');
ProtectFile(__DIR__ . '/../../index.php', '[PROD_INDEX]');
ProtectFile(__DIR__ . '/spex_v4_func.php', 'SpeXV4Func');
// END Protect-File's

startSecureSession();
CheckLicenseAndDie();

function CheckLicenseAndDie() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $now = time();
    $sessionKey = 'SPEX_STORED_WORK_0';
    $sessionTimeKey = 'SPEX_STORED_WORK_0_TIME';
    $expireSeconds = 30 * 60;

    $checkLicenseNeeded = true;

    if (isset($_SESSION[$sessionKey], $_SESSION[$sessionTimeKey])) {
        $lastCheck = $_SESSION[$sessionTimeKey];
        $status = $_SESSION[$sessionKey];
        if (($now - $lastCheck) < $expireSeconds && $status === 0) {
            $checkLicenseNeeded = false;
        }
    }

    if ($checkLicenseNeeded) {
        $license = new SpeXLicense();
        $domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        $cleanDomain = $license->cleanDomain($domain);

        $result = $license->checkLicenseSigs($cleanDomain);
        // $result = [
        //    'success' => true/false,
        //    'license' => [...],
        //    'signature' => '...'
        // ]

        if (!$result['success']) {
            $_SESSION[$sessionKey] = 1;
            $_SESSION[$sessionTimeKey] = $now;
            $message = htmlspecialchars($result['error'] ?? 'License check failed.');
            dieWithLicenseError($message);
        }

        if (!verifyLicenseSignature($result['license'], $result['signature'])) {
            $_SESSION[$sessionKey] = 1;
            $_SESSION[$sessionTimeKey] = $now;
            dieWithLicenseError('License signature verification failed.');
        }

        $expires = strtotime($result['license']['expires']);
        if ($now > $expires) {
            $_SESSION[$sessionKey] = 1;
            $_SESSION[$sessionTimeKey] = $now;
            dieWithLicenseError('License has expired.');
        }

        // valid !!
        $_SESSION[$sessionKey] = 0;
        $_SESSION[$sessionTimeKey] = $now;

    } else {
        if ($_SESSION[$sessionKey] === 1) {
            $message = 'License previously marked as invalid. Please check your license or contact support.';
            dieWithLicenseError($message);
        }
    }
}

function verifyLicenseSignature(array $license, string $signature): bool {
    $secretKey = getenv('SPEX_LICENSE_WEBHOOK_SECRET') ?: '';
    if ($secretKey === '') {
        return false;
    }
    $json = json_encode($license, JSON_UNESCAPED_SLASHES);
    $expectedSignature = hash_hmac('sha256', $json, $secretKey);
    return hash_equals($expectedSignature, $signature);
}

function dieWithLicenseError(string $message): void {
    die('
        <!DOCTYPE html>
        <html lang="en" class="dark">
        <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>License Error - SpeX</title>
        <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gradient-to-tr from-gray-950 via-gray-900 to-gray-950 text-gray-100 min-h-screen flex items-center justify-center px-4">
        <main class="bg-gray-900 bg-opacity-70 backdrop-blur-xl rounded-3xl shadow-2xl border border-red-700 max-w-xl w-full p-12 text-center select-none">
            <h1 class="text-4xl font-extrabold tracking-tight mb-4 text-gray-500 drop-shadow-md">
            SpeX-SecV3
            </h1>
            <h1 class="text-4xl font-extrabold tracking-tight mb-4 text-red-500 drop-shadow-md">
            License Verification Failed
            </h1>
            <p class="text-gray-300 text-lg max-w-md mx-auto mb-8 leading-relaxed">
            We could not verify a valid license for this domain.  
            This may be because the license is missing, expired, or invalid.
            </p>
            <section aria-label="Error Details" tabindex="0" class="bg-red-900 bg-opacity-40 border border-red-600 rounded-lg p-5 text-red-200 font-mono text-sm leading-snug max-h-44 overflow-auto mb-10 select-text">
            <strong>Error Details:</strong><br />' . htmlspecialchars($message) . '
            </section>
            <footer class="text-gray-400 text-sm">
            If you believe this is an error, please contact us via  
            <a href="https://instagram.com/spexd" target="_blank" rel="noopener noreferrer" class="text-red-400 font-semibold hover:text-red-600 underline transition">
                @spexd on Instagram
            </a>.
            </footer>
        </main>
        </body>
        </html>
    ');
}

// SpeX-License END

// Theme Functions ENTRY

function getActiveTheme() {
    global $spexcon;
    
    if (isset($_SESSION['active_theme_cache']) && isset($_SESSION['theme_cache_time'])) {
        $cacheTime = $_SESSION['theme_cache_time'];
        if ((time() - $cacheTime) < 300) {
            return $_SESSION['active_theme_cache'];
        }
    }
    
    $query = $spexcon->query("SELECT * FROM custom_theme_styles WHERE is_active = 1 LIMIT 1");
    if ($query && $query->num_rows > 0) {
        $theme = $query->fetch_assoc();
        
        $_SESSION['active_theme_cache'] = $theme;
        $_SESSION['theme_cache_time'] = time();
        
        return $theme;
    }
    
    $defaultQuery = $spexcon->query("SELECT * FROM custom_theme_styles WHERE is_default = 1 LIMIT 1");
    if ($defaultQuery && $defaultQuery->num_rows > 0) {
        $theme = $defaultQuery->fetch_assoc();
        
        $_SESSION['active_theme_cache'] = $theme;
        $_SESSION['theme_cache_time'] = time();
        
        return $theme;
    }
    
    return null;
}

function clearThemeCache() {
    unset($_SESSION['active_theme_cache']);
    unset($_SESSION['theme_cache_time']);
}

function renderOptimizedThemeCSS() {
    $theme = getActiveTheme();
    if (!$theme) {
        echo "<!-- No active theme found -->\n";
        return;
    }
    
    echo "<!-- Dynamic Theme: {$theme['theme_name']} -->\n";
    echo "<style id=\"dynamic-theme-override\">\n";
    
    // Root variables
    echo ":root {\n";
    echo "  --delay: {$theme['delay']};\n";
    echo "  --sidebar-width: {$theme['sidebar_width']};\n";
    echo "  --color-data-up: {$theme['color_data_up']};\n";
    echo "  --color-data-down: {$theme['color_data_down']};\n";
    echo "  --color-dark: {$theme['color_dark']};\n";
    echo "  --preset-1: {$theme['preset_1']};\n";
    
    // GSAP highlights
    echo "  --color-text: {$theme['color_text']};\n";
    echo "  --color-text-highlight: {$theme['color_text_highlight']};\n";
    echo "  --bg-color-highlight: {$theme['bg_color_highlight']};\n";
    
    // Fonts
    echo "  --global-font: {$theme['global_font']};\n";
    echo "  --font-1: {$theme['font_1']};\n";
    echo "  --font-2: {$theme['font_2']};\n";
    echo "  --font-mono: {$theme['font_mono']};\n";
    echo "}\n\n";
    
    // Dark theme variables
    echo "body.crypt-dark {\n";
    echo "  --color-text: {$theme['dark_color_text']};\n";
    echo "  --color-text-highlight: {$theme['dark_color_text_highlight']};\n";
    echo "  --color-primary-50: {$theme['dark_color_primary_50']};\n";
    echo "  --color-primary-100: {$theme['dark_color_primary_100']};\n";
    echo "  --color-primary-200: {$theme['dark_color_primary_200']};\n";
    echo "  --color-primary-300: {$theme['dark_color_primary_300']};\n";
    echo "  --color-primary-400: {$theme['dark_color_primary_400']};\n";
    echo "  --color-primary-500: {$theme['dark_color_primary_500']};\n";
    echo "  --color-primary-600: {$theme['dark_color_primary_600']};\n";
    echo "  --color-primary-700: {$theme['dark_color_primary_700']};\n";
    echo "  --color-primary-800: {$theme['dark_color_primary_800']};\n";
    echo "  --color-primary-900: {$theme['dark_color_primary_900']};\n";
    echo "  --color-primary-950: {$theme['dark_color_primary_950']};\n";
    echo "  --dl-reverse: {$theme['dark_dl_reverse']};\n";
    echo "  --brand-color: {$theme['dark_brand_color']};\n";
    echo "  --brand-hover-color: {$theme['dark_brand_hover_color']};\n";
    echo "  --brand-hover-bg: {$theme['dark_brand_hover_bg']};\n";
    echo "  --card-color: {$theme['dark_card_color']};\n";
    echo "  --border-color: {$theme['dark_border_color']};\n";
    echo "  --color-bg: {$theme['dark_color_bg']};\n";
    echo "  --tab-bg-color: {$theme['dark_tab_bg_color']};\n";
    echo "  --color-input-field: {$theme['dark_color_input_field']};\n";
    echo "}\n\n";
    
    // Light theme variables
    echo "body.crypt-light {\n";
    echo "  --color-primary-50: {$theme['light_color_primary_50']};\n";
    echo "  --color-primary-100: {$theme['light_color_primary_100']};\n";
    echo "  --color-primary-200: {$theme['light_color_primary_200']};\n";
    echo "  --color-primary-300: {$theme['light_color_primary_300']};\n";
    echo "  --color-primary-400: {$theme['light_color_primary_400']};\n";
    echo "  --color-primary-500: {$theme['light_color_primary_500']};\n";
    echo "  --color-primary-600: {$theme['light_color_primary_600']};\n";
    echo "  --color-primary-700: {$theme['light_color_primary_700']};\n";
    echo "  --color-primary-800: {$theme['light_color_primary_800']};\n";
    echo "  --color-primary-900: {$theme['light_color_primary_900']};\n";
    echo "  --color-primary-950: {$theme['light_color_primary_950']};\n";
    echo "  --dl-reverse: {$theme['light_dl_reverse']};\n";
    echo "  --brand-color: {$theme['light_brand_color']};\n";
    echo "  --brand-hover-color: {$theme['light_brand_hover_color']};\n";
    echo "  --brand-hover-bg: {$theme['light_brand_hover_bg']};\n";
    echo "  --card-color: {$theme['light_card_color']};\n";
    echo "  --border-color: {$theme['light_border_color']};\n";
    echo "  --color-bg: {$theme['light_color_bg']};\n";
    echo "  --tab-bg-color: {$theme['light_tab_bg_color']};\n";
    echo "  --color-input-field: {$theme['light_color_input_field']};\n";
    echo "}\n";

    echo "@media screen and (max-width: 575px) {:root {--sidebar-width: 42px;}}";

    echo "</style>\n";
}

function renderMinimalThemeCSS() {
    $theme = getActiveTheme();
    if (!$theme) return;
    
    echo "<style id=\"minimal-theme\">\n";
    echo ":root{--brand-color:{$theme['dark_brand_color']};--brand-hover-color:{$theme['dark_brand_hover_color']};--card-color:{$theme['dark_card_color']};--border-color:{$theme['dark_border_color']};--color-bg:{$theme['dark_color_bg']};}\n";
    echo "body.crypt-light{--brand-color:{$theme['light_brand_color']};--brand-hover-color:{$theme['light_brand_hover_color']};--card-color:{$theme['light_card_color']};--border-color:{$theme['light_border_color']};--color-bg:{$theme['light_color_bg']};}\n";
    echo "</style>\n";
}

function getThemePreviewCSS($themeData) {
    if (empty($themeData)) return '';
    
    $css = ":root {\n";
    $css .= "  --brand-color: {$themeData['dark_brand_color']};\n";
    $css .= "  --brand-hover-color: {$themeData['dark_brand_hover_color']};\n";
    $css .= "  --card-color: {$themeData['dark_card_color']};\n";
    $css .= "  --border-color: {$themeData['dark_border_color']};\n";
    $css .= "  --color-bg: {$themeData['dark_color_bg']};\n";
    $css .= "}\n";
    
    $css .= "body.crypt-light {\n";
    $css .= "  --brand-color: {$themeData['light_brand_color']};\n";
    $css .= "  --brand-hover-color: {$themeData['light_brand_hover_color']};\n";
    $css .= "  --card-color: {$themeData['light_card_color']};\n";
    $css .= "  --border-color: {$themeData['light_border_color']};\n";
    $css .= "  --color-bg: {$themeData['light_color_bg']};\n";
    $css .= "}\n";
    
    return $css;
}

// Theme Functions END

// Advanced Theme Management Functions ENTRY

function getAllThemes($orderBy = 'theme_name', $orderDirection = 'ASC') {
    global $spexcon;
    
    $validOrderBy = ['id', 'theme_name', 'created_at', 'updated_at', 'is_active', 'is_default'];
    $orderBy = in_array($orderBy, $validOrderBy) ? $orderBy : 'theme_name';
    $orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';
    
    $query = $spexcon->query("
        SELECT *, 
               CASE WHEN is_default = 1 THEN 'Default' 
                    WHEN is_active = 1 THEN 'Active' 
                    ELSE 'Inactive' END as status
        FROM custom_theme_styles 
        ORDER BY is_default DESC, is_active DESC, {$orderBy} {$orderDirection}
    ");
    
    $themes = [];
    while ($row = $query->fetch_assoc()) {
        $themes[] = $row;
    }
    
    return $themes;
}

function getThemeById($themeId) {
    global $spexcon;
    
    $stmt = $spexcon->prepare("SELECT * FROM custom_theme_styles WHERE id = ?");
    $stmt->bind_param("i", $themeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

function getThemeStats() {
    global $spexcon;
    
    $stats = [];
    
    $query = $spexcon->query("SELECT COUNT(*) as total FROM custom_theme_styles");
    $stats['total_themes'] = $query->fetch_assoc()['total'];
    
    $query = $spexcon->query("SELECT COUNT(*) as active FROM custom_theme_styles WHERE is_active = 1");
    $stats['active_themes'] = $query->fetch_assoc()['active'];
    
    $query = $spexcon->query("SELECT COUNT(*) as default_count FROM custom_theme_styles WHERE is_default = 1");
    $stats['default_themes'] = $query->fetch_assoc()['default_count'];
    
    $query = $spexcon->query("SELECT theme_name, updated_at FROM custom_theme_styles ORDER BY updated_at DESC LIMIT 1");
    $lastUpdated = $query->fetch_assoc();
    $stats['last_updated_theme'] = $lastUpdated['theme_name'] ?? 'None';
    $stats['last_updated_date'] = $lastUpdated['updated_at'] ?? null;
    
    return $stats;
}

function ProtectFile(string $filePath, string $name): void {
    if (!file_exists($filePath)) {
        die('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <title>' . htmlspecialchars($name) . ' Missing</title>
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Fira+Mono&display=swap");

                body {
                    background: linear-gradient(135deg, #1e1e2f, #111118);
                    color: #ff4c4c;
                    font-family: "Fira Mono", monospace, monospace;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    text-align: center;
                    padding: 0 20px;
                    user-select: none;
                }
                h1 {
                    font-size: 3.6rem;
                    margin-bottom: 0.6em;
                    letter-spacing: 2px;
                    text-shadow: 0 0 8px #ff4c4c88;
                }
                p {
                    font-size: 1.2rem;
                    color: #ccc;
                    max-width: 600px;
                    line-height: 1.5;
                    margin-bottom: 2rem;
                }
                a.contact-link {
                    color: #ff7373;
                    text-decoration: none;
                    font-weight: 600;
                    border-bottom: 2px solid transparent;
                    transition: border-color 0.3s ease;
                }
                a.contact-link:hover,
                a.contact-link:focus {
                    border-color: #ff4c4c;
                    outline: none;
                }
                footer {
                    font-size: 0.9rem;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <h1>' . htmlspecialchars($name) . ' NOT FOUND</h1>
            <p>The required file <strong>' . htmlspecialchars($name) . '</strong> is missing or corrupted.<br><br>
            This component is essential for the system to operate properly.<br><br>
            Without it, critical functionalities will not work.</p>
            <p>If you need assistance, please <a href="https://instagram.com/spexd" target="_blank" rel="noopener noreferrer" class="contact-link">contact the developer on Instagram</a>.</p>
            <footer>Thank you for your understanding and support.</footer>
        </body>
        </html>
        ');
    }
}

function includeThemeCSS() {
    renderOptimizedThemeCSS();
}

function createNewTheme($themeData) {
    global $spexcon;
    
    $requiredFields = ['theme_name'];
    foreach ($requiredFields as $field) {
        if (empty($themeData[$field])) {
            throw new Exception("Field '{$field}' is required");
        }
    }
    
    $stmt = $spexcon->prepare("SELECT id FROM custom_theme_styles WHERE theme_name = ?");
    $stmt->bind_param("s", $themeData['theme_name']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("Theme name already exists");
    }
    
    $allFields = getAllThemeFields();
    
    $insertFields = [];
    $insertPlaceholders = [];
    $params = [];
    $types = '';
    
    foreach ($allFields as $field) {
        if (isset($themeData[$field])) {
            $insertFields[] = $field;
            $insertPlaceholders[] = '?';
            $params[] = Sanitize($themeData[$field]);
            $types .= 's';
        }
    }
    
    $insertFields[] = 'is_active';
    $insertFields[] = 'is_default';
    $insertFields[] = 'created_at';
    $insertFields[] = 'updated_at';
    
    $insertPlaceholders[] = '?';
    $insertPlaceholders[] = '?';
    $insertPlaceholders[] = 'current_timestamp()';
    $insertPlaceholders[] = 'current_timestamp()';
    
    $params[] = '0'; // Not active by default
    $params[] = '0'; // Not default
    $types .= 'ss';
    
    $sql = "INSERT INTO custom_theme_styles (" . implode(', ', $insertFields) . ") 
            VALUES (" . implode(', ', $insertPlaceholders) . ")";
    
    $stmt = $spexcon->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        return $spexcon->insert_id;
    } else {
        throw new Exception("Failed to create theme: " . $stmt->error);
    }
}

function updateTheme($themeId, $themeData) {
    global $spexcon;
    
    $themeId = (int)$themeId;
    if (!$themeId) {
        throw new Exception("Invalid theme ID");
    }
    
    if (!getThemeById($themeId)) {
        throw new Exception("Theme not found");
    }
    
    if (isset($themeData['theme_name'])) {
        $stmt = $spexcon->prepare("SELECT id FROM custom_theme_styles WHERE theme_name = ? AND id != ?");
        $stmt->bind_param("si", $themeData['theme_name'], $themeId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Theme name already exists");
        }
    }
    
    $allFields = getAllThemeFields();
    
    $updateFields = [];
    $params = [];
    $types = '';
    
    foreach ($allFields as $field) {
        if (isset($themeData[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = Sanitize($themeData[$field]);
            $types .= 's';
        }
    }
    
    if (empty($updateFields)) {
        throw new Exception("No valid fields to update");
    }
    
    $updateFields[] = "updated_at = current_timestamp()";
    $params[] = $themeId;
    $types .= 'i';
    
    $sql = "UPDATE custom_theme_styles SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $spexcon->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        clearThemeCache();
        return true;
    } else {
        throw new Exception("Failed to update theme: " . $stmt->error);
    }
}

function deleteTheme($themeId) {
    global $spexcon;
    
    $themeId = (int)$themeId;
    if (!$themeId) {
        throw new Exception("Invalid theme ID");
    }
    
    $theme = getThemeById($themeId);
    if (!$theme) {
        throw new Exception("Theme not found");
    }
    
    if ($theme['is_default'] == 1) {
        throw new Exception("Cannot delete default theme");
    }
    
    if ($theme['is_active'] == 1) {
        $spexcon->query("UPDATE custom_theme_styles SET is_active = 1 WHERE is_default = 1");
    }
    
    $stmt = $spexcon->prepare("DELETE FROM custom_theme_styles WHERE id = ?");
    $stmt->bind_param("i", $themeId);
    
    if ($stmt->execute()) {
        clearThemeCache();
        return true;
    } else {
        throw new Exception("Failed to delete theme: " . $stmt->error);
    }
}

function activateTheme($themeId) {
    global $spexcon;
    
    $themeId = (int)$themeId;
    if (!$themeId) {
        throw new Exception("Invalid theme ID");
    }
    
    if (!getThemeById($themeId)) {
        throw new Exception("Theme not found");
    }
    
    $spexcon->query("UPDATE custom_theme_styles SET is_active = 0");
    
    $stmt = $spexcon->prepare("UPDATE custom_theme_styles SET is_active = 1 WHERE id = ?");
    $stmt->bind_param("i", $themeId);
    
    if ($stmt->execute()) {
        clearThemeCache();
        return true;
    } else {
        throw new Exception("Failed to activate theme: " . $stmt->error);
    }
}

function duplicateTheme($themeId, $newName = null) {
    global $spexcon;
    
    $original = getThemeById($themeId);
    if (!$original) {
        throw new Exception("Original theme not found");
    }
    
    unset($original['id']);
    $original['theme_name'] = $newName ?: ($original['theme_name'] . ' - Copy ' . date('Y-m-d H:i'));
    $original['is_active'] = 0;
    $original['is_default'] = 0;
    
    $stmt = $spexcon->prepare("SELECT id FROM custom_theme_styles WHERE theme_name = ?");
    $stmt->bind_param("s", $original['theme_name']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $original['theme_name'] .= ' (' . uniqid() . ')';
    }
    
    $fields = array_keys($original);
    $placeholders = str_repeat('?,', count($fields) - 1) . '?';
    
    $sql = "INSERT INTO custom_theme_styles (" . implode(', ', $fields) . ") VALUES ($placeholders)";
    $stmt = $spexcon->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($fields)), ...array_values($original));
    
    if ($stmt->execute()) {
        return $spexcon->insert_id;
    } else {
        throw new Exception("Failed to duplicate theme: " . $stmt->error);
    }
}

function setDefaultTheme($themeId) {
    global $spexcon;
    
    $themeId = (int)$themeId;
    if (!$themeId) {
        throw new Exception("Invalid theme ID");
    }
    
    if (!getThemeById($themeId)) {
        throw new Exception("Theme not found");
    }
    
    $spexcon->query("UPDATE custom_theme_styles SET is_default = 0");
    
    $stmt = $spexcon->prepare("UPDATE custom_theme_styles SET is_default = 1 WHERE id = ?");
    $stmt->bind_param("i", $themeId);
    
    if ($stmt->execute()) {
        clearThemeCache();
        return true;
    } else {
        throw new Exception("Failed to set default theme: " . $stmt->error);
    }
}

function getAllThemeFields() {
    return [
        'theme_name',
        'theme_description',
        
        // Root Variables
        'delay',
        'sidebar_width',
        'color_data_up',
        'color_data_down',
        'color_dark',
        'preset_1',
        
        // GSAP Highlight Colors
        'color_text',
        'color_text_highlight',
        'bg_color_highlight',
        
        // Fonts
        'global_font',
        'font_1',
        'font_2',
        'font_mono',
        
        // Dark Theme Colors
        'dark_color_text',
        'dark_color_text_highlight',
        'dark_color_primary_50',
        'dark_color_primary_100',
        'dark_color_primary_200',
        'dark_color_primary_300',
        'dark_color_primary_400',
        'dark_color_primary_500',
        'dark_color_primary_600',
        'dark_color_primary_700',
        'dark_color_primary_800',
        'dark_color_primary_900',
        'dark_color_primary_950',
        'dark_dl_reverse',
        'dark_brand_color',
        'dark_brand_hover_color',
        'dark_brand_hover_bg',
        'dark_card_color',
        'dark_border_color',
        'dark_color_bg',
        'dark_tab_bg_color',
        'dark_color_input_field',
        
        // Light Theme Colors
        'light_color_primary_50',
        'light_color_primary_100',
        'light_color_primary_200',
        'light_color_primary_300',
        'light_color_primary_400',
        'light_color_primary_500',
        'light_color_primary_600',
        'light_color_primary_700',
        'light_color_primary_800',
        'light_color_primary_900',
        'light_color_primary_950',
        'light_dl_reverse',
        'light_brand_color',
        'light_brand_hover_color',
        'light_brand_hover_bg',
        'light_card_color',
        'light_border_color',
        'light_color_bg',
        'light_tab_bg_color',
        'light_color_input_field'
    ];
}

function getThemeFieldGroups() {
    return [
        'basic' => [
            'title' => 'Basic Information',
            'fields' => [
                'theme_name' => ['type' => 'text', 'label' => 'Theme Name', 'required' => true],
                'theme_description' => ['type' => 'textarea', 'label' => 'Description']
            ]
        ],
        'root' => [
            'title' => 'Root Variables',
            'fields' => [
                'delay' => ['type' => 'text', 'label' => 'Animation Delay', 'default' => '0s'],
                'sidebar_width' => ['type' => 'text', 'label' => 'Sidebar Width', 'default' => '220px'],
                'color_data_up' => ['type' => 'color', 'label' => 'Up Color', 'default' => '#00c288'],
                'color_data_down' => ['type' => 'color', 'label' => 'Down Color', 'default' => '#dc3545'],
                'color_dark' => ['type' => 'color', 'label' => 'Dark Color', 'default' => '#212529'],
                'preset_1' => ['type' => 'text', 'label' => 'Preset 1', 'default' => 'rgb(13, 110, 253)']
            ]
        ],
        'gsap' => [
            'title' => 'GSAP Highlight Colors',
            'fields' => [
                'color_text' => ['type' => 'text', 'label' => 'Text Color', 'default' => 'hsl(48, 100%, 52%)'],
                'color_text_highlight' => ['type' => 'text', 'label' => 'Text Highlight', 'default' => 'hsl(115, 96%, 45%)'],
                'bg_color_highlight' => ['type' => 'text', 'label' => 'Background Highlight', 'default' => 'hsl(45, 100%, 57%)']
            ]
        ],
        'fonts' => [
            'title' => 'Typography',
            'fields' => [
                'global_font' => ['type' => 'text', 'label' => 'Global Font', 'default' => '"Space Grotesk", sans-serif'],
                'font_1' => ['type' => 'text', 'label' => 'Font 1', 'default' => '"Gorditas", sans-serif'],
                'font_2' => ['type' => 'text', 'label' => 'Font 2', 'default' => '"DynaPuff", sans-serif'],
                'font_mono' => ['type' => 'text', 'label' => 'Monospace Font', 'default' => '"Geist Mono", serif']
            ]
        ],
        'dark' => [
            'title' => 'Dark Theme Colors',
            'fields' => [
                'dark_color_text' => ['type' => 'text', 'label' => 'Text Color', 'default' => 'hsl(56, 100%, 57%)'],
                'dark_color_text_highlight' => ['type' => 'text', 'label' => 'Text Highlight', 'default' => 'hsl(233, 100%, 53%)'],
                'dark_color_primary_50' => ['type' => 'color', 'label' => 'Primary 50', 'default' => '#f9fafb'],
                'dark_color_primary_100' => ['type' => 'color', 'label' => 'Primary 100', 'default' => '#f3f4f6'],
                'dark_color_primary_200' => ['type' => 'color', 'label' => 'Primary 200', 'default' => '#e5e7eb'],
                'dark_color_primary_300' => ['type' => 'color', 'label' => 'Primary 300', 'default' => '#d1d5db'],
                'dark_color_primary_400' => ['type' => 'color', 'label' => 'Primary 400', 'default' => '#9ca3af'],
                'dark_color_primary_500' => ['type' => 'color', 'label' => 'Primary 500', 'default' => '#6b7280'],
                'dark_color_primary_600' => ['type' => 'color', 'label' => 'Primary 600', 'default' => '#4b5563'],
                'dark_color_primary_700' => ['type' => 'color', 'label' => 'Primary 700', 'default' => '#374151'],
                'dark_color_primary_800' => ['type' => 'color', 'label' => 'Primary 800', 'default' => '#1f2937'],
                'dark_color_primary_900' => ['type' => 'color', 'label' => 'Primary 900', 'default' => '#111827'],
                'dark_color_primary_950' => ['type' => 'color', 'label' => 'Primary 950', 'default' => '#030712'],
                'dark_dl_reverse' => ['type' => 'color', 'label' => 'DL Reverse', 'default' => '#ffffff'],
                'dark_brand_color' => ['type' => 'color', 'label' => 'Brand Color', 'default' => '#facc15'],
                'dark_brand_hover_color' => ['type' => 'color', 'label' => 'Brand Hover', 'default' => '#eab308'],
                'dark_brand_hover_bg' => ['type' => 'text', 'label' => 'Brand Hover BG', 'default' => 'hsla(0, 0%, 100%, 0.020)'],
                'dark_card_color' => ['type' => 'color', 'label' => 'Card Color', 'default' => '#121212'],
                'dark_border_color' => ['type' => 'text', 'label' => 'Border Color', 'default' => '#ffffff0c'],
                'dark_color_bg' => ['type' => 'color', 'label' => 'Background', 'default' => '#06090b'],
                'dark_tab_bg_color' => ['type' => 'color', 'label' => 'Tab Background', 'default' => '#030507'],
                'dark_color_input_field' => ['type' => 'color', 'label' => 'Input Field', 'default' => '#1b1b1b']
            ]
        ],
        'light' => [
            'title' => 'Light Theme Colors',
            'fields' => [
                'light_color_primary_50' => ['type' => 'color', 'label' => 'Primary 50', 'default' => '#030712'],
                'light_color_primary_100' => ['type' => 'color', 'label' => 'Primary 100', 'default' => '#111827'],
                'light_color_primary_200' => ['type' => 'color', 'label' => 'Primary 200', 'default' => '#1f2937'],
                'light_color_primary_300' => ['type' => 'color', 'label' => 'Primary 300', 'default' => '#374151'],
                'light_color_primary_400' => ['type' => 'color', 'label' => 'Primary 400', 'default' => '#4b5563'],
                'light_color_primary_500' => ['type' => 'color', 'label' => 'Primary 500', 'default' => '#6b7280'],
                'light_color_primary_600' => ['type' => 'color', 'label' => 'Primary 600', 'default' => '#9ca3af'],
                'light_color_primary_700' => ['type' => 'color', 'label' => 'Primary 700', 'default' => '#d1d5db'],
                'light_color_primary_800' => ['type' => 'color', 'label' => 'Primary 800', 'default' => '#e5e7eb'],
                'light_color_primary_900' => ['type' => 'color', 'label' => 'Primary 900', 'default' => '#f3f4f6'],
                'light_color_primary_950' => ['type' => 'color', 'label' => 'Primary 950', 'default' => '#f9fafb'],
                'light_dl_reverse' => ['type' => 'color', 'label' => 'DL Reverse', 'default' => '#121212'],
                'light_brand_color' => ['type' => 'color', 'label' => 'Brand Color', 'default' => '#facc15'],
                'light_brand_hover_color' => ['type' => 'color', 'label' => 'Brand Hover', 'default' => '#eab308'],
                'light_brand_hover_bg' => ['type' => 'text', 'label' => 'Brand Hover BG', 'default' => 'hsl(210, 17%, 95%)'],
                'light_card_color' => ['type' => 'color', 'label' => 'Card Color', 'default' => '#ffffff'],
                'light_border_color' => ['type' => 'text', 'label' => 'Border Color', 'default' => '#00000013'],
                'light_color_bg' => ['type' => 'color', 'label' => 'Background', 'default' => '#f1f3f5'],
                'light_tab_bg_color' => ['type' => 'color', 'label' => 'Tab Background', 'default' => '#f3f4f6'],
                'light_color_input_field' => ['type' => 'color', 'label' => 'Input Field', 'default' => '#f1f3f5']
            ]
        ]
    ];
}

function exportTheme($themeId, $format = 'json') {
    $theme = getThemeById($themeId);
    if (!$theme) {
        throw new Exception("Theme not found");
    }
    
    unset($theme['id'], $theme['is_active'], $theme['is_default'], $theme['created_at'], $theme['updated_at']);
    
    switch ($format) {
        case 'json':
            return json_encode($theme, JSON_PRETTY_PRINT);
        case 'css':
            return generateThemeCSS($theme);
        case 'array':
            return $theme;
        default:
            throw new Exception("Unsupported export format");
    }
}

function importTheme($data, $format = 'json') {
    switch ($format) {
        case 'json':
            $themeData = json_decode($data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON format");
            }
            break;
        case 'array':
            $themeData = $data;
            break;
        default:
            throw new Exception("Unsupported import format");
    }
    
    if (!isset($themeData['theme_name'])) {
        $themeData['theme_name'] = 'Imported Theme ' . date('Y-m-d H:i:s');
    }
    
    return createNewTheme($themeData);
}

function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $ipParts = explode('.', $_SERVER['REMOTE_ADDR']);
    $safeIP = implode('.', array_slice($ipParts, 0, 3));

    $currentFingerprint = hash('sha256', $safeIP . $_SERVER['HTTP_USER_AGENT']);

    if (!isset($_SESSION['SPEX_SESSION_FINGERPRINT'])) {
        $_SESSION['SPEX_SESSION_FINGERPRINT'] = $currentFingerprint;
    } elseif ($_SESSION['SPEX_SESSION_FINGERPRINT'] !== $currentFingerprint) {
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();

        die('
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Session Security Alert - SpeX</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-tr from-gray-950 via-gray-900 to-gray-950 text-gray-100 min-h-screen flex items-center justify-center px-4">
  <main class="bg-gray-900 bg-opacity-70 backdrop-blur-xl rounded-3xl shadow-2xl border border-yellow-600 max-w-xl w-full p-12 text-center select-none">
    <h1 class="text-4xl font-extrabold tracking-tight mb-4 text-yellow-500 drop-shadow-md">
      Session Verification Failed
    </h1>
    <p class="text-gray-300 text-lg max-w-md mx-auto mb-6 leading-relaxed">
      Your session was terminated due to a fingerprint mismatch. This could mean someone attempted to hijack your session or your IP/User-Agent has changed significantly.
    </p>
    <section class="bg-yellow-900 bg-opacity-40 border border-yellow-700 rounded-lg p-5 text-yellow-200 font-mono text-sm leading-snug mb-10 select-text">
      <strong>Security Warning:</strong><br />
      Fingerprint mismatch detected.<br />
      Session has been invalidated for safety.
    </section>
  </main>
</body>
</html>
        ');
    }
}

function resetThemeToDefaults($themeId) {
    global $spexcon;
    
    $defaultTheme = getThemeById(1); // Assuming ID 1 is default
    if (!$defaultTheme) {
        throw new Exception("Default theme not found");
    }
    
    $theme = getThemeById($themeId);
    if (!$theme) {
        throw new Exception("Theme not found");
    }
    
    $originalName = $theme['theme_name'];
    $originalDesc = $theme['theme_description'];
    
    $fieldsToReset = array_diff(getAllThemeFields(), ['theme_name', 'theme_description']);
    
    $updateData = [];
    foreach ($fieldsToReset as $field) {
        if (isset($defaultTheme[$field])) {
            $updateData[$field] = $defaultTheme[$field];
        }
    }
    
    $updateData['theme_name'] = $originalName;
    $updateData['theme_description'] = $originalDesc;
    
    return updateTheme($themeId, $updateData);
}

// Advanced Theme Management Functions END

//-------------------------------------------------------------------------------------------------------\\


// AlertLib ENTRY

function Pop($type, $msg) {
    echo "
    <style>
    .chakra-alert {
    padding: 16px;
    border-radius: 4px;
    display: flex;
    font-family: sans-serif;
    align-items: center;
    gap: 12px;
    margin: 10px 0;
    animation: slideIn 0.3s ease-in-out;
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    max-width: 90%;
    width: auto;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 999999;
    font-size: 14px;
    }

    @media screen and (max-width: 768px) {
    .chakra-alert {
        top: 10px;
        right: 10px;
        left: 10px;
        min-width: unset;
        margin: 0;
        font-size: 13px;
        padding: 12px;
    }
    }

    @keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
    }

    .alert-success { background-color: #C6F6D5; color: #2F855A; }
    .alert-error { background-color: #FED7D7; color: #C53030; }
    .alert-warning { background-color: #FEEBC8; color: #C05621; }
    .alert-info { background-color: #BEE3F8; color: #2B6CB0; }
    </style>
 
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.createElement('div');
        alert.className = 'chakra-alert alert-" . $type . "';
        alert.textContent = '" . $msg . "';
        document.body.appendChild(alert);
 
        setTimeout(() => {
            alert.style.animation = 'slideIn 0.3s ease-in-out reverse';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    });
    </script>";
}

// AlertLib END


//-------------------------------------------------------------------------------------------------------\\


// SpeX-V4 Functions ENTRY

function FetchWebSettings(){
    global $spexcon;

    $ref = $spexcon->query("SELECT * FROM settings WHERE id='1'");
    $dat = mysqli_fetch_assoc($ref);
    return $dat;
}

function FetchUserData($id){
    global $spexcon;

    $ref = $spexcon->query("SELECT * FROM users WHERE uid='$id'");
    $dat = mysqli_fetch_assoc($ref);
    return $dat;
}

function Sanitize($arg) {
    if ($arg === null || $arg === '') {
        return '';
    }
    
    if (is_array($arg)) {
        return array_map('Sanitize', $arg);
    }
    
    $arg = (string) $arg;
    $arg = trim($arg);
    $arg = htmlspecialchars($arg, ENT_QUOTES, 'UTF-8');
    
    return $arg;
}

function setSession($name,$arg) {
    $_SESSION["$name"] = $arg;
    return true;
}

function getSession($name) {
    return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
}

function UserLogin($mail,$pass){
    global $spexcon;

    $Mail = Sanitize($mail);
    $Pass = md5(Sanitize($pass));

    if ($mail == '' || $pass == '') {
        return 'Fields Empty.';
    }

    $query = "SELECT * FROM users WHERE mail='$Mail' AND pass='$Pass' AND disablead=0";

    $sec = $spexcon->query($query);
    
    if (!$sec) {
        return 'Database error.';
    }
    
    $row = mysqli_num_rows($sec);
    
    if ($row > 0) {
        $rec = mysqli_fetch_assoc($sec);
        
        $UserID = $rec['uid'];
        $Check2Fa = checkUserHas2FA($UserID);
        setSession('UID',$UserID);
        setSession('Login',1);
        setSession('EMail',$Mail);
        if ($Check2Fa==1) {
            setSession('LoginVia2Fa','1');
        }
        return true;

    } else {
        return 'User mail or password mismatch.';
    }
}

function checkUserHas2FA($uid) {
    global $spexcon;
    
    $query = $spexcon->query("SELECT * FROM user_security_settings WHERE user_id='$uid'");
    $fetch = mysqli_fetch_assoc($query);
    if ($fetch['two_factor_enabled']==1) {
        return true;
    } else {
        return false;
    }
}

function PushSend($uid, $title, $descr, $type) {
    global $spexcon;
    
    try {
        $que = $spexcon->query("INSERT INTO `user_pushs`(uid,title,descr,type,date) VALUES('$uid','$title','$descr','$type',current_timestamp()) ");
        if ($que) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log("PushSend Error: " . $e->getMessage());
        return false;
    }
}

function getUIDToMail($mail){
    global $spexcon;

    $que = $spexcon->query("SELECT * FROM users WHERE mail='$mail' ");
    $fet = mysqli_fetch_assoc($que);

    return $fet['uid'];
}

function getPinboardData(){
    global $spexcon;
    
    $que = $spexcon->query("SELECT * FROM pins ORDER BY id DESC");
    $pins = [];
    
    while($row = $que->fetch_assoc()) {
        $pins[] = $row;
    }
    
    return $pins;
}
function updateUserLastLogin($uid){
    global $spexcon;

    $q = $spexcon->query("UPDATE users SET `last_login`=current_timestamp() WHERE uid='$uid' ");

    if ($q) {
        return true;
    } else {
        return false;
    }
}

function MailToUID($mail){
    global $spexcon;

    $Mail = Sanitize($mail);
    $que = $spexcon->query("SELECT * FROM users WHERE mail='$Mail' ");
    $fet = mysqli_fetch_assoc($que);
    return $fet['uid'];
}

function generatePassword($length = 8) {
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '0123456789';
    $special = '!@#$%^&*';
    
    $password = '';
    $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
    $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
    $password .= $numbers[random_int(0, strlen($numbers) - 1)];
    $password .= $special[random_int(0, strlen($special) - 1)];
    
    $allCharacters = $lowercase . $uppercase . $numbers . $special;
    for ($i = 4; $i < $length; $i++) {
        $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
    }
    
    return str_shuffle($password);
}

function generateUniqueUID() {
    global $spexcon;
    
    $maxAttempts = 100;
    $attempts = 0;
    
    do {
        // (100000 - 999999)
        $uid = rand(100000, 999999);

        $checkQuery = "SELECT uid FROM users WHERE uid = '$uid'";
        $result = $spexcon->query($checkQuery);
        
        if (!$result) {
            return false;
        }
        
        $exists = mysqli_num_rows($result) > 0;
        $attempts++;
        
        if (!$exists) {
            return $uid;
        }
        
    } while ($exists && $attempts < $maxAttempts);
    
    return false;
}

function generateUniqueInvatationCode() {
    global $spexcon;
    
    $maxAttempts = 100;
    $attempts = 0;
    
    do {
        // (100000 - 999999)
        $uid = "C".rand(100000, 999999);

        $checkQuery = "SELECT invitation_code FROM users WHERE invitation_code = '$uid'";
        $result = $spexcon->query($checkQuery);
        
        if (!$result) {
            return false;
        }
        
        $exists = mysqli_num_rows($result) > 0;
        $attempts++;
        
        if (!$exists) {
            return $uid;
        }
        
    } while ($exists && $attempts < $maxAttempts);
    
    return false;
}

function updateUserPassword($uid,$pass){
    global $spexcon;

    $hashedpass = md5($pass);
    $que = $spexcon->query("UPDATE users SET `pass`='$hashedpass' WHERE uid='$uid'");
    return true;
}

function refToUID($code){
    global $spexcon;

    $que = $spexcon->query("SELECT * FROM users WHERE invitation_code='$code'");
    $fet = mysqli_fetch_assoc($que);
    return $fet['uid'];
}

function mailExist($arg){
    global $spexcon;

    $que = $spexcon->query("SELECT * FROM users WHERE mail='$arg' ");
    $row = mysqli_num_rows($que);

    if ($row>0) {
        return true;
    } else {
        return false;
    }
}

function newUserLog($uid,$type,$msg,$amount='',$cat=''){
    global $spexcon;

    $que = $spexcon->query("INSERT INTO `user_logs`(uid,type,msg,amount,cat) VALUES('$uid','$type','$msg','$amount','$cat') ");
    if ($que) {
        return true;
    }
}

function phoneExist($arg){
    global $spexcon;

    $que = $spexcon->query("SELECT * FROM users WHERE phone='$arg' ");
    $row = mysqli_num_rows($que);

    if ($row>0) {
        return true;
    } else {
        return false;
    }
}

function generateUniqueWalletAddress() {
    global $spexcon;
    
    $maxAttempts = 100;
    $attempts = 0;
    
    do {
        // 0x18
        $address = '0x' . generateHexString(18);
        
        $checkQuery = "SELECT virtual_address FROM users WHERE virtual_address = '$address'";
        $result = $spexcon->query($checkQuery);
        
        if (!$result) {
            return false;
        }
        
        $exists = mysqli_num_rows($result) > 0;
        $attempts++;
        
        if (!$exists) {
            return $address;
        }
        
    } while ($exists && $attempts < $maxAttempts);
    
    return false;
}

function generateHexString($length) {
    $characters = '0123456789abcdef';
    $hexString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $hexString .= $characters[rand(0, 15)];
    }
    
    return $hexString;
}

function Go($arg){
    header("Location:?r=$arg");
}

function DGO($delay,$arg){
    header("Refresh:$delay;url=?r=$arg");
}

function randomUserImgPath(){
    // imgs* images/social/ava_1.png - 10

    $rand = mt_rand(1,10);
    $path = "core/pages/assets/images/social/ava_" . $rand . ".png";
    return $path;
}

function newUserLoginActivity($uid, $status = 'SUCCESS'){
    global $spexcon;

    $ip_address = getUserIP();
    
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    // Extract device information
    $device = 'Unknown';
    if (preg_match('/Mobile|Android|iPhone|iPad/', $user_agent)) {
        if (preg_match('/iPhone/', $user_agent)) {
            $device = 'iPhone';
        } elseif (preg_match('/iPad/', $user_agent)) {
            $device = 'iPad';
        } elseif (preg_match('/Android/', $user_agent)) {
            $device = 'Android Device';
        } else {
            $device = 'Mobile Device';
        }
    } elseif (preg_match('/Windows/', $user_agent)) {
        $device = 'Windows PC';
    } elseif (preg_match('/Macintosh/', $user_agent)) {
        $device = 'Mac';
    } elseif (preg_match('/Linux/', $user_agent)) {
        $device = 'Linux';
    }
    
    // Extract browser information
    $browser = 'Unknown';
    if (preg_match('/Chrome\/([0-9.]+)/', $user_agent, $matches)) {
        $browser = 'Chrome ' . $matches[1];
    } elseif (preg_match('/Firefox\/([0-9.]+)/', $user_agent, $matches)) {
        $browser = 'Firefox ' . $matches[1];
    } elseif (preg_match('/Safari\/([0-9.]+)/', $user_agent, $matches)) {
        if (!preg_match('/Chrome/', $user_agent)) {
            $browser = 'Safari ' . $matches[1];
        }
    } elseif (preg_match('/Edge\/([0-9.]+)/', $user_agent, $matches)) {
        $browser = 'Edge ' . $matches[1];
    } elseif (preg_match('/Opera\/([0-9.]+)/', $user_agent, $matches)) {
        $browser = 'Opera ' . $matches[1];
    }
    
    $location = getLocationFromIPCached($ip_address);
    
    $uid = Sanitize($uid);
    $ip_address = Sanitize($ip_address);
    $device = Sanitize($device);
    $browser = Sanitize($browser);
    $location = Sanitize($location);
    $status = in_array($status, ['SUCCESS', 'FAILED']) ? $status : 'SUCCESS';
    
    $query = "INSERT INTO `user_login_history` 
              (user_id, ip_address, device, browser, location, status, login_time) 
              VALUES ('$uid', '$ip_address', '$device', '$browser', '$location', '$status', current_timestamp())";
    
    $result = $spexcon->query($query);
    
    if ($result) {
        return true;
    } else {
        return false;
    }
}


function getLocationFromIP($ip) {
    if ($ip === '127.0.0.1' || $ip === '::1' || 
        strpos($ip, '192.168.') === 0 || 
        strpos($ip, '10.') === 0 || 
        strpos($ip, '172.') === 0) {
        return 'Local/Private Network';
    }
    
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return 'Invalid IP';
    }
    
    try {
        $url = "http://ip-api.com/json/{$ip}?fields=status,message,country,regionName,city,lat,lon,timezone";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'TradingApp/1.0');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false || $httpCode !== 200) {
            return 'Unknown Location';
        }
        
        $data = json_decode($response, true);
        
        if (!$data || $data['status'] !== 'success') {
            return 'Unknown Location';
        }
        
        $location = '';
        
        if (!empty($data['country'])) {
            $location .= $data['country'];
        }
        
        if (!empty($data['regionName']) && $data['regionName'] !== $data['country']) {
            $location .= '/' . $data['regionName'];
        }
        
        if (!empty($data['city']) && $data['city'] !== $data['regionName']) {
            $location .= '/' . $data['city'];
        }
        
        return !empty($location) ? $location : 'Unknown Location';
        
    } catch (Exception $e) {
        error_log("GeoIP Error for IP {$ip}: " . $e->getMessage());
        return 'Unknown Location';
    }
}

function getLocationFromIPAlternative($ip) {
    if ($ip === '127.0.0.1' || $ip === '::1' || 
        strpos($ip, '192.168.') === 0 || 
        strpos($ip, '10.') === 0 || 
        strpos($ip, '172.') === 0) {
        return 'Local/Private Network';
    }
    
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return 'Invalid IP';
    }
    
    try {
        $url = "https://ipinfo.io/{$ip}/json";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'SpeX-Trading-App/1.0'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return 'Unknown Location';
        }
        
        $data = json_decode($response, true);
        
        if (!$data || isset($data['error'])) {
            return 'Unknown Location';
        }
        
        $location = '';
        
        if (!empty($data['country'])) {
            $location .= $data['country'];
        }
        
        if (!empty($data['region'])) {
            $location .= '/' . $data['region'];
        }
        
        if (!empty($data['city'])) {
            $location .= '/' . $data['city'];
        }
        
        return !empty($location) ? $location : 'Unknown Location';
        
    } catch (Exception $e) {
        error_log("GeoIP Error for IP {$ip}: " . $e->getMessage());
        return 'Unknown Location';
    }
}

function getLocationFromIPCached($ip) {
    global $spexcon;
    
    if ($ip === '127.0.0.1' || $ip === '::1' || 
        strpos($ip, '192.168.') === 0 || 
        strpos($ip, '10.') === 0 || 
        strpos($ip, '172.') === 0) {
        return 'Local/Private Network';
    }
    
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return 'Invalid IP';
    }
    
    $createCacheTable = "
        CREATE TABLE IF NOT EXISTS `ip_location_cache` (
            `ip_address` varchar(45) PRIMARY KEY,
            `location` varchar(255) NOT NULL,
            `created_at` timestamp DEFAULT current_timestamp(),
            `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            INDEX `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci
    ";
    $spexcon->query($createCacheTable);
    
    $cacheQuery = "SELECT location FROM ip_location_cache 
                   WHERE ip_address = '$ip' 
                   AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $cacheResult = $spexcon->query($cacheQuery);
    
    if ($cacheResult && mysqli_num_rows($cacheResult) > 0) {
        $cached = mysqli_fetch_assoc($cacheResult);
        return $cached['location'];
    }
    
    $location = getLocationFromIP($ip);
    
    if ($location !== 'Unknown Location') {
        $insertCache = "INSERT INTO ip_location_cache (ip_address, location) 
                        VALUES ('$ip', '" . Sanitize($location) . "')
                        ON DUPLICATE KEY UPDATE 
                        location = '" . Sanitize($location) . "',
                        updated_at = current_timestamp()";
        $spexcon->query($insertCache);
    }
    
    return $location;
}

function GetUserTokenBalance($userId, $coinSymbol) {
    global $spexcon;
    
    $query = $spexcon->prepare("
        SELECT balance FROM user_tokens 
        WHERE user_id = ? AND coin_symbol = ?
    ");
    $query->bind_param("is", $userId, $coinSymbol);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();
    
    return $result ? $result['balance'] : 0;
}

function UpdateUserTokenBalance($userId, $coinSymbol, $amount, $operation = 'add') {
    global $spexcon;
    
    $currentBalance = GetUserTokenBalance($userId, $coinSymbol);
    
    if ($operation === 'add') {
        $newBalance = $currentBalance + $amount;
    } else {
        $newBalance = $currentBalance - $amount;
        if ($newBalance < 0) {
            return false; // Insufficient balance
        }
    }
    
    $query = $spexcon->prepare("
        INSERT INTO user_tokens (user_id, coin_symbol, balance) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        balance = ?, updated_at = NOW()
    ");
    $query->bind_param("isdd", $userId, $coinSymbol, $newBalance, $newBalance);
    
    return $query->execute();
}

function GetUserPortfolioStats($userId) {
    global $spexcon;
    
    $query = $spexcon->prepare("
        SELECT 
            SUM(pp.total_cost) as total_invested,
            SUM(pp.amount) as total_tokens,
            SUM(CASE WHEN p.status = 'ended' AND p.claim_enabled = 1 AND pp.claimed = 0 THEN 1 ELSE 0 END) as pending_claims,
            SUM(CASE WHEN pp.claimed = 1 THEN pp.amount ELSE 0 END) as claimed_tokens
        FROM presale_purchases pp
        JOIN presales p ON pp.presale_id = p.id
        WHERE pp.user_id = ? AND pp.status = 'completed'
    ");
    $query->bind_param("i", $userId);
    $query->execute();
    return $query->get_result()->fetch_assoc();
}

function GetAllUserTokens($userId) {
    global $spexcon;
    
    $query = $spexcon->prepare("
        SELECT * FROM user_tokens 
        WHERE user_id = ? AND balance > 0
        ORDER BY coin_symbol ASC
    ");
    $query->bind_param("i", $userId);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

function IsPresaleClaimable($presaleId) {
    global $spexcon;
    
    $query = $spexcon->prepare("
        SELECT status, claim_enabled FROM presales 
        WHERE id = ?
    ");
    $query->bind_param("i", $presaleId);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();
    
    return $result && $result['status'] === 'ended' && $result['claim_enabled'] == 1;
}

function isMaintenance() {
    global $spexcon;

    $que = $spexcon->query("SELECT * FROM settings WHERE 1");
    $dat = mysqli_fetch_assoc($que);

    if ($dat['MaintenanceMode'] == 1) {
        $Msg = htmlspecialchars($dat['MaintenanceMessage']);
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Site Under Maintenance</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    background: radial-gradient(circle at top left, #1b2735, #090a0f);
                    color: #ffffff;
                    font-family: "Outfit", sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    overflow: hidden;
                }

                .card {
                    background-color: #121212;
                    border: 1px solid rgba(255,255,255,0.1);
                    border-radius: 20px;
                    padding: 40px;
                    text-align: center;
                    box-shadow: 0 0 30px rgba(0,0,0,0.4);
                    max-width: 600px;
                    width: 100%;
                }

                .card i {
                    font-size: 4rem;
                    color: #ff6f61;
                    margin-bottom: 20px;
                }

                .card h1 {
                    font-weight: 700;
                    font-size: 2.5rem;
                    margin-bottom: 15px;
                }

                .card p {
                    font-size: 1.1rem;
                    color: #ccc;
                    margin-bottom: 0;
                }

                .footer {
                    position: absolute;
                    bottom: 15px;
                    font-size: 0.9rem;
                    color: #888;
                }

                @media (max-width: 576px) {
                    .card {
                        padding: 20px;
                    }
                    .card h1 {
                        font-size: 1.8rem;
                    }
                }
            </style>
        </head>
        <body>
            <div class="card">
                <i class="fas fa-tools"></i>
                <h1>Well Be Back Soon</h1>
                <p>' . $Msg . '</p>
            </div>
        </body>
        </html>
        ';
        exit(503); // Maintenance HTTP status
    }
}


function EnablePresaleTrading($presaleId) {
    global $spexcon;
    
    $query = $spexcon->prepare("
        UPDATE presales 
        SET trading_enabled = 1 
        WHERE id = ? AND status = 'ended'
    ");
    $query->bind_param("i", $presaleId);
    return $query->execute();
}

function GetPresaleData($presaleId) {
    global $spexcon;
    
    $query = $spexcon->prepare("SELECT * FROM presales WHERE id = ?");
    $query->bind_param("i", $presaleId);
    $query->execute();
    return $query->get_result()->fetch_assoc();
}

function GetUserPresalePurchases($userId) {
    global $spexcon;
    
    $query = $spexcon->prepare("
        SELECT pp.*, p.coin_name, p.coin_symbol 
        FROM presale_purchases pp
        JOIN presales p ON pp.presale_id = p.id
        WHERE pp.user_id = ?
        ORDER BY pp.created_at DESC
    ");
    $query->bind_param("i", $userId);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

function UpdatePresaleStatus() {
    global $spexcon;
    
    $spexcon->query("
        UPDATE presales 
        SET status = 'active' 
        WHERE status = 'upcoming' AND start_date <= NOW()
    ");
    
    $spexcon->query("
        UPDATE presales 
        SET status = 'ended' 
        WHERE status = 'active' AND end_date <= NOW()
    ");
}

function getUserIP() {
    $ipKeys = [
        'HTTP_CF_CONNECTING_IP',     // Cloudflare
        'HTTP_CLIENT_IP',            // Proxy
        'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
        'HTTP_X_FORWARDED',          // Proxy
        'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
        'HTTP_FORWARDED_FOR',        // Proxy
        'HTTP_FORWARDED',            // Proxy
        'REMOTE_ADDR'                // Standart IP
    ];
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            $ip = $_SERVER[$key];
            
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            
            if (filter_var($ip, FILTER_VALIDATE_IP, 
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function newRef($uid,$refuid){
    global $spexcon;

    $que = $spexcon->query("INSERT INTO `invitations`(user_uid,ref_uid,date) VALUES('$uid','$refuid',current_timestamp())");
    if ($que) {
        return true;
    } else {
        return false;
    }
}

function addUserBalance($uid,$arg){
    global $spexcon;

    $que = $spexcon->query("UPDATE users SET `balance`=balance+'$arg' WHERE uid='$uid'");
    if ($que) {
        return true;
    } else {
        return false;
    }
}

function LogAdminLogin($user){
    global $spexcon;
    
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $ipAddress = getUserIP();
    
    $user = Sanitize($user);
    $userAgent = Sanitize($userAgent);
    $ipAddress = Sanitize($ipAddress);
    
    $query = "INSERT INTO `admin_logs` (user, userAgent, ip_address, date) VALUES ('$user', '$userAgent', '$ipAddress', current_timestamp())";
    
    $result = $spexcon->query($query);
    
    if ($result) {
        return true;
    } else {
        return false;
    }
}

function registerUser($uid,$mail,$phone,$pass){
    global $spexcon;

    $Web = FetchWebSettings();

    if ($Web['RegistrationEnabled']==0) {
        return false;
    }

    if (mailExist($mail)) {
        return false;
    }

    if (phoneExist($phone)) {
        return false;
    }

    $ProfileImg = randomUserImgPath();
    $InvitationCode = generateUniqueInvatationCode();
    $VirtualAdress = generateUniqueWalletAddress();
    $UserIP = getUserIP();

    $que = $spexcon->query("INSERT INTO `users`(uid,mail,balance,phone,pass,profile_img,virtual_address,invitation_code,ip_address,register_date) VALUES('$uid','$mail','0','$phone','$pass','$ProfileImg','$VirtualAdress','$InvitationCode','$UserIP',current_timestamp()) ");
    if ($que) {
        return true;
    } else {
        return false;
    }
}

function getCurrentAdminInfo() {
    global $spexcon;
    
    $adminId = $_SESSION['adminID'] ?? 1;
    
    $query = $spexcon->prepare("SELECT user, isSuper FROM admin WHERE id = ?");
    $query->bind_param("i", $adminId);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return ['user' => 'admin', 'isSuper' => 1];
}

function getLogs() {
    global $spexcon;
    
    $adminInfo = getCurrentAdminInfo();
    $isSuper = $adminInfo['isSuper'] == 1;
    $currentUser = $adminInfo['user'];
    
    if ($isSuper) {
        $query = $spexcon->query("SELECT * FROM admin_logs ORDER BY date DESC LIMIT 1000");
    } else {
        $stmt = $spexcon->prepare("SELECT * FROM admin_logs WHERE user = ? ORDER BY date DESC LIMIT 1000");
        $stmt->bind_param("s", $currentUser);
        $stmt->execute();
        $query = $stmt->get_result();
    }
    
    if (!$query) {
        throw new Exception("Failed to get logs: " . $spexcon->error);
    }
    
    $logs = [];
    while ($row = $query->fetch_assoc()) {
        $logs[] = $row;
    }
    
    echo json_encode([
        'success' => true, 
        'logs' => $logs, 
        'isSuper' => $isSuper,
        'currentUser' => $currentUser
    ]);
}

function deleteLog() {
    global $spexcon;
    
    $adminInfo = getCurrentAdminInfo();
    $isSuper = $adminInfo['isSuper'] == 1;
    
    if (!$isSuper) {
        throw new Exception("Permission denied. Only super administrators can delete logs.");
    }
    
    $id = (int)$_POST['id'];
    if (!$id) {
        throw new Exception("Log ID is required");
    }
    
    $stmt = $spexcon->prepare("DELETE FROM admin_logs WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Log deleted successfully']);
        } else {
            throw new Exception("Log not found");
        }
    } else {
        throw new Exception("Failed to delete log: " . $stmt->error);
    }
}

function clearAllLogs() {
    global $spexcon;
    
    $adminInfo = getCurrentAdminInfo();
    $isSuper = $adminInfo['isSuper'] == 1;
    
    if (!$isSuper) {
        throw new Exception("Permission denied. Only super administrators can clear all logs.");
    }
    
    $query = $spexcon->query("TRUNCATE TABLE admin_logs");
    
    if ($query) {
        echo json_encode(['success' => true, 'message' => 'All logs cleared successfully']);
    } else {
        throw new Exception("Failed to clear logs: " . $spexcon->error);
    }
}

function getAvailablePages() {
   // ref core/z-admin/inc/header.php
   $pages = [
       'home' => 'Dashboard',
       'users' => 'All Users',
       'user-balances' => 'User Balances [RISKY]',
       'user-transactions' => 'User Transactions',
       'kyc-management' => 'KYC Management',
       'login-history' => 'Login History',
       'coins-management' => 'Coins Management [RISKY]',
       'journey' => 'Trader Journey',
       'journey-settings' => 'Trader Journey Settings',
       'user-journeys' => 'User Trader Journeys',
       'market-data' => 'Market Data [RISKY]',
       'trading-bots' => 'Trading Bots',
       'bot-subscriptions' => 'Bot Subscriptions',
       'futures-positions' => 'Futures Positions',
       'staking-positions' => 'Staking Positions',
       'presales-management' => 'Presales Management',
       'presale-purchases' => 'Presale Purchases',
       'deposits' => 'Deposits',
       'withdrawals' => 'Withdrawals',
       'deposit-accounts' => 'Deposit Accounts [DANGER FEATURE]',
       'promocodes' => 'Promo Codes [RISKY]',
       'financial-reports' => 'Financial Reports',
       'mystery-boxes' => 'Mystery Boxes',
       'mystery-box-settings' => 'Mystery Box Settings',
       'mystery-box-users' => 'Mystery Box Users',
       'mystery-box-drops' => 'Mystery Box Drops',
       'notifications' => 'Push Notifications [RISKY]',
       'invitations' => 'Invitations',
       'home-board' => 'Home Board',
       'emails' => 'E-Mails [RISKY]',
       'system-settings' => 'System Settings [DANGER FEATURE]',
       'system-info' => 'System Information [DANGER FEATURE]',
       'cron-jobs' => 'Cron Jobs [RISKY]',
       'admin-management' => 'Admin Management [DANGER FEATURE]',
       'theme-management' => 'Theme Management [RISKY]',
       'database-editor' => 'Database Editor [DANGER FEATURE]',
       'system-logs' => 'System Logs [DANGER FEATURE]',
       'user-analytics' => 'User Analytics',
       'revenue-reports' => 'Revenue Reports'
   ];
   
   echo json_encode(['success' => true, 'pages' => $pages]);
}

function getAdmins() {
    global $spexcon;
    
    $query = $spexcon->query("SELECT * FROM admin ORDER BY id ASC");
    if (!$query) {
        throw new Exception("Failed to get admins: " . $spexcon->error);
    }
    
    $admins = [];
    while ($row = $query->fetch_assoc()) {
        $admins[] = $row;
    }
    
    echo json_encode(['success' => true, 'admins' => $admins]);
}

function getAdmin() {
    global $spexcon;
    
    $id = (int)$_POST['id'];
    if (!$id) {
        throw new Exception("Admin ID is required");
    }
    
    $stmt = $spexcon->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Admin not found");
    }
    
    $admin = $result->fetch_assoc();
    echo json_encode(['success' => true, 'admin' => $admin]);
}

function createAdmin() {
    global $spexcon;
    
    $username = Sanitize($_POST['username']);
    $password = Sanitize($_POST['password']);
    $allowedPages = Sanitize($_POST['allowed_pages']);
    $isSuper = isset($_POST['isSuper']) ? (int)$_POST['isSuper'] : 0;
    
    if (empty($username) || empty($password)) {
        throw new Exception("Username and password are required");
    }
    
    $checkQuery = $spexcon->prepare("SELECT id FROM admin WHERE user = ?");
    $checkQuery->bind_param("s", $username);
    $checkQuery->execute();
    
    if ($checkQuery->get_result()->num_rows > 0) {
        throw new Exception("Username already exists");
    }
    
    $hashedPassword = md5($password);
    
    $stmt = $spexcon->prepare("INSERT INTO admin (user, pass, allowed_pages, isSuper, date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssi", $username, $hashedPassword, $allowedPages, $isSuper);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Admin created successfully']);
    } else {
        throw new Exception("Failed to create admin: " . $stmt->error);
    }
}

function updateAdmin() {
    global $spexcon;
    
    $id = (int)$_POST['id'];
    $username = Sanitize($_POST['username']);
    $password = Sanitize($_POST['password']);
    $allowedPages = Sanitize($_POST['allowed_pages']);
    $isSuper = isset($_POST['isSuper']) ? (int)$_POST['isSuper'] : 0;
    
    if (!$id || empty($username)) {
        throw new Exception("Admin ID and username are required");
    }
    
    $checkQuery = $spexcon->prepare("SELECT id FROM admin WHERE user = ? AND id != ?");
    $checkQuery->bind_param("si", $username, $id);
    $checkQuery->execute();
    
    if ($checkQuery->get_result()->num_rows > 0) {
        throw new Exception("Username already exists");
    }
    
    if (!empty($password)) {
        $hashedPassword = md5($password);
        $stmt = $spexcon->prepare("UPDATE admin SET user = ?, pass = ?, allowed_pages = ?, isSuper = ? WHERE id = ?");
        $stmt->bind_param("sssii", $username, $hashedPassword, $allowedPages, $isSuper, $id);
    } else {
        $stmt = $spexcon->prepare("UPDATE admin SET user = ?, allowed_pages = ?, isSuper = ? WHERE id = ?");
        $stmt->bind_param("ssii", $username, $allowedPages, $isSuper, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Admin updated successfully']);
    } else {
        throw new Exception("Failed to update admin: " . $stmt->error);
    }
}

function deleteAdmin() {
    global $spexcon;
    
    $id = (int)$_POST['id'];
    if (!$id) {
        throw new Exception("Admin ID is required");
    }
    
    if ($id == 1) {
        throw new Exception("Cannot delete the main admin user");
    }
    
    $stmt = $spexcon->prepare("DELETE FROM admin WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Admin deleted successfully']);
        } else {
            throw new Exception("Admin not found");
        }
    } else {
        throw new Exception("Failed to delete admin: " . $stmt->error);
    }
}

function getCronStatus() {
    global $spexcon;
    
    $query = "SELECT MAX(last_updated) as last_update FROM coin_market_data";
    $result = $spexcon->query($query);
    $lastUpdate = $result->fetch_assoc()['last_update'];
    
    if ($lastUpdate) {
        $date = new DateTime($lastUpdate);
        return $date->format('Y-m-d H:i:s');
    }
    
    return 'Never updated';
}

function getCoinStats() {
    global $spexcon;
    
    $stats = array();
    
    $query = "SELECT COUNT(*) as count FROM coins";
    $result = $spexcon->query($query);
    $stats['total_coins'] = $result->fetch_assoc()['count'];
    
    $query = "SELECT COUNT(*) as count FROM coins c 
              JOIN coin_market_data cmd ON c.id = cmd.coin_id 
              WHERE cmd.market_cap_rank > 0";
    $result = $spexcon->query($query);
    $stats['active_coins'] = $result->fetch_assoc()['count'];
    
    $query = "SELECT COUNT(*) as count FROM coin_market_data 
              WHERE last_updated >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $result = $spexcon->query($query);
    $stats['recent_updates'] = $result->fetch_assoc()['count'];
    
    $query = "SELECT COUNT(*) as count FROM coins c 
              JOIN coin_images ci ON c.id = ci.coin_id 
              WHERE ci.small IS NOT NULL AND ci.small != ''";
    $result = $spexcon->query($query);
    $stats['coins_with_images'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

function getCoins($limit, $offset, $search, $rank, $change, $image) {
    global $spexcon;
    
    $whereConditions = array();
    $params = array();
    $types = '';
    
    if (!empty($search)) {
        $whereConditions[] = "(c.name LIKE ? OR c.symbol LIKE ? OR c.id LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'sss';
    }
    
    if (!empty($rank)) {
        switch ($rank) {
            case '1-50':
                $whereConditions[] = "cmd.market_cap_rank BETWEEN 1 AND 50";
                break;
            case '51-100':
                $whereConditions[] = "cmd.market_cap_rank BETWEEN 51 AND 100";
                break;
            case '101-500':
                $whereConditions[] = "cmd.market_cap_rank BETWEEN 101 AND 500";
                break;
            case '500+':
                $whereConditions[] = "cmd.market_cap_rank > 500";
                break;
        }
    }
    
    if (!empty($change)) {
        switch ($change) {
            case 'positive':
                $whereConditions[] = "cmd.price_change_percentage_24h > 0";
                break;
            case 'negative':
                $whereConditions[] = "cmd.price_change_percentage_24h < 0";
                break;
            case 'high-positive':
                $whereConditions[] = "cmd.price_change_percentage_24h >= 10";
                break;
            case 'high-negative':
                $whereConditions[] = "cmd.price_change_percentage_24h <= -10";
                break;
        }
    }
    
    if ($image !== '') {
        if ($image == '1') {
            $whereConditions[] = "ci.small IS NOT NULL AND ci.small != ''";
        } else {
            $whereConditions[] = "(ci.small IS NULL OR ci.small = '')";
        }
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $countQuery = "SELECT COUNT(*) as total 
                   FROM coins c 
                   LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id 
                   LEFT JOIN coin_images ci ON c.id = ci.coin_id 
                   $whereClause";
    
    if (!empty($params)) {
        $countStmt = $spexcon->prepare($countQuery);
        $countStmt->bind_param($types, ...$params);
        $countStmt->execute();
        $total = $countStmt->get_result()->fetch_assoc()['total'];
    } else {
        $total = $spexcon->query($countQuery)->fetch_assoc()['total'];
    }
    
    $query = "SELECT c.*, cmd.*, ci.small as small_image 
              FROM coins c 
              LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id 
              LEFT JOIN coin_images ci ON c.id = ci.coin_id 
              $whereClause 
              ORDER BY COALESCE(cmd.market_cap_rank, 999999) ASC, c.name ASC
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    if (!empty($whereConditions) || !empty($params)) {
        $stmt = $spexcon->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($query);
    }
    
    $coins = array();
    while ($row = $result->fetch_assoc()) {
        $coins[] = $row;
    }
    
    return array('coins' => $coins, 'total' => $total);
}

function getCoinDetails($coinId) {
    global $spexcon;
    
    $query = "SELECT c.*, cmd.*, ci.thumb as thumb_image, ci.small as small_image, ci.large as large_image 
              FROM coins c 
              LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id 
              LEFT JOIN coin_images ci ON c.id = ci.coin_id 
              WHERE c.id = ?";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("s", $coinId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

function getCoinDataFromPost() {
    return array(
        'id' => Sanitize($_POST['coin_id']),
        'name' => Sanitize($_POST['name']),
        'symbol' => Sanitize($_POST['symbol']),
        'description' => Sanitize($_POST['description']),
        'homepage' => Sanitize($_POST['homepage']),
        'blockchain_site' => Sanitize($_POST['blockchain_site']),
        'twitter_screen_name' => Sanitize($_POST['twitter_screen_name']),
        'telegram_channel_identifier' => Sanitize($_POST['telegram_channel_identifier']),
        'genesis_date' => Sanitize($_POST['genesis_date']),
        'current_price_usd' => floatval($_POST['current_price_usd'] ?? 0),
        'market_cap_usd' => intval($_POST['market_cap_usd'] ?? 0),
        'market_cap_rank' => intval($_POST['market_cap_rank'] ?? 0),
        'total_volume_usd' => intval($_POST['total_volume_usd'] ?? 0),
        'circulating_supply' => floatval($_POST['circulating_supply'] ?? 0),
        'total_supply' => floatval($_POST['total_supply'] ?? 0),
        'max_supply' => floatval($_POST['max_supply'] ?? 0),
        'thumb_image' => Sanitize($_POST['thumb_image']),
        'small_image' => Sanitize($_POST['small_image']),
        'large_image' => Sanitize($_POST['large_image'])
    );
}

function createCoin($coinData) {
    global $spexcon;
    
    $spexcon->begin_transaction();
    
    try {
        $query = "INSERT INTO coins (id, symbol, name, description, homepage, blockchain_site, 
                  twitter_screen_name, telegram_channel_identifier, genesis_date, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp(), current_timestamp())";
        
        $stmt = $spexcon->prepare($query);
        $stmt->bind_param("sssssssss", 
            $coinData['id'], 
            $coinData['symbol'], 
            $coinData['name'], 
            $coinData['description'], 
            $coinData['homepage'], 
            $coinData['blockchain_site'], 
            $coinData['twitter_screen_name'], 
            $coinData['telegram_channel_identifier'], 
            $coinData['genesis_date']
        );
        $stmt->execute();
        
        $marketQuery = "INSERT INTO coin_market_data (coin_id, current_price_usd, market_cap_usd, 
                        market_cap_rank, total_volume_usd, circulating_supply, total_supply, max_supply, last_updated) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, current_timestamp())";
        
        $marketStmt = $spexcon->prepare($marketQuery);
        $marketStmt->bind_param("sdiidddd", 
            $coinData['id'], 
            $coinData['current_price_usd'], 
            $coinData['market_cap_usd'], 
            $coinData['market_cap_rank'], 
            $coinData['total_volume_usd'], 
            $coinData['circulating_supply'], 
            $coinData['total_supply'], 
            $coinData['max_supply']
        );
        $marketStmt->execute();
        
        if (!empty($coinData['thumb_image']) || !empty($coinData['small_image']) || !empty($coinData['large_image'])) {
            $imageQuery = "INSERT INTO coin_images (coin_id, thumb, small, large, updated_at) 
                           VALUES (?, ?, ?, ?, current_timestamp())";
            
            $imageStmt = $spexcon->prepare($imageQuery);
            $imageStmt->bind_param("ssss", 
                $coinData['id'], 
                $coinData['thumb_image'], 
                $coinData['small_image'], 
                $coinData['large_image']
            );
            $imageStmt->execute();
        }
        
        $spexcon->commit();
        return true;
        
    } catch (Exception $e) {
        $spexcon->rollback();
        return false;
    }
}

function updateCoin($originalId, $coinData) {
    global $spexcon;
    
    $spexcon->begin_transaction();
    
    try {
        $query = "UPDATE coins SET id = ?, symbol = ?, name = ?, description = ?, homepage = ?, 
                  blockchain_site = ?, twitter_screen_name = ?, telegram_channel_identifier = ?, 
                  genesis_date = ?, updated_at = current_timestamp() WHERE id = ?";
        
        $stmt = $spexcon->prepare($query);
        $stmt->bind_param("ssssssssss", 
            $coinData['id'], 
            $coinData['symbol'], 
            $coinData['name'], 
            $coinData['description'], 
            $coinData['homepage'], 
            $coinData['blockchain_site'], 
            $coinData['twitter_screen_name'], 
            $coinData['telegram_channel_identifier'], 
            $coinData['genesis_date'],
            $originalId
        );
        $stmt->execute();
        
        $marketQuery = "UPDATE coin_market_data SET coin_id = ?, current_price_usd = ?, market_cap_usd = ?, 
                        market_cap_rank = ?, total_volume_usd = ?, circulating_supply = ?, total_supply = ?, 
                        max_supply = ?, last_updated = current_timestamp() WHERE coin_id = ?";
        
        $marketStmt = $spexcon->prepare($marketQuery);
        $marketStmt->bind_param("sdiidddds", 
            $coinData['id'], 
            $coinData['current_price_usd'], 
            $coinData['market_cap_usd'], 
            $coinData['market_cap_rank'], 
            $coinData['total_volume_usd'], 
            $coinData['circulating_supply'], 
            $coinData['total_supply'], 
            $coinData['max_supply'],
            $originalId
        );
        $marketStmt->execute();
        
        $checkImageQuery = "SELECT coin_id FROM coin_images WHERE coin_id = ?";
        $checkStmt = $spexcon->prepare($checkImageQuery);
        $checkStmt->bind_param("s", $originalId);
        $checkStmt->execute();
        $imageExists = $checkStmt->get_result()->num_rows > 0;
        
        if ($imageExists) {
            $imageQuery = "UPDATE coin_images SET coin_id = ?, thumb = ?, small = ?, large = ?, 
                           updated_at = current_timestamp() WHERE coin_id = ?";
            
            $imageStmt = $spexcon->prepare($imageQuery);
            $imageStmt->bind_param("sssss", 
                $coinData['id'], 
                $coinData['thumb_image'], 
                $coinData['small_image'], 
                $coinData['large_image'],
                $originalId
            );
            $imageStmt->execute();
        } else if (!empty($coinData['thumb_image']) || !empty($coinData['small_image']) || !empty($coinData['large_image'])) {
            $imageQuery = "INSERT INTO coin_images (coin_id, thumb, small, large, updated_at) 
                           VALUES (?, ?, ?, ?, current_timestamp())";
            
            $imageStmt = $spexcon->prepare($imageQuery);
            $imageStmt->bind_param("ssss", 
                $coinData['id'], 
                $coinData['thumb_image'], 
                $coinData['small_image'], 
                $coinData['large_image']
            );
            $imageStmt->execute();
        }
        
        $spexcon->commit();
        return true;
        
    } catch (Exception $e) {
        $spexcon->rollback();
        return false;
    }
}

function deleteCoin($coinId) {
    global $spexcon;
    
    $query = "DELETE FROM coins WHERE id = ?";
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("s", $coinId);
    
    return $stmt->execute();
}

function syncSingleCoin($coinId) {
    
    return true;
}

function syncAllCoins() {
    global $spexcon;
    
    $query = "UPDATE coin_market_data SET last_updated = current_timestamp()";
    $spexcon->query($query);
    
    $countQuery = "SELECT COUNT(*) as count FROM coin_market_data";
    $result = $spexcon->query($countQuery);
    
    require __DIR__ . '/../crons/UpdateMarket.php';

    return $result->fetch_assoc()['count'];
}

function exportCoinsCSV() {
    global $spexcon;
    
    $search = Sanitize($_GET['search'] ?? '');
    $rank = Sanitize($_GET['rank'] ?? '');
    $change = Sanitize($_GET['change'] ?? '');
    $image = $_GET['image'] ?? '';
    
    $whereConditions = array();
    $params = array();
    $types = '';
    
    if (!empty($search)) {
        $whereConditions[] = "(c.name LIKE ? OR c.symbol LIKE ? OR c.id LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'sss';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $query = "SELECT c.id, c.name, c.symbol, cmd.current_price_usd, cmd.market_cap_usd, 
                     cmd.market_cap_rank, cmd.price_change_percentage_24h, cmd.total_volume_usd, 
                     cmd.circulating_supply, cmd.total_supply, cmd.max_supply, cmd.last_updated
              FROM coins c 
              LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id 
              $whereClause 
              ORDER BY COALESCE(cmd.market_cap_rank, 999999) ASC";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($query);
    }
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="coins_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, array(
        'Coin ID', 'Name', 'Symbol', 'Price USD', 'Market Cap USD', 'Rank', 
        'Change 24h %', 'Volume 24h USD', 'Circulating Supply', 'Total Supply', 
        'Max Supply', 'Last Updated'
    ));
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['id'],
            $row['name'],
            $row['symbol'],
            $row['current_price_usd'],
            $row['market_cap_usd'],
            $row['market_cap_rank'],
            $row['price_change_percentage_24h'],
            $row['total_volume_usd'],
            $row['circulating_supply'],
            $row['total_supply'],
            $row['max_supply'],
            $row['last_updated']
        ));
    }
    
    fclose($output);
}

function getMonthlyUserData() {
    global $spexcon;
    
    $monthlyData = [];
    
    for($i = 11; $i >= 0; $i--) {
        $startDate = date('Y-m-01', strtotime("-$i months"));
        $endDate = date('Y-m-t', strtotime("-$i months"));
        
        $query = $spexcon->query("
            SELECT COUNT(*) as count 
            FROM users 
            WHERE register_date >= '$startDate' 
            AND register_date <= '$endDate 23:59:59'
            AND disablead = 0
        ");
        
        $result = $query->fetch_assoc();
        $monthlyData[] = [
            'month' => date('M Y', strtotime("-$i months")),
            'count' => (int)$result['count']
        ];
    }
    
    return $monthlyData;
}

function getTables() {
    global $spexcon;
    
    $query = "SHOW TABLES";
    $result = $spexcon->query($query);
    
    if (!$result) {
        throw new Exception("Failed to get tables: " . $spexcon->error);
    }
    
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        
        // Get row count
        $countQuery = $spexcon->query("SELECT COUNT(*) as count FROM `$tableName`");
        $rowCount = $countQuery ? $countQuery->fetch_assoc()['count'] : 0;
        
        $tables[] = [
            'name' => $tableName,
            'rows' => $rowCount
        ];
    }
    
    echo json_encode(['success' => true, 'tables' => $tables]);
}

function getTableData() {
    global $spexcon;
    
    $table = Sanitize($_POST['table']);
    if (empty($table)) {
        throw new Exception("Table name is required");
    }
    
    // Get table columns
    $columnsQuery = $spexcon->query("SHOW COLUMNS FROM `$table`");
    if (!$columnsQuery) {
        throw new Exception("Failed to get table columns: " . $spexcon->error);
    }
    
    $columns = [];
    while ($col = $columnsQuery->fetch_assoc()) {
        $columns[] = [
            'name' => $col['Field'],
            'type' => $col['Type'],
            'null' => $col['Null'],
            'key' => $col['Key'],
            'default' => $col['Default'],
            'extra' => $col['Extra']
        ];
    }
    
    // Get table data (limit to 100 records for performance)
    $dataQuery = $spexcon->query("SELECT * FROM `$table` ORDER BY 1 DESC LIMIT 100");
    if (!$dataQuery) {
        throw new Exception("Failed to get table data: " . $spexcon->error);
    }
    
    $data = [];
    while ($row = $dataQuery->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'columns' => $columns,
        'data' => $data
    ]);
}

function getRecord() {
    global $spexcon;
    
    $table = Sanitize($_POST['table']);
    $id = Sanitize($_POST['id']);
    
    if (empty($table) || empty($id)) {
        throw new Exception("Table name and ID are required");
    }
    
    // Get primary key column
    $pkQuery = $spexcon->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
    if (!$pkQuery || $pkQuery->num_rows === 0) {
        throw new Exception("No primary key found for table");
    }
    
    $pkCol = $pkQuery->fetch_assoc()['Column_name'];
    
    // Get record
    $query = $spexcon->prepare("SELECT * FROM `$table` WHERE `$pkCol` = ?");
    $query->bind_param("s", $id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Record not found");
    }
    
    $data = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $data]);
}

function insertRecord() {
    global $spexcon;
    
    $table = Sanitize($_POST['table']);
    if (empty($table)) {
        throw new Exception("Table name is required");
    }
    
    // Get table columns
    $columnsQuery = $spexcon->query("SHOW COLUMNS FROM `$table`");
    $columns = [];
    while ($col = $columnsQuery->fetch_assoc()) {
        if ($col['Extra'] !== 'auto_increment') {
            $columns[] = $col['Field'];
        }
    }
    
    // Build insert query
    $fields = [];
    $values = [];
    $types = '';
    $params = [];
    
    foreach ($columns as $column) {
        if (isset($_POST[$column])) {
            $fields[] = "`$column`";
            $values[] = "?";
            $types .= 's';
            $params[] = $_POST[$column];
        }
    }
    
    if (empty($fields)) {
        throw new Exception("No valid fields provided");
    }
    
    $sql = "INSERT INTO `$table` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
    $stmt = $spexcon->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Record inserted successfully']);
        } else {
            throw new Exception("Failed to insert record: " . $stmt->error);
        }
    } else {
        throw new Exception("Failed to prepare statement: " . $spexcon->error);
    }
}

function updateRecord() {
    global $spexcon;
    
    $table = Sanitize($_POST['table']);
    $id = Sanitize($_POST['id']);
    
    if (empty($table) || empty($id)) {
        throw new Exception("Table name and ID are required");
    }
    
    // Get primary key column
    $pkQuery = $spexcon->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
    if (!$pkQuery || $pkQuery->num_rows === 0) {
        throw new Exception("No primary key found for table");
    }
    
    $pkCol = $pkQuery->fetch_assoc()['Column_name'];
    
    // Get table columns
    $columnsQuery = $spexcon->query("SHOW COLUMNS FROM `$table`");
    $columns = [];
    while ($col = $columnsQuery->fetch_assoc()) {
        $columns[] = $col['Field'];
    }
    
    // Build update query
    $fields = [];
    $types = '';
    $params = [];
    
    foreach ($columns as $column) {
        if (isset($_POST[$column]) && $column !== $pkCol) {
            $fields[] = "`$column` = ?";
            $types .= 's';
            $params[] = $_POST[$column];
        }
    }
    
    if (empty($fields)) {
        throw new Exception("No valid fields provided");
    }
    
    $types .= 's'; // for WHERE condition
    $params[] = $id;
    
    $sql = "UPDATE `$table` SET " . implode(', ', $fields) . " WHERE `$pkCol` = ?";
    $stmt = $spexcon->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Record updated successfully']);
        } else {
            throw new Exception("Failed to update record: " . $stmt->error);
        }
    } else {
        throw new Exception("Failed to prepare statement: " . $spexcon->error);
    }
}

function deleteRecord() {
    global $spexcon;
    
    $table = Sanitize($_POST['table']);
    $id = Sanitize($_POST['id']);
    
    if (empty($table) || empty($id)) {
        throw new Exception("Table name and ID are required");
    }
    
    // Get primary key column
    $pkQuery = $spexcon->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
    if (!$pkQuery || $pkQuery->num_rows === 0) {
        throw new Exception("No primary key found for table");
    }
    
    $pkCol = $pkQuery->fetch_assoc()['Column_name'];
    
    // Delete record
    $stmt = $spexcon->prepare("DELETE FROM `$table` WHERE `$pkCol` = ?");
    $stmt->bind_param("s", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
    } else {
        throw new Exception("Failed to delete record: " . $stmt->error);
    }
}

function exportTable() {
    global $spexcon;
    
    $table = Sanitize($_GET['table']);
    if (empty($table)) {
        throw new Exception("Table name is required");
    }
    
    // Get table data
    $result = $spexcon->query("SELECT * FROM `$table`");
    if (!$result) {
        throw new Exception("Failed to export table: " . $spexcon->error);
    }
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $table . '_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Write CSV headers
    if ($result->num_rows > 0) {
        $firstRow = $result->fetch_assoc();
        fputcsv($output, array_keys($firstRow));
        fputcsv($output, $firstRow);
        
        // Write remaining rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }
    
    fclose($output);
    exit;
}

function getKYCStats() {
    global $spexcon;
    
    // Total applications
    $totalQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_identifications");
    $totalApplications = $totalQuery ? $totalQuery->fetch_assoc()['count'] : 0;
    
    // Pending applications
    $pendingQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_identifications WHERE status = 'PENDING'");
    $pendingApplications = $pendingQuery ? $pendingQuery->fetch_assoc()['count'] : 0;
    
    // Approved applications
    $approvedQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_identifications WHERE status = 'APPROVED'");
    $approvedApplications = $approvedQuery ? $approvedQuery->fetch_assoc()['count'] : 0;
    
    // Rejected applications
    $rejectedQuery = $spexcon->query("SELECT COUNT(*) as count FROM user_identifications WHERE status = 'REJECTED'");
    $rejectedApplications = $rejectedQuery ? $rejectedQuery->fetch_assoc()['count'] : 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'approved_applications' => $approvedApplications,
            'rejected_applications' => $rejectedApplications
        ]
    ]);
}

function getKYCApplications() {
    global $spexcon;
    
    $query = $spexcon->query("
        SELECT 
            ui.*,
            u.mail as user_email
        FROM user_identifications ui
        LEFT JOIN users u ON ui.user_id = u.uid
        ORDER BY ui.submitted_at DESC
    ");
    
    if (!$query) {
        throw new Exception("Database error: " . $spexcon->error);
    }
    
    $applications = [];
    while ($row = $query->fetch_assoc()) {
        $applications[] = $row;
    }
    
    echo json_encode(['success' => true, 'applications' => $applications]);
}

function updateKYCStatus() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    $status = Sanitize($_POST['status'] ?? '');
    $admin_notes = Sanitize($_POST['admin_notes'] ?? '');
    $admin_id = $_SESSION['adminID'] ?? 1;
    
    if (!$id || empty($status)) {
        throw new Exception("ID and status are required");
    }
    
    // Valid statuses
    $validStatuses = ['PENDING', 'APPROVED', 'REJECTED'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception("Invalid status");
    }
    
    $stmt = $spexcon->prepare("
        UPDATE user_identifications SET 
            status = ?, 
            admin_notes = ?, 
            approved_by = ?, 
            approved_at = NOW(),
            updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->bind_param("ssii", $status, $admin_notes, $admin_id, $id);
    
    if ($stmt->execute()) {
        // Update user's identification_verify status
        if ($status === 'APPROVED') {
            $userQuery = $spexcon->prepare("SELECT user_id FROM user_identifications WHERE id = ?");
            $userQuery->bind_param("i", $id);
            $userQuery->execute();
            $userResult = $userQuery->get_result();
            
            if ($userResult->num_rows > 0) {
                $userId = $userResult->fetch_assoc()['user_id'];
                $updateUser = $spexcon->prepare("UPDATE users SET identification_verify = 1 WHERE uid = ?");
                $updateUser->bind_param("i", $userId);
                $updateUser->execute();
            }
        } elseif ($status === 'REJECTED') {
            $userQuery = $spexcon->prepare("SELECT user_id FROM user_identifications WHERE id = ?");
            $userQuery->bind_param("i", $id);
            $userQuery->execute();
            $userResult = $userQuery->get_result();
            
            if ($userResult->num_rows > 0) {
                $userId = $userResult->fetch_assoc()['user_id'];
                $updateUser = $spexcon->prepare("UPDATE users SET identification_verify = 0 WHERE uid = ?");
                $updateUser->bind_param("i", $userId);
                $updateUser->execute();
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'KYC status updated successfully']);
    } else {
        throw new Exception("Failed to update KYC status: " . $stmt->error);
    }
}

function saveAdminNotes() {
    global $spexcon;
    
    $id = (int)($_POST['id'] ?? 0);
    $admin_notes = Sanitize($_POST['admin_notes'] ?? '');
    
    if (!$id) {
        throw new Exception("ID is required");
    }
    
    $stmt = $spexcon->prepare("UPDATE user_identifications SET admin_notes = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $admin_notes, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Notes saved successfully']);
    } else {
        throw new Exception("Failed to save notes: " . $stmt->error);
    }
}

function getLoginStats() {
    global $spexcon;
    
    $stats = array();
    
    // Successful logins
    $query = "SELECT COUNT(*) as count FROM user_login_history WHERE status = 'SUCCESS'";
    $result = $spexcon->query($query);
    $stats['successful_logins'] = $result->fetch_assoc()['count'];
    
    // Failed logins
    $query = "SELECT COUNT(*) as count FROM user_login_history WHERE status = 'FAILED'";
    $result = $spexcon->query($query);
    $stats['failed_logins'] = $result->fetch_assoc()['count'];
    
    // Unique users
    $query = "SELECT COUNT(DISTINCT user_id) as count FROM user_login_history";
    $result = $spexcon->query($query);
    $stats['unique_users'] = $result->fetch_assoc()['count'];
    
    // Last 24 hours
    $query = "SELECT COUNT(*) as count FROM user_login_history WHERE login_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $result = $spexcon->query($query);
    $stats['last_24_hours'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

function getLoginHistory($limit, $offset, $search, $status, $device, $dateFrom, $dateTo) {
    global $spexcon;
    
    $whereConditions = array();
    $params = array();
    $types = '';
    
    // Search filter
    if (!empty($search)) {
        if (is_numeric($search)) {
            $whereConditions[] = "ulh.user_id = ?";
            $params[] = intval($search);
            $types .= 'i';
        } else {
            $whereConditions[] = "(u.mail LIKE ? OR u.phone LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types .= 'ss';
        }
    }
    
    // Status filter
    if (!empty($status)) {
        $whereConditions[] = "ulh.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    // Device filter
    if (!empty($device)) {
        $whereConditions[] = "ulh.device LIKE ?";
        $params[] = "%$device%";
        $types .= 's';
    }
    
    // Date filters
    if (!empty($dateFrom)) {
        $whereConditions[] = "DATE(ulh.login_time) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $whereConditions[] = "DATE(ulh.login_time) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Count total records
    $countQuery = "SELECT COUNT(*) as total 
                   FROM user_login_history ulh 
                   LEFT JOIN users u ON ulh.user_id = u.uid 
                   $whereClause";
    
    if (!empty($params)) {
        $countStmt = $spexcon->prepare($countQuery);
        $countStmt->bind_param($types, ...$params);
        $countStmt->execute();
        $total = $countStmt->get_result()->fetch_assoc()['total'];
    } else {
        $total = $spexcon->query($countQuery)->fetch_assoc()['total'];
    }
    
    // Get paginated results
    $query = "SELECT ulh.*, u.mail as user_email, u.phone as user_phone 
              FROM user_login_history ulh 
              LEFT JOIN users u ON ulh.user_id = u.uid 
              $whereClause 
              ORDER BY ulh.login_time DESC 
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    if (!empty($whereConditions) || !empty($params)) {
        $stmt = $spexcon->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($query);
    }
    
    $history = array();
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    return array('history' => $history, 'total' => $total);
}

function getLoginDetails($id) {
    global $spexcon;
    
    $query = "SELECT ulh.*, u.mail, u.phone, u.disablead, u.register_date, u.last_login 
              FROM user_login_history ulh 
              LEFT JOIN users u ON ulh.user_id = u.uid 
              WHERE ulh.id = ?";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        return array(
            'record' => array(
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'ip_address' => $row['ip_address'],
                'device' => $row['device'],
                'browser' => $row['browser'],
                'location' => $row['location'],
                'status' => $row['status'],
                'login_time' => $row['login_time']
            ),
            'user_info' => array(
                'mail' => $row['mail'],
                'phone' => $row['phone'],
                'disablead' => $row['disablead'],
                'register_date' => $row['register_date'],
                'last_login' => $row['last_login']
            )
        );
    }
    
    return false;
}

function deleteLoginRecord($id) {
    global $spexcon;
    
    $query = "DELETE FROM user_login_history WHERE id = ?";
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("i", $id);
    
    return $stmt->execute();
}

function clearOldLoginHistory() {
    global $spexcon;
    
    $query = "DELETE FROM user_login_history WHERE login_time < DATE_SUB(NOW(), INTERVAL 90 DAY)";
    $result = $spexcon->query($query);
    
    return $spexcon->affected_rows;
}

function exportLoginHistoryCSV() {
    global $spexcon;
    
    $search = Sanitize($_GET['search'] ?? '');
    $status = Sanitize($_GET['status'] ?? '');
    $device = Sanitize($_GET['device'] ?? '');
    $dateFrom = Sanitize($_GET['date_from'] ?? '');
    $dateTo = Sanitize($_GET['date_to'] ?? '');
    
    $whereConditions = array();
    $params = array();
    $types = '';
    
    // Apply same filters as getLoginHistory
    if (!empty($search)) {
        if (is_numeric($search)) {
            $whereConditions[] = "ulh.user_id = ?";
            $params[] = intval($search);
            $types .= 'i';
        } else {
            $whereConditions[] = "(u.mail LIKE ? OR u.phone LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types .= 'ss';
        }
    }
    
    if (!empty($status)) {
        $whereConditions[] = "ulh.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    if (!empty($device)) {
        $whereConditions[] = "ulh.device LIKE ?";
        $params[] = "%$device%";
        $types .= 's';
    }
    
    if (!empty($dateFrom)) {
        $whereConditions[] = "DATE(ulh.login_time) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $whereConditions[] = "DATE(ulh.login_time) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $query = "SELECT ulh.id, ulh.user_id, u.mail as user_email, u.phone as user_phone, 
                     ulh.ip_address, ulh.device, ulh.browser, ulh.location, 
                     ulh.status, ulh.login_time 
              FROM user_login_history ulh 
              LEFT JOIN users u ON ulh.user_id = u.uid 
              $whereClause 
              ORDER BY ulh.login_time DESC";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($query);
    }
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="login_history_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // CSV headers
    fputcsv($output, array(
        'ID', 'User ID', 'User Email', 'User Phone', 'IP Address', 
        'Device', 'Browser', 'Location', 'Status', 'Login Time'
    ));
    
    // CSV data
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['id'],
            $row['user_id'],
            $row['user_email'],
            $row['user_phone'],
            $row['ip_address'],
            $row['device'],
            $row['browser'],
            $row['location'],
            $row['status'],
            $row['login_time']
        ));
    }
    
    fclose($output);
}

function getLastSync() {
    global $spexcon;
    
    $query = "SELECT MAX(last_updated) as last_sync FROM coin_market_data";
    $result = $spexcon->query($query);
    $lastSync = $result->fetch_assoc()['last_sync'];
    
    if ($lastSync) {
        $date = new DateTime($lastSync);
        return $date->format('Y-m-d H:i:s');
    }
    
    return 'Never synced';
}

function getMarketStats() {
    global $spexcon;
    
    $stats = array();
    
    $query = "SELECT 
                SUM(market_cap_usd) as total_market_cap,
                SUM(total_volume_usd) as total_volume,
                COUNT(*) as active_coins
              FROM coin_market_data 
              WHERE market_cap_rank > 0 AND market_cap_usd > 0";
    $result = $spexcon->query($query);
    $data = $result->fetch_assoc();
    
    $stats['total_market_cap'] = $data['total_market_cap'] ?? 0;
    $stats['total_volume'] = $data['total_volume'] ?? 0;
    $stats['active_coins'] = $data['active_coins'] ?? 0;
    
    $btcQuery = "SELECT market_cap_usd FROM coin_market_data WHERE coin_id = 'bitcoin'";
    $btcResult = $spexcon->query($btcQuery);
    $btcData = $btcResult->fetch_assoc();
    
    if ($btcData && $stats['total_market_cap'] > 0) {
        $stats['btc_dominance'] = ($btcData['market_cap_usd'] / $stats['total_market_cap']) * 100;
    } else {
        $stats['btc_dominance'] = 0;
    }
    
    return $stats;
}

function getTopGainersLosers() {
    global $spexcon;
    
    $gainersQuery = "SELECT c.id, c.name, c.symbol, cmd.current_price_usd, cmd.price_change_percentage_24h, ci.small as image
                     FROM coins c 
                     JOIN coin_market_data cmd ON c.id = cmd.coin_id 
                     LEFT JOIN coin_images ci ON c.id = ci.coin_id 
                     WHERE cmd.price_change_percentage_24h > 0 AND cmd.market_cap_rank <= 500
                     ORDER BY cmd.price_change_percentage_24h DESC 
                     LIMIT 10";
    
    $losersQuery = "SELECT c.id, c.name, c.symbol, cmd.current_price_usd, cmd.price_change_percentage_24h, ci.small as image
                    FROM coins c 
                    JOIN coin_market_data cmd ON c.id = cmd.coin_id 
                    LEFT JOIN coin_images ci ON c.id = ci.coin_id 
                    WHERE cmd.price_change_percentage_24h < 0 AND cmd.market_cap_rank <= 500
                    ORDER BY cmd.price_change_percentage_24h ASC 
                    LIMIT 10";
    
    $gainers = array();
    $losers = array();
    
    $gainersResult = $spexcon->query($gainersQuery);
    while ($row = $gainersResult->fetch_assoc()) {
        $gainers[] = $row;
    }
    
    $losersResult = $spexcon->query($losersQuery);
    while ($row = $losersResult->fetch_assoc()) {
        $losers[] = $row;
    }
    
    return array('gainers' => $gainers, 'losers' => $losers);
}

function getMarketData($limit, $offset, $search, $rank, $price, $change, $sortBy, $sortOrder) {
    global $spexcon;
    
    $whereConditions = array();
    $params = array();
    $types = '';
    
    if (!empty($search)) {
        $whereConditions[] = "(c.name LIKE ? OR c.symbol LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
    }
    
    if (!empty($rank)) {
        switch ($rank) {
            case '1-10':
                $whereConditions[] = "cmd.market_cap_rank BETWEEN 1 AND 10";
                break;
            case '11-50':
                $whereConditions[] = "cmd.market_cap_rank BETWEEN 11 AND 50";
                break;
            case '51-100':
                $whereConditions[] = "cmd.market_cap_rank BETWEEN 51 AND 100";
                break;
            case '101-500':
                $whereConditions[] = "cmd.market_cap_rank BETWEEN 101 AND 500";
                break;
            case '500+':
                $whereConditions[] = "cmd.market_cap_rank > 500";
                break;
        }
    }
    
    if (!empty($price)) {
        switch ($price) {
            case '0-1':
                $whereConditions[] = "cmd.current_price_usd BETWEEN 0 AND 1";
                break;
            case '1-10':
                $whereConditions[] = "cmd.current_price_usd BETWEEN 1 AND 10";
                break;
            case '10-100':
                $whereConditions[] = "cmd.current_price_usd BETWEEN 10 AND 100";
                break;
            case '100-1000':
                $whereConditions[] = "cmd.current_price_usd BETWEEN 100 AND 1000";
                break;
            case '1000+':
                $whereConditions[] = "cmd.current_price_usd > 1000";
                break;
        }
    }
    
    if (!empty($change)) {
        switch ($change) {
            case 'positive':
                $whereConditions[] = "cmd.price_change_percentage_24h > 0";
                break;
            case 'negative':
                $whereConditions[] = "cmd.price_change_percentage_24h < 0";
                break;
            case 'high-gain':
                $whereConditions[] = "cmd.price_change_percentage_24h >= 20";
                break;
            case 'high-loss':
                $whereConditions[] = "cmd.price_change_percentage_24h <= -20";
                break;
        }
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $validSortColumns = ['market_cap_rank', 'market_cap_usd', 'current_price_usd', 'price_change_percentage_24h', 'total_volume_usd'];
    if (!in_array($sortBy, $validSortColumns)) {
        $sortBy = 'market_cap_rank';
    }
    
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
    
    $countQuery = "SELECT COUNT(*) as total 
                   FROM coins c 
                   JOIN coin_market_data cmd ON c.id = cmd.coin_id 
                   $whereClause";
    
    if (!empty($params)) {
        $countStmt = $spexcon->prepare($countQuery);
        $countStmt->bind_param($types, ...$params);
        $countStmt->execute();
        $total = $countStmt->get_result()->fetch_assoc()['total'];
    } else {
        $total = $spexcon->query($countQuery)->fetch_assoc()['total'];
    }
    
    $query = "SELECT c.id, c.name, c.symbol, cmd.*, ci.small as image 
              FROM coins c 
              JOIN coin_market_data cmd ON c.id = cmd.coin_id 
              LEFT JOIN coin_images ci ON c.id = ci.coin_id 
              $whereClause 
              ORDER BY cmd.$sortBy $sortOrder, c.name ASC
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    if (!empty($whereConditions) || !empty($params)) {
        $stmt = $spexcon->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($query);
    }
    
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return array('data' => $data, 'total' => $total);
}

function getCoinFullDetails($coinId) {
    global $spexcon;
    
    $query = "SELECT c.*, cmd.*, ci.small as image 
              FROM coins c 
              LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id 
              LEFT JOIN coin_images ci ON c.id = ci.coin_id 
              WHERE c.id = ?";
    
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("s", $coinId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

function refreshMarketData() {
    global $spexcon;
    
    $query = "UPDATE coin_market_data SET last_updated = current_timestamp()";
    $spexcon->query($query);
    
    $countQuery = "SELECT COUNT(*) as count FROM coin_market_data";
    $result = $spexcon->query($countQuery);
    
    return $result->fetch_assoc()['count'];
}

function exportMarketDataCSV() {
    global $spexcon;
    
    $search = Sanitize($_GET['search'] ?? '');
    $rank = Sanitize($_GET['rank'] ?? '');
    $price = Sanitize($_GET['price'] ?? '');
    $change = Sanitize($_GET['change'] ?? '');
    $sortBy = Sanitize($_GET['sort_by'] ?? 'market_cap_rank');
    $sortOrder = Sanitize($_GET['sort_order'] ?? 'asc');
    
    $whereConditions = array();
    $params = array();
    $types = '';
    
    if (!empty($search)) {
        $whereConditions[] = "(c.name LIKE ? OR c.symbol LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $validSortColumns = ['market_cap_rank', 'market_cap_usd', 'current_price_usd', 'price_change_percentage_24h', 'total_volume_usd'];
    if (!in_array($sortBy, $validSortColumns)) {
        $sortBy = 'market_cap_rank';
    }
    
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
    
    $query = "SELECT c.id, c.name, c.symbol, cmd.current_price_usd, cmd.market_cap_usd, 
                     cmd.market_cap_rank, cmd.price_change_percentage_24h, cmd.price_change_percentage_7d,
                     cmd.total_volume_usd, cmd.circulating_supply, cmd.total_supply, cmd.max_supply,
                     cmd.ath_usd, cmd.atl_usd, cmd.last_updated
              FROM coins c 
              JOIN coin_market_data cmd ON c.id = cmd.coin_id 
              $whereClause 
              ORDER BY cmd.$sortBy $sortOrder";
    
    if (!empty($params)) {
        $stmt = $spexcon->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $spexcon->query($query);
    }
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="market_data_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, array(
        'Coin ID', 'Name', 'Symbol', 'Price USD', 'Market Cap USD', 'Rank', 
        'Change 24h %', 'Change 7d %', 'Volume 24h USD', 'Circulating Supply', 
        'Total Supply', 'Max Supply', 'ATH USD', 'ATL USD', 'Last Updated'
    ));
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['id'],
            $row['name'],
            $row['symbol'],
            $row['current_price_usd'],
            $row['market_cap_usd'],
            $row['market_cap_rank'],
            $row['price_change_percentage_24h'],
            $row['price_change_percentage_7d'],
            $row['total_volume_usd'],
            $row['circulating_supply'],
            $row['total_supply'],
            $row['max_supply'],
            $row['ath_usd'],
            $row['atl_usd'],
            $row['last_updated']
        ));
    }
    
    fclose($output);
}

function getSettings() {
    global $spexcon;
    
    $query = $spexcon->query("SELECT * FROM settings WHERE id = 1");
    if (!$query) {
        throw new Exception("Failed to get settings: " . $spexcon->error);
    }
    
    $settings = $query->fetch_assoc();
    if (!$settings) {
        $settings = createDefaultSettings();
    }
    
    echo json_encode(['success' => true, 'settings' => $settings]);
}

function createDefaultSettings() {
    global $spexcon;
    
    $defaults = [
        'BaseName' => 'Z-Admin Trading Platform',
        'BaseTitle' => 'Professional Trading Platform', 
        'BaseDesc' => 'Advanced cryptocurrency trading platform',
        'BaseLogo' => '',
        'minDeposit' => '10.00',
        'minWithdraw' => '5.00',
        'RegBonus' => '50.00',
        'RefBonus' => '25.00',
        'PayBonus' => '10.00',
        'DailyBonus' => '5.00',
        'daily_withdraw_limit' => '1000.00',
        'DailyPageOn' => '1',
        'ref_lvl_percentage1' => '10.00',
        'ref_lvl_percentage2' => '5.00',
        'ref_lvl_percentage3' => '3.00',
        'ref_lvl_percentage4' => '2.00',
        'ref_lvl_percentage5' => '1.00',
        'smtp_host' => '',
        'smtp_post' => '587',
        'smtp_user' => '',
        'smtp_pass' => '',
        'tawk_to' => '',
        'MaintenanceMode' => '0',
        'RegistrationEnabled' => '1',
        'MaintenanceMessage' => 'System is under maintenance. Please try again later.'
    ];
    
    $fields = array_keys($defaults);
    $values = array_values($defaults);
    $placeholders = str_repeat('?,', count($defaults) - 1) . '?';
    
    $sql = "INSERT INTO settings (id, " . implode(', ', $fields) . ") VALUES (1, $placeholders)";
    $stmt = $spexcon->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($defaults)), ...$values);
    $stmt->execute();
    
    return $defaults;
}

function uploadLogo() {
    global $spexcon;

    if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No file uploaded or upload error");
    }
    
    $file = $_FILES['logo'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.");
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("File too large. Maximum size is 5MB.");
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = generateRandomString(12) . '.' . $extension;
    
    $uploadDir = __DIR__ . '/../../pages/assets/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploadPath = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception("Failed to upload file");
    }

    $spexcon->query("UPDATE settings SET `BaseLogo`='core/pages/assets/images/$filename' WHERE 1");
    
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'message' => 'Logo uploaded successfully'
    ]);
}

function generateRandomString($length = 12) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function saveSettings() {
    global $spexcon;
    
    $allowedFields = [
        'BaseName', 'BaseTitle', 'BaseDesc', 'BaseLogo',
        'minDeposit', 'minWithdraw', 'RegBonus', 'RefBonus', 
        'PayBonus', 'DailyBonus', 'daily_withdraw_limit', 'DailyPageOn',
        'ref_lvl_percentage1', 'ref_lvl_percentage2', 'ref_lvl_percentage3', 
        'ref_lvl_percentage4', 'ref_lvl_percentage5',
        'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass',
        'tawk_to', 'MaintenanceMode', 'RegistrationEnabled', 'MaintenanceMessage'
    ];
    
    $updateFields = [];
    $updateValues = [];
    
    foreach ($allowedFields as $field) {
        if (isset($_POST[$field])) {
            $updateFields[] = "$field = ?"; 
            $updateValues[] = Sanitize($_POST[$field]);
        }
    }
    
    if (empty($updateFields)) {
        throw new Exception("No valid fields to update");
    }
    
    $checkQuery = $spexcon->query("SELECT id FROM settings WHERE id = 1");
    
    if ($checkQuery && $checkQuery->num_rows > 0) {
        $sql = "UPDATE settings SET " . implode(', ', $updateFields) . " WHERE id = 1";
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($updateValues)), ...$updateValues);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
        } else {
            throw new Exception("Failed to update settings: " . $stmt->error);
        }
    } else {
        $updateFields[] = "id = ?";
        $updateValues[] = "1";
        
        $sql = "INSERT INTO settings SET " . implode(', ', $updateFields);
        $stmt = $spexcon->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($updateValues)), ...$updateValues);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Settings created successfully']);
        } else {
            throw new Exception("Failed to create settings: " . $stmt->error);
        }
    }
}

function getSystemInfo2() {
    global $spexcon;
    
    $mysqlVersion = $spexcon->server_info;
    $phpVersion = PHP_VERSION;
    $serverOS = php_uname('s') . ' ' . php_uname('r');
    $memoryUsage = formatBytes(memory_get_usage(true));
    $diskSpace = formatBytes(disk_free_space('.')) . ' / ' . formatBytes(disk_total_space('.'));
    
    $uptimeFile = '/proc/uptime';
    $uptime = 'N/A';
    if (file_exists($uptimeFile)) {
        $uptimeSeconds = floatval(explode(' ', file_get_contents($uptimeFile))[0]);
        $uptime = formatUptime($uptimeSeconds);
    } elseif (function_exists('shell_exec')) {
        $uptime = shell_exec('uptime -p') ?: 'N/A';
        $uptime = trim($uptime);
    }
    
    $info = [
        'php_version' => $phpVersion,
        'mysql_version' => $mysqlVersion,
        'server_os' => $serverOS,
        'memory_usage' => $memoryUsage,
        'disk_space' => $diskSpace,
        'uptime' => $uptime
    ];
    
    echo json_encode(['success' => true, 'info' => $info]);
}

function formatUptime($seconds) {
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    
    return "{$days}d {$hours}h {$minutes}m";
}

function Censor($text, $type = 'word') {
    if ($type === 'email') {
        if (filter_var($text, FILTER_VALIDATE_EMAIL)) {
            $parts = explode('@', $text);
            $username = $parts[0];
            $domain = $parts[1];
            
            $censoredUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
            
            $domainParts = explode('.', $domain);
            $censoredDomain = substr($domainParts[0], 0, 1) . str_repeat('*', strlen($domainParts[0]) - 1);
            if (isset($domainParts[1])) {
                $censoredDomain .= '.' . $domainParts[1];
            }
            
            return $censoredUsername . '@' . $censoredDomain;
        }
    }
    
    if (strlen($text) <= 2) {
        return str_repeat('*', strlen($text));
    }
    
    return $text[0] . str_repeat('*', strlen($text) - 2) . $text[strlen($text) - 1];
}

function generateEmailTemplate($uid, $newPassword) {
    $BaseSet = FetchWebSettings();
    $BaseName = $BaseSet['BaseName'];
    $domain = $_SERVER['HTTP_HOST'];
    $loginLink = "https://$domain/?r=login";
    $IPAddress = $_SERVER['REMOTE_ADDR']; 
    $Time = date('l jS \of F Y h:i:s A');
    
    return '
    <!DOCTYPE html>
    <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Password Reset - '.$BaseName.'</title>
        <link rel="stylesheet" href="https://'.$domain.'/core/pages/assets/mailtemp.css">
    </head>

    <body class="email-body">
        <div class="preheader">
            Your '.$BaseName.' account password has been reset successfully. Access details and new password inside.
        </div>
        
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="email-container">
            <tr>
                <td align="center" class="main-wrapper">
                    
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-wrapper">
                        
                        <!-- Header -->
                        <tr>
                            <td class="header-bg">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td class="header-content">
                                            <div class="logo-container">
                                                <span class="logo-text">
                                                    📈 '.$BaseName.'
                                                </span>
                                            </div>
                                            
                                            <h1 class="header-title">
                                                Password Reset Successful
                                            </h1>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- Main content -->
                        <tr>
                            <td class="main-content mobile-padding">
                                <!-- Welcome message -->
                                <p class="greeting">
                                    Dear <strong>'.$uid.'</strong>,
                                </p>
                                
                                <p class="intro-text">
                                    Your '.$BaseName.' account password has been successfully reset. For your security, we have generated a new temporary password.
                                </p>
                                
                                <!-- New password display -->
                                <div class="password-container">
                                    <h2 class="password-title">
                                        🔐 Your New Password
                                    </h2>
                                    
                                    <div class="password-box">
                                        <span class="password-text">
                                            '.$newPassword.'
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- CTA Button -->
                                <div class="cta-container">
                                    <table role="presentation" cellspacing="0" cellpadding="0" class="cta-table">
                                        <tr>
                                            <td class="cta-button">
                                                <a href="'.$loginLink.'" class="cta-link">
                                                    Login to Your Account
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <!-- Security Information -->
                                <div class="security-info">
                                    <h3 class="security-title">
                                        🛡️ Security Details
                                    </h3>
                                    
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="security-table">
                                        <tr>
                                            <td class="security-label">IP Address:</td>
                                            <td class="security-value security-ip">'.$IPAddress.'</td>
                                        </tr>
                                        <tr>
                                            <td class="security-label">Time:</td>
                                            <td class="security-value">'.$Time.'</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <!-- Security warning -->
                                <div class="security-warning">
                                    <h3 class="warning-title">
                                        ⚠️ Important Security Notice
                                    </h3>
                                    
                                    <p class="warning-text">
                                        If you did not request this password reset, you can safely ignore this email and your password will remain unchanged.
                                    </p>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td class="footer-bg">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td class="footer-content">
                                            <p class="footer-text">
                                                This is an automated email. Please do not reply to this message.
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="footer-copyright">
                                            <p class="copyright-text">
                                                © 2025 '.$BaseName.'. All rights reserved.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ';
}

function getLocalUpdateStatus($updateId) {
    global $spexcon;
    
    $stmt = $spexcon->prepare("SELECT is_success FROM system_updates WHERE update_id = ?");
    $stmt->bind_param("i", $updateId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['is_success'] ? 'completed' : 'failed';
    }
    
    return 'pending';
}

function saveUpdateStatus($updateId, $updateName, $version, $path, $isSuccess, $errorMessage = null) {
    global $spexcon;
    
    $stmt = $spexcon->prepare("
        INSERT INTO system_updates (update_id, update_name, version, path, is_success, error_message) 
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        is_success = VALUES(is_success), 
        error_message = VALUES(error_message),
        installed_at = CURRENT_TIMESTAMP
    ");
    
    $stmt->bind_param("isssis", $updateId, $updateName, $version, $path, $isSuccess, $errorMessage);
    return $stmt->execute();
}

function getUpdateStats($blipBlap) {
    global $spexcon;
    
    if (!$blipBlap->login()['success']) {
        echo json_encode(['success' => false, 'message' => 'CoreBase login failed']);
        return;
    }
    
    $updates = $blipBlap->getAllUpdates(['product_id' => '1']);
    
    if (!$updates['success']) {
        echo json_encode(['success' => false, 'message' => 'Failed to get updates']);
        return;
    }
    
    $stats = [
        'available' => 0,
        'completed' => 0,
        'security' => 0,
        'failed' => 0
    ];
    
    foreach ($updates['data'] as $update) {
        $localStatus = getLocalUpdateStatus($update['id']);
        
        if ($localStatus === 'completed') {
            $stats['completed']++;
        } elseif ($localStatus === 'failed') {
            $stats['failed']++;
        } else {
            $stats['available']++;
        }
        
        if ($update['is_security_update']) {
            $stats['security']++;
        }
    }
    
    echo json_encode(['success' => true, 'stats' => $stats]);
}

function getUpdates($blipBlap) {
    global $spexcon;
    
    if (!$blipBlap->login()['success']) {
        echo json_encode(['success' => false, 'message' => 'CoreBase login failed']);
        return;
    }
    
    $updates = $blipBlap->getAllUpdates(['product_id' => '1']);
    
    if (!$updates['success']) {
        echo json_encode(['success' => false, 'message' => 'Failed to get updates']);
        return;
    }
    
    foreach ($updates['data'] as &$update) {
        $update['status'] = getLocalUpdateStatus($update['id']);
    }
    
    usort($updates['data'], function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    echo json_encode(['success' => true, 'updates' => $updates['data']]);
}

function installUpdate($blipBlap) {
    global $spexcon;
    
    $updateId = $_POST['id'] ?? 0;
    
    if (!$updateId) {
        echo json_encode(['success' => false, 'message' => 'Update ID required']);
        return;
    }
    
    if (!$blipBlap->login()['success']) {
        echo json_encode(['success' => false, 'message' => 'CoreBase login failed']);
        return;
    }
    
    $processId = uniqid('install_', true);
    
    $tempDir = __DIR__ . '/../temp/';
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }
    
    $logFile = $tempDir . "install_{$processId}.log";
    $statusFile = $tempDir . "status_{$processId}.json";
    
    try {
        updateInstallStatus($statusFile, $logFile, 'Getting update details...', 10);
        
        $update = $blipBlap->getUpdate($updateId);
        if (!$update['success']) {
            throw new Exception('Failed to get update details');
        }
        
        $updateData = $update['data'];
        $updateName = $updateData['update_name'];
        $version = $updateData['version'];
        
        logInstallMessage($logFile, "Update: {$updateName} v{$version}");
        
        updateInstallStatus($statusFile, $logFile, 'Downloading update...', 30);
        
        $downloadResult = $blipBlap->downloadUpdate($updateId);
        if (!$downloadResult['success']) {
            throw new Exception('Failed to download update');
        }
        
        logInstallMessage($logFile, "Downloaded: {$downloadResult['filename']} ({$downloadResult['size']} bytes)");
        
        updateInstallStatus($statusFile, $logFile, 'Verifying checksum...', 50);
        
        if ($downloadResult['checksum'] && $updateData['checksum']) {
            $localChecksum = md5_file($downloadResult['file_path']);
            if ($localChecksum !== $updateData['checksum']) {
                throw new Exception('Checksum verification failed');
            }
            logInstallMessage($logFile, "Checksum verified: {$localChecksum}");
        }
        
        updateInstallStatus($statusFile, $logFile, 'Processing update...', 70);
        
        $processResult = $blipBlap->processUpdate($downloadResult['file_path'], false);
        if (!$processResult['success']) {
            throw new Exception('Failed to process update: ' . $processResult['message']);
        }
        
        logInstallMessage($logFile, "Update processed successfully");
        
        updateInstallStatus($statusFile, $logFile, 'Finalizing installation...', 90);
        
        saveUpdateStatus($updateId, $updateName, $version, $downloadResult['file_path'], 1);
        
        updateInstallStatus($statusFile, $logFile, 'Installation completed successfully!', 100, true);
        logInstallMessage($logFile, "Installation completed successfully");
        
        echo json_encode(['success' => true, 'process_id' => $processId]);
        
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        
        if (isset($updateData)) {
            saveUpdateStatus($updateId, $updateData['update_name'], $updateData['version'], null, 0, $errorMessage);
        }
        
        updateInstallStatus($statusFile, $logFile, 'Installation failed: ' . $errorMessage, 100, true, $errorMessage);
        logInstallMessage($logFile, "ERROR: {$errorMessage}");
        
        echo json_encode(['success' => false, 'message' => $errorMessage]);
    }
}

function updateInstallStatus($statusFile, $logFile, $status, $progress, $completed = false, $error = null) {
    $statusData = [
        'status' => $status,
        'progress' => $progress,
        'completed' => $completed,
        'error' => $error,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($statusFile, json_encode($statusData));
    logInstallMessage($logFile, $status);
}

function logInstallMessage($logFile, $message) {
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] {$message}\n", FILE_APPEND);
}

function getInstallProgress() {
    $processId = $_POST['process_id'] ?? '';
    
    if (!$processId) {
        echo json_encode(['success' => false, 'message' => 'Process ID required']);
        return;
    }
    
    $logFile = __DIR__ . "/../temp/install_{$processId}.log";
    $statusFile = __DIR__ . "/../temp/status_{$processId}.json";
    
    $logs = '';
    $status = 'Installing...';
    $progress = 0;
    $completed = false;
    $error = null;
    
    if (file_exists($statusFile)) {
        $statusData = json_decode(file_get_contents($statusFile), true);
        $status = $statusData['status'] ?? 'Installing...';
        $progress = $statusData['progress'] ?? 0;
        $completed = $statusData['completed'] ?? false;
        $error = $statusData['error'] ?? null;
    }
    
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
    }
    
    if ($completed) {
        if (file_exists($logFile)) unlink($logFile);
        if (file_exists($statusFile)) unlink($statusFile);
    }
    
    echo json_encode([
        'success' => true,
        'status' => $status,
        'progress' => $progress,
        'completed' => $completed,
        'error' => $error,
        'logs' => $logs
    ]);
}

function rollbackUpdate($blipBlap) {
    global $spexcon;
    
    $updateId = $_POST['id'] ?? 0;
    
    if (!$updateId) {
        echo json_encode(['success' => false, 'message' => 'Update ID required']);
        return;
    }
    
    if (!$blipBlap->login()['success']) {
        echo json_encode(['success' => false, 'message' => 'CoreBase login failed']);
        return;
    }
    
    try {
        $stmt = $spexcon->prepare("SELECT * FROM system_updates WHERE update_id = ? AND is_success = 1");
        $stmt->bind_param("i", $updateId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$localUpdate = $result->fetch_assoc()) {
            throw new Exception('Update not found or not successfully installed');
        }
        
        $zipPath = $localUpdate['path'];
        if (!file_exists($zipPath)) {
            throw new Exception('Update ZIP file not found: ' . $zipPath);
        }
        
        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== TRUE) {
            throw new Exception('Cannot open update ZIP file');
        }
        
        $metaJson = $zip->getFromName('meta.json');
        $zip->close();
        
        if (!$metaJson) {
            throw new Exception('meta.json not found in update ZIP');
        }
        
        $meta = json_decode($metaJson, true);
        if (!$meta) {
            throw new Exception('Invalid meta.json in update ZIP');
        }

        $metaUpdateName = $meta['update']['name'];
        $metaVersion = $meta['update']['version'];
        
        $result = $blipBlap->rollbackUpdate($metaUpdateName, $metaVersion);
        
        if (!$result['success']) {
            echo json_encode([
                'success' => false, 
                'message' => $result['message'],
                'debug' => [
                    'rollback_result' => $result,
                    'meta_name' => $metaUpdateName,
                    'meta_version' => $metaVersion,
                    'zip_path' => $zipPath
                ]
            ]);
            return;
        }
        
        $stmt = $spexcon->prepare("DELETE FROM system_updates WHERE update_id = ?");
        $stmt->bind_param("i", $updateId);
        $deleteResult = $stmt->execute();
        
        
        echo json_encode([
            'success' => true, 
            'message' => "Update {$metaUpdateName} v{$metaVersion} has been rolled back successfully",
            'debug' => [
                'rollback_result' => $result,
                'database_deleted' => $deleteResult
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage(),
            'debug' => [
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]
        ]);
    }
}

function checkForUpdates($blipBlap) {
    global $spexcon;
    
    if (!$blipBlap->login()['success']) {
        echo json_encode(['success' => false, 'message' => 'CoreBase login failed']);
        return;
    }
    
    $updates = $blipBlap->getAllUpdates(['product_id' => '1']);
    
    if (!$updates['success']) {
        echo json_encode(['success' => false, 'message' => 'Failed to check for updates']);
        return;
    }
    
    $newUpdates = 0;
    foreach ($updates['data'] as $update) {
        $localStatus = getLocalUpdateStatus($update['id']);
        if ($localStatus === 'pending') {
            $newUpdates++;
        }
    }
    
    echo json_encode([
        'success' => true, 
        'new_updates' => $newUpdates,
        'message' => $newUpdates > 0 ? "Found {$newUpdates} new updates" : 'System is up to date'
    ]);
}

// SpeX-V4 Function END

//-------------------------------------------------------------------------------------------------------\\

// -- END OF V4 -- \\
