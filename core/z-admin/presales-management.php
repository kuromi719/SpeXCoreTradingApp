<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">Presales Management</h4>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row" id="presaleStats">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle">
                                <span class="avatar-title text-primary rounded-circle fs-3">
                                    <i class="fas fa-rocket"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Presales</h6>
                            <b id="totalPresales">0</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle">
                                <span class="avatar-title text-success rounded-circle fs-3">
                                    <i class="fas fa-play-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Active Presales</h6>
                            <b id="activePresales">0</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle ">
                                <span class="avatar-title text-info rounded-circle fs-3">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Raised</h6>
                            <b id="totalRaised">$0</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle">
                                <span class="avatar-title text-warning rounded-circle fs-3">
                                    <i class="fas fa-users"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Investors</h6>
                            <b id="totalInvestors">0</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Presales Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Presales List</h4>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <button type="button" class="btn btn-success btn-sm" onclick="addPresale()">
                                    <i class="fas fa-plus me-1"></i> Add Presale
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="refreshData()">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="exportPresales()">
                                    <i class="fas fa-download me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchPresale" placeholder="Search presales...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="active">Active</option>
                                <option value="ended">Ended</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="featuredFilter">
                                <option value="">All Presales</option>
                                <option value="1">Featured Only</option>
                                <option value="0">Non-Featured</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="blockchainFilter">
                                <option value="">All Blockchains</option>
                                <option value="Ethereum">Ethereum</option>
                                <option value="Binance Smart Chain">BSC</option>
                                <option value="Polygon">Polygon</option>
                                <option value="Arbitrum">Arbitrum</option>
                                <option value="Optimism">Optimism</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="fas fa-filter"></i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Coin</th>
                                    <th>Price</th>
                                    <th>Supply</th>
                                    <th>Sold</th>
                                    <th>Progress</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="presalesTableBody">
                                <tr>
                                    <td colspan="10" class="text-center">
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

<!-- Add/Edit Presale Modal -->
<div class="modal fade" id="presaleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="presaleModalTitle">Add New Presale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="presaleForm">
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold">Basic Information</h6>
                            <div class="mb-3">
                                <label class="form-label">Coin Name *</label>
                                <input type="text" class="form-control" id="coinName" name="coin_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Coin Symbol *</label>
                                <input type="text" class="form-control" id="coinSymbol" name="coin_symbol" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Coin Logo URL</label>
                                <input type="url" class="form-control" id="coinLogo" name="coin_logo" onchange="updateLogoPreview()">
                                <div id="logoPreview" class="mt-2"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Blockchain *</label>
                                <select class="form-select" id="blockchain" name="blockchain" required>
                                    <option value="Ethereum">Ethereum</option>
                                    <option value="Binance Smart Chain">Binance Smart Chain</option>
                                    <option value="Polygon">Polygon</option>
                                    <option value="Arbitrum">Arbitrum</option>
                                    <option value="Optimism">Optimism</option>
                                </select>
                            </div>
                        </div>

                        <!-- Presale Configuration -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold">Presale Configuration</h6>
                            <div class="mb-3">
                                <label class="form-label">Price (USD) *</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.00000001" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Supply *</label>
                                <input type="number" class="form-control" id="totalSupply" name="total_supply" required>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Min Purchase (USD)</label>
                                        <input type="number" class="form-control" id="minPurchase" name="min_purchase" step="0.00000001" value="0.001">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Max Purchase (USD)</label>
                                        <input type="number" class="form-control" id="maxPurchase" name="max_purchase" step="0.00000001" value="1000">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">24h Volume</label>
                                        <input type="number" class="form-control" id="volume24h" name="volume_24h" step="0.00000001" value="0.001">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Market Cap</label>
                                        <input type="number" class="form-control" id="marketcap" name="market_cap" step="0.00000001" value="1000">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Start Date *</label>
                                        <input type="datetime-local" class="form-control" id="startDate" name="start_date" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">End Date *</label>
                                        <input type="datetime-local" class="form-control" id="endDate" name="end_date" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured">
                                    <label class="form-check-label" for="featured">Featured Presale</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Social Links -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-semibold">Social Links</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Whitepaper</label>
                                <input type="url" class="form-control" id="whitepaper" name="whitepaper">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Telegram</label>
                                <input type="url" class="form-control" id="telegram" name="telegram">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Twitter</label>
                                <input type="url" class="form-control" id="twitter" name="twitter">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Advanced Settings -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-semibold">Advanced Settings</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="upcoming">Upcoming</option>
                                    <option value="active">Active</option>
                                    <option value="ended">Ended</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="tradingEnabled" name="trading_enabled">
                                    <label class="form-check-label" for="tradingEnabled">Trading Enabled</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="claimEnabled" name="claim_enabled">
                                    <label class="form-check-label" for="claimEnabled">Claim Enabled</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="savePresaleBtn">Save Presale</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Presale Details Modal -->
<div class="modal fade" id="presaleDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Presale Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="presaleDetailsContent">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Presale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this presale? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Presale ID:</strong> <span id="deletePresaleId"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadPresaleStats();
    loadPresales();
    
    // Search functionality
    $('#searchPresale').on('keyup', function() {
        loadPresales();
    });
    
    // Filter functionality
    $('#statusFilter, #featuredFilter, #blockchainFilter').on('change', function() {
        loadPresales();
    });
    
    // Form submission
    $('#presaleForm').on('submit', function(e) {
        e.preventDefault();
        savePresale();
    });
    
    // Delete confirmation
    $('#confirmDeleteBtn').on('click', function() {
        const presaleId = $(this).data('presale-id');
        deletePresale(presaleId);
    });
});

let isEditing = false;
let editingPresaleId = null;

function loadPresaleStats() {
    $.ajax({
        url: 'ajax/presales-management-ajax.php',
        method: 'POST',
        data: { action: 'getStats' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#totalPresales').text(response.stats.total_presales);
                $('#activePresales').text(response.stats.active_presales);
                $('#totalRaised').text('$' + formatNumber(response.stats.total_raised));
                $('#totalInvestors').text(response.stats.total_investors);
            }
        }
    });
}

function loadPresales() {
    const search = $('#searchPresale').val();
    const status = $('#statusFilter').val();
    const featured = $('#featuredFilter').val();
    const blockchain = $('#blockchainFilter').val();
    
    $.ajax({
        url: 'ajax/presales-management-ajax.php',
        method: 'POST',
        data: {
            action: 'getPresales',
            search: search,
            status: status,
            featured: featured,
            blockchain: blockchain
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayPresales(response.presales);
            } else {
                showAlert('error', 'Error loading presales: ' + response.message);
            }
        },
        error: function() {
            showAlert('error', 'Failed to load presales');
        }
    });
}

function displayPresales(presales) {
    let html = '';
    
    if(presales.length === 0) {
        html = '<tr><td colspan="10" class="text-center">No presales found</td></tr>';
    } else {
        presales.forEach(function(presale) {
            const progress = presale.total_supply > 0 ? (presale.sold_amount / presale.total_supply * 100).toFixed(2) : 0;
            const statusBadge = getStatusBadge(presale.status);
            const featuredBadge = presale.featured == 1 ? '<span class="badge bg-warning"><i class="fas fa-star"></i></span>' : '';
            
            html += `
                <tr>
                    <td>${presale.id}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${presale.coin_logo ? `<img src="${presale.coin_logo}" width="24" height="24" class="rounded me-2" onerror="this.style.display='none'">` : ''}
                            <div>
                                <div class="fw-medium">${presale.coin_name}</div>
                                <small class="text-muted">${presale.coin_symbol}</small>
                            </div>
                        </div>
                    </td>
                    <td>$${parseFloat(presale.price).toFixed(8)}</td>
                    <td>${formatNumber(presale.total_supply)}</td>
                    <td>${formatNumber(presale.sold_amount)}</td>
                    <td>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: ${progress}%"></div>
                        </div>
                        <small class="text-muted">${progress}%</small>
                    </td>
                    <td>
                        <small>${formatDateTime(presale.start_date)}</small><br>
                        <small class="text-muted">to ${formatDateTime(presale.end_date)}</small>
                    </td>
                    <td>${statusBadge}</td>
                    <td>${featuredBadge}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="viewPresaleDetails(${presale.id})"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                <li><a class="dropdown-item" href="#" onclick="editPresale(${presale.id})"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item" href="#" onclick="duplicatePresale(${presale.id})"><i class="fas fa-copy me-2"></i>Duplicate</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteModal(${presale.id})"><i class="fas fa-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#presalesTableBody').html(html);
}

function getStatusBadge(status) {
    const badges = {
        'upcoming': '<span class="badge bg-info">Upcoming</span>',
        'active': '<span class="badge bg-success">Active</span>',
        'ended': '<span class="badge bg-secondary">Ended</span>',
        'cancelled': '<span class="badge bg-danger">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-light">Unknown</span>';
}

function addPresale() {
    isEditing = false;
    editingPresaleId = null;
    $('#presaleModalTitle').text('Add New Presale');
    $('#presaleForm')[0].reset();
    $('#logoPreview').empty();
    $('#savePresaleBtn').text('Save Presale');
    $('#presaleModal').modal('show');
}

function editPresale(presaleId) {
    isEditing = true;
    editingPresaleId = presaleId;
    $('#presaleModalTitle').text('Edit Presale');
    $('#savePresaleBtn').text('Update Presale');
    
    $.ajax({
        url: 'ajax/presales-management-ajax.php',
        method: 'POST',
        data: { action: 'getPresale', presale_id: presaleId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                const presale = response.presale;
                
                $('#coinName').val(presale.coin_name);
                $('#coinSymbol').val(presale.coin_symbol);
                $('#coinLogo').val(presale.coin_logo);
                $('#description').val(presale.description);
                $('#blockchain').val(presale.blockchain);
                $('#price').val(presale.price);
                $('#totalSupply').val(presale.total_supply);
                $('#minPurchase').val(presale.min_purchase);
                $('#maxPurchase').val(presale.max_purchase);
                $('#volume24h').val(presale.volume_24h);
                $('#marketcap').val(presale.market_cap);
                $('#startDate').val(presale.start_date.replace(' ', 'T'));
                $('#endDate').val(presale.end_date.replace(' ', 'T'));
                $('#featured').prop('checked', presale.featured == 1);
                $('#website').val(presale.website);
                $('#whitepaper').val(presale.whitepaper);
                $('#telegram').val(presale.telegram);
                $('#twitter').val(presale.twitter);
                $('#status').val(presale.status);
                $('#tradingEnabled').prop('checked', presale.trading_enabled == 1);
                $('#claimEnabled').prop('checked', presale.claim_enabled == 1);
                
                updateLogoPreview();
                $('#presaleModal').modal('show');
            } else {
                showAlert('error', 'Failed to load presale data: ' + response.message);
            }
        }
    });
}

function duplicatePresale(presaleId) {
    $.ajax({
        url: 'ajax/presales-management-ajax.php',
        method: 'POST',
        data: { action: 'duplicatePresale', presale_id: presaleId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                showAlert('success', 'Presale duplicated successfully!');
                loadPresales();
                loadPresaleStats();
            } else {
                showAlert('error', 'Failed to duplicate presale: ' + response.message);
            }
        }
    });
}

function savePresale() {
    const formData = new FormData($('#presaleForm')[0]);
    formData.append('action', isEditing ? 'updatePresale' : 'addPresale');
    
    if(isEditing) {
        formData.append('presale_id', editingPresaleId);
    }
    
    $.ajax({
        url: 'ajax/presales-management-ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                showAlert('success', isEditing ? 'Presale updated successfully!' : 'Presale created successfully!');
                $('#presaleModal').modal('hide');
                loadPresales();
                loadPresaleStats();
            } else {
                showAlert('error', 'Failed to save presale: ' + response.message);
            }
        },
        error: function() {
            showAlert('error', 'Failed to save presale');
        }
    });
}

function viewPresaleDetails(presaleId) {
    $.ajax({
        url: 'ajax/presales-management-ajax.php',
        method: 'POST',
        data: { action: 'getPresaleDetails', presale_id: presaleId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayPresaleDetails(response.presale, response.stats);
                $('#presaleDetailsModal').modal('show');
            } else {
                showAlert('error', 'Failed to load presale details: ' + response.message);
            }
        }
    });
}

function displayPresaleDetails(presale, stats) {
    const progress = presale.total_supply > 0 ? (presale.sold_amount / presale.total_supply * 100).toFixed(2) : 0;
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Basic Information</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>ID:</strong></td><td>${presale.id}</td></tr>
                    <tr><td><strong>Coin:</strong></td><td>${presale.coin_name} (${presale.coin_symbol})</td></tr>
                    <tr><td><strong>Price:</strong></td><td>$${parseFloat(presale.price).toFixed(8)}</td></tr>
                    <tr><td><strong>Blockchain:</strong></td><td>${presale.blockchain}</td></tr>
                    <tr><td><strong>24h Volume:</strong></td><td>${presale.volume_24h}</td></tr>
                    <tr><td><strong>Market Cap:</strong></td><td>${presale.market_cap}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>${getStatusBadge(presale.status)}</td></tr>
                    <tr><td><strong>Featured:</strong></td><td>${presale.featured == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'}</td></tr>
                </table>
                
                <h6 class="fw-semibold mb-3">Supply & Sales</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Total Supply:</strong></td><td>${formatNumber(presale.total_supply)}</td></tr>
                    <tr><td><strong>Sold Amount:</strong></td><td>${formatNumber(presale.sold_amount)}</td></tr>
                    <tr><td><strong>Progress:</strong></td><td>${progress}%</td></tr>
                    <tr><td><strong>Min Purchase:</strong></td><td>$${parseFloat(presale.min_purchase).toFixed(8)}</td></tr>
                    <tr><td><strong>Max Purchase:</strong></td><td>$${parseFloat(presale.max_purchase).toFixed(8)}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Timeline</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Start Date:</strong></td><td>${formatDateTime(presale.start_date)}</td></tr>
                    <tr><td><strong>End Date:</strong></td><td>${formatDateTime(presale.end_date)}</td></tr>
                    <tr><td><strong>Created:</strong></td><td>${formatDateTime(presale.created_at)}</td></tr>
                    <tr><td><strong>Updated:</strong></td><td>${formatDateTime(presale.updated_at)}</td></tr>
                </table>
                
                <h6 class="fw-semibold mb-3">Statistics</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Total Purchases:</strong></td><td>${stats.total_purchases}</td></tr>
                    <tr><td><strong>Total Investors:</strong></td><td>${stats.total_investors}</td></tr>
                    <tr><td><strong>Total Raised:</strong></td><td>$${formatNumber(stats.total_raised)}</td></tr>
                    <tr><td><strong>Average Purchase:</strong></td><td>$${formatNumber(stats.avg_purchase)}</td></tr>
                </table>
                
                <h6 class="fw-semibold mb-3">Settings</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Trading:</strong></td><td>${presale.trading_enabled == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'}</td></tr>
                    <tr><td><strong>Claim:</strong></td><td>${presale.claim_enabled == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'}</td></tr>
                </table>
            </div>
        </div>
        
        ${presale.description ? `
        <hr>
        <h6 class="fw-semibold mb-3">Description</h6>
        <p>${presale.description}</p>
        ` : ''}
        
        <hr>
        <h6 class="fw-semibold mb-3">Social Links</h6>
        <div class="d-flex gap-2">
            ${presale.website ? `<a href="${presale.website}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fas fa-globe"></i> Website</a>` : ''}
            ${presale.whitepaper ? `<a href="${presale.whitepaper}" target="_blank" class="btn btn-outline-info btn-sm"><i class="fas fa-file-pdf"></i> Whitepaper</a>` : ''}
            ${presale.telegram ? `<a href="${presale.telegram}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fab fa-telegram"></i> Telegram</a>` : ''}
            ${presale.twitter ? `<a href="${presale.twitter}" target="_blank" class="btn btn-outline-info btn-sm"><i class="fab fa-twitter"></i> Twitter</a>` : ''}
        </div>
    `;
    
    $('#presaleDetailsContent').html(html);
}

function showDeleteModal(presaleId) {
    $('#deletePresaleId').text(presaleId);
    $('#confirmDeleteBtn').data('presale-id', presaleId);
    $('#deleteModal').modal('show');
}

function deletePresale(presaleId) {
    $.ajax({
        url: 'ajax/presales-management-ajax.php',
        method: 'POST',
        data: { action: 'deletePresale', presale_id: presaleId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                showAlert('success', 'Presale deleted successfully!');
                $('#deleteModal').modal('hide');
                loadPresales();
                loadPresaleStats();
            } else {
                showAlert('error', 'Failed to delete presale: ' + response.message);
            }
        }
    });
}

function clearFilters() {
    $('#searchPresale').val('');
    $('#statusFilter').val('');
    $('#featuredFilter').val('');
    $('#blockchainFilter').val('');
    loadPresales();
}

function refreshData() {
    loadPresaleStats();
    loadPresales();
    showAlert('success', 'Data refreshed successfully!');
}

function exportPresales() {
    const params = new URLSearchParams({
        action: 'exportCSV',
        search: $('#searchPresale').val(),
        status: $('#statusFilter').val(),
        featured: $('#featuredFilter').val(),
        blockchain: $('#blockchainFilter').val()
    });
    
    window.open('ajax/presales-management-ajax.php?' + params.toString(), '_blank');
}

function updateLogoPreview() {
    const url = $('#coinLogo').val();
    if (url) {
        $('#logoPreview').html(`<img src="${url}" alt="Logo Preview" class="img-thumbnail" style="max-width: 64px; max-height: 64px;" onerror="this.style.display='none'">`);
    } else {
        $('#logoPreview').empty();
    }
}

function formatNumber(num) {
    return Number(num).toLocaleString();
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}

function showAlert(type, message) {
    if (type=="error") {
        type="danger";
    }
    Swal.fire({
        title: '',
        text: message,
        icon: type === 'success' ? 'success' : 'danger',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        confirmButtonText: `Yes`
    }).then((result) => {

    });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>