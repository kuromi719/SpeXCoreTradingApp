<?php require __DIR__ . '/inc/home_header.php'; ?>

    <!-- Hero -->
    <canvas id="particleCanvas"></canvas>
    <section class="container-fluid bs5-bg hero-temp1">
        <div class="container bs5-hero pt-5 p-0">
            <div class="d-sm-flex flex-row align-items-center mt-5 pt-5 animation-element">
                <div class="col-md-6 in-view">
                    <h1 class="fw-medium">Find the Next <span class="fw-bold gd-text">Crypto Gem</span></h1>
                    <p class="crypt-grayscale-300 mt-4">Unleash Crypto Freedom: Limitless, Trustworthy, and <a
                            href="?r=login" class="text-info text-link underline"> Sign-Up Free</a>.</p>
                    <div class="d-flex align-items-center gap-3 mt-4">
                        <button class="custom-btn btn-11"
                            onclick="window.location.assign('?r=login')"><span>Start Trading</span>
                            <svg width="24" height="24" viewbox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 7L13.5 15.5L8.5 10.5L2 17" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M16 7H22V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="row mt-4 align-items-center">
                        <p class="crypt-grayscale-500">Excellent <span class="crypt-grayscale-100 meta">4.7 out of
                                5</span>
                            <a href="https://www.trustpilot.com/" target="_blank">
                                <img class="ms-2" src="core/pages/assets/images/partner/logo-11.svg" style="width:100px;" alt="">
                            </a>
                        </p>
                    </div>
                    <div class="d-flex flex-row gap-4 mt-5 mb-5">
                        <div class="meta">
                            <h4>$12.46B+</h4>
                            <p class="mt-2">24h Trading Volume</p>
                        </div>
                        <div class="meta">
                            <h4>2600+</h4>
                            <p class="mt-2">Cryptocurrencies</p>
                        </div>
                        <div class="meta">
                            <h4>150%</h4>
                            <p class="mt-2">Simple Earn APR</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <img class="ms-2" src="core/pages/assets/images/slider/rewards.svg" alt="" width="700">
                </div>
            </div>
        </div>
        <!-- TradingView Ticker -->
        <div class="tradingview-widget-container__widget mt-5 animation-element circle card-bs">
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
    </section>

    <!-- Trusted by industry leaders -->
    <section class="container mt-5 animation-element">
        <h5 class="gd-text text-center text-uppercase">Trusted by industry leaders</h5>
        <div class="partner grayscale-item crypt-scroll in-view">
            <div class="crypt-scrolling scroll-right">
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-3.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-2.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-4.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-9.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-10.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-5.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-3.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-2.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-4.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-9.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-10.svg"></div>
                <div><img alt="" style="width:200px;" src="core/pages/assets/images/partner/logo-5.svg"></div>
            </div>
        </div>
    </section>


    <section class="container mt-0 mt-sm-5 pt-0 pt-sm-4">
        <div class="row align-items-center animation-element in-view">
            <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-7 p-5 in-view">
                <div class="text-left pb-4">
                    <h2>How to Get Started</h2>
                    <p class="crypt-grayscale-500 mt-3">You can receive your payments in your digital wallet in crypto
                        or
                        <br>in your bank account in fiat — it’s your choice.
                    </p>
                </div>
                <div class="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button fs-4 fw-medium" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                                1. Create an account
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse show" aria-labelledby="headingFive"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                Pick one of 50 000+ supported currency pairs from the list.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSix">
                            <button class="accordion-button collapsed fs-4 fw-medium" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false"
                                aria-controls="collapseSix">
                                2. Enter swap details
                            </button>
                        </h2>
                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                Confirm details and send your assets to the generated address.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSeven">
                            <button class="accordion-button collapsed fs-4 fw-medium" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false"
                                aria-controls="collapseSeven">
                                3. Trade your crypto
                            </button>
                        </h2>
                        <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                Receive exchanged crypto with the best possible rate.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-5 in-view">
                <img class="grayscale-item" style="width:100%;" src="core/pages/assets/images/slider/crypt02.png" alt="">
            </div>
        </div>
    </section>

    <br><br><br>


<?php require __DIR__ . '/inc/home_footer.php'; ?>