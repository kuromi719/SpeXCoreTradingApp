<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">System Settings</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Z-Admin</a></li>
                        <li class="breadcrumb-item active">System Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">General Settings</h4>
                </div>
                <div class="card-body">
                    <form id="generalSettingsForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="BaseName" id="BaseName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Site Title</label>
                                <input type="text" class="form-control" name="BaseTitle" id="BaseTitle" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Site Description</label>
                                <textarea class="form-control" name="BaseDesc" id="BaseDesc" rows="3"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Site Logo</label>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="file" class="form-control" id="logoFile" accept="image/*">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-primary w-100" id="uploadLogoBtn">
                                            <i class="fas fa-upload me-1"></i>Upload Logo
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2" id="currentLogo">
                                    <small class="text-muted">Current logo:</small>
                                    <div class="mt-1">
                                        <img id="logoPreview" src="" alt="Logo" style="max-height: 60px; display: none;" class="border rounded">
                                        <span id="noLogo" class="text-muted">No logo uploaded</span>
                                    </div>
                                </div>
                                <input type="hidden" name="BaseLogo" id="BaseLogo">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" id="saveGeneralBtn">
                        <i class="fas fa-save me-1"></i>Save General Settings
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Trading Settings</h4>
                </div>
                <div class="card-body">
                    <form id="tradingSettingsForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Deposit Amount</label>
                                <input type="number" step="0.01" class="form-control" name="minDeposit" id="minDeposit">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Withdrawal Amount</label>
                                <input type="number" step="0.01" class="form-control" name="minWithdraw" id="minWithdraw">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Registration Bonus</label>
                                <input type="number" step="0.01" class="form-control" name="RegBonus" id="RegBonus">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Referral Bonus</label>
                                <input type="number" step="0.01" class="form-control" name="RefBonus" id="RefBonus">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Bonus</label>
                                <input type="number" step="0.01" class="form-control" name="PayBonus" id="PayBonus">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Daily Bonus</label>
                                <input type="number" step="0.01" class="form-control" name="DailyBonus" id="DailyBonus">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Daily Withdrawal Limit</label>
                                <input type="number" step="0.01" class="form-control" name="daily_withdraw_limit" id="daily_withdraw_limit">
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="DailyPageOn" id="DailyPageOn">
                                    <label class="form-check-label" for="DailyPageOn">Enable Daily Page</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6 class="mb-3">Referral Level Percentages</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Level 1 (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" name="ref_lvl_percentage1" id="ref_lvl_percentage1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Level 2 (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" name="ref_lvl_percentage2" id="ref_lvl_percentage2">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Level 3 (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" name="ref_lvl_percentage3" id="ref_lvl_percentage3">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Level 4 (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" name="ref_lvl_percentage4" id="ref_lvl_percentage4">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Level 5 (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" name="ref_lvl_percentage5" id="ref_lvl_percentage5">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" id="saveTradingBtn">
                        <i class="fas fa-save me-1"></i>Save Trading Settings
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">SMTP Settings</h4>
                </div>
                <div class="card-body">
                    <form id="smtpSettingsForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" name="smtp_host" id="smtp_host">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SMTP Port</label>
                                <input type="number" class="form-control" name="smtp_port" id="smtp_port" value="587">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SMTP Username</label>
                                <input type="text" class="form-control" name="smtp_user" id="smtp_user">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SMTP Password</label>
                                <input type="password" class="form-control" name="smtp_pass" id="smtp_pass">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" id="saveSmtpBtn">
                        <i class="fas fa-save me-1"></i>Save SMTP Settings
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Integration Settings</h4>
                </div>
                <div class="card-body">
                    <form id="integrationSettingsForm">
                        <div class="mb-3">
                            <label class="form-label">Tawk.to Chat Code</label>
                            <textarea class="form-control" name="tawk_to" id="tawk_to" rows="5" placeholder="Paste your Tawk.to chat widget code here..."></textarea>
                            <small class="text-muted">Paste the complete Tawk.to script code</small>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" id="saveIntegrationBtn">
                        <i class="fas fa-save me-1"></i>Save Integration Settings
                    </button>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">System Status</h4>
                </div>
                <div class="card-body">
                    <form id="systemSettings">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="MaintenanceMode" id="MaintenanceMode">
                                <label class="form-check-label" for="MaintenanceMode">Maintenance Mode</label>
                            </div>
                            <small class="text-muted">Enable to block user access for maintenance</small>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="RegistrationEnabled" id="RegistrationEnabled">
                                <label class="form-check-label" for="RegistrationEnabled">Allow New Registrations</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maintenance Message</label>
                            <textarea class="form-control" name="MaintenanceMessage" id="MaintenanceMessage" rows="3" placeholder="System under maintenance..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" id="saveSystemBtn">
                        <i class="fas fa-save me-1"></i>Save System Settings
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadSettings();
    loadSystemInfo();
    
    function loadSettings() {
        $.ajax({
            url: 'ajax/system_settings.php',
            method: 'POST',
            data: { action: 'getSettings' },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    populateSettings(response.settings);
                } else {
                    Swal.fire("Error!", "Failed to load settings: " + response.message, "error");
                }
            },
            error: function() {
                Swal.fire("Error!", "Failed to load settings", "error");
            }
        });
    }
    
    function populateSettings(settings) {
        Object.keys(settings).forEach(function(key) {
            let element = $(`#${key}`);
            if(element.length) {
                if(element.is('input[type="checkbox"]')) {
                    element.prop('checked', settings[key] == '1' || settings[key] === true);
                } else {
                    element.val(settings[key]);
                }
            }
        });
        
        if(settings.BaseLogo && settings.BaseLogo !== '') {
            $('#logoPreview').attr('src', '../../' + settings.BaseLogo).show();
            $('#noLogo').hide();
        }
    }
    
    function loadSystemInfo() {
        $.ajax({
            url: 'ajax/system_settings.php',
            method: 'POST',
            data: { action: 'getSystemInfo' },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    displaySystemInfo(response.info);
                }
            }
        });
    }
    
    function displaySystemInfo(info) {
        let html = `
            <div class="row">
                <div class="col-12 mb-2">
                    <strong>PHP Version:</strong> ${info.php_version}
                </div>
                <div class="col-12 mb-2">
                    <strong>MySQL Version:</strong> ${info.mysql_version}
                </div>
                <div class="col-12 mb-2">
                    <strong>Server OS:</strong> ${info.server_os}
                </div>
                <div class="col-12 mb-2">
                    <strong>Memory Usage:</strong> ${info.memory_usage}
                </div>
                <div class="col-12 mb-2">
                    <strong>Disk Space:</strong> ${info.disk_space}
                </div>
                <div class="col-12 mb-2">
                    <strong>Uptime:</strong> ${info.uptime}
                </div>
            </div>
        `;
        $('#systemInfo').html(html);
    }
    
    $('#uploadLogoBtn').click(function() {
        let fileInput = $('#logoFile')[0];
        if(!fileInput.files[0]) {
            Swal.fire("Warning!", "Please select a logo file first", "warning");
            return;
        }
        
        let formData = new FormData();
        formData.append('action', 'uploadLogo');
        formData.append('logo', fileInput.files[0]);
        
        $.ajax({
            url: 'ajax/system_settings.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#BaseLogo').val(response.filename);
                    $('#logoPreview').attr('src', '../pages/assets/images/' + response.filename).show();
                    $('#noLogo').hide();
                    Swal.fire("Success!", "Logo uploaded successfully!", "success");
                } else {
                    Swal.fire("Error!", "Failed to upload logo: " + response.message, "error");
                }
            },
            error: function() {
                Swal.fire("Error!", "Failed to upload logo", "error");
            }
        });
    });
    
    $('#saveGeneralBtn').click(function() {
        saveSettings('general');
    });
    
    $('#saveTradingBtn').click(function() {
        saveSettings('trading');
    });
    
    $('#saveSmtpBtn').click(function() {
        saveSettings('smtp');
    });
    
    $('#saveIntegrationBtn').click(function() {
        saveSettings('integration');
    });
    
    $('#saveSystemBtn').click(function() {
        saveSettings('system');
    });
    
    function saveSettings(type) {
        let formData = new FormData();
        formData.append('action', 'saveSettings');
        formData.append('type', type);
        
        let formSelector = '';
        switch(type) {
            case 'general':
                formSelector = '#generalSettingsForm';
                break;
            case 'trading':
                formSelector = '#tradingSettingsForm';
                break;
            case 'smtp':
                formSelector = '#smtpSettingsForm';
                break;
            case 'integration':
                formSelector = '#integrationSettingsForm';
                break;
            case 'system':
                formSelector = '#systemSettings';
                break;
        }
        
        $(formSelector).find('input, select, textarea').each(function() {
            let element = $(this);
            let name = element.attr('name');
            let value = element.is('input[type="checkbox"]') ? (element.is(':checked') ? '1' : '0') : element.val();
            if(name) {
                formData.append(name, value);
            }
        });
        
        $.ajax({
            url: 'ajax/system_settings.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    Swal.fire("Success!", "Settings saved successfully!", "success");
                } else {
                    Swal.fire("Error!", "Failed to save settings: " + response.message, "error");
                }
            },
            error: function() {
                Swal.fire("Error!", "Failed to save settings", "error");
            }
        });
    }
});
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>