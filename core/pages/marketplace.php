<?php require __DIR__ . '/inc/app_header.php'; ?>

<div class="row g-2 animation-element">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold crypt-grayscale-100 mb-1">Cryptocurrency Marketplace</h3>
                <p class="crypt-grayscale-600 mb-0">Discover, analyze and trade digital assets</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-2 animation-element">
    <div class="col-12 col-xl-8">
        <div class="card-bs p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M7 12H17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M10 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span id="sortFilterText">Market Cap</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-sort="market_cap">Market Cap</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="volume">Volume</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="price_high">Price (High to Low)</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="price_low">Price (Low to High)</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="change_24h">24h Change</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search coins by name or symbol...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="minPriceFilter" placeholder="Min Price" step="0.01">
                        <span class="input-group-text">-</span>
                        <input type="number" class="form-control" id="maxPriceFilter" placeholder="Max Price" step="0.01">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr class='mb-0'>
                            <th scope="col" class="underline">Coin</th>
                            <th scope="col">Price</th>
                            <th scope="col">24h Change</th>
                            <th scope="col">Market Cap</th>
                            <th scope="col">Volume (24h)</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody id="marketplaceTableBody">
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="crypt-grayscale">
                    <small>Showing <span id="showingCount">0</span> of <span id="totalCount">0</span> cryptocurrencies</small>
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="paginationContainer">
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="row g-2">
            <div class="col-12">
                <div class="card-bs p-3">
                    <h5 class="fw-bold crypt-grayscale-100 mb-3">Market Statistics</h5>
                    <div id="marketStatsContainer">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card-bs p-3">
                    <h5 class="fw-bold crypt-grayscale-100 mb-3">Top Gainers</h5>
                    <div id="topGainersContainer">
                        <div class="text-center py-3">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card-bs p-3">
                    <h5 class="fw-bold crypt-grayscale-100 mb-3">Top Losers</h5>
                    <div id="topLosersContainer">
                        <div class="text-center py-3">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Loading...</span>
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
    let currentPage = 1;
    let currentFilters = {
        category: '',
        sort: 'market_cap',
        search: '',
        minPrice: '',
        maxPrice: ''
    };
    
    const itemsPerPage = 50;
    
    loadMarketplace();
    loadMarketStats();
    loadTopGainers();
    loadTopLosers();
    loadCategories();
    
    $('#searchInput').on('input', debounce(function() {
        currentFilters.search = $(this).val();
        currentPage = 1;
        loadMarketplace();
    }, 500));
    
    $('#minPriceFilter, #maxPriceFilter').on('input', debounce(function() {
        currentFilters.minPrice = $('#minPriceFilter').val();
        currentFilters.maxPrice = $('#maxPriceFilter').val();
        currentPage = 1;
        loadMarketplace();
    }, 800));
    
    $(document).on('click', '[data-category]', function(e) {
        e.preventDefault();
        currentFilters.category = $(this).data('category');
        $('#categoryFilterText').text($(this).text());
        currentPage = 1;
        loadMarketplace();
    });
    
    $(document).on('click', '[data-sort]', function(e) {
        e.preventDefault();
        currentFilters.sort = $(this).data('sort');
        $('#sortFilterText').text($(this).text());
        currentPage = 1;
        loadMarketplace();
    });
    
    $('#marketOverviewBtn').on('click', function() {
        loadMarketOverview();
        $('#marketOverviewModal').modal('show');
    });
    
    $(document).on('click', '.watchlist-btn', function(e) {
        e.preventDefault();
        const coinId = $(this).data('coin-id');
        const btn = $(this);
    });
    
    $(document).on('click', '[data-page]', function(e) {
        e.preventDefault();
        currentPage = parseInt($(this).data('page'));
        loadMarketplace();
    });
    
    setInterval(function() {
        loadMarketplace();
        loadMarketStats();
        loadTopGainers();
        loadTopLosers();
    }, 30000);
    
    function loadMarketplace() {
        showTableLoading();
        
        $.ajax({
            url: 'core/ajax/marketplace/getMarketData.php',
            type: 'GET',
            data: {
                page: currentPage,
                limit: itemsPerPage,
                ...currentFilters
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderMarketplaceTable(response.data);
                    updatePagination(response.pagination);
                    updateCounters(response.pagination);
                } else {
                    showTableError(response.error);
                }
            },
            error: function() {
                showTableError('Failed to load marketplace data');
            }
        });
    }
    
    function loadMarketStats() {
        $.ajax({
            url: 'core/ajax/marketplace/getMarketStats.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderMarketStats(response.data);
                }
            },
            error: function() {
                $('#marketStatsContainer').html('<p class="text-danger text-center">Failed to load market stats</p>');
            }
        });
    }
    
    function loadTopGainers() {
        $.ajax({
            url: 'core/ajax/marketplace/getTopGainers.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderTopGainers(response.data);
                }
            },
            error: function() {
                $('#topGainersContainer').html('<p class="text-danger text-center">Failed to load data</p>');
            }
        });
    }
    
    function loadTopLosers() {
        $.ajax({
            url: 'core/ajax/marketplace/getTopLosers.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderTopLosers(response.data);
                }
            },
            error: function() {
                $('#topLosersContainer').html('<p class="text-danger text-center">Failed to load data</p>');
            }
        });
    }
    
    function loadCategories() {
        $.ajax({
            url: 'core/ajax/marketplace/getCategories.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderCategories(response.data);
                }
            }
        });
    }
    
    function loadMarketOverview() {
        $.ajax({
            url: 'core/ajax/marketplace/getMarketOverview.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderMarketOverview(response.data);
                }
            }
        });
    }
    
    function renderMarketplaceTable(coins) {
        let html = '';
        
        coins.forEach((coin, index) => {
            const changeClass24h = coin.price_change_percentage_24h >= 0 ? 'text-success' : 'text-danger';
            const changeClass7d = coin.price_change_percentage_7d >= 0 ? 'text-success' : 'text-danger';
            const changeSign24h = coin.price_change_percentage_24h >= 0 ? '+' : '';
            const changeSign7d = coin.price_change_percentage_7d >= 0 ? '+' : '';
            
            const rank = ((currentPage - 1) * itemsPerPage) + index + 1;
            
            html += `
            <tr class="align-middle">
                <td>
                    <div class="d-flex align-items-center gap-2">
                        ${coin.logo ? `<img src="${coin.logo}" alt="${coin.symbol}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">` : '<div style="width: 32px; height: 32px; border-radius: 50%; background: var(--bs-secondary);"></div>'}
                        <div class='d-flex flex-row gap-2'>
                            <h6 class="fw-bold mb-0 crypt-grayscale-100">${coin.symbol.toUpperCase()}</h6>
                            <small class="crypt-grayscale-600 d-none d-sm-block">${coin.name}</small>
                        </div>
                    </div>
                </td>
                <td class="fw-medium crypt-grayscale-100">$${formatPrice(coin.current_price_usd)}</td>
                <td class="${changeClass24h} fw-medium d-none d-md-table-cell">${changeSign24h}${formatPercentage(coin.price_change_percentage_24h)}%</td>
                <td class="fw-medium crypt-grayscale-100 d-none d-lg-table-cell">$${formatLargeNumber(coin.market_cap_usd)}</td>
                <td class="fw-medium crypt-grayscale-100 d-none d-xl-table-cell">$${formatLargeNumber(coin.total_volume_usd)}</td>
                <td>
                    <div class="d-flex gap-2 align-items-center">
                       <a href="?r=trade&coin=${coin.id}" class="text-danger text-decoration-none fw-bold">Trade</a>
                    </div>
                </td>
            </tr>`;
        });
        
        $('#marketplaceTableBody').html(html);
    }
    
    function renderMarketStats(stats) {
        const html = `
            <div class="row g-2">
                <div class="col-6">
                    <div class="text-center p-2">
                        <div class="small crypt-grayscale-600">Total Market Cap</div>
                        <div class="fw-bold text-primary">$${formatLargeNumber(stats.total_market_cap)}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-2">
                        <div class="small crypt-grayscale-600">24h Volume</div>
                        <div class="fw-bold text-info">$${formatLargeNumber(stats.total_volume_24h)}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-2">
                        <div class="small crypt-grayscale-600">Gainers</div>
                        <div class="fw-bold text-success">${stats.gainers_count}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-2">
                        <div class="small crypt-grayscale-600">Losers</div>
                        <div class="fw-bold text-danger">${stats.losers_count}</div>
                    </div>
                </div>
            </div>
        `;
        
        $('#marketStatsContainer').html(html);
    }
    
    function renderTopGainers(gainers) {
        let html = '';
        
        gainers.slice(0, 5).forEach(coin => {
            html += `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-success bg-opacity-10 rounded">
                <div class="d-flex align-items-center gap-2">
                    ${coin.logo ? `<img src="${coin.logo}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">` : '<div style="width: 24px; height: 24px; border-radius: 50%; background: var(--bs-secondary);"></div>'}
                    <div>
                        <div class="fw-bold small crypt-grayscale-100">${coin.symbol.toUpperCase()}</div>
                        <div class="small crypt-grayscale-600">$${formatPrice(coin.current_price_usd)}</div>
                    </div>
                </div>
                <div class="text-success fw-bold small">
                    +${formatPercentage(coin.change_percent)}%
                </div>
            </div>`;
        });
        
        $('#topGainersContainer').html(html);
    }
    
    function renderTopLosers(losers) {
        let html = '';
        
        losers.slice(0, 5).forEach(coin => {
            html += `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-danger bg-opacity-10 rounded">
                <div class="d-flex align-items-center gap-2">
                    ${coin.logo ? `<img src="${coin.logo}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">` : '<div style="width: 24px; height: 24px; border-radius: 50%; background: var(--bs-secondary);"></div>'}
                    <div>
                        <div class="fw-bold small crypt-grayscale-100">${coin.symbol.toUpperCase()}</div>
                        <div class="small crypt-grayscale-600">$${formatPrice(coin.current_price_usd)}</div>
                    </div>
                </div>
                <div class="text-danger fw-bold small">
                    ${formatPercentage(coin.change_percent)}%
                </div>
            </div>`;
        });
        
        $('#topLosersContainer').html(html);
    }
    
    function renderCategories(categories) {
        let html = '<li><a class="dropdown-item" href="#" data-category="">All Categories</a></li>';
        
        categories.forEach(category => {
            html += `<li><a class="dropdown-item" href="#" data-category="${category}">${category}</a></li>`;
        });
        
        $('#categoryDropdown').html(html);
    }
    
    function renderMarketOverview(data) {
        $('#totalMarketCap').text('$' + formatLargeNumber(data.overview.total_market_cap));
        $('#totalVolume').text('$' + formatLargeNumber(data.overview.total_volume_24h));
        $('#totalCoins').text(data.overview.total_coins);
        
        const sentiment = data.overview.avg_change_24h >= 0 ? 'Bullish' : 'Bearish';
        const sentimentClass = data.overview.avg_change_24h >= 0 ? 'text-success' : 'text-danger';
        $('#marketSentiment').html(`<span class="${sentimentClass}">${sentiment}</span>`);
    }
    
    function updatePagination(pagination) {
        if (pagination.total_pages <= 1) {
            $('#paginationContainer').empty();
            return;
        }
        
        let html = '';
        const maxVisible = 5;
        const start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        const end = Math.min(pagination.total_pages, start + maxVisible - 1);
        
        if (currentPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage - 1}">‹</a></li>`;
        }
        
        for (let i = start; i <= end; i++) {
            const activeClass = i === currentPage ? 'active' : '';
            html += `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
        
        if (currentPage < pagination.total_pages) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage + 1}">›</a></li>`;
        }
        
        $('#paginationContainer').html(html);
    }
    
    function updateCounters(pagination) {
        const start = ((currentPage - 1) * itemsPerPage) + 1;
        const end = Math.min(currentPage * itemsPerPage, pagination.total_items);
        $('#showingCount').text(`${start}-${end}`);
        $('#totalCount').text(pagination.total_items);
    }
    
    function showTableLoading() {
        $('#marketplaceTableBody').html(`
            <tr><td colspan="8" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0 crypt-grayscale-600">Loading market data...</p>
            </td></tr>
        `);
    }
    
    function showTableError(message) {
        $('#marketplaceTableBody').html(`
            <tr><td colspan="8" class="text-center py-4">
                <div class="text-danger mb-2">⚠️</div>
                <p class="text-danger mb-0">${message}</p>
            </td></tr>
        `);
    }
    
    function formatPrice(price) {
        if (price >= 1) {
            return parseFloat(price).toFixed(2);
        } else if (price >= 0.01) {
            return parseFloat(price).toFixed(4);
        } else {
            return parseFloat(price).toFixed(8);
        }
    }
    
    function formatPercentage(percent) {
        return parseFloat(percent).toFixed(2);
    }
    
    function formatLargeNumber(num) {
        if (num >= 1e12) {
            return (num / 1e12).toFixed(2) + 'T';
        } else if (num >= 1e9) {
            return (num / 1e9).toFixed(2) + 'B';
        } else if (num >= 1e6) {
            return (num / 1e6).toFixed(2) + 'M';
        } else if (num >= 1e3) {
            return (num / 1e3).toFixed(2) + 'K';
        }
        return parseFloat(num).toFixed(2);
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
});
</script>

<?php require __DIR__ . '/inc/app_footer.php'; ?>