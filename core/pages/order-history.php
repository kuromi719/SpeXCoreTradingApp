<?php require __DIR__ . '/inc/app_header.php'; ?>

<div class="main-content">
    <div class="wrapper pb-2">
        
        <div class="d-flex flex-column animation-element in-view">
            <div class="tab-content card-bs">
                <h3 class="flex p-3">Order History</h3>
                
                <!-- Tabs -->
                <div class="flex p-2 p-sm-3 mb-4">
                    <div class="nav nav-tabs nav-justified card-line card-bs p-1" role="tablist">
                        <button class="nav-link fw-medium active" id="open-order-tab" data-bs-toggle="tab" data-bs-target="#open-order-tab-pane" type="button" aria-controls="open-order-tab-pane" aria-selected="true" role="tab">Open Orders</button>
                        <button class="nav-link fw-medium" id="order-history-tab" data-bs-toggle="tab" data-bs-target="#order-history-tab-pane" type="button" aria-controls="order-history-tab-pane" aria-selected="false" role="tab" tabindex="-1">Order History</button>
                        <button class="nav-link fw-medium" id="trade-history-tab" data-bs-toggle="tab" data-bs-target="#trade-history-tab-pane" type="button" aria-controls="trade-history-tab-pane" aria-selected="false" role="tab" tabindex="-1">Trade History</button>
                        <button class="nav-link fw-medium" id="auto-invest-tab" data-bs-toggle="tab" data-bs-target="#auto-invest-tab-pane" type="button" aria-controls="auto-invest-tab-pane" aria-selected="false" role="tab" tabindex="-1">Auto-Invest</button>
                    </div>
                </div>
                
                <!-- Meta -->
                <div class="row row-cols-auto justify-content-between align-items-center px-2 px-sm-4">
                    <!-- Search | Filter -->
                    <div class="d-sm-flex flex-row gap-2">
                        <!-- Filter -->
                        <div class="col mb-3">
                            <div class="d-flex gap-2">
                                <!-- Pair Filter -->
                                <div class="input-group">
                                    <label class="input-group-text">Pair</label>
                                    <select class="form-select text-sm crypt-grayscale-300" id="pairFilter">
                                        <option value="">All</option>
                                        <option value="BTCUSDT">BTC/USDT</option>
                                        <option value="ETHUSDT">ETH/USDT</option>
                                        <option value="ADAUSDT">ADA/USDT</option>
                                        <option value="DOTUSDT">DOT/USDT</option>
                                        <option value="LINKUSDT">LINK/USDT</option>
                                        <option value="SOLUSDT">SOL/USDT</option>
                                    </select>
                                </div>
                                <!-- Side Filter -->
                                <div class="input-group">
                                    <label class="input-group-text">Side</label>
                                    <select class="form-select text-sm crypt-grayscale-300" id="sideFilter">
                                        <option value="">All</option>
                                        <option value="BUY">Buy</option>
                                        <option value="SELL">Sell</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Checks -->
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="encrypt">
                            <label class="form-check-label text-light"><abbr title="Hide sensitive trading data">Hide all data</abbr></label>
                        </div>
                    </div>
                </div>
                
                <!-- Open Orders Table -->
                <div class="tab-pane fade active show" id="open-order-tab-pane" role="tabpanel" aria-labelledby="open-order-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr class="table-active mb-0">
                                    <th scope="col">Date</th>
                                    <th scope="col">Pair</th>
                                    <th scope="col">Order Type</th>
                                    <th scope="col">Side</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody id="openOrdersTable">
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Order History Table -->
                <div class="tab-pane fade" id="order-history-tab-pane" role="tabpanel" aria-labelledby="order-history-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr class="table-active mb-0">
                                    <th scope="col">Date</th>
                                    <th scope="col">Pair</th>
                                    <th scope="col">Order Type</th>
                                    <th scope="col">Side</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Avg Price</th>
                                    <th scope="col">Executed</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody id="orderHistoryTable">
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Trade History Table -->
                <div class="tab-pane fade" id="trade-history-tab-pane" role="tabpanel" aria-labelledby="trade-history-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-dark mb-0">
                            <thead>
                                <tr class="table-active mb-0">
                                    <th scope="col">Date</th>
                                    <th scope="col">Pair</th>
                                    <th scope="col">Side</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Fee</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Order ID</th>
                                </tr>
                            </thead>
                            <tbody id="tradeHistoryTable">
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Auto-Invest Table -->
                <div class="tab-pane fade" id="auto-invest-tab-pane" role="tabpanel" aria-labelledby="auto-invest-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-dark mb-0">
                            <thead>
                                <tr class="table-active mb-0">
                                    <th scope="col">Asset</th>
                                    <th scope="col">Auto-Subscribe</th>
                                    <th scope="col">Total Amount</th>
                                    <th scope="col">APR</th>
                                    <th scope="col">Total Reward</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="autoInvestTable">
                                <tr class="align-middle">
                                    <td colspan="6" class="no-orders text-center">
                                        <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                                        <p class="crypt-grayscale-600 pb-4">No records</p>
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

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

<script>
$(document).ready(function() {
    let currentFilters = {
        search: '',
        type: '',
        pair: '',
        side: '',
        encrypt: false,
        onlyActive: false
    };
    
    // Load initial data
    loadOpenOrders();
    
    // Tab switching
    $('[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('data-bs-target');
        switch(target) {
            case '#open-order-tab-pane':
                loadOpenOrders();
                break;
            case '#order-history-tab-pane':
                loadOrderHistory();
                break;
            case '#trade-history-tab-pane':
                loadTradeHistory();
                break;
            case '#auto-invest-tab-pane':
                loadAutoInvest();
                break;
        }
    });
    
    // Filter events
    $('#searchInput').on('input', debounce(function() {
        currentFilters.search = $(this).val();
        reloadCurrentTab();
    }, 500));
    
    $('#typeFilter, #pairFilter, #sideFilter').on('change', function() {
        currentFilters.type = $('#typeFilter').val();
        currentFilters.pair = $('#pairFilter').val();
        currentFilters.side = $('#sideFilter').val();
        reloadCurrentTab();
    });
    
    $('#encrypt').on('change', function() {
        currentFilters.encrypt = $(this).is(':checked');
        toggleEncryption(currentFilters.encrypt);
    });
    
    $('#onlyActive').on('change', function() {
        currentFilters.onlyActive = $(this).is(':checked');
        reloadCurrentTab();
    });
    
    // Auto refresh
    setInterval(function() {
        reloadCurrentTab();
    }, 30000);
    
    function reloadCurrentTab() {
        const activeTab = $('.nav-link.active').attr('data-bs-target');
        switch(activeTab) {
            case '#open-order-tab-pane':
                loadOpenOrders();
                break;
            case '#order-history-tab-pane':
                loadOrderHistory();
                break;
            case '#trade-history-tab-pane':
                loadTradeHistory();
                break;
        }
    }
    
    function loadOpenOrders() {
        $.ajax({
            url: 'core/ajax/order-history/getOpenOrders.php',
            type: 'GET',
            data: currentFilters,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderOpenOrders(response.data);
                } else {
                    showError('#openOrdersTable', response.message);
                }
            },
            error: function() {
                showError('#openOrdersTable', 'Failed to load open orders');
            }
        });
    }
    
    function loadOrderHistory() {
        $.ajax({
            url: 'core/ajax/order-history/getOrderHistory.php',
            type: 'GET',
            data: currentFilters,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderOrderHistory(response.data);
                } else {
                    showError('#orderHistoryTable', response.message);
                }
            },
            error: function() {
                showError('#orderHistoryTable', 'Failed to load order history');
            }
        });
    }
    
    function loadTradeHistory() {
        $.ajax({
            url: 'core/ajax/order-history/getTradeHistory.php',
            type: 'GET',
            data: currentFilters,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderTradeHistory(response.data);
                } else {
                    showError('#tradeHistoryTable', response.message);
                }
            },
            error: function() {
                showError('#tradeHistoryTable', 'Failed to load trade history');
            }
        });
    }
    
    function loadAutoInvest() {
        // Auto-invest functionality placeholder
        $('#autoInvestTable').html(`
            <tr class="align-middle">
                <td colspan="6" class="no-orders text-center">
                    <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                    <p class="crypt-grayscale-600 pb-4">Auto-Invest feature coming soon</p>
                </td>
            </tr>
        `);
    }
    
    function renderOpenOrders(orders) {
        if (!orders || orders.length === 0) {
            $('#openOrdersTable').html(`
                <tr class="align-middle">
                    <td colspan="8" class="no-orders text-center">
                        <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                        <p class="crypt-grayscale-600 pb-3">No open orders</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        let html = '';
        orders.forEach(order => {
            const sideClass = order.side === 'BUY' ? 'text-primary' : 'text-danger';
            const total = (parseFloat(order.price) * parseFloat(order.amount)).toFixed(2);
            
            html += `
                <tr class="align-middle">
                    <td class="fw-medium">${formatDate(order.created_at)}</td>
                    <td class="encrypted fw-medium" data-content="${order.pair}">${order.pair}</td>
                    <td class="fw-medium">${order.type}</td>
                    <td class="fw-medium ${sideClass}">${order.side}</td>
                    <td class="encrypted fw-medium" data-content="${order.price}">${parseFloat(order.price).toFixed(2)}</td>
                    <td class="encrypted fw-medium" data-content="${order.amount}">${parseFloat(order.amount).toFixed(6)}</td>
                    <td class="encrypted fw-medium" data-content="${total} USDT">${total} USDT</td>
                </tr>
            `;
        });
        
        $('#openOrdersTable').html(html);
        if (currentFilters.encrypt) toggleEncryption(true);
    }
    
    function renderOrderHistory(orders) {
        if (!orders || orders.length === 0) {
            $('#orderHistoryTable').html(`
                <tr class="align-middle">
                    <td colspan="9" class="no-orders text-center">
                        <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                        <p class="crypt-grayscale-600 pb-3">No order history</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        let html = '';
        orders.forEach(order => {
            const sideClass = order.side === 'BUY' ? 'text-primary' : 'text-danger';
            const statusClass = getStatusClass(order.status);
            const rowClass = order.status === 'CANCELLED' || order.status === 'EXPIRED' ? 'table-row-disabled' : '';
            
            html += `
                <tr class="align-middle ${rowClass}">
                    <td class="fw-medium">${formatDate(order.created_at)}</td>
                    <td class="encrypted fw-medium" data-content="${order.pair}">${order.pair}</td>
                    <td class="fw-medium">${order.type}</td>
                    <td class="fw-medium ${sideClass}">${order.side}</td>
                    <td class="encrypted fw-medium" data-content="${order.price}">${order.price ? parseFloat(order.price).toFixed(2) : '--'}</td>
                    <td class="encrypted fw-medium" data-content="${order.avg_price}">${order.avg_price ? parseFloat(order.avg_price).toFixed(2) : '0.00'}</td>
                    <td class="encrypted fw-medium" data-content="${order.executed_qty}">${parseFloat(order.executed_qty).toFixed(6)}</td>
                    <td class="encrypted fw-medium" data-content="${order.amount}">${parseFloat(order.amount).toFixed(6)}</td>
                    <td>
                        <a class="${statusClass} fw-bold ms-3" href="#!">${order.status}</a>
                    </td>
                </tr>
            `;
        });
        
        $('#orderHistoryTable').html(html);
        if (currentFilters.encrypt) toggleEncryption(true);
    }
    
    function renderTradeHistory(trades) {
        if (!trades || trades.length === 0) {
            $('#tradeHistoryTable').html(`
                <tr class="align-middle">
                    <td colspan="8" class="no-orders text-center">
                        <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                        <p class="crypt-grayscale-600 pb-3">No trade history</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        let html = '';
        trades.forEach(trade => {
            const sideClass = trade.side === 'BUY' ? 'text-primary' : 'text-danger';
            const total = (parseFloat(trade.price) * parseFloat(trade.amount)).toFixed(2);
            
            html += `
                <tr class="align-middle">
                    <td class="fw-medium">${formatDate(trade.created_at)}</td>
                    <td class="encrypted fw-medium" data-content="${trade.pair}">${trade.pair}</td>
                    <td class="fw-medium ${sideClass}">${trade.side}</td>
                    <td class="encrypted fw-medium" data-content="${trade.price}">${parseFloat(trade.price).toFixed(2)}</td>
                    <td class="encrypted fw-medium" data-content="${trade.amount}">${parseFloat(trade.amount).toFixed(6)}</td>
                    <td class="encrypted fw-medium" data-content="${trade.fee}">${parseFloat(trade.fee).toFixed(6)} USDT</td>
                    <td class="encrypted fw-medium" data-content="${total}">${total} USDT</td>
                    <td class="fw-medium">${trade.order_id}</td>
                </tr>
            `;
        });
        
        $('#tradeHistoryTable').html(html);
        if (currentFilters.encrypt) toggleEncryption(true);
    }
    
    function showError(tableId, message) {
        const colCount = $(tableId).closest('table').find('thead th').length;
        $(tableId).html(`
            <tr>
                <td colspan="${colCount}" class="text-center py-4 text-danger">
                    ${message}
                </td>
            </tr>
        `);
    }
    
    function toggleEncryption(encrypt) {
        $('.encrypted').each(function() {
            if (encrypt) {
                $(this).text('****');
            } else {
                $(this).text($(this).data('content'));
            }
        });
    }
    
    function getStatusClass(status) {
        switch(status) {
            case 'COMPLETED':
            case 'FILLED':
                return 'link-primary';
            case 'CANCELLED':
            case 'EXPIRED':
                return 'link-secondary';
            default:
                return 'link-warning';
        }
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: '2-digit',
            day: '2-digit',
            year: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
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

function cancelOrder(orderId) {
    if (!confirm('Are you sure you want to cancel this order?')) {
        return;
    }
    
    $.ajax({
        url: 'core/ajax/trade/cancel_order.php',
        type: 'POST',
        data: JSON.stringify({orderId: orderId}),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success','Order cancelled successfully!');
                loadOpenOrders();
            } else {
                JSPop('error','Error');
            }
        },
        error: function() {
            JSPop('error','Failed to cancel order');
        }
    });
}
</script>

<?php require __DIR__ . '/inc/app_footer.php'; ?>