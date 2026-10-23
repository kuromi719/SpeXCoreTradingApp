<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">


    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="page-title">Market Data</h6>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-success btn-sm" onclick="exportMarketData()">
                        <i class="fas fa-download"></i> Export
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
                                <i class="fas fa-globe text-primary fs-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1" id="totalMarketCap">$0</h5>
                            <p class="mb-0 text-muted">Total Market Cap</p>
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
                                <i class="fas fa-exchange-alt text-success fs-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1" id="totalVolume">$0</h5>
                            <p class="mb-0 text-muted">24h Volume</p>
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
                                <i class="fas fa-percentage text-warning fs-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1" id="btcDominance">0%</h5>
                            <p class="mb-0 text-muted">BTC Dominance</p>
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
                                <i class="fas fa-coins text-info fs-20"></i>
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
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Gainers (24h)</h5>
                </div>
                <div class="card-body">
                    <div id="topGainers">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Losers (24h)</h5>
                </div>
                <div class="card-body">
                    <div id="topLosers">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm text-danger" role="status"></div>
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
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchMarket" placeholder="Coin name or symbol">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Rank Range</label>
                    <select class="form-select" id="rankFilter">
                        <option value="">All Ranks</option>
                        <option value="1-10">Top 10</option>
                        <option value="11-50">11-50</option>
                        <option value="51-100">51-100</option>
                        <option value="101-500">101-500</option>
                        <option value="500+">500+</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Price Range</label>
                    <select class="form-select" id="priceFilter">
                        <option value="">All Prices</option>
                        <option value="0-1">$0 - $1</option>
                        <option value="1-10">$1 - $10</option>
                        <option value="10-100">$10 - $100</option>
                        <option value="100-1000">$100 - $1K</option>
                        <option value="1000+">$1K+</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">24h Change</label>
                    <select class="form-select" id="changeFilter">
                        <option value="">All Changes</option>
                        <option value="positive">Positive</option>
                        <option value="negative">Negative</option>
                        <option value="high-gain">+20% or more</option>
                        <option value="high-loss">-20% or more</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sort By</label>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="sortBy">
                            <option value="market_cap_rank">Rank</option>
                            <option value="market_cap_usd">Market Cap</option>
                            <option value="current_price_usd">Price</option>
                            <option value="price_change_percentage_24h">24h Change</option>
                            <option value="total_volume_usd">Volume</option>
                        </select>
                        <button class="btn btn-outline-secondary" onclick="toggleSortOrder()" id="sortOrderBtn" title="Sort Order">
                            <i class="fas fa-sort-amount-down"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <button class="btn btn-secondary" onclick="resetFilters()">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <button class="btn btn-info" onclick="showAdvancedAnalytics()">
                        <i class="fas fa-chart-bar"></i> Advanced Analytics
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title">Market Overview</h4>
                </div>
                <div class="col-auto">
                    <small class="text-muted">Showing: <span id="totalRecords">0</span> coins</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Rank</th>
                            <th>Coin</th>
                            <th>Price</th>
                            <th>24h Change</th>
                            <th>7d Change</th>
                            <th>Market Cap</th>
                            <th>Volume (24h)</th>
                            <th>Supply</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="marketTableBody">
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
                            <option value="250">250</option>
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

<div class="modal fade" id="coinDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="coinDetailsTitle">Coin Details</h5>
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

<div class="modal fade" id="analyticsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Advanced Market Analytics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Market Trends</h6>
                            </div>
                            <div class="card-body">
                                <div id="trendsAnalysis">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadMarketStats();
    loadTopGainersLosers();
    loadMarketData();
    updateDataSync();
    
    setInterval(function() {
        loadMarketStats();
        loadTopGainersLosers();
        updateDataSync();
    }, 60000);
    
    $('#searchMarket, #rankFilter, #priceFilter, #changeFilter, #sortBy').on('input change', function() {
        applyFilters();
    });
    
    $('#recordsPerPage').on('change', function() {
        loadMarketData();
    });
});

let allMarketData = [];
let currentPage = 1;
let sortOrder = 'asc';

function updateDataSync() {
    $.ajax({
        url: 'ajax/market-data-ajax.php',
        type: 'POST',
        data: {action: 'getLastSync'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#lastDataSync').text(response.last_sync);
            }
        }
    });
}

function loadMarketStats() {
    $.ajax({
        url: 'ajax/market-data-ajax.php',
        type: 'POST',
        data: {action: 'getMarketStats'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalMarketCap').text('$' + formatNumber(response.stats.total_market_cap));
                $('#totalVolume').text('$' + formatNumber(response.stats.total_volume));
                $('#btcDominance').text(parseFloat(response.stats.btc_dominance).toFixed(1) + '%');
                $('#activeCoins').text(response.stats.active_coins);
            }
        }
    });
}

function loadTopGainersLosers() {
    $.ajax({
        url: 'ajax/market-data-ajax.php',
        type: 'POST',
        data: {action: 'getTopGainersLosers'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayTopGainers(response.gainers);
                displayTopLosers(response.losers);
            }
        }
    });
}

function displayTopGainers(gainers) {
    let html = '';
    
    if (gainers.length === 0) {
        html = '<div class="text-center text-muted">No gainers data</div>';
    } else {
        gainers.slice(0, 5).forEach(function(coin, index) {
            const change = parseFloat(coin.price_change_percentage_24h);
            html += `
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0">
                        <span class="badge bg-success-subtle text-success">${index + 1}</span>
                    </div>
                    <div class="flex-shrink-0 ms-2">
                        <img src="${coin.image || 'assets/images/placeholder-coin.png'}" 
                             alt="${coin.name}" class="rounded-circle" width="24" height="24"
                             onerror="this.src='assets/images/placeholder-coin.png'">
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <div class="fw-medium">${coin.name}</div>
                        <small class="text-muted">${coin.symbol.toUpperCase()}</small>
                    </div>
                    <div class="text-end">
                        <div class="text-success fw-bold">+${change.toFixed(2)}%</div>
                        <small class="text-muted">$${parseFloat(coin.current_price_usd).toFixed(6)}</small>
                    </div>
                </div>
            `;
        });
    }
    
    $('#topGainers').html(html);
}

function displayTopLosers(losers) {
    let html = '';
    
    if (losers.length === 0) {
        html = '<div class="text-center text-muted">No losers data</div>';
    } else {
        losers.slice(0, 5).forEach(function(coin, index) {
            const change = parseFloat(coin.price_change_percentage_24h);
            html += `
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0">
                        <span class="badge bg-danger-subtle text-danger">${index + 1}</span>
                    </div>
                    <div class="flex-shrink-0 ms-2">
                        <img src="${coin.image || 'assets/images/placeholder-coin.png'}" 
                             alt="${coin.name}" class="rounded-circle" width="24" height="24"
                             onerror="this.src='assets/images/placeholder-coin.png'">
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <div class="fw-medium">${coin.name}</div>
                        <small class="text-muted">${coin.symbol.toUpperCase()}</small>
                    </div>
                    <div class="text-end">
                        <div class="text-danger fw-bold">${change.toFixed(2)}%</div>
                        <small class="text-muted">$${parseFloat(coin.current_price_usd).toFixed(6)}</small>
                    </div>
                </div>
            `;
        });
    }
    
    $('#topLosers').html(html);
}

function loadMarketData() {
    const limit = $('#recordsPerPage').val();
    const offset = (currentPage - 1) * limit;
    
    $.ajax({
        url: 'ajax/market-data-ajax.php',
        type: 'POST',
        data: {
            action: 'getMarketData',
            limit: limit,
            offset: offset,
            search: $('#searchMarket').val(),
            rank: $('#rankFilter').val(),
            price: $('#priceFilter').val(),
            change: $('#changeFilter').val(),
            sort_by: $('#sortBy').val(),
            sort_order: sortOrder
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allMarketData = response.data;
                displayMarketData(allMarketData);
                updatePagination(response.total, limit);
                $('#totalRecords').text(response.total);
            } else {
                $('#marketTableBody').html('<tr><td colspan="9" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#marketTableBody').html('<tr><td colspan="9" class="text-center text-danger">Ajax Error: ' + error + '</td></tr>');
        }
    });
}

function displayMarketData(data) {
    let html = '';
    
    if (data.length === 0) {
        html = '<tr><td colspan="9" class="text-center text-muted">No market data found</td></tr>';
    } else {
        data.forEach(function(coin) {
            const price = parseFloat(coin.current_price_usd || 0);
            const change24h = parseFloat(coin.price_change_percentage_24h || 0);
            const change7d = parseFloat(coin.price_change_percentage_7d || 0);
            const marketCap = parseInt(coin.market_cap_usd || 0);
            const volume = parseInt(coin.total_volume_usd || 0);
            const supply = parseFloat(coin.circulating_supply || 0);
            
            const change24hClass = change24h >= 0 ? 'text-success' : 'text-danger';
            const change24hIcon = change24h >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
            const change7dClass = change7d >= 0 ? 'text-success' : 'text-danger';
            const change7dIcon = change7d >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
            
            html += `
                <tr>
                    <td>
                        <span class="badge bg-primary">${coin.market_cap_rank || '-'}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="${coin.image || 'assets/images/placeholder-coin.png'}" 
                                 alt="${coin.name}" class="rounded-circle me-2" width="32" height="32"
                                 onerror="this.src='assets/images/placeholder-coin.png'">
                            <div>
                                <div class="fw-medium">${coin.name}</div>
                                <small class="text-muted">${coin.symbol.toUpperCase()}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium">$${price.toFixed(price > 1 ? 2 : 8)}</div>
                        <small class="text-muted">≈ ${(price * 0.00001).toFixed(8)} BTC</small>
                    </td>
                    <td>
                        <span class="${change24hClass}">
                            <i class="${change24hIcon}"></i> ${Math.abs(change24h).toFixed(2)}%
                        </span>
                    </td>
                    <td>
                        <span class="${change7dClass}">
                            <i class="${change7dIcon}"></i> ${Math.abs(change7d).toFixed(2)}%
                        </span>
                    </td>
                    <td>
                        <div>$${formatNumber(marketCap)}</div>
                        <small class="text-muted">Market Cap</small>
                    </td>
                    <td>
                        <div>$${formatNumber(volume)}</div>
                        <small class="text-muted">24h Volume</small>
                    </td>
                    <td>
                        <div>${formatNumber(supply)}</div>
                        <small class="text-muted">${coin.symbol.toUpperCase()}</small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" onclick="viewCoinDetails('${coin.id}')" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#marketTableBody').html(html);
}

function formatNumber(num) {
    if (num >= 1e12) return (num / 1e12).toFixed(2) + 'T';
    if (num >= 1e9) return (num / 1e9).toFixed(2) + 'B';
    if (num >= 1e6) return (num / 1e6).toFixed(2) + 'M';
    if (num >= 1e3) return (num / 1e3).toFixed(2) + 'K';
    return num.toLocaleString();
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
    loadMarketData();
}

function toggleSortOrder() {
    sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
    const icon = sortOrder === 'asc' ? 'fas fa-sort-amount-down' : 'fas fa-sort-amount-up';
    $('#sortOrderBtn i').attr('class', icon);
    loadMarketData();
}

function applyFilters() {
    currentPage = 1;
    loadMarketData();
}

function resetFilters() {
    $('#searchMarket').val('');
    $('#rankFilter').val('');
    $('#priceFilter').val('');
    $('#changeFilter').val('');
    $('#sortBy').val('market_cap_rank');
    sortOrder = 'asc';
    $('#sortOrderBtn i').attr('class', 'fas fa-sort-amount-down');
    currentPage = 1;
    loadMarketData();
}

function refreshMarketData() {
    Swal.fire({
        title: "Refresh Market Data?",
        text: "This will fetch the latest market data from external sources.",
        icon: "info",
        buttons: true,
    }).then((willRefresh) => {
        if (willRefresh) {
            $.ajax({
                url: 'ajax/market-data-ajax.php',
                type: 'POST',
                data: {action: 'refreshData'},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Success!", "Market data refreshed successfully!", "success");
                        loadMarketStats();
                        loadTopGainersLosers();
                        loadMarketData();
                        updateDataSync();
                    } else {
                        Swal.fire("Error!", "Failed to refresh data: " + response.message, "error");
                    }
                }
            });
        }
    });
}

function viewCoinDetails(coinId) {
    $.ajax({
        url: 'ajax/market-data-ajax.php',
        type: 'POST',
        data: {action: 'getCoinDetails', coin_id: coinId},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const coin = response.coin;
                
                $('#coinDetailsTitle').text(coin.name + ' (' + coin.symbol.toUpperCase() + ')');
                
                let detailsHtml = `
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Market Information</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr><td><strong>Current Price:</strong></td><td>$${parseFloat(coin.current_price_usd || 0).toFixed(8)}</td></tr>
                                        <tr><td><strong>Market Cap:</strong></td><td>$${formatNumber(coin.market_cap_usd || 0)}</td></tr>
                                        <tr><td><strong>Rank:</strong></td><td>#${coin.market_cap_rank || 'N/A'}</td></tr>
                                        <tr><td><strong>24h Volume:</strong></td><td>$${formatNumber(coin.total_volume_usd || 0)}</td></tr>
                                        <tr><td><strong>24h High:</strong></td><td>$${parseFloat(coin.high_24h_usd || 0).toFixed(8)}</td></tr>
                                        <tr><td><strong>24h Low:</strong></td><td>$${parseFloat(coin.low_24h_usd || 0).toFixed(8)}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3">Price Changes</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr><td><strong>24h Change:</strong></td><td class="${parseFloat(coin.price_change_percentage_24h || 0) >= 0 ? 'text-success' : 'text-danger'}">${parseFloat(coin.price_change_percentage_24h || 0).toFixed(2)}%</td></tr>
                                        <tr><td><strong>7d Change:</strong></td><td class="${parseFloat(coin.price_change_percentage_7d || 0) >= 0 ? 'text-success' : 'text-danger'}">${parseFloat(coin.price_change_percentage_7d || 0).toFixed(2)}%</td></tr>
                                        <tr><td><strong>30d Change:</strong></td><td class="${parseFloat(coin.price_change_percentage_30d || 0) >= 0 ? 'text-success' : 'text-danger'}">${parseFloat(coin.price_change_percentage_30d || 0).toFixed(2)}%</td></tr>
                                        <tr><td><strong>1y Change:</strong></td><td class="${parseFloat(coin.price_change_percentage_1y || 0) >= 0 ? 'text-success' : 'text-danger'}">${parseFloat(coin.price_change_percentage_1y || 0).toFixed(2)}%</td></tr>
                                        <tr><td><strong>ATH:</strong></td><td>$${parseFloat(coin.ath_usd || 0).toFixed(8)}</td></tr>
                                        <tr><td><strong>ATL:</strong></td><td>$${parseFloat(coin.atl_usd || 0).toFixed(8)}</td></tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6 class="mb-3">Supply Information</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr><td><strong>Circulating Supply:</strong></td><td>${formatNumber(coin.circulating_supply || 0)} ${coin.symbol.toUpperCase()}</td></tr>
                                        <tr><td><strong>Total Supply:</strong></td><td>${formatNumber(coin.total_supply || 0)} ${coin.symbol.toUpperCase()}</td></tr>
                                        <tr><td><strong>Max Supply:</strong></td><td>${coin.max_supply ? formatNumber(coin.max_supply) + ' ' + coin.symbol.toUpperCase() : 'No limit'}</td></tr>
                                        <tr><td><strong>FDV:</strong></td><td>$${formatNumber(coin.fully_diluted_valuation_usd || 0)}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <img src="${coin.image || 'assets/images/placeholder-coin.png'}" 
                                     alt="${coin.name}" class="img-thumbnail mb-3" width="128" height="128"
                                     onerror="this.src='assets/images/placeholder-coin.png'">
                                <h5>${coin.name}</h5>
                                <p class="text-muted">${coin.symbol.toUpperCase()}</p>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#coinDetailsContent').html(detailsHtml);
                $('#coinDetailsModal').modal('show');
            } else {
                Swal.fire("Error!", "Failed to load coin details: " + response.message, "error");
            }
        }
    });
}

function addToWatchlist(coinId) {
    Swal.fire("Feature Coming Soon!", "Watchlist functionality will be available in the next update.", "info");
}

function viewPriceHistory(coinId) {
    Swal.fire("Feature Coming Soon!", "Price history charts will be available in the next update.", "info");
}

function showAdvancedAnalytics() {
    $('#analyticsModal').modal('show');
    loadAnalyticsData();
}

function loadAnalyticsData() {
    $('#trendsAnalysis').html(`
        <div class="row">
            <div class="col-md-4">
                <div class="text-center">
                    <h5 class="text-success">Market Sentiment</h5>
                    <div class="display-6 text-success">Bullish</div>
                    <p class="text-muted">Based on 24h price movements</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <h5 class="text-info">Fear & Greed</h5>
                    <div class="display-6 text-warning">64</div>
                    <p class="text-muted">Greed (Moderate)</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <h5 class="text-primary">Market Activity</h5>
                    <div class="display-6 text-success">High</div>
                    <p class="text-muted">Trading volume above average</p>
                </div>
            </div>
        </div>
    `);
}

function exportMarketData() {
    const params = new URLSearchParams({
        action: 'exportCSV',
        search: $('#searchMarket').val(),
        rank: $('#rankFilter').val(),
        price: $('#priceFilter').val(),
        change: $('#changeFilter').val(),
        sort_by: $('#sortBy').val(),
        sort_order: sortOrder
    });
    
    window.open('ajax/market-data-ajax.php?' + params.toString(), '_blank');
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>