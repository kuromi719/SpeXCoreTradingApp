<?php require __DIR__ . '/inc/assets_header.php'; ?>

<div class="main-content">
    <div class="wrapper pb-2">
        
        <div class="card-bs p-3 p-sm-4 animation-element in-view">
            <!-- Total balance -->
            <div class="d-sm-flex flex-row justify-content-between gap-2">
                <!-- Head -->
                <div class="col-md-3 mb-4">
                    <h3 class="fw-medium">Futures Account</h3>
                </div>
                <div class="col-auto mb-4">
                    <div class="d-sm-flex gap-2">
                        <a class="btn btn-editor btn-primary mb-2" href="?r=trade">Trade Futures</a>
                        <button class="btn btn-editor btn-success mb-2" onclick="openFuturesModal()">Open Future</button>
                        <a class="btn btn-editor btn-danger mb-2" href="#!" onclick="closeAllPositions()">Close All</a>
                    </div>
                </div>
            </div>
            
            <!-- Account Data -->
            <div class="row g-2 card-border mb-4">
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3">
                    <div class="card-bs border-secondary border-end border-opacity-50 p-3 me-5">
                        <div class="d-flex gap-1 ">
                            <p class="crypt-grayscale-500">Total Margin</p>
                        </div>
                        <div class="d-flex pb-3 border-secondary border-end border-opacity-50">
                            <h3 class="fw-bold encrypted mb-0" data-content="0.00" id="totalMargin">0.00</h3>
                        </div>
                        <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="totalMarginUSD">≈ 0.00 USD</p>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3">
                    <div class="card-bs p-3">
                        <p class="crypt-grayscale-500">Unrealized PnL</p>
                        <div class="d-flex flex-row align-items-center gap-2 pb-3">
                            <h3 class="fw-bold encrypted mb-0" data-content="0 (0.00%)" id="unrealizedPnL">0 <span class="fs-5">(0.00%)</span></h3>
                            <div>
                                <a href="#" class="badge bg-info text-dark col-auto">Risk analysis →</a>
                            </div>
                        </div>
                        <p class="crypt-grayscale-700 encrypted" data-content="+0.00 USD" id="unrealizedPnLUSD">+0.00 USD</p>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 col-auto">
                    <div class="card-bs p-3">
                        <p class="crypt-grayscale-500">Available Balance</p>
                        <div class="d-flex pb-3">
                            <h3 class="fw-bold encrypted mb-0" data-content="0.00 USDT" id="availableBalance">0.00 <span class="fs-5">USDT</span></h3>
                        </div>
                        <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="availableBalanceUSD">≈ 0.00 USD</p>
                    </div>
                </div>
            </div>
            
            <!-- Positions -->
            <div class="tab-content card-border overflow-hidden">
                <div class="d-sm-flex flex-row align-items-center p-3 gap-3">
                    <!-- Search -->
                    <div class="col-md-3 mb-2">
                        <input type="text" class="search form-control text-sm" placeholder="Search position" id="positionSearch">
                    </div>
                    <!-- Checks -->
                    <div class="col-auto d-flex gap-3 align-items-center mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="hideSmallPositions">
                            <label class="form-check-label text-light" for="hideSmallPositions">
                                <abbr title="Positions valued less than $10.">Hide small positions</abbr>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="onlyLongPositions">
                            <label class="form-check-label text-light" for="onlyLongPositions">Only LONG</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="onlyShortPositions">
                            <label class="form-check-label text-light" for="onlyShortPositions">Only SHORT</label>
                        </div>
                    </div>
                </div>
                
                <!-- Alert -->
                <div class="px-3 mb-3">
                    <div class="alert alert-warning d-flex p-2" role="alert">
                        <svg class="flex-shrink-0 me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.4" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor"></path>
                            <path d="M12 13.75C12.41 13.75 12.75 13.41 12.75 13V8C12.75 7.59 12.41 7.25 12 7.25C11.59 7.25 11.25 7.59 11.25 8V13C11.25 13.41 11.59 13.75 12 13.75Z" fill="currentColor"></path>
                            <path d="M12.92 15.6199C12.87 15.4999 12.8 15.3899 12.71 15.2899C12.61 15.1999 12.5 15.1299 12.38 15.0799C12.14 14.9799 11.86 14.9799 11.62 15.0799C11.5 15.1299 11.39 15.1999 11.29 15.2899C11.2 15.3899 11.13 15.4999 11.08 15.6199C11.03 15.7399 11 15.8699 11 15.9999C11 16.1299 11.03 16.2599 11.08 16.3799C11.13 16.5099 11.2 16.6099 11.29 16.7099C11.39 16.7999 11.5 16.8699 11.62 16.9199C11.74 16.9699 11.87 16.9999 12 16.9999C12.13 16.9999 12.26 16.9699 12.38 16.9199C12.5 16.8699 12.61 16.7999 12.71 16.7099C12.8 16.6099 12.87 16.5099 12.92 16.3799C12.97 16.2599 13 16.1299 13 15.9999C13 15.8699 12.97 15.7399 12.92 15.6199Z" fill="currentColor"></path>
                        </svg>
                        <p class="mb-0">
                            <strong>Risk Warning:</strong> Futures trading involves high risk. Only trade with funds you can afford to lose.
                        </p>
                    </div>
                </div>
                
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <tr class="table-active mb-0">
                                <th scope="col" class="underline">Symbol</th>
                                <th scope="col" class="underline">Position</th>
                                <th scope="col" class="underline">Size</th>
                                <th scope="col" class="underline">Entry Price</th>
                                <th scope="col" class="underline">Mark Price</th>
                                <th scope="col" class="underline">Margin</th>
                                <th scope="col" class="underline">Unrealized PnL</th>
                                <th scope="col" class="underline">Liquidation</th>
                                <th scope="col" class="underline">Action</th>
                            </tr>
                        </thead>
                        <tbody id="futuresPositionsTable">
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
        </div>
    </div>
</div>

<!-- Open Future Modal -->
<div class="modal fade" id="openFutureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Open Futures Position</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-light">Select Coin</label>
                        <select class="form-select" id="futuresCoinSelect">
                            <option value="">Select a coin...</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-light">Position Type</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success flex-fill" id="longBtn" onclick="selectPositionType('LONG')">LONG</button>
                            <button type="button" class="btn btn-danger flex-fill" id="shortBtn" onclick="selectPositionType('SHORT')">SHORT</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-light">Leverage</label>
                        <select class="form-select" id="leverageSelect">
                            <option value="2">2x</option>
                            <option value="5">5x</option>
                            <option value="10" selected>10x</option>
                            <option value="20">20x</option>
                            <option value="50">50x</option>
                            <option value="100">100x</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-light">Margin Amount (USDT)</label>
                        <input type="number" class="form-control" id="marginAmount" placeholder="0.00" step="0.01" min="1">
                        <small class="text-muted">Min: $1.00</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card border-secondary">
                            <div class="card-body">
                                <h6 class="card-title text-light">Position Summary</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Position Size:</small>
                                        <div id="positionSize">0.00</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Liquidation Price:</small>
                                        <div id="liquidationPrice">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="openFuturesPosition()" id="openPositionBtn" disabled>Open Position</button>
            </div>
        </div>
    </div>
</div>

<!-- Close Position Modal -->
<div class="modal fade" id="closePositionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-bs">
            <div class="modal-header">
                <h5 class="modal-title text-light">Close Position</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="closePositionForm">
                    <div class="mb-3">
                        <label class="form-label text-light">Position</label>
                        <input type="text" class="form-control" id="closePositionSymbol" readonly>
                        <input type="hidden" id="closePositionId">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Close Percentage</label>
                        <input type="range" class="form-range" id="closePercentage" min="1" max="100" value="100" oninput="updateCloseSize()">
                        <div class="d-flex justify-content-between">
                            <small>1%</small>
                            <span id="closePercentageValue">100%</span>
                            <small>100%</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Size to Close</label>
                        <input type="number" class="form-control" id="closeSize" step="0.00000001" readonly>
                        <small class="text-muted">Original Size: <span id="originalSizeDisplay"></span></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Estimated PnL</label>
                        <input type="text" class="form-control" id="estimatedPnL" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitClosePosition()">Close Position</button>
            </div>
        </div>
    </div>
</div>

<script>
let allPositions = [];
let availableCoins = [];
let selectedCoin = null;
let selectedPositionType = null;
let currentPrice = 0;

$(document).ready(function() {
    loadFuturesData();
    loadAvailableCoins();
    
    // Auto refresh
    setInterval(loadFuturesData, 10000);
    
    // Event handlers
    $('#encrypt').on('click', function(e) {
        e.preventDefault();
        toggleEncryption();
    });
    
    $('#positionSearch').on('input', function() {
        filterPositions();
    });
    
    $('#hideSmallPositions, #onlyLongPositions, #onlyShortPositions').on('change', function() {
        filterPositions();
    });
    
    $('#closePercentage').on('input', function() {
        updateCloseSize();
    });
    
    $('#inputGroupFuturesCoin').on('change', function() {
        updateCurrencyDisplay();
    });
    
    // Open Future Modal events
    $('#futuresCoinSelect').on('change', function() {
        const coinId = $(this).val();
        if (coinId) {
            selectedCoin = availableCoins.find(coin => coin.id === coinId);
            updatePositionCalculations();
        }
    });
    
    $('#leverageSelect, #marginAmount').on('change input', function() {
        updatePositionCalculations();
    });
});

function loadAvailableCoins() {
    $.ajax({
        url: 'core/ajax/assets/getAvailableCoins.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                availableCoins = response.data;
                renderCoinSelect(response.data);
            }
        },
        error: function() {
            console.error('Error loading available coins');
        }
    });
}

function renderCoinSelect(coins) {
    let html = '<option value="">Select a coin...</option>';
    coins.forEach(coin => {
        html += `<option value="${coin.id}" data-price="${coin.current_price_usd}">
                    ${coin.symbol.toUpperCase()} - $${parseFloat(coin.current_price_usd).toFixed(2)}
                 </option>`;
    });
    $('#futuresCoinSelect').html(html);
}

function openFuturesModal() {
    $('#openFutureModal').modal('show');
    selectedCoin = null;
    selectedPositionType = null;
    $('#longBtn, #shortBtn').removeClass('active');
    $('#openPositionBtn').prop('disabled', true);
    $('#marginAmount').val('');
    updatePositionCalculations();
}

function selectPositionType(type) {
    selectedPositionType = type;
    $('#longBtn, #shortBtn').removeClass('active');
    $('#' + type.toLowerCase() + 'Btn').addClass('active');
    updateOpenButtonState();
    updatePositionCalculations();
}

function updateOpenButtonState() {
    const canOpen = selectedCoin && selectedPositionType && $('#marginAmount').val() > 0;
    $('#openPositionBtn').prop('disabled', !canOpen);
}

function updatePositionCalculations() {
    if (!selectedCoin) {
        $('#positionSize').text('0.00');
        $('#liquidationPrice').text('0.00');
        return;
    }
    
    const marginAmount = parseFloat($('#marginAmount').val()) || 0;
    const leverage = parseFloat($('#leverageSelect').val()) || 1;
    const price = parseFloat(selectedCoin.current_price_usd) || 0;
    
    if (marginAmount > 0 && price > 0) {
        const positionValue = marginAmount * leverage;
        const positionSize = positionValue / price;
        
        // Simple liquidation price calculation
        let liquidationPrice = 0;
        if (selectedPositionType === 'LONG') {
            liquidationPrice = price - (marginAmount / positionSize);
        } else if (selectedPositionType === 'SHORT') {
            liquidationPrice = price + (marginAmount / positionSize);
        }
        
        $('#positionSize').text(positionSize.toFixed(6) + ' ' + selectedCoin.symbol.toUpperCase());
        $('#liquidationPrice').text('$' + Math.max(0, liquidationPrice).toFixed(2));
    } else {
        $('#positionSize').text('0.00');
        $('#liquidationPrice').text('0.00');
    }
    
    updateOpenButtonState();
}

function openFuturesPosition() {
    if (!selectedCoin || !selectedPositionType) {
        JSPop('error', 'Please select coin and position type');
        return;
    }
    
    const marginAmount = parseFloat($('#marginAmount').val());
    if (marginAmount < 1) {
        JSPop('error', 'Minimum margin amount is $1.00');
        return;
    }
    
    const openBtn = $('#openPositionBtn');
    if (openBtn.prop('disabled')) {
        return; // Already processing
    }
    openBtn.prop('disabled', true).text('Opening...');
    
    const leverage = parseFloat($('#leverageSelect').val());
    const price = parseFloat(selectedCoin.current_price_usd);
    const positionValue = marginAmount * leverage;
    const positionSize = positionValue / price;
    
    // Calculate liquidation price
    let liquidationPrice = 0;
    if (selectedPositionType === 'LONG') {
        liquidationPrice = price - (marginAmount / positionSize);
    } else {
        liquidationPrice = price + (marginAmount / positionSize);
    }
    
    const positionData = {
        coin_id: selectedCoin.id,
        position_type: selectedPositionType,
        position_size: positionSize,
        entry_price: price,
        margin_amount: marginAmount,
        leverage: leverage,
        liquidation_price: Math.max(0, liquidationPrice)
    };
    
    $.ajax({
        url: 'core/ajax/assets/openFuturesPosition.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(positionData),
        dataType: 'json',
        timeout: 30000,
        success: function(response) {
            
            if (response && response.success === true) {
                JSPop('success', 'Futures position opened successfully!');
                $('#openFutureModal').modal('hide');
                loadFuturesData();
                
                selectedCoin = null;
                selectedPositionType = null;
                $('#longBtn, #shortBtn').removeClass('active');
                $('#marginAmount').val('');
                $('#futuresCoinSelect').val('');
                updatePositionCalculations();
            } else {
                JSPop('error', response.message || 'Failed to open position');
            }
        },
        error: function(xhr, status, error) {
            JSPop('error', 'Error opening position: ' + error);
        },
        complete: function() {
            // RE-ENABLE BUTTON
            openBtn.prop('disabled', false).text('Open Position');
        }
    });
}
function loadFuturesData() {
    $.ajax({
        url: 'core/ajax/assets/getFuturesData.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateFuturesBalances(response.data);
                renderFuturesPositions(response.data.positions);
                allPositions = response.data.positions;
            }
        },
        error: function() {
            console.error('Error loading futures data');
        }
    });
}

function updateFuturesBalances(data) {
    // Update total margin
    $('#totalMargin').text(data.total_margin.toFixed(2));
    $('#totalMargin').attr('data-content', data.total_margin.toFixed(2));
    $('#totalMarginUSD').text('≈ $' + data.total_margin.toFixed(2));
    $('#totalMarginUSD').attr('data-content', '≈ $' + data.total_margin.toFixed(2));
    
    // Update unrealized PnL
    const pnlClass = data.unrealized_pnl >= 0 ? 'text-success' : 'text-danger';
    const pnlSign = data.unrealized_pnl >= 0 ? '+' : '';
    const pnlPercentage = data.total_margin > 0 ? (data.unrealized_pnl / data.total_margin * 100) : 0;
    
    $('#unrealizedPnL').html(`${pnlSign}${data.unrealized_pnl.toFixed(2)} <span class="fs-5">(${pnlSign}${pnlPercentage.toFixed(2)}%)</span>`);
    $('#unrealizedPnL').attr('data-content', `${pnlSign}${data.unrealized_pnl.toFixed(2)} (${pnlSign}${pnlPercentage.toFixed(2)}%)`);
    $('#unrealizedPnL').removeClass('text-success text-danger').addClass(pnlClass);
    $('#unrealizedPnLUSD').text(`${pnlSign}$${Math.abs(data.unrealized_pnl).toFixed(2)} USD`);
    $('#unrealizedPnLUSD').attr('data-content', `${pnlSign}$${Math.abs(data.unrealized_pnl).toFixed(2)} USD`);
    
    // Update available balance
    $('#availableBalance').html(`${data.available_balance.toFixed(2)} <span class="fs-5">USDT</span>`);
    $('#availableBalance').attr('data-content', `${data.available_balance.toFixed(2)} USDT`);
    $('#availableBalanceUSD').text('≈ $' + data.available_balance.toFixed(2));
    $('#availableBalanceUSD').attr('data-content', '≈ $' + data.available_balance.toFixed(2));
}

function renderFuturesPositions(positions) {
    if (!positions || positions.length === 0) {
        $('#futuresPositionsTable').html(`
            <tr>
                <td colspan="9" class="text-center py-4">
                    <img src="core/pages/assets/images/empty.svg" alt="no-positions" style="width: 100px; opacity: 0.5;">
                    <p class="text-muted mt-2">No open positions</p>
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    positions.forEach(position => {
        const positionClass = position.position_type === 'LONG' ? 'text-success' : 'text-danger';
        const pnlClass = parseFloat(position.unrealized_pnl) >= 0 ? 'text-success' : 'text-danger';
        const pnlSign = parseFloat(position.unrealized_pnl) >= 0 ? '+' : '';
        const leverage = parseFloat(position.leverage);
        
        html += `
            <tr class="align-middle position-row" data-symbol="${position.symbol.toLowerCase()}" data-type="${position.position_type}" data-size="${position.position_size}">
                <td>
                    <div class="d-flex flex-row align-items-center gap-2">
                        <img alt="" src="${position.logo || 'core/pages/assets/images/coins/default.png'}" width="24" 
                             onerror="this.src='core/pages/assets/images/coins/default.png'">
                        <p class="fw-bold fs-6 mb-0">${position.symbol.toUpperCase()}/USDT</p>
                    </div>
                </td>
                <td>
                    <span class="badge ${position.position_type === 'LONG' ? 'bg-success' : 'bg-danger'}">${position.position_type}</span>
                    <small class="ms-1">${leverage}x</small>
                </td>
                <td>
                    <h6 class="encrypted mb-0" data-content="${position.position_size}">${parseFloat(position.position_size).toFixed(6)}</h6>
                    <span class="crypt-grayscale-600">${position.symbol.toUpperCase()}</span>
                </td>
                <td class="encrypted fw-medium" data-content="$${position.entry_price}">$${parseFloat(position.entry_price).toFixed(2)}</td>
                <td class="encrypted fw-medium" data-content="$${position.current_price}">$${parseFloat(position.current_price).toFixed(2)}</td>
                <td class="encrypted fw-medium" data-content="$${position.margin_amount}">$${parseFloat(position.margin_amount).toFixed(2)}</td>
                <td class="encrypted ${pnlClass} fw-medium" data-content="${pnlSign}$${position.unrealized_pnl}">${pnlSign}$${parseFloat(position.unrealized_pnl).toFixed(2)}</td>
                <td class="encrypted fw-medium text-warning" data-content="$${position.liquidation_price}">$${parseFloat(position.liquidation_price || 0).toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger" onclick="openClosePositionModal(${position.id}, '${position.symbol}', ${position.position_size}, ${position.unrealized_pnl})">Close</button>
                </td>
            </tr>
        `;
    });
    
    $('#futuresPositionsTable').html(html);
    filterPositions();
}

function filterPositions() {
    const searchTerm = $('#positionSearch').val().toLowerCase();
    const hideSmall = $('#hideSmallPositions').is(':checked');
    const onlyLong = $('#onlyLongPositions').is(':checked');
    const onlyShort = $('#onlyShortPositions').is(':checked');
    
    $('.position-row').each(function() {
        const symbol = $(this).data('symbol');
        const type = $(this).data('type');
        const size = parseFloat($(this).data('size'));
        
        let show = true;
        
        // Search filter
        if (searchTerm && !symbol.includes(searchTerm)) {
            show = false;
        }
        
        // Hide small positions (less than $10)
        if (hideSmall && size < 0.001) {
            show = false;
        }
        
        // Position type filters
        if (onlyLong && type !== 'LONG') {
            show = false;
        }
        
        if (onlyShort && type !== 'SHORT') {
            show = false;
        }
        
        // Mutually exclusive checkboxes
        if (onlyLong && $(this).is('#onlyShortPositions:checked')) {
            $('#onlyShortPositions').prop('checked', false);
        }
        if (onlyShort && $(this).is('#onlyLongPositions:checked')) {
            $('#onlyLongPositions').prop('checked', false);
        }
        
        $(this).toggle(show);
    });
}

function toggleEncryption() {
    const isEncrypted = $('.encrypted').first().text().includes('****');
    
    $('.encrypted').each(function() {
        const $this = $(this);
        if (isEncrypted) {
            const originalValue = $this.attr('data-content');
            if ($this.is('h6, h3, p')) {
                $this.html(originalValue);
            } else {
                $this.text(originalValue);
            }
        } else {
            $this.text('****');
        }
    });
}

function updateCurrencyDisplay() {
    const selectedCurrency = $('#inputGroupFuturesCoin').val();
    console.log('Currency changed to:', selectedCurrency);
}

function openClosePositionModal(positionId, symbol, size, pnl) {
    $('#closePositionId').val(positionId);
    $('#closePositionSymbol').val(symbol.toUpperCase() + '/USDT');
    $('#closeSize').val(size).attr('data-original', size);
    $('#estimatedPnL').val('$' + parseFloat(pnl).toFixed(2));
    $('#originalSizeDisplay').text(parseFloat(size).toFixed(8));
    $('#closePercentage').val(100);
    $('#closePercentageValue').text('100%');
    $('#closePositionModal').modal('show');
}

function updateCloseSize() {
    const percentage = $('#closePercentage').val();
    const originalSize = parseFloat($('#closeSize').attr('data-original'));
    
    if (!originalSize || originalSize <= 0) {
        console.error('Invalid original size');
        return;
    }
    
    const newSize = (originalSize * percentage / 100).toFixed(8); // ✅ 8 decimal precision
    
    $('#closePercentageValue').text(percentage + '%');
    $('#closeSize').val(newSize);
}

function submitClosePosition() {
    const positionId = $('#closePositionId').val();
    const closeSize = parseFloat($('#closeSize').val());
    const originalSize = parseFloat($('#closeSize').attr('data-original'));
    
    if (!positionId || !closeSize || closeSize <= 0) {
        JSPop('warning', 'Invalid position data');
        return;
    }
    
    if (closeSize > originalSize) {
        JSPop('warning', 'Close size cannot exceed position size');
        return;
    }
    
    const closeBtn = $('.btn-danger[onclick="submitClosePosition()"]');
    closeBtn.prop('disabled', true).text('Closing...');
    
    $.ajax({
        url: 'core/ajax/assets/closePosition.php',
        type: 'POST',
        data: JSON.stringify({
            position_id: parseInt(positionId),
            size: closeSize
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success', 'Position closed successfully!');
                $('#closePositionModal').modal('hide');
                loadFuturesData();
            } else {
                JSPop('error', response.message || 'Failed to close position');
            }
        },
        error: function(xhr, status, error) {
            JSPop('error', 'Error closing position: ' + error);
        },
        complete: function() {
            closeBtn.prop('disabled', false).text('Close Position');
        }
    });
}

function closeAllPositions() {
    if (!confirm('Are you sure you want to close all positions? This action cannot be undone.')) {
        return;
    }
    
    $.ajax({
        url: 'core/ajax/assets/closeAllPositions.php',
        type: 'POST',
        success: function(response) {
            if (response.success) {
                JSPop('success', 'All positions closed successfully!');
                loadFuturesData();
            } else {
                JSPop('error', response.message || 'Failed to close positions');
            }
        }
    });
}
</script>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

<?php require __DIR__ . '/inc/assets_footer.php'; ?>