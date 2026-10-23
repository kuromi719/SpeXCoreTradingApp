<?php require_once __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Cron-Jobs [DEVELOPERS ONLY]</h4>
                </div>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    If you don't know about this part, DEFINITELY do not touch anything.
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Interval Secs</th>
                                    <th>Priority</th>
                                    <th>Active</th>
                                    <th>Last Run</th>
                                    <th>Next Run</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="cronjobsTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(() => loadCronJobs());

function loadCronJobs() {
    $.post('ajax/cronjobs-ajax.php', {action: 'getCrons'}, function(data) {
        let html = '';
        data.crons.forEach(j => {
            const req = JSON.parse(j.id);
            html += `<tr>
                <td>${j.job_name}</td>
                <td>${j.description}</td>
                <td>${j.interval_seconds}</td>
                <td>${j.priority}</td>
                <td>${j.is_active == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'}</td>
                <td>${j.last_run}</td>
                <td>${j.next_run}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="runJob(${j.id})"><i class="fas fa-play"></i></button>
                </td>
            </tr>`;
        });
        $('#cronjobsTable').html(html);
    }, 'json');
}

function runJob(id) {
   Swal.fire({
       title: "Are you sure?",
       text: "This cron-job will RUN !",
       icon: "warning",
       showCancelButton: true,
       confirmButtonColor: "#d33",
       cancelButtonColor: "#3085d6",
       confirmButtonText: "Yes, run it!",
       cancelButtonText: "Cancel"
   }).then((result) => {
       if (result.isConfirmed) {
           $.post('ajax/cronjobs-ajax.php', {action: 'runCron', id: id}, function(data) {
               if(data.success) {
                   Swal.fire('Successfully!', data.message, 'success');
               } else {
                   Swal.fire('Error', data.message, 'error');
               }
           }, 'json');
       }
   });
}

</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>