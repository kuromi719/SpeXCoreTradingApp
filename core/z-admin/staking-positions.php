<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="page-title">Staking Positions Management</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-coins text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalStakings">0</h5>
                            <p class="mb-0 text-white-50">Total Stakings</p>
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
                            <h5 class="mb-1 text-white" id="activeStakings">0</h5>
                            <p class="mb-0 text-white-50">Active Stakings</p>
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
                            <h5 class="mb-1 text-white" id="totalStakeValue">$0</h5>
                            <p class="mb-0 text-white-50">Total Staked Value</p>
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
                            <i class="fas fa-chart-line text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalRewards">$0</h5>
                            <p class="mb-0 text-white-50">Total Rewards</p>
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
                    <h4 class="card-title">Staking Positions</h4>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="statusFilter" onchange="loadStakings()">
                            <option value="">All Status</option>
                            <option value="ACTIVE">Active</option>
                            <option value="COMPLETED">Completed</option>
                            <option value="CANCELLED">Cancelled</option>
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
                            <th>Staked Amount</th>
                            <th>Stake Value</th>
                            <th>APY</th>
                            <th>Rewards Earned</th>
                            <th>Rewards Claimed</th>
                            <th>Lock Period</th>
                            <th>Status</th>
                            <th>Dates</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="stakingsTableBody">
                        <tr>
                            <td colspan="11" class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Staking Details Modal -->
<div class="modal fade" id="stakingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stakingModalLabel">Staking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="stakingContent">
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

<!-- Claim Rewards Modal -->
<div class="modal fade" id="claimModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Claim Rewards</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-gift text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Claim Staking Rewards</h5>
                    <p class="text-muted">Are you sure you want to claim available rewards?</p>
                    <div class="alert alert-info">
                        <strong>Available Rewards:</strong> <span id="availableRewards">0</span> tokens
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmClaimBtn">Claim Rewards</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadStakingStats();
    loadStakings();
    
    setInterval(function() {
        loadStakingStats();
        if ($('#statusFilter').val() === 'ACTIVE' || $('#statusFilter').val() === '') {
            loadStakings();
        }
    }, 30000);
});

let allStakings = [];

function loadStakingStats() {
    $.ajax({
        url: 'ajax/staking-positions-ajax.php',
        type: 'POST',
        data: {action: 'getStakingStats18289'},
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.success) {
                    $('#totalStakings').text(response.stats.total_stakings);
                    $('#activeStakings').text(response.stats.active_stakings);
                    $('#totalStakeValue').text('$' + parseFloat(response.stats.total_stake_value).toFixed(2));
                    $('#totalRewards').text(parseFloat(response.stats.total_rewards).toFixed(6));
                }
            } catch(e) {
                console.log('Stats parse error:', e);
            }
        }
    });
}

function loadStakings() {
    const status = $('#statusFilter').val();
    
    $.ajax({
        url: 'ajax/staking-positions-ajax.php',
        type: 'POST',
        data: {
            action: 'getStakings18289',
            status: status
        },
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.success) {
                    allStakings = response.stakings;
                    displayStakingsTable(allStakings);
                } else {
                    $('#stakingsTableBody').html('<tr><td colspan="11" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
                }
            } catch(e) {
                $('#stakingsTableBody').html('<tr><td colspan="11" class="text-center text-danger">Parse error</td></tr>');
            }
        }
    });
}

function displayStakingsTable(stakings) {
    let html = '';
    
    if (stakings.length === 0) {
        html = '<tr><td colspan="11" class="text-center text-muted">No stakings found</td></tr>';
    } else {
        stakings.forEach(function(stake) {
            const statusColors = {
                'ACTIVE': 'success',
                'COMPLETED': 'info',
                'CANCELLED': 'danger'
            };
            
            const lockPeriodText = stake.lock_period_days > 0 ? stake.lock_period_days + ' days' : 'Flexible';
            const unclaimedRewards = parseFloat(stake.rewards_earned) - parseFloat(stake.rewards_claimed);
            
            const now = new Date();
            const endDate = stake.stake_end_date ? new Date(stake.stake_end_date) : null;
            const isExpired = endDate && now > endDate;
            
            html += `
                <tr>
                    <td>
                        <div>
                            <div class="fw-medium">${stake.user_email || 'User #' + stake.user_id}</div>
                            <small class="text-muted">ID: ${stake.user_id}</small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${stake.coin_image ? 
                                `<img src="${stake.coin_image}" alt="${stake.coin_name || stake.coin_id}" width="24" height="24" class="rounded me-2">` :
                                `<i class="fas fa-coins text-primary me-2"></i>`
                            }
                            <div>
                                <div class="fw-medium">${(stake.coin_name || stake.coin_id).toUpperCase()}</div>
                                <small class="text-muted">${stake.coin_symbol || stake.coin_id}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="fw-bold text-primary">${parseFloat(stake.staked_amount).toFixed(6)}</span>
                    </td>
                    <td>
                        <span class="text-success fw-bold">$${parseFloat(stake.stake_value_usd).toFixed(2)}</span>
                    </td>
                    <td>
                        <span class="badge bg-warning text-dark">${parseFloat(stake.apy_percentage).toFixed(2)}%</span>
                    </td>
                    <td>
                        <span class="text-info fw-bold">${parseFloat(stake.rewards_earned).toFixed(6)}</span>
                    </td>
                    <td>
                        <span class="text-secondary fw-bold">${parseFloat(stake.rewards_claimed).toFixed(6)}</span>
                        ${unclaimedRewards > 0 ? `<br><small class="text-success">+${unclaimedRewards.toFixed(6)} unclaimed</small>` : ''}
                    </td>
                    <td>
                        <span class="badge bg-${stake.lock_period_days > 0 ? 'danger' : 'success'}">${lockPeriodText}</span>
                        ${isExpired && stake.status === 'ACTIVE' ? '<br><small class="text-warning">Expired</small>' : ''}
                    </td>
                    <td>
                        <span class="badge bg-${statusColors[stake.status]}">${stake.status}</span>
                    </td>
                    <td>
                        <div>
                            <small class="text-muted">Start: ${formatDateTime(stake.stake_start_date)}</small>
                            ${stake.stake_end_date ? `<br><small class="text-muted">End: ${formatDateTime(stake.stake_end_date)}</small>` : '<br><small class="text-success">No End Date</small>'}
                            ${stake.last_reward_date ? `<br><small class="text-info">Last Reward: ${formatDateTime(stake.last_reward_date)}</small>` : ''}
                        </div>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" onclick="viewStaking(${stake.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${stake.status === 'ACTIVE' && unclaimedRewards > 0 ? 
                                `<button class="btn btn-outline-success" onclick="claimRewards(${stake.id}, ${unclaimedRewards})" title="Claim Rewards">
                                    <i class="fas fa-gift"></i>
                                </button>` : ''
                            }
                            ${stake.status === 'ACTIVE' ? 
                                `<button class="btn btn-outline-warning" onclick="unstakePosition(${stake.id})" title="Unstake">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>` : ''
                            }
                            <button class="btn btn-outline-danger" onclick="deleteStaking(${stake.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#stakingsTableBody').html(html);
}

function viewStaking(stakingId) {
    const staking = allStakings.find(s => s.id == stakingId);
    if (!staking) return;
    
    $('#stakingModalLabel').text(`Staking Details - ${(staking.coin_name || staking.coin_id).toUpperCase()}`);
    $('#stakingModal').modal('show');
    
    const unclaimedRewards = parseFloat(staking.rewards_earned) - parseFloat(staking.rewards_claimed);
    const lockPeriodText = staking.lock_period_days > 0 ? staking.lock_period_days + ' days' : 'Flexible';
    
    let html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Staking Info</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">User</label>
                            <div class="fw-bold">${staking.user_email || 'User #' + staking.user_id}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Coin</label>
                            <div class="fw-bold">${(staking.coin_name || staking.coin_id).toUpperCase()}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Staked Amount</label>
                            <div class="fw-bold text-primary">${parseFloat(staking.staked_amount).toFixed(8)}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Stake Value (USD)</label>
                            <div class="fw-bold text-success">$${parseFloat(staking.stake_value_usd).toFixed(2)}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">APY Percentage</label>
                            <div><span class="badge bg-warning text-dark">${parseFloat(staking.apy_percentage).toFixed(2)}%</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Rewards & Lock</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Total Rewards Earned</label>
                            <div class="fw-bold text-info">${parseFloat(staking.rewards_earned).toFixed(8)}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Rewards Claimed</label>
                            <div class="fw-bold text-secondary">${parseFloat(staking.rewards_claimed).toFixed(8)}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Unclaimed Rewards</label>
                            <div class="fw-bold text-success">${unclaimedRewards.toFixed(8)}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Lock Period</label>
                            <div><span class="badge bg-${staking.lock_period_days > 0 ? 'danger' : 'success'}">${lockPeriodText}</span></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Status</label>
                            <div><span class="badge bg-info">${staking.status}</span></div>
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
                                    <label class="form-label text-muted">Stake Start Date</label>
                                    <div class="fw-bold">${formatDateTime(staking.stake_start_date)}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Stake End Date</label>
                                    <div class="fw-bold">${staking.stake_end_date ? formatDateTime(staking.stake_end_date) : 'No End Date'}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Last Reward Date</label>
                                    <div class="fw-bold">${staking.last_reward_date ? formatDateTime(staking.last_reward_date) : 'No Rewards Yet'}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Created At</label>
                            <div class="fw-bold">${formatDateTime(staking.created_at)}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#stakingContent').html(html);
}

function claimRewards(stakingId, amount) {
    $('#availableRewards').text(amount.toFixed(6));
    $('#claimModal').modal('show');
    
    $('#confirmClaimBtn').off('click').on('click', function() {
        $.ajax({
            url: 'ajax/staking-positions-ajax.php',
            type: 'POST',
            data: {
                action: 'claimRewards18289',
                staking_id: stakingId,
                amount: amount
            },
            success: function(data) {
                try {
                    const response = JSON.parse(data);
                    if (response.success) {
                        $('#claimModal').modal('hide');
                        Swal.fire('Success!', 'Rewards claimed successfully!', 'success');
                        loadStakings();
                        loadStakingStats();
                    } else {
                        Swal.fire('Error!', 'Failed to claim rewards: ' + response.message, 'error');
                    }
                } catch(e) {
                    Swal.fire('Error!', 'Response parse error', 'error');
                }
            }
        });
    });
}

function unstakePosition(stakingId) {
    Swal.fire({
        title: 'Unstake Position?',
        text: 'This will complete the staking position and claim all rewards',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, unstake!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/staking-positions-ajax.php',
                type: 'POST',
                data: {
                    action: 'unstakePosition18289',
                    staking_id: stakingId
                },
                success: function(data) {
                    try {
                        const response = JSON.parse(data);
                        if (response.success) {
                            Swal.fire('Success!', 'Position unstaked successfully!', 'success');
                            loadStakings();
                            loadStakingStats();
                        } else {
                            Swal.fire('Error!', 'Failed to unstake position: ' + response.message, 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error!', 'Response parse error', 'error');
                    }
                }
            });
        }
    });
}

function deleteStaking(stakingId) {
    Swal.fire({
        title: 'Delete Staking?',
        text: 'This will permanently delete the staking record!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/staking-positions-ajax.php',
                type: 'POST',
                data: {
                    action: 'deleteStaking18289',
                    staking_id: stakingId
                },
                success: function(data) {
                    try {
                        const response = JSON.parse(data);
                        if (response.success) {
                            Swal.fire('Deleted!', 'Staking deleted successfully!', 'success');
                            loadStakings();
                            loadStakingStats();
                        } else {
                            Swal.fire('Error!', 'Failed to delete staking: ' + response.message, 'error');
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
    loadStakingStats();
    loadStakings();
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