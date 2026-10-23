<?php require __DIR__ . '/inc/header.php'; ?>

    <div class="container-xxl">
            
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="page-title">Mystery Boxes</h4>
                            <p class="page-title-desc">Manage mystery boxes and their reward configurations</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoxModal">
                            <i class="fas fa-plus me-1"></i>Add New Box
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="boxesTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Box Name</th>
                                            <th>USD Range</th>
                                            <th>Rarity Distribution</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="boxesTableBody">
                                        <tr><td colspan="6" class="text-center"><div class="spinner-border"></div></td></tr>
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

<!-- Add/Edit Box Modal -->
<div class="modal fade" id="addBoxModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Mystery Box</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="boxForm">
                    <input type="hidden" id="boxId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Box Name</label>
                                <input type="text" class="form-control" id="boxName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="boxStatus">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="boxDescription" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Min USD Reward</label>
                                <input type="number" step="0.01" class="form-control" id="minUsdReward" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Max USD Reward</label>
                                <input type="number" step="0.01" class="form-control" id="maxUsdReward" required>
                            </div>
                        </div>
                    </div>

                    <h6>Rarity Distribution (%)</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Common</label>
                                <input type="number" step="0.01" max="100" class="form-control" id="commonChance" value="50">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Rare</label>
                                <input type="number" step="0.01" max="100" class="form-control" id="rareChance" value="30">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Epic</label>
                                <input type="number" step="0.01" max="100" class="form-control" id="epicChance" value="15">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Legendary</label>
                                <input type="number" step="0.01" max="100" class="form-control" id="legendaryChance" value="5">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Coin Rewards (JSON)</label>
                        <textarea class="form-control" id="coinRewards" rows="3" placeholder='{"BTC": 0.0001, "ETH": 0.01, "BNB": 0.1}'></textarea>
                        <small class="text-muted">JSON format: {"SYMBOL": amount}</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBox()">Save Box</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadBoxes();

    $('#addBoxModal').on('hidden.bs.modal', function() {
        resetForm();
    });
    
    $('#addBoxModal').on('show.bs.modal', function() {
        if (!$('#boxId').val()) {
            resetForm();
        }
    });
});

function loadBoxes() {
    $.ajax({
        url: 'ajax/mystery_boxes_admin.php',
        method: 'POST',
        data: { action: 'getBoxes' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayBoxes(response.data);
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function displayBoxes(boxes) {
    let html = '';
    boxes.forEach(box => {
        const statusBadge = box.is_active == 1 ? 
            '<span class="badge bg-success">Active</span>' : 
            '<span class="badge bg-secondary">Inactive</span>';
        
        const rarityInfo = `C:${box.rarity_common_chance}% R:${box.rarity_rare_chance}% E:${box.rarity_epic_chance}% L:${box.rarity_legendary_chance}%`;
        
        html += `
            <tr>
                <td>${box.id}</td>
                <td>
                    <strong>${box.box_name}</strong><br>
                    <small class="text-muted">${box.box_description || 'No description'}</small>
                </td>
                <td>$${box.min_usd_reward} - $${box.max_usd_reward}</td>
                <td><small>${rarityInfo}</small></td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editBox(${box.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteBox(${box.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    $('#boxesTableBody').html(html);
}

function editBox(id) {
    $.ajax({
        url: 'ajax/mystery_boxes_admin.php',
        method: 'POST',
        data: { action: 'getBox', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const box = response.data;
                $('#boxId').val(box.id);
                $('#boxName').val(box.box_name);
                $('#boxDescription').val(box.box_description);
                $('#minUsdReward').val(box.min_usd_reward);
                $('#maxUsdReward').val(box.max_usd_reward);
                $('#commonChance').val(box.rarity_common_chance);
                $('#rareChance').val(box.rarity_rare_chance);
                $('#epicChance').val(box.rarity_epic_chance);
                $('#legendaryChance').val(box.rarity_legendary_chance);
                $('#boxStatus').val(box.is_active);
                $('#coinRewards').val(box.coin_rewards);
                
                $('.modal-title').text('Edit Mystery Box');
                $('#addBoxModal').modal('show');
            }
        }
    });
}

function saveBox() {
    const data = {
        action: $('#boxId').val() ? 'updateBox' : 'createBox',
        id: $('#boxId').val(),
        box_name: $('#boxName').val(),
        box_description: $('#boxDescription').val(),
        min_usd_reward: $('#minUsdReward').val(),
        max_usd_reward: $('#maxUsdReward').val(),
        rarity_common_chance: $('#commonChance').val(),
        rarity_rare_chance: $('#rareChance').val(),
        rarity_epic_chance: $('#epicChance').val(),
        rarity_legendary_chance: $('#legendaryChance').val(),
        is_active: $('#boxStatus').val(),
        coin_rewards: $('#coinRewards').val()
    };
    
    $.ajax({
        url: 'ajax/mystery_boxes_admin.php',
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire("Success!", response.message, "success");
                $('#addBoxModal').modal('hide');
                $('#boxForm')[0].reset();
                $('#boxId').val('');
                $('.modal-title').text('Add Mystery Box');
                loadBoxes();
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function deleteBox(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will delete the mystery box permanently!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/mystery_boxes_admin.php',
                method: 'POST',
                data: { action: 'deleteBox', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Deleted!", response.message, "success");
                        loadBoxes();
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }
            });
        }
    });
}

function resetForm() {
    $('#boxId').val('');
    $('#boxName').val('');
    $('#boxStatus').val('');
    $('#boxDescription').val('');
    $('#minUsdReward').val('');
    $('#maxUsdReward').val('');
    $('#commonChance').val('');
    $('#rareChance').val('0');
    $('#epicChance').val('');
    $('#legendaryChance').val('');
    $('#coinRewards').val('');
}

</script>

<?php require __DIR__ . '/inc/footer.php'; ?>