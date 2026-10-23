<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-sync-alt fa-spin me-2"></i>
            <div>
                <strong>Cron Job Active!</strong> Coin data is being updated automatically.
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="page-title">Coins Management</h6>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-success btn-sm" onclick="addNewCoin()">
                        <i class="fas fa-plus"></i> Add New Coin
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-primary-subtle">
                                <i class="fas fa-coins text-primary fs-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1" id="totalCoins">0</h5>
                            <p class="mb-0 text-muted">Total Coins</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-success-subtle">
                                <i class="fas fa-chart-line text-success fs-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1" id="activeCoins">0</h5>
                            <p class="mb-0 text-muted">Active Coins</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-warning-subtle">
                                <i class="fas fa-sync text-warning fs-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1" id="recentUpdates">0</h5>
                            <p class="mb-0 text-muted">Recent Updates</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-info-subtle">
                                <i class="fas fa-images text-info fs-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1" id="coinsWithImages">0</h5>
                            <p class="mb-0 text-muted">With Images</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search Coin</label>
                    <input type="text" class="form-control" id="searchCoin" placeholder="Name, Symbol or ID">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Market Cap Rank</label>
                    <select class="form-select" id="rankFilter">
                        <option value="">All Ranks</option>
                        <option value="1-50">Top 50</option>
                        <option value="51-100">51-100</option>
                        <option value="101-500">101-500</option>
                        <option value="500+">500+</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Price Change 24h</label>
                    <select class="form-select" id="changeFilter">
                        <option value="">All Changes</option>
                        <option value="positive">Positive</option>
                        <option value="negative">Negative</option>
                        <option value="high-positive">+10% or more</option>
                        <option value="high-negative">-10% or more</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Has Image</label>
                    <select class="form-select" id="imageFilter">
                        <option value="">All</option>
                        <option value="1">With Image</option>
                        <option value="0">No Image</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Actions</label>
                    <div>
                        <button class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button class="btn btn-secondary" onclick="resetFilters()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button class="btn btn-success" onclick="exportCoins()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title">Coins List</h4>
                </div>
                <div class="col-auto">
                    <small class="text-muted">Total: <span id="totalRecords">0</span> coins</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Coin</th>
                            <th>Symbol</th>
                            <th>Price (USD)</th>
                            <th>Market Cap</th>
                            <th>24h Change</th>
                            <th>Volume 24h</th>
                            <th>Rank</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="coinsTableBody">
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
            
            <div class="row align-items-center mt-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <label class="me-2">Show:</label>
                        <select class="form-select form-select-sm" id="recordsPerPage" style="width: auto;">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-2 text-muted">records per page</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <nav>
                        <ul class="pagination pagination-sm justify-content-end mb-0" id="pagination">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="coinModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="coinModalLabel">Add New Coin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="coinForm">
                    <input type="hidden" id="editingCoinId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Basic Information</h6>
                            <div class="mb-3">
                                <label class="form-label">Coin ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="coinId" required>
                                <small class="text-muted">Unique identifier (e.g. bitcoin)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="coinName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Symbol <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="coinSymbol" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="coinDescription" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="mb-3">Links & Information</h6>
                            <div class="mb-3">
                                <label class="form-label">Homepage URL</label>
                                <input type="url" class="form-control" id="coinHomepage">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Blockchain Site</label>
                                <input type="url" class="form-control" id="coinBlockchainSite">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Twitter Username</label>
                                <input type="text" class="form-control" id="coinTwitter">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telegram Channel</label>
                                <input type="text" class="form-control" id="coinTelegram">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Genesis Date</label>
                                <input type="date" class="form-control" id="coinGenesisDate">
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Market Data</h6>
                            <div class="mb-3">
                                <label class="form-label">Current Price (USD)</label>
                                <input type="number" class="form-control" id="coinPrice" step="0.00000001">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Market Cap (USD)</label>
                                <input type="number" class="form-control" id="coinMarketCap">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Market Cap Rank</label>
                                <input type="number" class="form-control" id="coinRank">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">24h Volume (USD)</label>
                                <input type="number" class="form-control" id="coinVolume">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="mb-3">Supply Information</h6>
                            <div class="mb-3">
                                <label class="form-label">Circulating Supply</label>
                                <input type="number" class="form-control" id="coinCirculatingSupply" step="0.00000001">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Supply</label>
                                <input type="number" class="form-control" id="coinTotalSupply" step="0.00000001">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Max Supply</label>
                                <input type="number" class="form-control" id="coinMaxSupply" step="0.00000001">
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="mb-3">Coin Images</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Thumb Image URL</label>
                                        <input type="url" class="form-control" id="coinThumbImage">
                                        <div class="mt-2" id="thumbPreview"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Small Image URL</label>
                                        <input type="url" class="form-control" id="coinSmallImage">
                                        <div class="mt-2" id="smallPreview"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Large Image URL</label>
                                        <input type="url" class="form-control" id="coinLargeImage">
                                        <div class="mt-2" id="largePreview"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCoin()">
                    <i class="fas fa-save"></i> Save Coin
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="coinDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Coin Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="coinDetailsContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadCoinStats();
    loadCoins();
    updateCronStatus();
    
    setInterval(function() {
        updateCronStatus();
        loadCoinStats();
    }, 30000);
    
    $('#searchCoin, #rankFilter, #changeFilter, #imageFilter').on('input change', function() {
        applyFilters();
    });
    
    $('#recordsPerPage').on('change', function() {
        loadCoins();
    });
    
    $('#coinThumbImage, #coinSmallImage, #coinLargeImage').on('input', function() {
        updateImagePreview($(this));
    });
});

let allCoins = [];
let currentPage = 1;

function updateCronStatus() {
    $.ajax({
        url: 'ajax/coins-management-ajax.php',
        type: 'POST',
        data: {action: 'getCronStatus'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#lastCronUpdate').text(response.last_update);
            }
        }
    });
}

function loadCoinStats() {
    $.ajax({
        url: 'ajax/coins-management-ajax.php',
        type: 'POST',
        data: {action: 'getStats'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalCoins').text(response.stats.total_coins);
                $('#activeCoins').text(response.stats.active_coins);
                $('#recentUpdates').text(response.stats.recent_updates);
                $('#coinsWithImages').text(response.stats.coins_with_images);
            }
        }
    });
}

function loadCoins() {
    const limit = $('#recordsPerPage').val();
    const offset = (currentPage - 1) * limit;
    
    $.ajax({
        url: 'ajax/coins-management-ajax.php',
        type: 'POST',
        data: {
            action: 'getCoins',
            limit: limit,
            offset: offset,
            search: $('#searchCoin').val(),
            rank: $('#rankFilter').val(),
            change: $('#changeFilter').val(),
            image: $('#imageFilter').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allCoins = response.coins;
                displayCoins(allCoins);
                updatePagination(response.total, limit);
                $('#totalRecords').text(response.total);
            } else {
                $('#coinsTableBody').html('<tr><td colspan="9" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#coinsTableBody').html('<tr><td colspan="9" class="text-center text-danger">Ajax Error: ' + error + '</td></tr>');
        }
    });
}

function displayCoins(coins) {
    let html = '';
    
    if (coins.length === 0) {
        html = '<tr><td colspan="9" class="text-center text-muted">No coins found</td></tr>';
    } else {
        coins.forEach(function(coin) {
            const priceChange = parseFloat(coin.price_change_percentage_24h || 0);
            const changeClass = priceChange >= 0 ? 'text-success' : 'text-danger';
            const changeIcon = priceChange >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
            
            const price = parseFloat(coin.current_price_usd || 0);
            const marketCap = parseInt(coin.market_cap_usd || 0);
            const volume = parseInt(coin.total_volume_usd || 0);
            
            html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="${coin.small_image || 'assets/images/placeholder-coin.png'}" 
                                 alt="${coin.name}" class="rounded-circle me-2" width="32" height="32"
                                 onerror="this.src='assets/images/placeholder-coin.png'">
                            <div>
                                <div class="fw-medium">${coin.name}</div>
                                <small class="text-muted">${coin.id}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-primary">${coin.symbol.toUpperCase()}</span></td>
                    <td>
                        <div>$${price.toFixed(8)}</div>
                        <small class="text-muted">≈ ${(price * 0.00001).toFixed(8)} BTC</small>
                    </td>
                    <td>
                        <div>$${formatNumber(marketCap)}</div>
                        <small class="text-muted">Market Cap</small>
                    </td>
                    <td>
                        <span class="${changeClass}">
                            <i class="${changeIcon}"></i> ${Math.abs(priceChange).toFixed(2)}%
                        </span>
                    </td>
                    <td>$${formatNumber(volume)}</td>
                    <td>
                        ${coin.market_cap_rank ? 
                            `<span class="badge bg-info">#${coin.market_cap_rank}</span>` : 
                            '<span class="badge bg-secondary">-</span>'
                        }
                    </td>
                    <td>
                        <div>${formatDateTime(coin.last_updated)}</div>
                        <small class="text-muted">${timeAgo(coin.last_updated)}</small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" onclick="viewCoinDetails('${coin.id}')" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-primary" onclick="editCoin('${coin.id}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteCoin('${coin.id}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#coinsTableBody').html(html);
}

function formatNumber(num) {
    if (num >= 1e12) return (num / 1e12).toFixed(2) + 'T';
    if (num >= 1e9) return (num / 1e9).toFixed(2) + 'B';
    if (num >= 1e6) return (num / 1e6).toFixed(2) + 'M';
    if (num >= 1e3) return (num / 1e3).toFixed(2) + 'K';
    return num.toLocaleString();
}

function formatDateTime(datetime) {
    if (!datetime) return 'Never';
    const date = new Date(datetime);
    return date.toLocaleString('tr-TR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function timeAgo(datetime) {
    if (!datetime) return 'No data';
    const now = new Date();
    const past = new Date(datetime);
    const diffInSeconds = Math.floor((now - past) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' min ago';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
    return Math.floor(diffInSeconds / 86400) + ' days ago';
}

function updatePagination(total, limit) {
    const totalPages = Math.ceil(total / limit);
    let paginationHtml = '';
    
    if (totalPages > 1) {
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">&laquo;</a>
        </li>`;
        
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                </li>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">&raquo;</a>
        </li>`;
    }
    
    $('#pagination').html(paginationHtml);
}

function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    loadCoins();
}

function applyFilters() {
    currentPage = 1;
    loadCoins();
}

function resetFilters() {
    $('#searchCoin').val('');
    $('#rankFilter').val('');
    $('#changeFilter').val('');
    $('#imageFilter').val('');
    currentPage = 1;
    loadCoins();
}

function addNewCoin() {
    $('#coinModalLabel').text('Add New Coin');
    $('#editingCoinId').val('');
    $('#coinForm')[0].reset();
    $('#thumbPreview, #smallPreview, #largePreview').empty();
    $('#coinModal').modal('show');
}

function editCoin(coinId) {
    $.ajax({
        url: 'ajax/coins-management-ajax.php',
        type: 'POST',
        data: {action: 'getCoinDetails', coin_id: coinId},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const coin = response.coin;
                
                $('#coinModalLabel').text('Edit Coin: ' + coin.name);
                $('#editingCoinId').val(coinId);
                $('#coinId').val(coin.id);
                $('#coinName').val(coin.name);
                $('#coinSymbol').val(coin.symbol);
                $('#coinDescription').val(coin.description);
                $('#coinHomepage').val(coin.homepage);
                $('#coinBlockchainSite').val(coin.blockchain_site);
                $('#coinTwitter').val(coin.twitter_screen_name);
                $('#coinTelegram').val(coin.telegram_channel_identifier);
                $('#coinGenesisDate').val(coin.genesis_date);
                
                $('#coinPrice').val(coin.current_price_usd);
                $('#coinMarketCap').val(coin.market_cap_usd);
                $('#coinRank').val(coin.market_cap_rank);
                $('#coinVolume').val(coin.total_volume_usd);
                $('#coinCirculatingSupply').val(coin.circulating_supply);
                $('#coinTotalSupply').val(coin.total_supply);
                $('#coinMaxSupply').val(coin.max_supply);
                
                $('#coinThumbImage').val(coin.thumb_image);
                $('#coinSmallImage').val(coin.small_image);
                $('#coinLargeImage').val(coin.large_image);
                
                updateImagePreview($('#coinThumbImage'));
                updateImagePreview($('#coinSmallImage'));
                updateImagePreview($('#coinLargeImage'));
                
                $('#coinModal').modal('show');
            } else {
                Swal.fire("Error!", "Failed to load coin details: " + response.message, "error");
            }
        }
    });
}

function saveCoin() {
    const isEditing = $('#editingCoinId').val() !== '';
    
    const formData = {
        action: isEditing ? 'updateCoin' : 'createCoin',
        coin_id: $('#coinId').val(),
        name: $('#coinName').val(),
        symbol: $('#coinSymbol').val(),
        description: $('#coinDescription').val(),
        homepage: $('#coinHomepage').val(),
        blockchain_site: $('#coinBlockchainSite').val(),
        twitter_screen_name: $('#coinTwitter').val(),
        telegram_channel_identifier: $('#coinTelegram').val(),
        genesis_date: $('#coinGenesisDate').val(),
        current_price_usd: $('#coinPrice').val(),
        market_cap_usd: $('#coinMarketCap').val(),
        market_cap_rank: $('#coinRank').val(),
        total_volume_usd: $('#coinVolume').val(),
        circulating_supply: $('#coinCirculatingSupply').val(),
        total_supply: $('#coinTotalSupply').val(),
        max_supply: $('#coinMaxSupply').val(),
        thumb_image: $('#coinThumbImage').val(),
        small_image: $('#coinSmallImage').val(),
        large_image: $('#coinLargeImage').val()
    };
    
    if (isEditing) {
        formData.original_id = $('#editingCoinId').val();
    }
    
    $.ajax({
        url: 'ajax/coins-management-ajax.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire("Success!", isEditing ? 'Coin updated successfully!' : 'Coin created successfully!', "success");
                $('#coinModal').modal('hide');
                loadCoins();
                loadCoinStats();
            } else {
                Swal.fire("Error!", "Failed to save coin: " + response.message, "error");
            }
        },
        error: function() {
            Swal.fire("Error!", "Failed to save coin", "error");
        }
    });
}

function updateImagePreview(input) {
    const url = input.val();
    const previewId = input.attr('id').replace('coin', '').replace('Image', 'Preview');
    
    if (url) {
        $(`#${previewId}`).html(`<img src="${url}" alt="Preview" class="img-thumbnail" style="max-width: 64px; max-height: 64px;" onerror="this.style.display='none'">`);
    } else {
        $(`#${previewId}`).empty();
    }
}

function viewCoinDetails(coinId) {
    $.ajax({
        url: 'ajax/coins-management-ajax.php',
        type: 'POST',
        data: {action: 'getCoinDetails', coin_id: coinId},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const coin = response.coin;
                
                let detailsHtml = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Basic Information</h6>
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>ID:</strong></td><td>${coin.id}</td></tr>
                                <tr><td><strong>Name:</strong></td><td>${coin.name}</td></tr>
                                <tr><td><strong>Symbol:</strong></td><td><span class="badge bg-primary">${coin.symbol.toUpperCase()}</span></td></tr>
                                <tr><td><strong>Description:</strong></td><td>${coin.description || 'No description'}</td></tr>
                                <tr><td><strong>Genesis Date:</strong></td><td>${coin.genesis_date || 'Unknown'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Market Data</h6>
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>Price:</strong></td><td>$${parseFloat(coin.current_price_usd || 0).toFixed(8)}</td></tr>
                                <tr><td><strong>Market Cap:</strong></td><td>$${formatNumber(coin.market_cap_usd || 0)}</td></tr>
                                <tr><td><strong>Rank:</strong></td><td>${coin.market_cap_rank ? '#' + coin.market_cap_rank : 'Unranked'}</td></tr>
                                <tr><td><strong>24h Volume:</strong></td><td>$${formatNumber(coin.total_volume_usd || 0)}</td></tr>
                                <tr><td><strong>24h Change:</strong></td><td class="${parseFloat(coin.price_change_percentage_24h || 0) >= 0 ? 'text-success' : 'text-danger'}">${parseFloat(coin.price_change_percentage_24h || 0).toFixed(2)}%</td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="mb-3">Supply Information</h6>
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>Circulating:</strong></td><td>${formatNumber(coin.circulating_supply || 0)}</td></tr>
                                <tr><td><strong>Total Supply:</strong></td><td>${formatNumber(coin.total_supply || 0)}</td></tr>
                                <tr><td><strong>Max Supply:</strong></td><td>${coin.max_supply ? formatNumber(coin.max_supply) : 'No limit'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Images</h6>
                            <div class="d-flex gap-2">
                                ${coin.thumb_image ? `<img src="${coin.thumb_image}" class="img-thumbnail" width="32" height="32">` : ''}
                                ${coin.small_image ? `<img src="${coin.small_image}" class="img-thumbnail" width="64" height="64">` : ''}
                                ${coin.large_image ? `<img src="${coin.large_image}" class="img-thumbnail" width="128" height="128">` : ''}
                            </div>
                        </div>
                    </div>
                    
                    ${coin.homepage || coin.twitter_screen_name || coin.telegram_channel_identifier ? `
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="mb-3">Links</h6>
                            <div class="d-flex gap-2">
                                ${coin.homepage ? `<a href="${coin.homepage}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fas fa-home"></i> Website</a>` : ''}
                                ${coin.twitter_screen_name ? `<a href="https://twitter.com/${coin.twitter_screen_name}" target="_blank" class="btn btn-outline-info btn-sm"><i class="fab fa-twitter"></i> Twitter</a>` : ''}
                                ${coin.telegram_channel_identifier ? `<a href="https://t.me/${coin.telegram_channel_identifier}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fab fa-telegram"></i> Telegram</a>` : ''}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                `;
                
                $('#coinDetailsContent').html(detailsHtml);
                $('#coinDetailsModal').modal('show');
            } else {
                Swal.fire("Error!", "Failed to load coin details: " + response.message, "error");
            }
        }
    });
}

function syncCoin(coinId) {
    Swal.fire({
        title: "Sync Coin Data?",
        text: "This Sync is not available. use Force Sync on the top.",
        icon: "warning",
        buttons: true,
    });
}

function syncAllCoins() {
    Swal.fire({
        title: "Force Sync All Coins?",
        text: "This will update all coin data. This may take a while.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willSync) => {
        if (willSync) {
            $.ajax({
                url: 'ajax/coins-management-ajax.php',
                type: 'POST',
                data: {action: 'syncAllCoins'},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Success!", `${response.updated_count} coins synced successfully!`, "success");
                        loadCoins();
                        loadCoinStats();
                    } else {
                        Swal.fire("Error!", "Failed to sync coins: " + response.message, "error");
                    }
                }
            });
        }
    });
}

function deleteCoin(coinId) {
    Swal.fire({
        title: "Delete Coin?",
        text: "This will permanently delete the coin and all related data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: 'ajax/coins-management-ajax.php',
                type: 'POST',
                data: {action: 'deleteCoin', coin_id: coinId},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Success!", "Coin deleted successfully!", "success");
                        loadCoins();
                        loadCoinStats();
                    } else {
                        Swal.fire("Error!", "Failed to delete coin: " + response.message, "error");
                    }
                }
            });
        }
    });
}

function exportCoins() {
    const params = new URLSearchParams({
        action: 'exportCSV',
        search: $('#searchCoin').val(),
        rank: $('#rankFilter').val(),
        change: $('#changeFilter').val(),
        image: $('#imageFilter').val()
    });
    
    window.open('ajax/coins-management-ajax.php?' + params.toString(), '_blank');
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>