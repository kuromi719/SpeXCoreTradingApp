<?php require_once __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Journey Management</h4>
                    <button class="btn btn-primary" onclick="addJourney()">
                        <i class="fas fa-plus me-2"></i>Add Journey
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Target</th>
                                    <th>Reward</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="journeyTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Journey Modal -->
<div class="modal fade" id="journeyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Journey</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="journeyForm">
                    <input type="hidden" id="journeyId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Order</label>
                            <input type="number" class="form-control" id="journey_order" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Icon</label>
                            <input type="text" class="form-control" id="icon" placeholder="fas fa-trophy">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="requirementType" required>
                                <option value="trade_count">Trade Count</option>
                                <option value="balance_amount">Balance Amount</option>
                                <option value="trading_volume">Trading Volume</option>
                                <option value="kyc_verified">KYC Verified</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Target</label>
                            <input type="number" class="form-control" id="target" step="0.01" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reward Amount</label>
                            <input type="number" class="form-control" id="reward_amount" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveJourney()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(() => loadJourneys());

function loadJourneys() {
    $.post('ajax/journey-ajax.php', {action: 'getJourneys'}, function(data) {
        let html = '';
        data.journeys.forEach(j => {
            const req = JSON.parse(j.requirements);
            html += `<tr>
                <td>${j.journey_order}</td>
                <td><strong>${j.title}</strong></td>
                <td><span class="badge bg-info">${req.type.replace('_', ' ')}</span></td>
                <td>${req.target}</td>
                <td>$${j.reward_amount}</td>
                <td>${j.is_active == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editJourney(${j.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="deleteJourney(${j.id})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
        });
        $('#journeyTable').html(html);
    }, 'json');
}

function addJourney() {
    $('#journeyForm')[0].reset();
    $('#journeyId').val('');
    $('#journeyModal').modal('show');
}

function editJourney(id) {
    $.post('ajax/journey-ajax.php', {action: 'getJourney', id: id}, function(data) {
        const j = data.journey;
        const req = JSON.parse(j.requirements);
        $('#journeyId').val(j.id);
        $('#title').val(j.title);
        $('#description').val(j.description);
        $('#icon').val(j.icon);
        $('#journey_order').val(j.journey_order);
        $('#requirementType').val(req.type);
        $('#target').val(req.target);
        $('#reward_amount').val(j.reward_amount);
        $('#is_active').val(j.is_active);
        $('#journeyModal').modal('show');
    }, 'json');
}

function saveJourney() {
    const formData = {
        action: $('#journeyId').val() ? 'updateJourney' : 'createJourney',
        id: $('#journeyId').val(),
        title: $('#title').val(),
        description: $('#description').val(),
        icon: $('#icon').val(),
        journey_order: $('#journey_order').val(),
        requirements: JSON.stringify({type: $('#requirementType').val(), target: parseFloat($('#target').val())}),
        reward_amount: $('#reward_amount').val(),
        is_active: $('#is_active').val()
    };
    
    $.post('ajax/journey-ajax.php', formData, function(data) {
        if(data.success) {
            $('#journeyModal').modal('hide');
            loadJourneys();
            Swal.fire('Success', data.message, 'success');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    }, 'json');
}

function deleteJourney(id) {
   Swal.fire({
       title: "Are you sure?",
       text: "This journey will be permanently deleted!",
       icon: "warning",
       showCancelButton: true,
       confirmButtonColor: "#d33",
       cancelButtonColor: "#3085d6",
       confirmButtonText: "Yes, delete it!",
       cancelButtonText: "Cancel"
   }).then((result) => {
       if (result.isConfirmed) {
           $.post('ajax/journey-ajax.php', {action: 'deleteJourney', id: id}, function(data) {
               if(data.success) {
                   loadJourneys();
                   Swal.fire('Deleted!', data.message, 'success');
               } else {
                   Swal.fire('Error', data.message, 'error');
               }
           }, 'json');
       }
   });
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>