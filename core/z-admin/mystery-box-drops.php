<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="page-title">Mystery Box Drops</h4>
                            <p class="page-title-desc">View and manage mystery box drop history</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-warning" onclick="simulateDropForUser()">
                                <i class="fas fa-gift me-1"></i>Simulate Drop
                            </button>
                            <button type="button" class="btn btn-info" onclick="loadDrops()">
                                <i class="fas fa-refresh me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4 id="totalDrops">0</h4>
                            <small>Total Drops</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4 id="claimedDrops">0</h4>
                            <small>Claimed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4 id="unclaimedDrops">0</h4>
                            <small>Unclaimed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4 id="todayDrops">0</h4>
                            <small>Today</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Drop History</h5>
                            <div class="d-flex gap-2">
                                <select class="form-select" id="statusFilter" style="width: 150px;">
                                    <option value="">All Status</option>
                                    <option value="0">Unclaimed</option>
                                    <option value="1">Claimed</option>
                                </select>
                                <select class="form-select" id="rarityFilter" style="width: 150px;">
                                    <option value="">All Rarity</option>
                                    <option value="common">Common</option>
                                    <option value="rare">Rare</option>
                                    <option value="epic">Epic</option>
                                    <option value="legendary">Legendary</option>
                                </select>
                                <input type="text" class="form-control" id="userFilter" placeholder="User ID" style="width: 150px;">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Box</th>
                                            <th>Reward</th>
                                            <th>Rarity</th>
                                            <th>Status</th>
                                            <th>Drop Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dropsTableBody">
                                        <tr><td colspan="8" class="text-center"><div class="spinner-border"></div></td></tr>
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

<!-- Simulate Drop Modal -->
<div class="modal fade" id="simulateDropModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Simulate Drop for User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="simulateDropForm">
                    <div class="mb-3">
                        <label class="form-label">User ID or Email</label>
                        <input type="text" class="form-control" id="simulateUserId" placeholder="Enter user ID or email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mystery Box</label>
                        <select class="form-select" id="simulateBoxId">
                            <option value="">Random Box</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="executeSimulateDrop()">Simulate Drop</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadDrops();
    loadDropStats();
    loadBoxesForSimulate();
    
    $('#statusFilter, #rarityFilter, #userFilter').on('change keyup', function() {
        loadDrops();
    });
});

function loadDrops() {
    const filters = {
        action: 'getDrops',
        status: $('#statusFilter').val(),
        rarity: $('#rarityFilter').val(),
        userId: $('#userFilter').val()
    };
    
    $.ajax({
        url: 'ajax/mystery_box_drops_admin.php',
        method: 'POST',
        data: filters,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayDrops(response.data);
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function loadDropStats() {
    $.ajax({
        url: 'ajax/mystery_box_drops_admin.php',
        method: 'POST',
        data: { action: 'getDropStats' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayDropStats(response.data);
            }
        }
    });
}

function loadBoxesForSimulate() {
    $.ajax({
        url: 'ajax/mystery_boxes_admin.php',
        method: 'POST',
        data: { action: 'getBoxes' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let options = '<option value="">Random Box</option>';
                response.data.forEach(box => {
                    if (box.is_active == 1) {
                        options += `<option value="${box.id}">${box.box_name}</option>`;
                    }
                });
                $('#simulateBoxId').html(options);
            }
        }
    });
}

function displayDrops(drops) {
    let html = '';
    
    if (drops.length === 0) {
        html = '<tr><td colspan="8" class="text-center">No drops found</td></tr>';
    } else {
        drops.forEach(drop => {
            const statusBadge = drop.is_claimed == 1 ? 
                '<span class="badge bg-success">Claimed</span>' : 
                '<span class="badge bg-warning">Unclaimed</span>';
            
            const rarityBadge = getRarityBadge(drop.rarity);
            
            const rewardText = drop.reward_type === 'usd' ? 
                `$${drop.reward_usd_value}` : 
                `${drop.reward_amount} ${drop.coin_symbol}`;
            
            const claimDate = drop.claimed_at ? 
                new Date(drop.claimed_at).toLocaleString() : '-';
            
            html += `
                <tr>
                    <td>${drop.id}</td>
                    <td>
                        <strong>ID: ${drop.user_id}</strong><br>
                        <small>${drop.user_email}</small>
                    </td>
                    <td>${drop.box_name}</td>
                    <td>${rewardText}</td>
                    <td>${rarityBadge}</td>
                    <td>${statusBadge}</td>
                    <td>
                        ${new Date(drop.dropped_at).toLocaleString()}<br>
                        <small class="text-muted">Claimed: ${claimDate}</small>
                    </td>
                    <td>
                        ${drop.is_claimed == 0 ? 
                            `<button class="btn btn-sm btn-danger" onclick="cancelDrop(${drop.id})">
                                <i class="fas fa-times"></i>
                            </button>` : 
                            '<span class="text-muted">-</span>'
                        }
                    </td>
                </tr>
            `;
        });
    }
    
    $('#dropsTableBody').html(html);
}

function displayDropStats(stats) {
    $('#totalDrops').text(stats.total_drops);
    $('#claimedDrops').text(stats.claimed_drops);
    $('#unclaimedDrops').text(stats.unclaimed_drops);
    $('#todayDrops').text(stats.today_drops);
}

function getRarityBadge(rarity) {
    const rarityColors = {
        'common': 'secondary',
        'rare': 'primary',
        'epic': 'info',
        'legendary': 'warning'
    };
    
    return `<span class="badge bg-${rarityColors[rarity]}">${rarity.toUpperCase()}</span>`;
}

function simulateDropForUser() {
    $('#simulateDropModal').modal('show');
}

function executeSimulateDrop() {
    const userId = $('#simulateUserId').val();
    const boxId = $('#simulateBoxId').val();
    
    if (!userId) {
        Swal.fire("Error!", "Please enter user ID or email", "error");
        return;
    }
    
    $.ajax({
        url: 'ajax/mystery_box_drops_admin.php',
        method: 'POST',
        data: { 
            action: 'simulateDrop',
            userId: userId,
            boxId: boxId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire("Success!", response.message, "success");
                $('#simulateDropModal').modal('hide');
                $('#simulateUserId').val('');
                loadDrops();
                loadDropStats();
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function cancelDrop(dropId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will cancel the unclaimed drop!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/mystery_box_drops_admin.php',
                method: 'POST',
                data: { action: 'cancelDrop', dropId: dropId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Cancelled!", response.message, "success");
                        loadDrops();
                        loadDropStats();
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }
            });
        }
    });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>