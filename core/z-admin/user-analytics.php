<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
   
   <div class="row mb-4">
       <div class="col-12">
           <div class="card">
               <div class="card-header">
                   <div class="row align-items-center">
                       <div class="col">
                           <h4 class="card-title">User Analytics</h4>
                           <p class="card-title-desc">Complete user behavior and statistics analysis</p>
                       </div>
                       <div class="col-auto">
                           <div class="btn-group">
                               <button type="button" class="btn btn-outline-secondary" onclick="exportAnalytics('csv')">
                                   <i class="fas fa-download me-1"></i> Export CSV
                               </button>
                               <button type="button" class="btn btn-outline-primary" onclick="refreshAnalytics()">
                                   <i class="fas fa-sync me-1"></i> Refresh
                               </button>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="card-body">
                   <div class="row">
                       <div class="col-md-3">
                           <select class="form-select" id="timeFilter" onchange="applyFilters()">
                               <option value="24h">Last 24 Hours</option>
                               <option value="7d">Last 7 Days</option>
                               <option value="30d" selected>Last 30 Days</option>
                               <option value="90d">Last 90 Days</option>
                               <option value="1y">Last Year</option>
                               <option value="all">All Time</option>
                           </select>
                       </div>
                       <div class="col-md-3">
                           <select class="form-select" id="userTypeFilter" onchange="applyFilters()">
                               <option value="">All Users</option>
                               <option value="active">Active Users</option>
                               <option value="disabled">Disabled Users</option>
                               <option value="verified">Verified Users</option>
                               <option value="unverified">Unverified Users</option>
                           </select>
                       </div>
                       <div class="col-md-3">
                           <select class="form-select" id="metricFilter" onchange="applyFilters()">
                               <option value="overview">Overview</option>
                               <option value="registration">Registration Stats</option>
                               <option value="activity">Activity Stats</option>
                               <option value="balance">Balance Stats</option>
                               <option value="trading">Trading Stats</option>
                           </select>
                       </div>
                       <div class="col-md-3">
                           <div class="input-group">
                               <input type="date" class="form-control" id="dateFrom" onchange="applyFilters()">
                               <input type="date" class="form-control" id="dateTo" onchange="applyFilters()">
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>

   <div class="row mb-4">
       <div class="col-md-3">
           <div class="card text-center">
               <div class="card-body">
                   <h5 class="card-title">Total Users</h5>
                   <h3 class="text-primary" id="totalUsers">0</h3>
                   <small class="text-muted" id="totalUsersChange">No change</small>
               </div>
           </div>
       </div>
       <div class="col-md-3">
           <div class="card text-center">
               <div class="card-body">
                   <h5 class="card-title">Active Users</h5>
                   <h3 class="text-success" id="activeUsers">0</h3>
                   <small class="text-muted" id="activeUsersChange">No change</small>
               </div>
           </div>
       </div>
       <div class="col-md-3">
           <div class="card text-center">
               <div class="card-body">
                   <h5 class="card-title">New Registrations</h5>
                   <h3 class="text-info" id="newRegistrations">0</h3>
                   <small class="text-muted" id="newRegistrationsChange">No change</small>
               </div>
           </div>
       </div>
       <div class="col-md-3">
           <div class="card text-center">
               <div class="card-body">
                   <h5 class="card-title">Verified Users</h5>
                   <h3 class="text-warning" id="verifiedUsers">0</h3>
                   <small class="text-muted" id="verifiedUsersPercentage">0%</small>
               </div>
           </div>
       </div>
   </div>

   <div class="row mb-4">
       <div class="col-md-12">
           <div class="card">
               <div class="card-header">
                   <h5 class="card-title mb-0">User Registration Trend</h5>
               </div>
               <div class="card-body">
                   <div id="registrationChart"></div>
               </div>
           </div>
       </div>
   </div>

   <div class="row mb-4">
       <div class="col-md-6">
           <div class="card">
               <div class="card-header">
                   <h5 class="card-title mb-0">Login Activity</h5>
               </div>
               <div class="card-body">
                   <div class="row">
                       <div class="col-6">
                           <div class="text-center">
                               <h4 class="text-success" id="successfulLogins">0</h4>
                               <p class="text-muted mb-0">Successful Logins</p>
                           </div>
                       </div>
                       <div class="col-6">
                           <div class="text-center">
                               <h4 class="text-danger" id="failedLogins">0</h4>
                               <p class="text-muted mb-0">Failed Logins</p>
                           </div>
                       </div>
                   </div>
                   <div class="mt-3">
                       <div id="loginActivityChart"></div>
                   </div>
               </div>
           </div>
       </div>
       <div class="col-md-6">
           <div class="card">
               <div class="card-header">
                   <h5 class="card-title mb-0">User Balances</h5>
               </div>
               <div class="card-body">
                   <div class="row">
                       <div class="col-6">
                           <div class="text-center">
                               <h4 class="text-primary" id="usersWithBalance">0</h4>
                               <p class="text-muted mb-0">Users with Balance</p>
                           </div>
                       </div>
                       <div class="col-6">
                           <div class="text-center">
                               <h4 class="text-info" id="totalValueUSD">$0</h4>
                               <p class="text-muted mb-0">Total Value</p>
                           </div>
                       </div>
                   </div>
                   <div class="mt-3">
                       <div id="balanceDistributionChart"></div>
                   </div>
               </div>
           </div>
       </div>
   </div>

   <div class="row">
       <div class="col-12">
           <div class="card">
               <div class="card-header">
                   <h5 class="card-title mb-0">Recent User Activity</h5>
               </div>
               <div class="card-body p-0">
                   <div class="table-responsive">
                       <table class="table table-striped table-hover mb-0">
                           <thead class="table-dark">
                               <tr>
                                   <th>User ID</th>
                                   <th>Email</th>
                                   <th>Registration Date</th>
                                   <th>Last Login</th>
                                   <th>Status</th>
                                   <th>KYC Status</th>
                                   <th>Balance Value</th>
                                   <th>Total Trades</th>
                                   <th>Actions</th>
                               </tr>
                           </thead>
                           <tbody id="userActivityTableBody">
                               <tr>
                                   <td colspan="9" class="text-center">
                                       <div class="spinner-border text-primary" role="status">
                                           <span class="visually-hidden">Loading...</span>
                                       </div>
                                   </td>
                               </tr>
                           </tbody>
                       </table>
                   </div>
               </div>
           </div>
       </div>
   </div>
   
</div>

<div class="modal fade" id="userDetailsModal" tabindex="-1">
   <div class="modal-dialog modal-xl">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title">User Analytics Details</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
           </div>
           <div class="modal-body">
               <div class="row">
                   <div class="col-md-6">
                       <h6>User Information</h6>
                       <table class="table table-sm">
                           <tr><td><strong>User ID:</strong></td><td id="modalUserId">-</td></tr>
                           <tr><td><strong>Email:</strong></td><td id="modalUserEmail">-</td></tr>
                           <tr><td><strong>Phone:</strong></td><td id="modalUserPhone">-</td></tr>
                           <tr><td><strong>Registration:</strong></td><td id="modalRegisterDate">-</td></tr>
                           <tr><td><strong>Last Login:</strong></td><td id="modalLastLogin">-</td></tr>
                           <tr><td><strong>Status:</strong></td><td id="modalUserStatus">-</td></tr>
                       </table>
                   </div>
                   <div class="col-md-6">
                       <h6>Activity Summary</h6>
                       <table class="table table-sm">
                           <tr><td><strong>Total Logins:</strong></td><td id="modalTotalLogins">-</td></tr>
                           <tr><td><strong>Total Balances:</strong></td><td id="modalTotalBalances">-</td></tr>
                           <tr><td><strong>Total Trades:</strong></td><td id="modalTotalTrades">-</td></tr>
                           <tr><td><strong>KYC Status:</strong></td><td id="modalKycStatus">-</td></tr>
                           <tr><td><strong>2FA Enabled:</strong></td><td id="modal2faStatus">-</td></tr>
                           <tr><td><strong>Referrals:</strong></td><td id="modalReferrals">-</td></tr>
                       </table>
                   </div>
               </div>
               <hr>
               <div class="row">
                   <div class="col-12">
                       <h6>Recent Activities</h6>
                       <div id="modalUserActivities">
                           <div class="text-center">
                               <div class="spinner-border text-primary" role="status">
                                   <span class="visually-hidden">Loading...</span>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
               <button type="button" class="btn btn-primary" onclick="exportUserData()">Export User Data</button>
           </div>
       </div>
   </div>
</div>

<script>
let registrationChart, userStatusChart, loginActivityChart, balanceDistributionChart;
let currentFilters = {
   time: '30d',
   userType: '',
   metric: 'overview',
   dateFrom: '',
   dateTo: ''
};

$(document).ready(function() {
   initCharts();
   loadAnalytics();
   
   const today = new Date();
   const lastMonth = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
   $('#dateTo').val(today.toISOString().split('T')[0]);
   $('#dateFrom').val(lastMonth.toISOString().split('T')[0]);
});

function initCharts() {
   const registrationOptions = {
       series: [{
           name: 'New Registrations',
           data: []
       }],
       chart: {
           type: 'area',
           height: 300,
           toolbar: { show: false }
       },
       colors: ['#22c55e'],
       stroke: { curve: 'smooth', width: 2 },
       fill: { 
           type: 'gradient',
           gradient: {
               shadeIntensity: 1,
               opacityFrom: 0.7,
               opacityTo: 0.3
           }
       },
       xaxis: { 
           type: 'datetime',
           labels: {
               format: 'dd MMM'
           }
       },
       yaxis: { 
           title: { text: 'Users' }
       },
       tooltip: {
           x: {
               format: 'dd MMM yyyy'
           }
       }
   };
   registrationChart = new ApexCharts(document.querySelector("#registrationChart"), registrationOptions);
   registrationChart.render();

   const statusOptions = {
       series: [{
           name: 'Users',
           data: [0, 0, 0, 0]
       }],
       chart: {
           type: 'donut',
           height: 300
       },
       colors: ['#22c55e', '#ef4444', '#f59e0b', '#8b5cf6'],
       labels: ['Active', 'Disabled', 'Verified', 'Unverified'],
       legend: { 
           position: 'bottom',
           fontSize: '14px'
       },
       plotOptions: {
           pie: {
               donut: {
                   size: '65%'
               }
           }
       },
       dataLabels: {
           enabled: true,
           formatter: function (val) {
               return Math.round(val) + "%";
           }
       },
       tooltip: {
           y: {
               formatter: function (val) {
                   return val + " users";
               }
           }
       }
   };
   userStatusChart = new ApexCharts(document.querySelector("#userStatusChart"), statusOptions);
   userStatusChart.render();

   const loginOptions = {
       series: [{
           name: 'Successful',
           data: []
       }, {
           name: 'Failed',
           data: []
       }],
       chart: {
           type: 'bar',
           height: 200,
           toolbar: { show: false }
       },
       colors: ['#22c55e', '#ef4444'],
       plotOptions: {
           bar: {
               horizontal: false,
               columnWidth: '55%',
               endingShape: 'rounded'
           }
       },
       dataLabels: {
           enabled: false
       },
       stroke: {
           show: true,
           width: 2,
           colors: ['transparent']
       },
       xaxis: { 
           type: 'datetime',
           labels: {
               format: 'dd MMM'
           }
       },
       yaxis: {
           title: {
               text: 'Login Count'
           }
       },
       fill: {
           opacity: 1
       },
       tooltip: {
           y: {
               formatter: function (val) {
                   return val + " logins";
               }
           },
           x: {
               format: 'dd MMM yyyy'
           }
       }
   };
   loginActivityChart = new ApexCharts(document.querySelector("#loginActivityChart"), loginOptions);
   loginActivityChart.render();

   const balanceOptions = {
       series: [{
           name: 'Users',
           data: [0, 0, 0, 0, 0]
       }],
       chart: {
           type: 'bar',
           height: 200,
           toolbar: { show: false }
       },
       colors: ['#3b82f6'],
       plotOptions: {
           bar: {
               borderRadius: 4,
               horizontal: false,
           }
       },
       dataLabels: {
           enabled: false
       },
       xaxis: {
           categories: ['$0-10', '$10-100', '$100-1K', '$1K-10K', '$10K+'],
           title: {
               text: 'Balance Range'
           }
       },
       yaxis: {
           title: {
               text: 'Number of Users'
           }
       },
       tooltip: {
           y: {
               formatter: function (val) {
                   return val + " users";
               }
           }
       }
   };
   balanceDistributionChart = new ApexCharts(document.querySelector("#balanceDistributionChart"), balanceOptions);
   balanceDistributionChart.render();
}

function loadAnalytics() {
   $.ajax({
       url: 'ajax/user-analytics-ajax.php',
       type: 'POST',
       data: {
           action: 'getAnalytics',
           ...currentFilters
       },
       dataType: 'json',
       success: function(response) {
           if (response.success) {
               updateAnalyticsCards(response.stats);
               updateCharts(response.charts);
               updateUserActivityTable(response.user_activity);
           } else {
               Swal.fire('Error!', response.message, 'error');
           }
       },
       error: function(xhr, status, error) {
           Swal.fire('Error!', 'Failed to load analytics: ' + error, 'error');
       }
   });
}

function updateAnalyticsCards(stats) {
   $('#totalUsers').text(stats.total_users || 0);
   $('#totalUsersChange').text(stats.total_users_change || 'No change');
   
   $('#activeUsers').text(stats.active_users || 0);
   $('#activeUsersChange').text(stats.active_users_change || 'No change');
   
   $('#newRegistrations').text(stats.new_registrations || 0);
   $('#newRegistrationsChange').text(stats.new_registrations_change || 'No change');
   
   $('#verifiedUsers').text(stats.verified_users || 0);
   $('#verifiedUsersPercentage').text((stats.verified_percentage || 0) + '%');
   
   $('#successfulLogins').text(stats.successful_logins || 0);
   $('#failedLogins').text(stats.failed_logins || 0);
   
   $('#usersWithBalance').text(stats.users_with_balance || 0);
   $('#totalValueUSD').text('$' + (stats.total_value_usd || 0));
}

function updateCharts(chartsData) {
    
    if (chartsData.registration_trend && Array.isArray(chartsData.registration_trend)) {
        registrationChart.updateSeries([{
            name: 'New Registrations',
            data: chartsData.registration_trend
        }]);
    }

    if (chartsData.user_status && Array.isArray(chartsData.user_status.values)) {
        // Convert strings to integers for pie chart
        const values = chartsData.user_status.values.map(val => parseInt(val) || 0);
        userStatusChart.updateSeries([{
            name: 'Users',
            data: values
        }]);
    }

    if (chartsData.login_activity) {
        const successfulData = chartsData.login_activity.successful || [];
        const failedData = chartsData.login_activity.failed || [];
        
        loginActivityChart.updateSeries([
            { name: 'Successful', data: successfulData },
            { name: 'Failed', data: failedData }
        ]);
    }

    if (chartsData.balance_distribution && Array.isArray(chartsData.balance_distribution)) {
        balanceDistributionChart.updateSeries([{
            name: 'Users',
            data: chartsData.balance_distribution
        }]);
    }
}

function updateUserActivityTable(userData) {
   let html = '';
   
   if (!userData || userData.length === 0) {
       html = '<tr><td colspan="9" class="text-center text-muted">No user data found</td></tr>';
   } else {
       userData.forEach(function(user) {
           const statusBadge = user.disablead == 0 ? 
               '<span class="badge bg-success">Active</span>' : 
               '<span class="badge bg-danger">Disabled</span>';
           
           const kycBadge = user.kyc_status == 'APPROVED' ? 
               '<span class="badge bg-success">Verified</span>' : 
               '<span class="badge bg-warning">Pending</span>';

           html += `
               <tr>
                   <td>${user.uid}</td>
                   <td>${user.mail}</td>
                   <td>${formatDateTime(user.register_date)}</td>
                   <td>${user.last_login ? formatDateTime(user.last_login) : 'Never'}</td>
                   <td>${statusBadge}</td>
                   <td>${kycBadge}</td>
                   <td>$${parseFloat(user.balance_value || 0).toFixed(2)}</td>
                   <td>${user.total_trades || 0}</td>
                   <td>
                       <button class="btn btn-sm btn-outline-primary" onclick="viewUserDetails(${user.uid})">
                           <i class="fas fa-eye"></i>
                       </button>
                   </td>
               </tr>
           `;
       });
   }
   
   $('#userActivityTableBody').html(html);
}

function applyFilters() {
   currentFilters = {
       time: $('#timeFilter').val(),
       userType: $('#userTypeFilter').val(),
       metric: $('#metricFilter').val(),
       dateFrom: $('#dateFrom').val(),
       dateTo: $('#dateTo').val()
   };
   
   loadAnalytics();
}

function refreshAnalytics() {
   loadAnalytics();
   Swal.fire({
       title: 'Refreshed!',
       text: 'Analytics data has been updated',
       icon: 'success',
       timer: 1500,
       showConfirmButton: false
   });
}

function exportAnalytics(format) {
   const params = new URLSearchParams(currentFilters);
   params.append('action', 'exportAnalytics');
   params.append('format', format);
   
   window.open(`ajax/user-analytics-ajax.php?${params.toString()}`, '_blank');
}

function viewUserDetails(userId) {
   $.ajax({
       url: 'ajax/user-analytics-ajax.php',
       type: 'POST',
       data: {
           action: 'getUserDetails',
           user_id: userId
       },
       dataType: 'json',
       success: function(response) {
           if (response.success) {
               const user = response.user;
               const stats = response.stats;
               
               $('#modalUserId').text(user.uid);
               $('#modalUserEmail').text(user.mail);
               $('#modalUserPhone').text(user.phone || 'Not provided');
               $('#modalRegisterDate').text(formatDateTime(user.register_date));
               $('#modalLastLogin').text(user.last_login ? formatDateTime(user.last_login) : 'Never');
               $('#modalUserStatus').html(user.disablead == 0 ? 
                   '<span class="badge bg-success">Active</span>' : 
                   '<span class="badge bg-danger">Disabled</span>');
               
               $('#modalTotalLogins').text(stats.total_logins || 0);
               $('#modalTotalBalances').text(stats.total_balances || 0);
               $('#modalTotalTrades').text(stats.total_trades || 0);
               $('#modalKycStatus').text(stats.kyc_status || 'Not submitted');
               $('#modal2faStatus').text(stats.two_factor_enabled ? 'Enabled' : 'Disabled');
               $('#modalReferrals').text(stats.referrals || 0);
               
               $('#userDetailsModal').modal('show');
               loadUserActivities(userId);
           } else {
               Swal.fire('Error!', response.message, 'error');
           }
       },
       error: function() {
           Swal.fire('Error!', 'Failed to load user details', 'error');
       }
   });
}

function loadUserActivities(userId) {
   $.ajax({
       url: 'ajax/user-analytics-ajax.php',
       type: 'POST',
       data: {
           action: 'getUserActivities',
           user_id: userId
       },
       dataType: 'json',
       success: function(response) {
           if (response.success) {
               let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Amount</th></tr></thead><tbody>';
               
               if (response.activities.length === 0) {
                   html += '<tr><td colspan="4" class="text-center text-muted">No activities found</td></tr>';
               } else {
                   response.activities.forEach(function(activity) {
                       html += `
                           <tr>
                               <td>${formatDateTime(activity.date)}</td>
                               <td><span class="badge bg-info">${activity.type}</span></td>
                               <td>${activity.msg}</td>
                               <td>${activity.amount}</td>
                           </tr>
                       `;
                   });
               }
               
               html += '</tbody></table></div>';
               $('#modalUserActivities').html(html);
           }
       },
       error: function() {
           $('#modalUserActivities').html('<p class="text-danger">Failed to load activities</p>');
       }
   });
}

function exportUserData() {
   const userId = $('#modalUserId').text();
   window.open(`ajax/user-analytics-ajax.php?action=exportUserData&user_id=${userId}`, '_blank');
}

function formatDateTime(dateString) {
   if (!dateString) return 'N/A';
   const date = new Date(dateString);
   return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>