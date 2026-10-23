<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="page-title">Promo Codes Management</h4>
                            <p class="page-title-desc">Manage promo codes and view user redemption history</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromoModal">
                            <i class="fas fa-plus me-1"></i>Create Promo Code
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4 id="totalPromos">0</h4>
                            <small>Total Codes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4 id="activePromos">0</h4>
                            <small>Active Codes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4 id="totalRedemptions">0</h4>
                            <small>Total Redemptions</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4 id="totalRewardValue">$0</h4>
                            <small>Total Rewards Given</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="promoTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="codes-tab" data-bs-toggle="tab" data-bs-target="#codes" type="button" role="tab">
                        <i class="fas fa-tags me-1"></i>Promo Codes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="usage-tab" data-bs-toggle="tab" data-bs-target="#usage" type="button" role="tab">
                        <i class="fas fa-history me-1"></i>Usage History
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="promoTabContent">
                
                <!-- Promo Codes Tab -->
                <div class="tab-pane fade show active" id="codes" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Promo Codes</h5>
                            <div class="d-flex gap-2">
                                <select class="form-select" id="codeStatusFilter" style="width: 150px;">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <select class="form-select" id="codeTypeFilter" style="width: 150px;">
                                    <option value="">All Types</option>
                                    <option value="usd">USD</option>
                                    <option value="coin">Coin</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Type</th>
                                            <th>Reward</th>
                                            <th>Usage</th>
                                            <th>Status</th>
                                            <th>Expires</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="codesTableBody">
                                        <tr><td colspan="7" class="text-center"><div class="spinner-border"></div></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage History Tab -->
                <div class="tab-pane fade" id="usage" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Usage History</h5>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control" id="userIdFilter" placeholder="User ID" style="width: 150px;">
                                <input type="text" class="form-control" id="promoCodeFilter" placeholder="Promo Code" style="width: 150px;">
                                <select class="form-select" id="rewardTypeFilter" style="width: 150px;">
                                    <option value="">All Types</option>
                                    <option value="usd">USD</option>
                                    <option value="coin">Coin</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Code</th>
                                            <th>Reward</th>
                                            <th>Used Date</th>
                                            <th>IP Address</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usageTableBody">
                                        <tr><td colspan="7" class="text-center"><div class="spinner-border"></div></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Add/Edit Promo Code Modal -->
<div class="modal fade" id="addPromoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Promo Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="promoForm">
                    <input type="hidden" id="promoId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Promo Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="promoCode" required maxlength="50" style="text-transform: uppercase;">
                                <small class="text-muted">Code will be automatically converted to uppercase</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Reward Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="promoType" required>
                                    <option value="">Select Type</option>
                                    <option value="usd">USD Reward</option>
                                    <option value="coin">Coin Reward</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" id="usdRewardSection" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">USD Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="usdAmount">
                            </div>
                        </div>
                        <div class="col-md-6" id="coinRewardSection" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Coin Symbol <span class="text-danger">*</span></label>
                                <select class="form-select" id="coinSymbol">
                                    <option value="">Select Coin</option>
                                    <option value="BTC">Bitcoin (BTC)</option>
                                    <option value="ETH">Ethereum (ETH)</option>
                                    <option value="BNB">Binance Coin (BNB)</option>
                                    <option value="ADA">Cardano (ADA)</option>
                                    <option value="SOL">Solana (SOL)</option>
                                    <option value="DOT">Polkadot (DOT)</option>
                                    <option value="MATIC">Polygon (MATIC)</option>
                                    <option value="AVAX">Avalanche (AVAX)</option>
                                    <option value="LINK">Chainlink (LINK)</option>
                                    <option value="UNI">Uniswap (UNI)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="coinAmountSection" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Coin Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.00000001" min="0.00000001" class="form-control" id="coinAmount">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">USD Value (for reference)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="coinUsdValue">
                                <small class="text-muted">Current market value in USD</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Max Usage</label>
                                <input type="number" min="0" class="form-control" id="maxUsage" value="0">
                                <small class="text-muted">0 = unlimited usage</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Expiration Date</label>
                                <input type="datetime-local" class="form-control" id="expiresAt">
                                <small class="text-muted">Leave empty for no expiration</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="promoDescription" rows="3" placeholder="Optional description for the promo code"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isActive" checked>
                            <label class="form-check-label" for="isActive">
                                Active (users can redeem this code)
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePromoCode()">Save Promo Code</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadPromoStats();
    loadPromoCodes();
    
    $('#promoTabs button').on('click', function() {
        const target = $(this).data('bs-target');
        if (target === '#usage') {
            loadUsageHistory();
        }
    });
    
    $('#codeStatusFilter, #codeTypeFilter').on('change', loadPromoCodes);
    $('#userIdFilter, #promoCodeFilter, #rewardTypeFilter').on('keyup change', debounce(loadUsageHistory, 500));
    
    $('#promoType').on('change', function() {
        const type = $(this).val();
        if (type === 'usd') {
            $('#usdRewardSection').show();
            $('#coinRewardSection, #coinAmountSection').hide();
        } else if (type === 'coin') {
            $('#coinRewardSection, #coinAmountSection').show();
            $('#usdRewardSection').hide();
        } else {
            $('#usdRewardSection, #coinRewardSection, #coinAmountSection').hide();
        }
    });
    
    $('#promoCode').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    $('#addPromoModal').on('hidden.bs.modal', function() {
        resetForm();
    });
    
    $('#addPromoModal').on('show.bs.modal', function() {
        if (!$('#promoId').val()) {
            resetForm();
        }
    });
});

function loadPromoStats() {
    $.ajax({
        url: 'ajax/promocodes_admin.php',
        method: 'POST',
        data: { action: 'getStats' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayStats(response.data);
            }
        }
    });
}

function displayStats(stats) {
    $('#totalPromos').text(stats.total_codes);
    $('#activePromos').text(stats.active_codes);
    $('#totalRedemptions').text(stats.total_redemptions);
    $('#totalRewardValue').text('$' + parseFloat(stats.total_reward_value || 0).toFixed(2));
}

function loadPromoCodes() {
    const filters = {
        action: 'getPromoCodes',
        status: $('#codeStatusFilter').val(),
        type: $('#codeTypeFilter').val()
    };
    
    $.ajax({
        url: 'ajax/promocodes_admin.php',
        method: 'POST',
        data: filters,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayPromoCodes(response.data);
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function displayPromoCodes(codes) {
    let html = '';
    
    if (codes.length === 0) {
        html = '<tr><td colspan="7" class="text-center">No promo codes found</td></tr>';
    } else {
        codes.forEach(code => {
            const statusBadge = code.is_active == 1 ? 
                '<span class="badge bg-success">Active</span>' : 
                '<span class="badge bg-secondary">Inactive</span>';
            
            const typeBadge = code.code_type === 'usd' ? 
                '<span class="badge bg-primary">USD</span>' : 
                '<span class="badge bg-info">Coin</span>';
            
            const rewardText = code.code_type === 'usd' ? 
                `$${code.reward_usd_value}` : 
                `${code.reward_amount} ${code.coin_id}`;
            
            const usageText = code.max_usage > 0 ? 
                `${code.current_usage}/${code.max_usage}` : 
                `${code.current_usage}/∞`;
            
            const expiresText = code.expires_at ? 
                new Date(code.expires_at).toLocaleDateString() : 'Never';
            
            html += `
                <tr>
                    <td>
                        <strong>${code.code}</strong><br>
                        <small class="text-muted">${code.description || 'No description'}</small>
                    </td>
                    <td>${typeBadge}</td>
                    <td>${rewardText}</td>
                    <td>${usageText}</td>
                    <td>${statusBadge}</td>
                    <td>${expiresText}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editPromoCode(${code.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deletePromoCode(${code.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#codesTableBody').html(html);
}

function loadUsageHistory() {
    const filters = {
        action: 'getUsageHistory',
        userId: $('#userIdFilter').val(),
        promoCode: $('#promoCodeFilter').val(),
        rewardType: $('#rewardTypeFilter').val()
    };
    
    $.ajax({
        url: 'ajax/promocodes_admin.php',
        method: 'POST',
        data: filters,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayUsageHistory(response.data);
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function displayUsageHistory(usage) {
    let html = '';
    
    if (usage.length === 0) {
        html = '<tr><td colspan="7" class="text-center">No usage history found</td></tr>';
    } else {
        usage.forEach(item => {
            const rewardText = item.reward_type === 'usd' ? 
                `$${item.reward_usd_value}` : 
                `${item.reward_amount} ${item.coin_id}`;
            
            html += `
                <tr>
                    <td>${item.id}</td>
                    <td>
                        <strong>ID: ${item.user_id}</strong><br>
                        <small class="text-muted">${item.user_email || 'N/A'}</small>
                    </td>
                    <td><strong>${item.promo_code}</strong></td>
                    <td>${rewardText}</td>
                    <td>${new Date(item.used_at).toLocaleString()}</td>
                    <td><small>${item.ip_address}</small></td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="deleteUsage(${item.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#usageTableBody').html(html);
}

function editPromoCode(id) {
    $.ajax({
        url: 'ajax/promocodes_admin.php',
        method: 'POST',
        data: { action: 'getPromoCode', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const promo = response.data;
                $('#promoId').val(promo.id);
                $('#promoCode').val(promo.code);
                $('#promoType').val(promo.code_type).trigger('change');
                $('#maxUsage').val(promo.max_usage);
                $('#promoDescription').val(promo.description);
                $('#isActive').prop('checked', promo.is_active == 1);
                
                if (promo.expires_at) {
                    const date = new Date(promo.expires_at);
                    $('#expiresAt').val(date.toISOString().slice(0, 16));
                }
                
                if (promo.code_type === 'usd') {
                    $('#usdAmount').val(promo.reward_usd_value);
                } else {
                    $('#coinSymbol').val(promo.coin_id);
                    $('#coinAmount').val(promo.reward_amount);
                    $('#coinUsdValue').val(promo.reward_usd_value);
                }
                
                $('.modal-title').text('Edit Promo Code');
                $('#addPromoModal').modal('show');
            }
        }
    });
}

function savePromoCode() {
    const data = {
        action: $('#promoId').val() ? 'updatePromoCode' : 'createPromoCode',
        id: $('#promoId').val(),
        code: $('#promoCode').val(),
        code_type: $('#promoType').val(),
        max_usage: $('#maxUsage').val(),
        description: $('#promoDescription').val(),
        is_active: $('#isActive').is(':checked') ? 1 : 0,
        expires_at: $('#expiresAt').val()
    };
    
    if (data.code_type === 'usd') {
        data.reward_usd_value = $('#usdAmount').val();
        data.reward_amount = 0;
        data.coin_id = null;
    } else {
        data.coin_id = $('#coinSymbol').val();
        data.reward_amount = $('#coinAmount').val();
        data.reward_usd_value = $('#coinUsdValue').val() || 0;
    }
    
    $.ajax({
        url: 'ajax/promocodes_admin.php',
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire("Success!", response.message, "success");
                $('#addPromoModal').modal('hide');
                resetForm();
                loadPromoCodes();
                loadPromoStats();
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function deletePromoCode(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will delete the promo code and all its usage history!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/promocodes_admin.php',
                method: 'POST',
                data: { action: 'deletePromoCode', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Deleted!", response.message, "success");
                        loadPromoCodes();
                        loadPromoStats();
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }
            });
        }
    });
}

function deleteUsage(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will delete the usage record but won\'t reverse the reward!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/promocodes_admin.php',
                method: 'POST',
                data: { action: 'deleteUsage', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Deleted!", response.message, "success");
                        loadUsageHistory();
                        loadPromoStats();
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }
            });
        }
    });
}

function resetForm() {
    $('#promoForm')[0].reset();
    $('#promoId').val('');
    $('#promoCode').val('');
    $('#promoType').val('');
    $('#usdAmount').val('');
    $('#coinSymbol').val('');
    $('#coinAmount').val('');
    $('#coinUsdValue').val('');
    $('#maxUsage').val('0');
    $('#promoDescription').val('');
    $('#expiresAt').val('');
    $('#isActive').prop('checked', true);
    $('.modal-title').text('Create Promo Code');
    $('#usdRewardSection, #coinRewardSection, #coinAmountSection').hide();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>