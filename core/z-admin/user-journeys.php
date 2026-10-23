<?php require_once __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">User Journeys</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchUser" placeholder="Search by email or ID">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="claimed">Claimed</option>
                                <option value="active">Active</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" onclick="loadUserJourneys()">Search</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>User</th>
                                    <th>Journey</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Reward</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="userJourneyTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User Journey</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editUserId">
                    <input type="hidden" id="editJourneyId">
                    <div class="mb-3">
                        <label class="form-label">Current Value</label>
                        <input type="number" class="form-control" id="editCurrentValue" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editCompleted">
                            <label class="form-check-label">Completed</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editClaimed">
                            <label class="form-check-label">Claimed</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveEdit()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(() => loadUserJourneys());

function loadUserJourneys() {
    const search = $('#searchUser').val();
    const status = $('#statusFilter').val();
    
    $.post('ajax/user-journeys-ajax.php', {action: 'getUserJourneys', search: search, status: status}, function(data) {
        let html = '';
        data.journeys.forEach(j => {
            // Progress değerini number'a çevir
            const progress = parseFloat(j.progress) || 0;
            
            const statusBadge = j.is_claimed == 1 ? '<span class="badge bg-success">Claimed</span>' : 
                               j.is_completed == 1 ? '<span class="badge bg-warning">Completed</span>' : 
                               '<span class="badge bg-primary">Active</span>';
            
            html += `<tr>
                <td><strong>${j.user_email}</strong><br><small>ID: ${j.user_id}</small></td>
                <td>${j.journey_title}</td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" style="width: ${progress}%">${progress.toFixed(1)}%</div>
                    </div>
                    <small>${j.current_value}/${j.target_value}</small>
                </td>
                <td>${statusBadge}</td>
                <td>$${j.reward_amount}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editUserJourney(${j.user_id}, ${j.journey_id})">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>`;
        });
        $('#userJourneyTable').html(html);
    }, 'json');
}

function editUserJourney(userId, journeyId) {
    $.post('ajax/user-journeys-ajax.php', {action: 'getUserJourney', user_id: userId, journey_id: journeyId}, function(data) {
        const j = data.journey;
        $('#editUserId').val(j.user_id);
        $('#editJourneyId').val(j.journey_id);
        $('#editCurrentValue').val(j.current_value);
        $('#editCompleted').prop('checked', j.is_completed == 1);
        $('#editClaimed').prop('checked', j.is_claimed == 1);
        $('#editModal').modal('show');
    }, 'json');
}

function saveEdit() {
    const formData = {
        action: 'updateUserJourney',
        user_id: $('#editUserId').val(),
        journey_id: $('#editJourneyId').val(),
        current_value: $('#editCurrentValue').val(),
        is_completed: $('#editCompleted').is(':checked') ? 1 : 0,
        is_claimed: $('#editClaimed').is(':checked') ? 1 : 0
    };
    
    $.post('ajax/user-journeys-ajax.php', formData, function(data) {
        if(data.success) {
            $('#editModal').modal('hide');
            loadUserJourneys();
            Swal.fire('Success', data.message, 'success');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    }, 'json');
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>