<?php require_once __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Journey Settings</h4>
                </div>
                <div class="card-body">
                    <form id="settingsForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Journey System</label>
                                <select class="form-select" id="is_enabled">
                                    <option value="1">Enabled</option>
                                    <option value="0">Disabled</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reset Period (Coming Next Update)</label>
                                <select class="form-select" id="reset_period">
                                    <option value="NEVER">Never</option>
                                    <option value="DAILY">Daily</option>
                                    <option value="WEEKLY">Weekly</option>
                                    <option value="MONTHLY">Monthly</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Welcome Message</label>
                            <textarea class="form-control" id="welcome_message" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Completion Message</label>
                            <textarea class="form-control" id="completion_message" rows="3"></textarea>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="saveSettings()">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(() => loadSettings());

function loadSettings() {
    $.post('ajax/journey-settings-ajax.php', {action: 'getSettings'}, function(data) {
        if(data.success) {
            const s = data.settings;
            $('#is_enabled').val(s.is_enabled);
            $('#reset_period').val(s.reset_period);
            $('#welcome_message').val(s.welcome_message);
            $('#completion_message').val(s.completion_message);
        }
    }, 'json');
}

function saveSettings() {
    const formData = {
        action: 'updateSettings',
        is_enabled: $('#is_enabled').val(),
        reset_period: $('#reset_period').val(),
        welcome_message: $('#welcome_message').val(),
        completion_message: $('#completion_message').val()
    };
    
    $.post('ajax/journey-settings-ajax.php', formData, function(data) {
        if(data.success) {
            Swal.fire('Success', 'Settings updated successfully!', 'success');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    }, 'json');
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>