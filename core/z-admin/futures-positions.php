<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="page-title">Futures Positions Management</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalPositions">0</h5>
                            <p class="mb-0 text-white-50">Total Positions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-play text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="openPositions">0</h5>
                            <p class="mb-0 text-white-50">Open Positions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-dollar-sign text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalMargin">$0</h5>
                            <p class="mb-0 text-white-50">Total Margin</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-area text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalPnL">$0</h5>
                            <p class="mb-0 text-white-50">Total PnL</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title">Futures Positions</h4>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="statusFilter" onchange="loadPositions()">
                            <option value="">All Status</option>
                            <option value="OPEN">Open</option>
                            <option value="CLOSED">Closed</option>
                            <option value="LIQUIDATED">Liquidated</option>
                        </select>
                        <select class="form-select form-select-sm" id="typeFilter" onchange="loadPositions()">
                            <option value="">All Types</option>
                            <option value="LONG">Long</option>
                            <option value="SHORT">Short</option>
                        </select>
                        <button class="btn btn-outline-secondary btn-sm" onclick="refreshData()">
                            <i class="fas fa-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>User</th>
                            <th>Coin</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Entry Price</th>
                            <th>Current Price</th>
                            <th>Margin</th>
                            <th>Leverage</th>
                            <th>PnL</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="positionsTableBody">
                        <tr>
                            <td colspan="12" class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Position Details Modal -->
<div class="modal fade" id="positionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="positionModalLabel">Position Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="positionContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadPositionStats();
    loadPositions();
    
    setInterval(function() {
        loadPositionStats();
        if ($('#statusFilter').val() === 'OPEN' || $('#statusFilter').val() === '') {
            loadPositions();
        }
    }, 30000);
});

let allPositions = [];

function loadPositionStats() {
    $.ajax({
        url: 'ajax/futures-positions-ajax.php',
        type: 'POST',
        data: {action: 'getPositionStats18289'},
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.success) {
                    $('#totalPositions').text(response.stats.total_positions);
                    $('#openPositions').text(response.stats.open_positions);
                    $('#totalMargin').text('$' + parseFloat(response.stats.total_margin).toFixed(2));
                    $('#totalPnL').text('$' + parseFloat(response.stats.total_pnl).toFixed(2));
                }
            } catch(e) {
                console.log('Stats parse error:', e);
            }
        }
    });
}

function loadPositions() {
    const status = $('#statusFilter').val();
    const type = $('#typeFilter').val();
    
    $.ajax({
        url: 'ajax/futures-positions-ajax.php',
        type: 'POST',
        data: {
            action: 'getPositions18289',
            status: status,
            type: type
        },
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.success) {
                    allPositions = response.positions;
                    displayPositionsTable(allPositions);
                } else {
                    $('#positionsTableBody').html('<tr><td colspan="12" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
                }
            } catch(e) {
                $('#positionsTableBody').html('<tr><td colspan="12" class="text-center text-danger">Parse error</td></tr>');
            }
        }
    });
}

function displayPositionsTable(positions) {
    let html = '';
    
    if (positions.length === 0) {
        html = '<tr><td colspan="12" class="text-center text-muted">No positions found</td></tr>';
    } else {
        positions.forEach(function(pos) {
            const statusColors = {
                'OPEN': 'success',
                'CLOSED': 'info',
                'LIQUIDATED': 'danger'
            };
            
            const typeColors = {
                'LONG': 'success',
                'SHORT': 'danger'
            };
            
            const pnlColor = parseFloat(pos.unrealized_pnl) >= 0 ? 'success' : 'danger';
            const pnlIcon = parseFloat(pos.unrealized_pnl) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            
            html += `
                <tr>
                    <td>
                        <div>
                            <div class="fw-medium">${pos.user_email || 'User #' + pos.user_id}</div>
                            <small class="text-muted">ID: ${pos.user_id}</small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${pos.coin_image ? 
                                `<img src="${pos.coin_image}" alt="${pos.coin_name || pos.coin_id}" width="24" height="24" class="rounded me-2">` :
                                `<i class="fas fa-coins text-primary me-2"></i>`
                            }
                            <div>
                                <div class="fw-medium">${(pos.coin_name || pos.coin_id).toUpperCase()}</div>
                                <small class="text-muted">${pos.coin_symbol || pos.coin_id}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-${typeColors[pos.position_type]}">${pos.position_type}</span>
                    </td>
                    <td>
                        <span class="fw-bold">${parseFloat(pos.position_size).toFixed(6)}</span>
                    </td>
                    <td>
                        <span class="text-info fw-bold">$${parseFloat(pos.entry_price).toFixed(2)}</span>
                    </td>
                    <td>
                        <span class="text-warning fw-bold">$${parseFloat(pos.current_price).toFixed(2)}</span>
                    </td>
                    <td>
                        <span class="text-primary fw-bold">$${parseFloat(pos.margin_amount).toFixed(2)}</span>
                    </td>
                    <td>
                        <span class="badge bg-secondary">${parseFloat(pos.leverage).toFixed(1)}x</span>
                    </td>
                    <td>
                        <span class="text-${pnlColor} fw-bold">
                            <i class="fas ${pnlIcon}"></i>
                            $${parseFloat(pos.unrealized_pnl).toFixed(2)}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-${statusColors[pos.status]}">${pos.status}</span>
                    </td>
                    <td>
                        <div>
                            <small class="text-muted">Opened: ${formatDateTime(pos.opened_at)}</small>
                            ${pos.closed_at ? `<br><small class="text-muted">Closed: ${formatDateTime(pos.closed_at)}</small>` : ''}
                        </div>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" onclick="viewPosition(${pos.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${pos.status === 'OPEN' ? 
                                `<button class="btn btn-outline-warning" onclick="closePosition(${pos.id})" title="Close Position">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="liquidatePosition(${pos.id})" title="Liquidate">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </button>` : ''
                            }
                            <button class="btn btn-outline-danger" onclick="deletePosition(${pos.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#positionsTableBody').html(html);
}

function viewPosition(positionId) {
    const position = allPositions.find(p => p.id == positionId);
    if (!position) return;
    
    $('#positionModalLabel').text(`Position Details - ${(position.coin_name || position.coin_id).toUpperCase()}`);
    $('#positionModal').modal('show');
    
    const pnlColor = parseFloat(position.unrealized_pnl) >= 0 ? 'success' : 'danger';
    const pnlIcon = parseFloat(position.unrealized_pnl) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
    
    let html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Position Info</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">User</label>
                            <div class="fw-bold">${position.user_email || 'User #' + position.user_id}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Coin</label>
                            <div class="fw-bold">${(position.coin_name || position.coin_id).toUpperCase()}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Position Type</label>
                            <div><span class="badge bg-${position.position_type === 'LONG' ? 'success' : 'danger'}">${position.position_type}</span></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Position Size</label>
                            <div class="fw-bold">${parseFloat(position.position_size).toFixed(8)}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Leverage</label>
                            <div><span class="badge bg-secondary">${parseFloat(position.leverage).toFixed(1)}x</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Financial Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Entry Price</label>
                            <div class="fw-bold text-info">$${parseFloat(position.entry_price).toFixed(2)}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Current Price</label>
                            <div class="fw-bold text-warning">$${parseFloat(position.current_price).toFixed(2)}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Margin Amount</label>
                            <div class="fw-bold text-primary">$${parseFloat(position.margin_amount).toFixed(2)}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Unrealized PnL</label>
                            <div class="fw-bold text-${pnlColor}">
                                <i class="fas ${pnlIcon}"></i>
                                $${parseFloat(position.unrealized_pnl).toFixed(2)}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Funding Fee</label>
                            <div class="fw-bold">$${parseFloat(position.funding_fee || 0).toFixed(4)}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Risk Management</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Liquidation Price</label>
                                    <div class="fw-bold ${position.liquidation_price ? 'text-danger' : 'text-muted'}">
                                        ${position.liquidation_price ? '$' + parseFloat(position.liquidation_price).toFixed(2) : 'Not Set'}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Stop Loss</label>
                                    <div class="fw-bold ${position.stop_loss ? 'text-warning' : 'text-muted'}">
                                        ${position.stop_loss ? '$' + parseFloat(position.stop_loss).toFixed(2) : 'Not Set'}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Take Profit</label>
                                    <div class="fw-bold ${position.take_profit ? 'text-success' : 'text-muted'}">
                                        ${position.take_profit ? '$' + parseFloat(position.take_profit).toFixed(2) : 'Not Set'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Timeline</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Status</label>
                                    <div><span class="badge bg-info">${position.status}</span></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Opened At</label>
                                    <div class="fw-bold">${formatDateTime(position.opened_at)}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Closed At</label>
                                    <div class="fw-bold">${position.closed_at ? formatDateTime(position.closed_at) : 'Still Open'}</div>
                                </div>
                            </div>
                        </div>
                        ${position.notes ? `
                            <div class="mb-3">
                                <label class="form-label text-muted">Notes</label>
                                <div class="fw-bold">${position.notes}</div>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#positionContent').html(html);
}

function closePosition(positionId) {
    Swal.fire({
        title: 'Close Position?',
        text: 'This will close the open position at current market price',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, close it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/futures-positions-ajax.php',
                type: 'POST',
                data: {
                    action: 'closePosition18289',
                    position_id: positionId
                },
                success: function(data) {
                    try {
                        const response = JSON.parse(data);
                        if (response.success) {
                            Swal.fire('Closed!', 'Position closed successfully!', 'success');
                            loadPositions();
                            loadPositionStats();
                        } else {
                            Swal.fire('Error!', 'Failed to close position: ' + response.message, 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error!', 'Response parse error', 'error');
                    }
                }
            });
        }
    });
}

function liquidatePosition(positionId) {
    Swal.fire({
        title: 'Liquidate Position?',
        text: 'This will liquidate the position - this action cannot be undone!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, liquidate!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/futures-positions-ajax.php',
                type: 'POST',
                data: {
                    action: 'liquidatePosition18289',
                    position_id: positionId
                },
                success: function(data) {
                    try {
                        const response = JSON.parse(data);
                        if (response.success) {
                            Swal.fire('Liquidated!', 'Position liquidated successfully!', 'success');
                            loadPositions();
                            loadPositionStats();
                        } else {
                            Swal.fire('Error!', 'Failed to liquidate position: ' + response.message, 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error!', 'Response parse error', 'error');
                    }
                }
            });
        }
    });
}

function deletePosition(positionId) {
    Swal.fire({
        title: 'Delete Position?',
        text: 'This will permanently delete the position record!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/futures-positions-ajax.php',
                type: 'POST',
                data: {
                    action: 'deletePosition18289',
                    position_id: positionId
                },
                success: function(data) {
                    try {
                        const response = JSON.parse(data);
                        if (response.success) {
                            Swal.fire('Deleted!', 'Position deleted successfully!', 'success');
                            loadPositions();
                            loadPositionStats();
                        } else {
                            Swal.fire('Error!', 'Failed to delete position: ' + response.message, 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error!', 'Response parse error', 'error');
                    }
                }
            });
        }
    });
}

function refreshData() {
    loadPositionStats();
    loadPositions();
    Swal.fire({
        title: 'Refreshed!',
        text: 'Data has been refreshed',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>