<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="page-title">Trading Bots Management</h6>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-end">
                    <button class="btn btn-success btn-sm" onclick="addNewBot()">
                        <i class="fas fa-robot"></i> Create Bot
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-robot text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalBots">0</h5>
                            <p class="mb-0 text-white-50">Total Bots</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-play text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="activeBots">0</h5>
                            <p class="mb-0 text-white-50">Active Bots</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalSubscribers">0</h5>
                            <p class="mb-0 text-white-50">Total Users</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Bots Management</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Bot</th>
                            <th>Level</th>
                            <th>Profit Rate</th>
                            <th>Success Rate</th>
                            <th>Risk</th>
                            <th>Users</th>
                            <th>Profit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="botsTableBody">
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bot Modal -->
<div class="modal fade" id="botModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="botModalLabel">Create New Bot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="botForm">
                    <input type="hidden" id="editingBotId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Bot Name</label>
                                <input type="text" class="form-control" id="botName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="botDescription" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Logo URL</label>
                                <input type="text" class="form-control" id="botLogo">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Level</label>
                                <select class="form-select" id="botLevel">
                                    <option value="BEGINNER">Beginner</option>
                                    <option value="INTERMEDIATE">Intermediate</option>
                                    <option value="ADVANCED">Advanced</option>
                                    <option value="EXPERT">Expert</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hourly Profit (%)</label>
                                <input type="number" class="form-control" id="botProfitRate" step="0.0001" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Success Rate (%)</label>
                                <input type="number" class="form-control" id="botSuccessRate" step="0.01" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Risk Level</label>
                                <select class="form-select" id="botRiskLevel">
                                    <option value="LOW">Low Risk</option>
                                    <option value="MEDIUM">Medium Risk</option>
                                    <option value="HIGH">High Risk</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Bot Type</label>
                                <select class="form-select" id="botType">
                                    <option value="SPOT_GRID">Spot Grid</option>
                                    <option value="FUTURES_GRID">Futures Grid</option>
                                    <option value="ARBITRAGE">Arbitrage</option>
                                    <option value="ALGO_ORDER">Algo Order</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">1 Hour ($)</label>
                                <input type="number" class="form-control" id="price1h" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">6 Hours ($)</label>
                                <input type="number" class="form-control" id="price6h" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">24 Hours ($)</label>
                                <input type="number" class="form-control" id="price24h" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">1 Week ($)</label>
                                <input type="number" class="form-control" id="price168h" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="botIsActive" checked>
                        <label class="form-check-label" for="botIsActive">Bot Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBot()">Save Bot</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadBotStats();
    loadBots();
});

let allBots = [];

function loadBotStats() {
    $.ajax({
        url: 'ajax/trading-bots-ajax.php',
        type: 'POST',
        data: {action: 'getStats'},
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.success) {
                    $('#totalBots').text(response.stats.total_bots);
                    $('#activeBots').text(response.stats.active_bots);
                    $('#totalSubscribers').text(response.stats.total_users);
                    $('#totalEarnings').text('$' + parseFloat(response.stats.total_profit).toFixed(2));
                }
            } catch(e) {
                console.log('Stats parse error:', e);
            }
        },
        error: function(xhr, status, error) {
            console.log('Stats ajax error:', error);
        }
    });
}

function loadBots() {
    $.ajax({
        url: 'ajax/trading-bots-ajax.php',
        type: 'POST',
        data: {action: 'getBots'},
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.success) {
                    allBots = response.bots;
                    displayBotsTable(allBots);
                } else {
                    $('#botsTableBody').html('<tr><td colspan="9" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
                }
            } catch(e) {
                $('#botsTableBody').html('<tr><td colspan="9" class="text-center text-danger">Parse error</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#botsTableBody').html('<tr><td colspan="9" class="text-center text-danger">Ajax error: ' + error + '</td></tr>');
        }
    });
}

function displayBotsTable(bots) {
    let html = '';
    
    if (bots.length === 0) {
        html = '<tr><td colspan="9" class="text-center text-muted">No bots found</td></tr>';
    } else {
        bots.forEach(function(bot) {
            const levelColors = {
                'BEGINNER': 'success',
                'INTERMEDIATE': 'warning', 
                'ADVANCED': 'info',
                'EXPERT': 'danger'
            };
            
            const riskColors = {
                'LOW': 'success',
                'MEDIUM': 'warning',
                'HIGH': 'danger'
            };
            
            html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                ${bot.logo ? 
                                    `<img src="${bot.logo}" alt="${bot.name}" width="32" height="32" class="rounded">` :
                                    `<i class="fas fa-robot text-primary fs-18"></i>`
                                }
                            </div>
                            <div>
                                <div class="fw-medium">${bot.name}</div>
                                <small class="text-muted">${bot.bot_type}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-${levelColors[bot.level]}">${bot.level}</span></td>
                    <td><span class="text-success fw-bold">${parseFloat(bot.hourly_profit_percentage).toFixed(4)}%</span></td>
                    <td><span class="text-info fw-bold">${parseFloat(bot.success_rate).toFixed(2)}%</span></td>
                    <td><span class="badge bg-${riskColors[bot.risk_level]}">${bot.risk_level}</span></td>
                    <td><span class="fw-bold text-primary">${bot.total_users || 0}</span></td>
                    <td><span class="text-success fw-bold">$${parseFloat(bot.total_profit_generated || 0).toFixed(2)}</span></td>
                    <td>
                        <span class="badge bg-${bot.is_active == 1 ? 'success' : 'secondary'}">
                            ${bot.is_active == 1 ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="editBot(${bot.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-${bot.is_active == 1 ? 'warning' : 'success'}" onclick="toggleBotStatus(${bot.id})" title="${bot.is_active == 1 ? 'Deactivate' : 'Activate'}">
                                <i class="fas fa-${bot.is_active == 1 ? 'pause' : 'play'}"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteBot(${bot.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#botsTableBody').html(html);
}

function addNewBot() {
    $('#botModalLabel').text('Create New Bot');
    $('#editingBotId').val('');
    $('#botForm')[0].reset();
    $('#botIsActive').prop('checked', true);
    $('#botModal').modal('show');
}

function editBot(botId) {
    const bot = allBots.find(b => b.id == botId);
    if (!bot) {
        Swal.fire('Error!', 'Bot not found', 'error');
        return;
    }
    
    $('#botModalLabel').text('Edit Bot: ' + bot.name);
    $('#editingBotId').val(botId);
    $('#botName').val(bot.name);
    $('#botDescription').val(bot.description);
    $('#botLogo').val(bot.logo);
    $('#botLevel').val(bot.level);
    $('#botProfitRate').val(bot.hourly_profit_percentage);
    $('#botSuccessRate').val(bot.success_rate);
    $('#botRiskLevel').val(bot.risk_level);
    $('#botType').val(bot.bot_type);
    $('#botIsActive').prop('checked', bot.is_active == 1);
    
    try {
        const pricing = JSON.parse(bot.rental_prices || '{}');
        $('#price1h').val(pricing['1'] || '');
        $('#price6h').val(pricing['6'] || '');
        $('#price24h').val(pricing['24'] || '');
        $('#price168h').val(pricing['168'] || '');
    } catch(e) {
        console.log('Pricing parse error:', e);
    }
    
    $('#botModal').modal('show');
}

function saveBot() {
    const isEditing = $('#editingBotId').val() !== '';
    
    const pricing = {
        "1": parseFloat($('#price1h').val()) || 0,
        "6": parseFloat($('#price6h').val()) || 0,
        "24": parseFloat($('#price24h').val()) || 0,
        "168": parseFloat($('#price168h').val()) || 0
    };
    
    const formData = {
        action: isEditing ? 'updateBot' : 'createBot',
        name: $('#botName').val(),
        description: $('#botDescription').val(),
        logo: $('#botLogo').val(),
        level: $('#botLevel').val(),
        hourly_profit_percentage: $('#botProfitRate').val(),
        success_rate: $('#botSuccessRate').val(),
        risk_level: $('#botRiskLevel').val(),
        bot_type: $('#botType').val(),
        rental_prices: JSON.stringify(pricing),
        is_active: $('#botIsActive').is(':checked') ? 1 : 0
    };
    
    if (isEditing) {
        formData.bot_id = $('#editingBotId').val();
    }
    
    $.ajax({
        url: 'ajax/trading-bots-ajax.php',
        type: 'POST',
        data: formData,
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.success) {
                    Swal.fire('Success!', isEditing ? 'Bot updated successfully!' : 'Bot created successfully!', 'success');
                    $('#botModal').modal('hide');
                    loadBots();
                    loadBotStats();
                } else {
                    Swal.fire('Error!', 'Failed to save bot: ' + response.message, 'error');
                }
            } catch(e) {
                Swal.fire('Error!', 'Response parse error', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Ajax error: ' + error, 'error');
        }
    });
}

function toggleBotStatus(botId) {
    const bot = allBots.find(b => b.id == botId);
    if (!bot) return;
    
    const newStatus = bot.is_active == 1 ? 0 : 1;
    const actionText = newStatus == 1 ? 'activate' : 'deactivate';
    
    Swal.fire({
        title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} Bot?`,
        text: `Are you sure you want to ${actionText} this bot?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, ' + actionText + '!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/trading-bots-ajax.php',
                type: 'POST',
                data: {
                    action: 'toggleBotStatus',
                    bot_id: botId,
                    status: newStatus
                },
                success: function(data) {
                    try {
                        const response = JSON.parse(data);
                        if (response.success) {
                            Swal.fire('Success!', `Bot ${actionText}d successfully!`, 'success');
                            loadBots();
                            loadBotStats();
                        } else {
                            Swal.fire('Error!', 'Failed to update bot status: ' + response.message, 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error!', 'Response parse error', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error!', 'Ajax error: ' + error, 'error');
                }
            });
        }
    });
}

function deleteBot(botId) {
    Swal.fire({
        title: 'Delete Bot?',
        text: 'This will permanently delete the bot!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/trading-bots-ajax.php',
                type: 'POST',
                data: {action: 'deleteBot', bot_id: botId},
                success: function(data) {
                    try {
                        const response = JSON.parse(data);
                        if (response.success) {
                            Swal.fire('Deleted!', 'Bot deleted successfully!', 'success');
                            loadBots();
                            loadBotStats();
                        } else {
                            Swal.fire('Error!', 'Failed to delete bot: ' + response.message, 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error!', 'Response parse error', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error!', 'Ajax error: ' + error, 'error');
                }
            });
        }
    });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>