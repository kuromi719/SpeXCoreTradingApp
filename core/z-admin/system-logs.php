<?php require __DIR__ . '/inc/header.php'; ?>

        <div class="container-xxl">
            
            <!-- Log Controls -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title">System Logs [DEVELOPERS ONLY]</h4>
                                    <p class="card-title-desc">Monitor system errors and activities</p>
                                </div>
                                <div class="col-auto">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="refreshLogs()" title="Refresh">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadLogs()" title="Download Logs">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearLogs()" title="Clear All Logs">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Log Level</label>
                                    <select class="form-select" id="logLevel" onchange="applyFilters()">
                                        <option value="">All Levels</option>
                                        <option value="ERROR">Error</option>
                                        <option value="WARNING">Warning</option>
                                        <option value="INFO">Info</option>
                                        <option value="DEBUG">Debug</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date From</label>
                                    <input type="date" class="form-control" id="dateFrom" onchange="applyFilters()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date To</label>
                                    <input type="date" class="form-control" id="dateTo" onchange="applyFilters()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" class="form-control" id="searchText" placeholder="Search logs..." onkeyup="applyFilters()">
                                </div>
                            </div>
                            
                            <!-- Auto Refresh Toggle -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="autoRefresh">
                                        <label class="form-check-label" for="autoRefresh">
                                            Auto Refresh (30s)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Log Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="icon-item icon-item-md bg-danger-subtle text-danger mx-auto mb-3">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="card-title">Errors</h5>
                            <h3 class="text-danger mb-2" id="errorCount">0</h3>
                            <small class="text-muted">Total error logs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="icon-item icon-item-md bg-warning-subtle text-warning mx-auto mb-3">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <h5 class="card-title">Warnings</h5>
                            <h3 class="text-warning mb-2" id="warningCount">0</h3>
                            <small class="text-muted">Total warning logs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="icon-item icon-item-md bg-info-subtle text-info mx-auto mb-3">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h5 class="card-title">Info</h5>
                            <h3 class="text-info mb-2" id="infoCount">0</h3>
                            <small class="text-muted">Total info logs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="icon-item icon-item-md bg-success-subtle text-success mx-auto mb-3">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h5 class="card-title">Log Files</h5>
                            <h3 class="text-success mb-2" id="fileCount">0</h3>
                            <small class="text-muted">Available log files</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Log Files List -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Log Files</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>File Name</th>
                                            <th>Size</th>
                                            <th>Last Modified</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="logFilesTableBody">
                                        <!-- Content will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Log Entries -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">Recent Log Entries</h5>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-primary" id="logCount">0 entries</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="log-container" id="logContainer" style="max-height: 600px; overflow-y: auto;">
                                <div class="text-center p-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Loading logs...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Log Detail Modal -->
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-labelledby="logDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logDetailModalLabel">Log Entry Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="logDetailContent">
                    <!-- Log details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.log-entry {
    border-left: 4px solid #dee2e6;
    margin: 0;
    padding: 12px 16px;
    border-bottom: 1px solid #f8f9fa;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.4;
    transition: background-color 0.2s;
}

.log-entry:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.log-entry.error {
    border-left-color: #dc3545;
    background-color: rgba(220, 53, 69, 0.05);
}

.log-entry.warning {
    border-left-color: #ffc107;
    background-color: rgba(255, 193, 7, 0.05);
}

.log-entry.info {
    border-left-color: #0dcaf0;
    background-color: rgba(13, 202, 240, 0.05);
}

.log-entry.debug {
    border-left-color: #6c757d;
    background-color: rgba(108, 117, 125, 0.05);
}

.log-timestamp {
    color: #6c757d;
    font-weight: 600;
}

.log-level {
    font-weight: 700;
    text-transform: uppercase;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
}

.log-level.error {
    color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
}

.log-level.warning {
    color: #f57c00;
    background-color: rgba(245, 124, 0, 0.1);
}

.log-level.info {
    color: #0dcaf0;
    background-color: rgba(13, 202, 240, 0.1);
}

.log-level.debug {
    color: #6c757d;
    background-color: rgba(108, 117, 125, 0.1);
}

.log-message {
    color: #212529;
    margin-top: 4px;
    word-break: break-word;
}

.log-file {
    color: #6c757d;
    font-size: 11px;
    margin-top: 4px;
}
</style>

<script>
let autoRefreshInterval;
let allLogs = [];
let filteredLogs = [];

$(document).ready(function() {
    loadLogStats();
    loadLogFiles();
    loadLogs();
    
    // Set default date range (last 7 days)
    const today = new Date();
    const lastWeek = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
    
    $('#dateTo').val(today.toISOString().split('T')[0]);
    $('#dateFrom').val(lastWeek.toISOString().split('T')[0]);
    
    // Auto refresh toggle
    $('#autoRefresh').change(function() {
        if ($(this).is(':checked')) {
            autoRefreshInterval = setInterval(function() {
                loadLogs();
                loadLogStats();
            }, 30000);
        } else {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        }
    });
    
    // Real-time search
    let searchTimeout;
    $('#searchText').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            applyFilters();
        }, 300);
    });
});

function loadLogStats() {
    $.ajax({
        url: 'ajax/system-logs-ajax.php',
        type: 'POST',
        data: {action: 'getStats'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#errorCount').text(response.stats.error_count);
                $('#warningCount').text(response.stats.warning_count);
                $('#infoCount').text(response.stats.info_count);
                $('#fileCount').text(response.stats.file_count);
            }
        }
    });
}

function loadLogFiles() {
    $.ajax({
        url: 'ajax/system-logs-ajax.php',
        type: 'POST',
        data: {action: 'getLogFiles'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayLogFiles(response.files);
            }
        }
    });
}

function displayLogFiles(files) {
    let html = '';
    
    if (files.length === 0) {
        html = '<tr><td colspan="4" class="text-center text-muted">No log files found</td></tr>';
    } else {
        files.forEach(function(file) {
            html += `
                <tr>
                    <td>
                        <i class="fas fa-file-alt text-info me-2"></i>
                        <strong>${file.name}</strong>
                    </td>
                    <td><code>${file.size}</code></td>
                    <td>
                        <i class="fas fa-clock text-muted me-1"></i>
                        ${file.modified}
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewLogFile('${file.name}')" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="downloadLogFile('${file.name}')" title="Download">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteLogFile('${file.name}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#logFilesTableBody').html(html);
}

function loadLogs() {
    $.ajax({
        url: 'ajax/system-logs-ajax.php',
        type: 'POST',
        data: {action: 'getLogs'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allLogs = response.logs;
                applyFilters();
            } else {
                $('#logContainer').html('<div class="text-center p-4 text-muted">No logs found</div>');
            }
        },
        error: function() {
            $('#logContainer').html('<div class="text-center p-4 text-danger">Failed to load logs</div>');
        }
    });
}

function applyFilters() {
    const logLevel = $('#logLevel').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    const searchText = $('#searchText').val().toLowerCase();
    
    filteredLogs = allLogs.filter(log => {
        // Filter by log level
        if (logLevel && log.level !== logLevel) {
            return false;
        }
        
        // Filter by date range
        if (dateFrom && log.date < dateFrom) {
            return false;
        }
        if (dateTo && log.date > dateTo) {
            return false;
        }
        
        // Filter by search text
        if (searchText && !log.message.toLowerCase().includes(searchText) && 
            !log.file.toLowerCase().includes(searchText)) {
            return false;
        }
        
        return true;
    });
    
    displayLogs(filteredLogs);
}

function displayLogs(logs) {
    let html = '';
    
    if (logs.length === 0) {
        html = '<div class="text-center p-4 text-muted">No matching log entries found</div>';
    } else {
        logs.forEach(function(log, index) {
            const levelClass = log.level.toLowerCase();
            html += `
                <div class="log-entry ${levelClass}" onclick="showLogDetail(${index})">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <span class="log-timestamp">${log.timestamp}</span>
                            <span class="log-level ${levelClass} ms-2">${log.level}</span>
                            <div class="log-message">${log.message}</div>
                            <div class="log-file">${log.file}:${log.line}</div>
                        </div>
                        <div class="text-end">
                            <i class="fas fa-external-link-alt text-muted"></i>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#logContainer').html(html);
    $('#logCount').text(logs.length + ' entries');
}

function showLogDetail(index) {
    const log = filteredLogs[index];
    const levelClass = log.level.toLowerCase();
    
    const detailHtml = `
        <div class="row">
            <div class="col-md-6">
                <strong>Timestamp:</strong><br>
                <code>${log.timestamp}</code>
            </div>
            <div class="col-md-6">
                <strong>Level:</strong><br>
                <span class="log-level ${levelClass}">${log.level}</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <strong>File:</strong><br>
                <code>${log.file}</code>
            </div>
            <div class="col-md-6">
                <strong>Line:</strong><br>
                <code>${log.line}</code>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <strong>Message:</strong><br>
                <div class="bg-light p-3 rounded mt-2" style="font-family: monospace; white-space: pre-wrap;">${log.message}</div>
            </div>
        </div>
        ${log.trace ? `
        <hr>
        <div class="row">
            <div class="col-12">
                <strong>Stack Trace:</strong><br>
                <div class="bg-dark text-light p-3 rounded mt-2" style="font-family: monospace; font-size: 12px; white-space: pre-wrap;">${log.trace}</div>
            </div>
        </div>
        ` : ''}
    `;
    
    $('#logDetailContent').html(detailHtml);
    $('#logDetailModal').modal('show');
}

function refreshLogs() {
    loadLogs();
    loadLogStats();
    loadLogFiles();
}

function downloadLogs() {
    window.location.href = 'ajax/system-logs-ajax.php?action=downloadAll';
}

function clearLogs() {
    Swal.fire({
        title: 'Clear All Logs?',
        text: "This will permanently delete all log files!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4d56',
        cancelButtonColor: '#22c55e',
        confirmButtonText: 'Yes, clear all!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/system-logs-ajax.php',
                type: 'POST',
                data: {action: 'clearAllLogs'},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Cleared!', response.message, 'success');
                        loadLogs();
                        loadLogStats();
                        loadLogFiles();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        }
    });
}

function viewLogFile(filename) {
    $.ajax({
        url: 'ajax/system-logs-ajax.php',
        type: 'POST',
        data: {action: 'getLogFileContent', filename: filename},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allLogs = response.logs;
                applyFilters();
                
                // Scroll to top
                $('#logContainer').scrollTop(0);
                
                Swal.fire({
                    title: 'Log File Loaded',
                    text: `Loaded ${response.logs.length} entries from ${filename}`,
                    icon: 'success',
                    timer: 2000
                });
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        }
    });
}

function downloadLogFile(filename) {
    window.location.href = 'ajax/system-logs-ajax.php?action=downloadFile&filename=' + encodeURIComponent(filename);
}

function deleteLogFile(filename) {
    Swal.fire({
        title: 'Delete Log File?',
        text: `Are you sure you want to delete ${filename}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4d56',
        cancelButtonColor: '#22c55e',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/system-logs-ajax.php',
                type: 'POST',
                data: {action: 'deleteLogFile', filename: filename},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success');
                        loadLogFiles();
                        loadLogStats();
                        if (allLogs.length > 0) {
                            loadLogs(); // Reload current logs
                        }
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        }
    });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>