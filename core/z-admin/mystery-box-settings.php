<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Mystery Box Settings</h4>
                        <p class="page-title-desc">Configure global mystery box system settings</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">System Configuration</h4>
                        </div>
                        <div class="card-body">
                            <form id="settingsForm">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="systemActive">
                                                <label class="form-check-label" for="systemActive">
                                                    <strong>System Active</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Enable/disable mystery box drop system</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="useEligibleOnly">
                                                <label class="form-check-label" for="useEligibleOnly">
                                                    <strong>Eligible Users Only</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Only drop boxes to selected users</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Drop Chance (%)</label>
                                            <input type="number" step="0.01" min="0" max="100" class="form-control" id="dropChance">
                                            <small class="text-muted">Percentage chance for drops</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Min Interval (Minutes)</label>
                                            <input type="number" min="1" class="form-control" id="dropIntervalMin">
                                            <small class="text-muted">Minimum time between drops</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Max Daily Drops</label>
                                            <input type="number" min="1" class="form-control" id="maxDailyDrops">
                                            <small class="text-muted">Per user daily limit</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Announcement Message</label>
                                    <textarea class="form-control" id="announcementMessage" rows="3"></textarea>
                                    <small class="text-muted">Message shown when system is activated</small>
                                </div>

                            </form>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" onclick="saveSettings()">
                                <i class="fas fa-save me-1"></i>Save Settings
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="loadSettings()">
                                <i class="fas fa-refresh me-1"></i>Reload
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">System Stats</h4>
                        </div>
                        <div class="card-body" id="systemStats">
                            <div class="text-center">
                                <div class="spinner-border"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadSettings();
    loadStats();
});

function loadSettings() {
    $.ajax({
        url: 'ajax/mystery_box_settings_admin.php',
        method: 'POST',
        data: { action: 'getSettings333' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const settings = response.data;
                $('#systemActive').prop('checked', settings.system_active == 1);
                $('#useEligibleOnly').prop('checked', settings.use_eligible_users_only == 1);
                $('#dropChance').val(settings.drop_chance);
                $('#dropIntervalMin').val(settings.drop_interval_min);
                $('#maxDailyDrops').val(settings.max_daily_drops);
                $('#announcementMessage').val(settings.announcement_message);
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function saveSettings() {
    const data = {
        action: 'updateSettings333',
        system_active: $('#systemActive').is(':checked') ? 1 : 0,
        use_eligible_users_only: $('#useEligibleOnly').is(':checked') ? 1 : 0,
        drop_chance: $('#dropChance').val(),
        drop_interval_min: $('#dropIntervalMin').val(),
        max_daily_drops: $('#maxDailyDrops').val(),
        announcement_message: $('#announcementMessage').val()
    };
    
    $.ajax({
        url: 'ajax/mystery_box_settings_admin.php',
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire("Success!", response.message, "success");
                loadStats();
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function loadStats() {
    $.ajax({
        url: 'ajax/mystery_box_settings_admin.php',
        method: 'POST',
        data: { action: 'getStats333' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayStats(response.data);
            }
        }
    });
}

function displayStats(stats) {
    const html = `
        <div class="row text-center">
            <div class="col-6">
                <h4 class="text-primary">${stats.total_drops}</h4>
                <small class="text-muted">Total Drops</small>
            </div>
            <div class="col-6">
                <h4 class="text-success">${stats.claimed_drops}</h4>
                <small class="text-muted">Claimed</small>
            </div>
        </div>
        <hr>
        <div class="row text-center">
            <div class="col-6">
                <h4 class="text-warning">${stats.eligible_users}</h4>
                <small class="text-muted">Eligible Users</small>
            </div>
            <div class="col-6">
                <h4 class="text-info">${stats.active_boxes}</h4>
                <small class="text-muted">Active Boxes</small>
            </div>
        </div>
    `;
    $('#systemStats').html(html);
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>