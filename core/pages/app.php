<?php require __DIR__ . '/inc/app_header.php'; ?>

    <?php $pins = getPinboardData(); ?>
    <div class="col-12 col-md-12 col-lg-12">
    <?php if(!empty($pins)): ?>
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach($pins as $index => $pin): ?>
            <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>">
                <img class="d-block w-100" 
                        src="<?php echo $pin['img']; ?>" 
                        alt="Pin <?php echo $pin['id']; ?>" 
                        style="height: 400px; object-fit: cover;">
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if(count($pins) > 1): ?>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        
        <div class="carousel-indicators">
            <?php foreach($pins as $index => $pin): ?>
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="<?php echo $index; ?>" 
                    <?php echo $index == 0 ? 'class="active"' : ''; ?>></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    </div>

    <script>
    <?php if(count($pins) > 1): ?>
    setInterval(function() {
    $('#carouselExample').carousel('next');
    }, 3000);
    <?php endif; ?>
    </script>
    

    <br><br>                
    
    <!-- User Profile -->
        <div class="row g-2 animation-element">
            <div class="col-12 col-md-12 col-lg-9">
                <div class="card-bs p-3 h-100">
                    <!-- Profile -->
                    <div class="row align-items-center p-2">
                        <div class="col-12 col-md-12 col-xl-7">
                            <div class="d-flex gap-3">
                                <div class="pb-2 ">
                                    <img class="user-profile " src="core/pages/assets/images/social/user.jpg" alt="">
                                </div>
                                <div class="pb-2 ">
                                    <div class="d-flex flex-wrap align-items-center gap-2 ">
                                    <h4 class="crypt-grayscale-100 fw-bold">Hello, <?php echo Censor($UserData['mail'],'email'); ?></h4>
                                    </div>
                                    <div class="pb-2 text-sm">
                                    <a href="#" class="crypt-grayscale-600" role="button">Last Login: <?php echo $UserData['last_login']; ?>
                                    </a>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-3">
                                    <div class="verified gd-bg px-0">
                                        <a class="d-flex gap-2 mb-2 verified mt-2" href="#!">
                                            <p class="crypt-grayscale-600 mb-0">UID:</p>
                                            <p class="crypt-grayscale-100 mb-0"><?php echo $UserData['uid']; ?></p>
                                        </a>
                                    </div>
                                    <div class="d-flex flex-sm-wrap justify-content-between gap-2">
                                    <p class="crypt-grayscale-600 mb-0">E-Mail</p>
                                    <a class="verified alert align-items-center d-flex mb-0 gap-2" href="#!" role="button">
                                        <svg width="22" height="22" viewbox="0 0 22 22" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.401 0.794778C10.7011 0.396626 11.2989 0.396625 11.599 0.794778L12.9886 2.6388C13.1879 2.90318 13.5369 3.00566 13.8474 2.89097L16.0135 2.091C16.4812 1.91827 16.9841 2.24147 17.0212 2.73864L17.1934 5.04126C17.218 5.37139 17.4562 5.64629 17.7795 5.7177L20.0342 6.21577C20.521 6.32331 20.7693 6.86709 20.5318 7.30543L19.4317 9.33557C19.274 9.62663 19.3258 9.98668 19.5591 10.2215L21.1866 11.8595C21.538 12.2132 21.4529 12.8049 21.0161 13.0452L18.9931 14.1583C18.703 14.3179 18.5519 14.6488 18.6213 14.9725L19.1048 17.2303C19.2092 17.7178 18.8178 18.1696 18.3204 18.1357L16.0167 17.9783C15.6864 17.9558 15.3804 18.1524 15.2637 18.4622L14.4498 20.6231C14.2741 21.0897 13.7005 21.2581 13.3004 20.9606L11.4475 19.5828C11.1819 19.3852 10.8181 19.3852 10.5525 19.5828L8.69955 20.9606C8.29948 21.2581 7.72589 21.0897 7.55016 20.6231L6.73628 18.4622C6.6196 18.1524 6.3136 17.9558 5.98332 17.9783L3.67964 18.1357C3.18224 18.1696 2.79076 17.7178 2.89517 17.2303L3.37874 14.9725C3.44807 14.6488 3.29697 14.3179 3.00692 14.1583L0.983894 13.0452C0.547091 12.8049 0.462014 12.2132 0.813412 11.8595L2.44089 10.2215C2.67423 9.98668 2.72599 9.62664 2.56827 9.33557L1.46819 7.30543C1.23066 6.86709 1.479 6.32331 1.96582 6.21577L4.2205 5.7177C4.54376 5.64629 4.78196 5.37139 4.80664 5.04126L4.97876 2.73864C5.01593 2.24147 5.51884 1.91827 5.98652 2.091L8.15255 2.89097C8.4631 3.00566 8.81211 2.90318 9.01135 2.6388L10.401 0.794778Z" fill="currentColor"></path>
                                        <path opacity="0.9" d="M6.46921 10.1359C6.94653 9.65862 7.72039 9.65862 8.19771 10.1359L9.77791 11.7161L13.8026 7.69147C14.2798 7.21417 15.0537 7.21417 15.531 7.69147C16.0083 8.16879 16.0083 8.94265 15.531 9.41996L10.6421 14.3088C10.1648 14.7861 9.39097 14.7861 8.91366 14.3088L6.46921 11.8644C5.99191 11.3871 5.99191 10.6132 6.46921 10.1359Z" fill="#000"></path>
                                        </svg>
                                        Verified
                                    </a>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-bs-tabs p-3 p-sm-4 mt-3">
                            <div class="row">
                                <!-- Asset Info -->
                                <div class="col-12 col-lg-6 ">
                                    <div class="d-flex gap-1 ">
                                        <h5>Total Balance</h5>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <h2 class="fw-bold encrypted mb-0" data-content="<?php echo number_format($UserData['balance'], 6, '.', ''); ?>">$<?php echo number_format($UserData['balance'], 6, '.', ''); ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-3">
                <!-- Deposit or Withdraw -->
                <div class="card-bs p-3 h-100">
                    <h5 class="fw-bold crypt-grayscale-100">Deposit or Withdraw</h5>
                    <div class="row gap-2 ">
                        <div class="col-12">
                            <div class="d-flex card-bs mesh3 p-3 justify-content-between mt-2">
                                <div class="d-flex flex-column ">
                                    <div class="d-flex flex-row gap-2">
                                    <div>
                                        <img alt="" style="width:38px;" src="core/pages/assets/images/icon/deposit.svg">
                                    </div>
                                    <h5 class="crypt-grayscale-0 fw-bold">Deposit</h5>
                                    </div>
                                    <p class="crypt-grayscale-0">Deposit a your money.</p>
                                </div>
                                <div>
                                    <a class="btn btn-editor btn-light mb-2" href="?r=wallet">Wallet</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex card-bs mesh4 p-3 justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <div class="d-flex flex-row gap-2">
                                    <div>
                                        <img alt="" style="width:38px;" src="core/pages/assets/images/icon/p2p.svg">
                                    </div>
                                    <h5 class="crypt-grayscale-0 fw-bold">Withdraw</h5>
                                    </div>
                                    <p class="crypt-grayscale-0">Use local balance or crypto.</p>
                                </div>
                                <div>
                                    <a class="btn btn-editor btn-light mb-2" href="?r=wallet" >Wallet</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-2 mt-1 animation-element in-view">
            <h5 class="crypt-grayscale-100 mb-2 mt-4 fw-bold">Account</h5>
            
            <!-- Spot Asset -->
            <div class="col-12 col-lg-4 col-xl-4 col-xxl-3 col-md-6">
                <div class="card-bs p-3 d-sm-flex flex-row align-items-center">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <p class="crypt-grayscale-500">Spot</p>
                                <div class="d-flex pb-3">
                                    <h3 class="fw-bold encrypted mb-0" id="spot-balance">0.00</h3>
                                    <select class="form-select text-bg-bs" id="inputGroupSpotCoin">
                                        <option value="BTC">BTC</option>
                                    </select>
                                </div>
                                <p class="crypt-grayscale-700 encrypted" id="spot-usd">≈ $0.00 USD</p>
                            </div>
                            <img alt="" style="width:60px;" src="core/pages/assets/images/icon/spot.svg">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Margin Asset -->
            <div class="col-12 col-lg-4 col-xl-4 col-xxl-3 col-md-6">
                <div class="card-bs p-3 d-sm-flex flex-row align-items-center">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <p class="crypt-grayscale-500">Margin</p>
                                <div class="d-flex pb-3">
                                    <h3 class="fw-bold encrypted mb-0" id="margin-balance">0.00</h3>
                                    <select class="form-select text-bg-bs" id="inputGroupMarginCoin">
                                        <option value="BNB">BNB</option>
                                    </select>
                                </div>
                                <p class="crypt-grayscale-700 encrypted" id="margin-usd">≈ $0.00 USD</p>
                            </div>
                            <img alt="" style="width:60px;" src="core/pages/assets/images/icon/margin.svg">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Futures Asset -->
            <div class="col-12 col-lg-4 col-xl-4 col-xxl-3 col-md-6">
                <div class="card-bs p-3 d-sm-flex flex-row align-items-center">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <p class="crypt-grayscale-500">Futures</p>
                                <div class="d-flex pb-3">
                                    <h3 class="fw-bold encrypted mb-0" id="futures-balance">0.00</h3>
                                    <select class="form-select text-bg-bs" id="inputGroupFuturesCoin">
                                        <option value="ETH">ETH</option>
                                    </select>
                                </div>
                                <p class="crypt-grayscale-700 encrypted" id="futures-usd">≈ $0.00 USD</p>
                            </div>
                            <img alt="" style="width:60px;" src="core/pages/assets/images/icon/funding.svg">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Earn Asset -->
            <div class="col-12 col-lg-4 col-xl-4 col-xxl-3 col-md-6">
                <div class="card-bs p-3 d-sm-flex flex-row align-items-center">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <p class="crypt-grayscale-500">Earn</p>
                                <div class="d-flex pb-3">
                                    <h3 class="fw-bold encrypted mb-0" id="earn-balance">0.00</h3>
                                    <select class="form-select text-bg-bs" id="inputGroupEarnCoin">
                                        <option value="USDT">USDT</option>
                                    </select>
                                </div>
                                <p class="crypt-grayscale-700 encrypted" id="earn-usd">≈ $0.00 USD</p>
                            </div>
                            <img alt="" style="width:60px;" src="core/pages/assets/images/icon/earn.svg">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        $(document).ready(function() {
            loadUserAssets();
            setInterval(loadUserAssets, 60000);
            $('.form-select').on('change', function() {
                updateAssetDisplay($(this));
            });
        });

        function loadUserAssets() {
            $.ajax({
                url: 'core/ajax/user/getAssets.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateAssetsDisplay(response.assets);
                        populateCoinSelectors(response.assets);
                    } else {
                        console.error('Error loading assets:', response.error);
                        showDefaultValues();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error loading assets:', error);
                    showDefaultValues();
                }
            });
        }

        function updateAssetsDisplay(assets) {
         
            const spot = assets.spot;
            $('#spot-balance').text(formatBalance(spot.top_coin.balance));
            $('#spot-usd').text('≈ $' + formatUSD(spot.top_coin.usd_value) + ' USD');
            
   
            const margin = assets.margin;
            $('#margin-balance').text(formatBalance(margin.top_coin.balance));
            $('#margin-usd').text('≈ $' + formatUSD(margin.top_coin.usd_value) + ' USD');
            

            const futures = assets.futures;
            $('#futures-balance').text(formatBalance(futures.top_coin.balance));
            let futuresText = '≈ $' + formatUSD(futures.top_coin.usd_value) + ' USD';
            if (futures.top_coin.pnl) {
                const pnlColor = futures.top_coin.pnl >= 0 ? 'text-success' : 'text-danger';
                futuresText += ` <span class="${pnlColor}">(${futures.top_coin.pnl >= 0 ? '+' : ''}$${formatUSD(Math.abs(futures.top_coin.pnl))})</span>`;
            }
            $('#futures-usd').html(futuresText);
            
        
            const earn = assets.earn;
            $('#earn-balance').text(formatBalance(earn.top_coin.balance));
            let earnText = '≈ $' + formatUSD(earn.top_coin.usd_value) + ' USD';
            if (earn.top_coin.apy) {
                earnText += ` <small class="text-warning">(${earn.top_coin.apy}% APY)</small>`;
            }
            $('#earn-usd').html(earnText);
            

            $('.encrypted').addClass('fade-in');
            setTimeout(() => $('.encrypted').removeClass('fade-in'), 500);
        }

        function populateCoinSelectors(assets) {
            populateSelector('#inputGroupSpotCoin', assets.spot.coins, assets.spot.top_coin.symbol);
            populateSelector('#inputGroupMarginCoin', assets.margin.coins, assets.margin.top_coin.symbol);
            populateSelector('#inputGroupFuturesCoin', assets.futures.coins, assets.futures.top_coin.symbol);
            populateSelector('#inputGroupEarnCoin', assets.earn.coins, assets.earn.top_coin.symbol);
        }

        function populateSelector(selectorId, coins, selectedSymbol) {
            const $selector = $(selectorId);
            $selector.empty();
            
            if (coins && coins.length > 0) {
                coins.forEach(coin => {
                    const option = $('<option>')
                        .val(coin.symbol)
                        .text(coin.symbol)
                        .data('balance', coin.balance)
                        .data('usd-value', coin.usd_value);
                        
                    if (coin.symbol === selectedSymbol) {
                        option.prop('selected', true);
                    }
                    
                    $selector.append(option);
                });
            } else {
                // Default option if no coins
                $selector.append($('<option>').val(selectedSymbol).text(selectedSymbol));
            }
        }

        function updateAssetDisplay($selector) {
            const selectedOption = $selector.find(':selected');
            const balance = selectedOption.data('balance') || 0;
            const usdValue = selectedOption.data('usd-value') || 0;
            
            const assetType = $selector.attr('id').replace('inputGroup', '').replace('Coin', '').toLowerCase();
            
            $(`#${assetType}-balance`).text(formatBalance(balance));
            $(`#${assetType}-usd`).text('≈ $' + formatUSD(usdValue) + ' USD');
        }

        function formatBalance(balance) {
            if (balance >= 1) {
                return parseFloat(balance).toFixed(4);
            } else if (balance > 0) {
                return parseFloat(balance).toFixed(6);
            }
            return '0.00';
        }

        function formatUSD(amount) {
            if (amount >= 1000000) {
                return (amount / 1000000).toFixed(2) + 'M';
            } else if (amount >= 1000) {
                return (amount / 1000).toFixed(2) + 'K';
            }
            return parseFloat(amount).toFixed(2);
        }

        function showDefaultValues() {
            $('.encrypted[id$="-balance"]').text('0.00');
            $('.encrypted[id$="-usd"]').text('≈ $0.00 USD');
        }
        </script>


        <div class="row g-2 mt-4 animation-element">
            
            <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                <div class="tab-content card-bs h-100">
                    <!-- Tabs -->
                    <div class="nav nav-tabs nav-justified card-border card-bs p-1 m-2" role="tablist">
                        <button class="nav-link active fw-medium" id="top-market-tab" data-bs-toggle="tab" data-bs-target="#top-market-tab-pane" type="button" aria-controls="top-market-tab-pane">
                            <svg width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.99984 17.7089C13.5667 17.7089 16.4582 14.5839 16.4582 10.8339C16.4582 6.52805 12.6138 3.47652 11.0653 2.42206C10.7107 2.1805 10.242 2.28502 9.995 2.636L7.9848 5.49288C7.68773 5.91509 7.08029 5.96651 6.71406 5.60264C6.38895 5.27962 5.859 5.27613 5.5672 5.62955C4.21674 7.26519 3.5415 9.30171 3.5415 10.8339C3.5415 14.5839 6.433 17.7089 9.99984 17.7089ZM9.99984 17.7089C11.3806 17.7089 12.4998 16.4313 12.4998 14.8552C12.4998 13.1193 11.1139 11.8545 10.4058 11.3217C10.1626 11.1386 9.83709 11.1386 9.59384 11.3217C8.88575 11.8545 7.49984 13.1193 7.49984 14.8552C7.49984 16.4313 8.61909 17.7089 9.99984 17.7089Z" stroke="currentColor" stroke-linejoin="round"></path>
                            </svg>
                            Most popular
                        </button>
                        <button class="nav-link fw-medium" id="reward-tab" data-bs-toggle="tab" data-bs-target="#reward-tab-pane" type="button" aria-controls="reward-tab-pane">
                            <svg width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.32506 12.2167L2.0584 10.95C1.54173 10.4334 1.54173 9.58335 2.0584 9.06668L3.32506 7.8C3.54172 7.58333 3.71672 7.15833 3.71672 6.85833V5.06664C3.71672 4.3333 4.31673 3.73333 5.05006 3.73333H6.84172C7.14172 3.73333 7.56673 3.55835 7.78339 3.34168L9.05005 2.075C9.56672 1.55833 10.4167 1.55833 10.9334 2.075L12.2001 3.34168C12.4167 3.55835 12.8417 3.73333 13.1417 3.73333H14.9334C15.6667 3.73333 16.2667 4.3333 16.2667 5.06664V6.85833C16.2667 7.15833 16.4417 7.58333 16.6584 7.8L17.9251 9.06668C18.4417 9.58335 18.4417 10.4334 17.9251 10.95L16.6584 12.2167C16.4417 12.4334 16.2667 12.8584 16.2667 13.1584V14.95C16.2667 15.6833 15.6667 16.2834 14.9334 16.2834H13.1417C12.8417 16.2834 12.4167 16.4583 12.2001 16.675L10.9334 17.9417C10.4167 18.4584 9.56672 18.4584 9.05005 17.9417L7.78339 16.675C7.56673 16.4583 7.14172 16.2834 6.84172 16.2834H5.05006C4.31673 16.2834 3.71672 15.6833 3.71672 14.95V13.1584C3.71672 12.85 3.54172 12.425 3.32506 12.2167Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path opacity="0.4" d="M7.5 12.5L12.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path opacity="0.4" d="M12.0786 12.0832H12.0861" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path opacity="0.4" d="M7.91209 7.91667H7.91957" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            Rewards
                        </button>
                        <button class="nav-link fw-medium" id="hot-coin-tab" data-bs-toggle="tab" data-bs-target="#hot-coin-tab-pane" type="button" aria-controls="hot-coin-tab-pane">
                            <svg width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 2.5L8.40667 7.34417C8.32512 7.59209 8.1865 7.8174 8.00195 8.00195C7.8174 8.1865 7.59209 8.32512 7.34417 8.40667L2.5 10L7.34417 11.5933C7.59209 11.6749 7.8174 11.8135 8.00195 11.9981C8.1865 12.1826 8.32512 12.4079 8.40667 12.6558L10 17.5L11.5933 12.6558C11.6749 12.4079 11.8135 12.1826 11.9981 11.9981C12.1826 11.8135 12.4079 11.6749 12.6558 11.5933L17.5 10L12.6558 8.40667C12.4079 8.32512 12.1826 8.1865 11.9981 8.00195C11.8135 7.8174 11.6749 7.59209 11.5933 7.34417L10 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M4.16675 2.5V5.83333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M15.8333 14.1665V17.4998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M2.5 4.1665H5.83333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M14.1667 15.8335H17.5001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            Newly listed
                        </button>
                    </div>
                    
                    <!-- Most Popular Tab -->
                    <div class="tab-pane fade show active" id="top-market-tab-pane" role="tabpanel" aria-labelledby="top-market-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr class="mb-0">
                                        <th scope="col" class="underline">Coin</th>
                                        <th scope="col" class="underline">Amount</th>
                                        <th scope="col" class="underline">Last Price</th>
                                        <th scope="col" class="underline">24h Change</th>
                                        <th scope="col" class="underline">Ratio</th>
                                        <th scope="col" class="underline">Market Cap</th>
                                        <th scope="col" class="underline">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="reward-tab-pane" role="tabpanel" aria-labelledby="reward-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-dark crypt-tab mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" class="underline">Reward Type</th>
                                        <th scope="col" class="underline">Amount</th>
                                        <th scope="col" class="underline">Message</th>
                                        <th scope="col" class="underline">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="rewards-table-body">
                                    <!-- AUTOLOAD -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Newly Listed Tab -->
                    <div class="tab-pane fade" id="hot-coin-tab-pane" role="tabpanel" aria-labelledby="hot-coin-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr class="mb-0">
                                        <th scope="col" class="underline">Coin</th>
                                        <th scope="col" class="underline">Amount</th>
                                        <th scope="col" class="underline">Price</th>
                                        <th scope="col" class="underline">24h Change</th>
                                        <th scope="col" class="underline">Volume</th>
                                        <th scope="col" class="underline">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script>
        $(document).ready(function() {
            loadPopularCoins();
            
            $('#top-market-tab').on('click', function() {
                loadPopularCoins();
            });
            
            $('#hot-coin-tab').on('click', function() {
                loadNewlyListed();
            });

            $('#reward-tab').on('click', function() {
                loadRewards();
            });
            
            setInterval(function() {
                if ($('#top-market-tab').hasClass('active')) {
                    loadPopularCoins();
                } else if ($('#hot-coin-tab').hasClass('active')) {
                    loadNewlyListed();
                } else if ($('#reward-tab').hasClass('active')) {
                    loadRewards();
                }
            }, 30000);
        });

        function loadPopularCoins() {
            showLoadingState('#top-market-tab-pane tbody');
            
            $.ajax({
                url: 'core/ajax/market/popular.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        showErrorState('#top-market-tab-pane tbody', response.error);
                    } else {
                        $('#top-market-tab-pane tbody').html(response.html);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading popular coins:', error);
                    showErrorState('#top-market-tab-pane tbody', 'Failed to load market data');
                }
            });
        }

        function loadNewlyListed() {
            showLoadingState('#hot-coin-tab-pane tbody');
            
            $.ajax({
                url: 'core/ajax/market/newly-listed.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        showErrorState('#hot-coin-tab-pane tbody', response.error);
                    } else {
                        $('#hot-coin-tab-pane tbody').html(response.html);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading newly listed coins:', error);
                    showErrorState('#hot-coin-tab-pane tbody', 'Failed to load market data');
                }
            });
        }

        function loadRewards() {
            showLoadingState('#rewards-table-body', 5);
            
            $.ajax({
                url: 'core/ajax/market/rewards.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        showErrorState('#rewards-table-body', response.error, 5);
                    } else {
                        $('#rewards-table-body').html(response.html);
                        $('#rewards-table-body tr').hide().fadeIn(300);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading rewards:', error);
                    showErrorState('#rewards-table-body', 'Failed to load rewards data', 5);
                }
            });
        }



        function showLoadingState(selector) {
            $(selector).html(`
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="crypt-grayscale-600 mt-2 mb-0">Loading market data...</p>
                    </td>
                </tr>
            `);
        }

        function showErrorState(selector, message) {
            $(selector).html(`
                                    <tr class="align-middle">
                                        <td colspan="6" class="no-orders text-center">
                                            <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                                            <p class="crypt-grayscale-600 pb-4 ">No records</p>
                                        </td>
                                    </tr>
            `);
        }

        </script>

        <br>


        <!-- TradingView Ticker -->
        <div class="container-fluid tradingview-widget-container__widget mt-2 card-bs animation-element">
            <script src="core/pages/assets/external-embedding/embed-widget-ticker-tape.js" async="">
            {
                "symbols": [
                {
                    "proName": "FOREXCOM:SPXUSD",
                    "title": "S&P 500 Index"
                },
                {
                    "proName": "FOREXCOM:NSXUSD",
                    "title": "US 100 Cash CFD"
                },
                {
                    "proName": "FX_IDC:EURUSD",
                    "title": "EUR to USD"
                },
                {
                    "proName": "BITSTAMP:BTCUSD",
                    "title": "Bitcoin"
                },
                {
                    "proName": "BITSTAMP:ETHUSD",
                    "title": "Ethereum"
                }
                ],
                "showSymbolLogo": true,
                "isTransparent": true,
                "displayMode": "adaptive",
                "colorTheme": "dark",
                "locale": "en"
            }
            </script>
        </div>

    </div>


<?php require __DIR__ . '/inc/app_footer.php'; ?>