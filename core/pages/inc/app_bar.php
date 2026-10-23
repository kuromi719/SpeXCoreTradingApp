    <!-- Sidebar -->
    <aside class="sidebar">
        <!-- Brand Logo -->
        <div class="d-flex align-items-center justify-content-between">
            <div class="justify-content-between disable-sm-screen mt-3">
                <!-- Logo -->
                <div class="crypt-logo dark">
                    <a href="#!">
                        <img src="<?php echo FetchWebSettings()['BaseLogo']; ?>" alt="">
                    </a>
                </div>
                <div class="crypt-logo light">
                    <a href="#!">
                        <img src="<?php echo FetchWebSettings()['BaseLogo']; ?>" alt="">
                    </a>
                </div>
                <!-- Logo sm -->
                <div class="crypt-logo logo-sm dark">
                    <a href="#!">
                        <img src="<?php echo FetchWebSettings()['BaseLogo']; ?>" alt="">
                    </a>
                </div>
                <div class="crypt-logo logo-sm light">
                    <a href="#!">
                        <img src="<?php echo FetchWebSettings()['BaseLogo']; ?>" alt="">
                    </a>
                </div>
            </div>
        </div>

        <!-- Menu -->
        <div class="side-wrapper">
            <div class="side-title">Assets</div>
            <div class="side-menu">
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='app'){ echo "is-active"; } ?>" href="?r=app" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Dashboard">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M18.6699 2H16.7699C14.5899 2 13.4399 3.15 13.4399 5.33V7.23C13.4399 9.41 14.5899 10.56 16.7699 10.56H18.6699C20.8499 10.56 21.9999 9.41 21.9999 7.23V5.33C21.9999 3.15 20.8499 2 18.6699 2Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M7.24 13.4301H5.34C3.15 13.4301 2 14.5801 2 16.7601V18.6601C2 20.8501 3.15 22.0001 5.33 22.0001H7.23C9.41 22.0001 10.56 20.8501 10.56 18.6701V16.7701C10.57 14.5801 9.42 13.4301 7.24 13.4301Z" fill="currentColor"></path>
                        <path d="M6.29 10.58C8.6593 10.58 10.58 8.6593 10.58 6.29C10.58 3.9207 8.6593 2 6.29 2C3.9207 2 2 3.9207 2 6.29C2 8.6593 3.9207 10.58 6.29 10.58Z" fill="currentColor"></path>
                        <path d="M17.7099 22C20.0792 22 21.9999 20.0793 21.9999 17.71C21.9999 15.3407 20.0792 13.42 17.7099 13.42C15.3406 13.42 13.4199 15.3407 13.4199 17.71C13.4199 20.0793 15.3406 22 17.7099 22Z" fill="currentColor"></path>
                    </svg>
                    <span> Dashboard </span>
                </a>
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='marketplace'){ echo "is-active"; } ?>" href="?r=marketplace" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Marketplace">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.9597 17.84L19.3297 18.39C18.8797 18.54 18.5197 18.89 18.3697 19.35L17.8197 20.98C17.3497 22.39 15.3697 22.36 14.9297 20.95L13.0797 15C12.7197 13.82 13.8097 12.72 14.9797 13.09L20.9397 14.94C22.3397 15.38 22.3597 17.37 20.9597 17.84Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z" fill="currentColor"></path>
                    </svg>
                    <span> Marketplace </span>
                </a>
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='exchange' || htmlspecialchars($_GET['r'])=='trade'){ echo "is-active"; } ?>" href="?r=exchange" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Exchange">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 15.5C15 19.09 12.09 22 8.5 22C4.91 22 2 19.09 2 15.5C2 11.91 4.91 9 8.5 9C8.67 9 8.84999 9.01 9.01999 9.02C12.19 9.27 14.73 11.81 14.98 14.98C14.99 15.15 15 15.33 15 15.5Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M22 8.5C22 12.09 19.09 15 15.5 15C15.33 15 15.15 14.99 14.98 14.98C14.73 11.81 12.19 9.27 9.01999 9.02C9.00999 8.85 9 8.67 9 8.5C9 4.91 11.91 2 15.5 2C19.09 2 22 4.91 22 8.5Z" fill="currentColor"></path>
                        <path d="M5.59 2H3C2.45 2 2 2.45 2 3V5.59C2 6.48 3.07999 6.93 3.70999 6.3L6.29999 3.71001C6.91999 3.08001 6.48 2 5.59 2Z" fill="currentColor"></path>
                        <path d="M18.41 22H21C21.55 22 22 21.55 22 21V18.41C22 17.52 20.92 17.07 20.29 17.7L17.7 20.29C17.08 20.92 17.52 22 18.41 22Z" fill="currentColor"></path>
                    </svg>
                    <span> Exchange </span>
                </a>
                <a class="sidebar-link trending <?php if(htmlspecialchars($_GET['r'])=='assets'){ echo "is-active"; } ?>" href="?r=assets" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="My Asset">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.0602 11.8201L20.9002 11.6001C20.6202 11.2601 20.2902 10.9901 19.9102 10.7901C19.4002 10.5001 18.8202 10.3501 18.2202 10.3501H5.7702C5.1702 10.3501 4.6002 10.5001 4.0802 10.7901C3.6902 11.0001 3.3402 11.2901 3.0502 11.6501C2.4802 12.3801 2.2102 13.2801 2.3002 14.1801L2.6702 18.8501C2.8002 20.2601 2.9702 22.0001 6.1402 22.0001H17.8602C21.0302 22.0001 21.1902 20.2601 21.3302 18.8401L21.7002 14.1901C21.7902 13.3501 21.5702 12.5101 21.0602 11.8201ZM14.3902 17.3401H9.6002C9.2102 17.3401 8.9002 17.0201 8.9002 16.6401C8.9002 16.2601 9.2102 15.9401 9.6002 15.9401H14.3902C14.7802 15.9401 15.0902 16.2601 15.0902 16.6401C15.0902 17.0301 14.7802 17.3401 14.3902 17.3401Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M3.37988 11.31C3.59988 11.11 3.81988 10.93 4.07988 10.79C4.58988 10.5 5.16988 10.35 5.76988 10.35H18.2299C18.8299 10.35 19.3999 10.5 19.9199 10.79C20.1799 10.93 20.4099 11.11 20.6199 11.32V10.79V9.82C20.6199 6.25 19.5299 5.16 15.9599 5.16H13.5799C13.1399 5.16 13.1299 5.15 12.8699 4.81L11.6699 3.2C11.0999 2.46 10.6499 2 9.21988 2H8.03988C4.46988 2 3.37988 3.09 3.37988 6.66V10.8V11.31Z" fill="currentColor"></path>
                    </svg>
                    <span> My Asset </span>
                </a>
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='wallet'){ echo "is-active"; } ?>" href="?r=wallet" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Wallet">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 10.97V13.03C22 13.58 21.56 14.03 21 14.05H19.04C17.96 14.05 16.97 13.26 16.88 12.18C16.82 11.55 17.06 10.96 17.48 10.55C17.85 10.17 18.36 9.95 18.92 9.95H21C21.56 9.97 22 10.42 22 10.97Z" fill="currentColor"/>
                        <path opacity="0.4" d="M19.48 10.55C19.06 10.96 18.82 11.55 18.88 12.18C18.97 13.26 19.96 14.05 21.04 14.05H22V15.5C22 18.5 20 20.5 17 20.5H7C4 20.5 2 18.5 2 15.5V8.5C2 5.78 3.64 3.88 6.19 3.56C6.45 3.52 6.72 3.5 7 3.5H17C17.26 3.5 17.51 3.50999 17.75 3.54999C20.33 3.84999 22 5.76 22 8.5V9.95H18.92C18.36 9.95 17.85 10.17 17.48 10.55Z" fill="currentColor"/>
                        <path d="M7.5 12C8.32843 12 9 11.3284 9 10.5C9 9.67157 8.32843 9 7.5 9C6.67157 9 6 9.67157 6 10.5C6 11.3284 6.67157 12 7.5 12Z" fill="currentColor"/>
                    </svg>
                    <span> Wallet </span>
                </a>
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='launchpad'){ echo "is-active"; } ?>" href="?r=launchpad" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Launch Pad">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 8H8V16H16V8Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M5 22C6.65 22 8 20.65 8 19V16H5C3.35 16 2 17.35 2 19C2 20.65 3.35 22 5 22Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M5 8H8V5C8 3.35 6.65 2 5 2C3.35 2 2 3.35 2 5C2 6.65 3.35 8 5 8Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M16 8H19C20.65 8 22 6.65 22 5C22 3.35 20.65 2 19 2C17.35 2 16 3.35 16 5V8Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M19 22C20.65 22 22 20.65 22 19C22 17.35 20.65 16 19 16H16V19C16 20.65 17.35 22 19 22Z" fill="currentColor"></path>
                    </svg>
                    <span> Launch Pad </span>
                </a>
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='order-history'){ echo "is-active"; } ?>" href="?r=order-history" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Order History">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M6.73049 19.7C7.55049 18.82 8.80049 18.89 9.52049 19.85L10.5305 21.2C11.3405 22.27 12.6505 22.27 13.4605 21.2L14.4705 19.85C15.1905 18.89 16.4405 18.82 17.2605 19.7C19.0405 21.6 20.4905 20.97 20.4905 18.31V7.04C20.5005 3.01 19.5605 2 15.7805 2H8.22049C4.44049 2 3.50049 3.01 3.50049 7.04V18.3C3.50049 20.97 4.96049 21.59 6.73049 19.7Z" fill="currentColor"></path>
                        <path d="M16.2305 11.75H10.7305C10.3205 11.75 9.98047 11.41 9.98047 11C9.98047 10.59 10.3205 10.25 10.7305 10.25H16.2305C16.6405 10.25 16.9805 10.59 16.9805 11C16.9805 11.41 16.6405 11.75 16.2305 11.75Z" fill="currentColor"></path>
                        <path d="M16.2305 7.75H10.7305C10.3205 7.75 9.98047 7.41 9.98047 7C9.98047 6.59 10.3205 6.25 10.7305 6.25H16.2305C16.6405 6.25 16.9805 6.59 16.9805 7C16.9805 7.41 16.6405 7.75 16.2305 7.75Z" fill="currentColor"></path>
                        <path d="M7.78027 8C7.23027 8 6.78027 7.55 6.78027 7C6.78027 6.45 7.23027 6 7.78027 6C8.33027 6 8.78027 6.45 8.78027 7C8.78027 7.55 8.33027 8 7.78027 8Z" fill="currentColor"></path>
                        <path d="M7.78027 12C7.23027 12 6.78027 11.55 6.78027 11C6.78027 10.45 7.23027 10 7.78027 10C8.33027 10 8.78027 10.45 8.78027 11C8.78027 11.55 8.33027 12 7.78027 12Z" fill="currentColor"></path>
                    </svg>
                    <span> Order History </span>
                </a>
            </div>
        </div>
        <!-- Divider -->
        <div class="nav-divider div-transparent mb-2 mt-2"></div>
        <!-- Menu 2 -->
        <div class="side-wrapper">
            <div class="side-title">Accounts</div>
            <div class="side-menu">
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='settings'){ echo "is-active"; } ?>" href="?r=settings" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Settings">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M2 12.8801V11.1201C2 10.0801 2.85 9.22006 3.9 9.22006C5.71 9.22006 6.45 7.94006 5.54 6.37006C5.02 5.47006 5.33 4.30006 6.24 3.78006L7.97 2.79006C8.76 2.32006 9.78 2.60006 10.25 3.39006L10.36 3.58006C11.26 5.15006 12.74 5.15006 13.65 3.58006L13.76 3.39006C14.23 2.60006 15.25 2.32006 16.04 2.79006L17.77 3.78006C18.68 4.30006 18.99 5.47006 18.47 6.37006C17.56 7.94006 18.3 9.22006 20.11 9.22006C21.15 9.22006 22.01 10.0701 22.01 11.1201V12.8801C22.01 13.9201 21.16 14.7801 20.11 14.7801C18.3 14.7801 17.56 16.0601 18.47 17.6301C18.99 18.5401 18.68 19.7001 17.77 20.2201L16.04 21.2101C15.25 21.6801 14.23 21.4001 13.76 20.6101L13.65 20.4201C12.75 18.8501 11.27 18.8501 10.36 20.4201L10.25 20.6101C9.78 21.4001 8.76 21.6801 7.97 21.2101L6.24 20.2201C5.33 19.7001 5.02 18.5301 5.54 17.6301C6.45 16.0601 5.71 14.7801 3.9 14.7801C2.85 14.7801 2 13.9201 2 12.8801Z" fill="currentColor"></path>
                        <path d="M12 15.25C13.7949 15.25 15.25 13.7949 15.25 12C15.25 10.2051 13.7949 8.75 12 8.75C10.2051 8.75 8.75 10.2051 8.75 12C8.75 13.7949 10.2051 15.25 12 15.25Z" fill="currentColor"></path>
                    </svg>
                    <span> Settings </span>
                </a>
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='identification'){ echo "is-active"; } ?>" href="?r=identification" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Identification">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 13C17.06 13 16.19 13.33 15.5 13.88C14.58 14.61 14 15.74 14 17C14 17.75 14.21 18.46 14.58 19.06C15.27 20.22 16.54 21 18 21C19.01 21 19.93 20.63 20.63 20C20.94 19.74 21.21 19.42 21.42 19.06C21.79 18.46 22 17.75 22 17C22 14.79 20.21 13 18 13ZM20.07 16.57L17.94 18.54C17.8 18.67 17.61 18.74 17.43 18.74C17.24 18.74 17.05 18.67 16.9 18.52L15.91 17.53C15.62 17.24 15.62 16.76 15.91 16.47C16.2 16.18 16.68 16.18 16.97 16.47L17.45 16.95L19.05 15.47C19.35 15.19 19.83 15.21 20.11 15.51C20.39 15.81 20.37 16.28 20.07 16.57Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M21.09 21.5C21.09 21.78 20.87 22 20.59 22H3.41003C3.13003 22 2.91003 21.78 2.91003 21.5C2.91003 17.36 6.99003 14 12 14C13.03 14 14.03 14.14 14.95 14.41C14.36 15.11 14 16.02 14 17C14 17.75 14.21 18.46 14.58 19.06C14.78 19.4 15.04 19.71 15.34 19.97C16.04 20.61 16.97 21 18 21C19.12 21 20.13 20.54 20.85 19.8C21.01 20.34 21.09 20.91 21.09 21.5Z" fill="currentColor"></path>
                        <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"></path>
                    </svg>
                    <span> Identification </span>
                </a>
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='security'){ echo "is-active"; } ?>" href="?r=security" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Security">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M20.91 11.12C20.91 16.01 17.36 20.59 12.51 21.93C12.18 22.02 11.82 22.02 11.49 21.93C6.63996 20.59 3.08997 16.01 3.08997 11.12V6.73006C3.08997 5.91006 3.70998 4.98007 4.47998 4.67007L10.05 2.39007C11.3 1.88007 12.71 1.88007 13.96 2.39007L19.53 4.67007C20.29 4.98007 20.92 5.91006 20.92 6.73006L20.91 11.12Z" fill="currentColor"></path>
                        <path d="M14.5 10.5C14.5 9.12 13.38 8 12 8C10.62 8 9.5 9.12 9.5 10.5C9.5 11.62 10.24 12.55 11.25 12.87V15.5C11.25 15.91 11.59 16.25 12 16.25C12.41 16.25 12.75 15.91 12.75 15.5V12.87C13.76 12.55 14.5 11.62 14.5 10.5Z" fill="currentColor"></path>
                    </svg>
                    <span> Security </span>
                </a>
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='user-analysis'){ echo "is-active"; } ?>" href="?r=user-analysis" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Analyze your trading habits and patterns">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M3 3H21V21H3V3Z" fill="currentColor"/>
                        <path d="M7 17H9V13H7V17ZM11 17H13V10H11V17ZM15 17H17V7H15V17Z" fill="currentColor"/>
                    </svg>
                    <span>Analysis</span>
                </a>
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='journey'){ echo "is-active"; } ?>" href="?r=journey" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Your achievements and milestones">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor"/>
                        <path d="M12 4.44L14.47 9.75L20.18 10.53L15.59 14.64L16.76 20.25L12 17.33L7.24 20.25L8.41 14.64L3.82 10.53L9.53 9.75L12 4.44Z" fill="currentColor"/>
                    </svg>
                    <span>Trader Journey</span>
                </a>
                <a class="sidebar-link <?php if(htmlspecialchars($_GET['r'])=='rewards'){ echo "is-active"; } ?>" href="?r=rewards" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Rewards Hub">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M18 20.5H17V20.25C17 19.15 16.1 18.25 15 18.25H12.75V15.96C12.5 15.99 12.25 16 12 16C11.75 16 11.5 15.99 11.25 15.96V18.25H9C7.9 18.25 7 19.15 7 20.25V20.5H6C5.59 20.5 5.25 20.84 5.25 21.25C5.25 21.66 5.59 22 6 22H18C18.41 22 18.75 21.66 18.75 21.25C18.75 20.84 18.41 20.5 18 20.5Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M5.51979 11.64C4.85979 11.39 4.27979 10.98 3.81979 10.52C2.88979 9.49 2.27979 8.26 2.27979 6.82C2.27979 5.38 3.40979 4.25 4.84979 4.25H5.40979C5.14979 4.78 4.99979 5.37 4.99979 6V9C4.99979 9.94 5.17979 10.83 5.51979 11.64Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M21.72 6.82C21.72 8.26 21.11 9.49 20.18 10.52C19.72 10.98 19.14 11.39 18.48 11.64C18.82 10.83 19 9.94 19 9V6C19 5.37 18.85 4.78 18.59 4.25H19.15C20.59 4.25 21.72 5.38 21.72 6.82Z" fill="currentColor"></path>
                        <path d="M15 2H9C6.79 2 5 3.79 5 6V9C5 12.87 8.13 16 12 16C15.87 16 19 12.87 19 9V6C19 3.79 17.21 2 15 2ZM14.84 8.45L14.22 9.21C14.12 9.32 14.05 9.54 14.06 9.69L14.12 10.67C14.16 11.27 13.73 11.58 13.17 11.36L12.26 11C12.12 10.95 11.88 10.95 11.74 11L10.83 11.36C10.27 11.58 9.84 11.27 9.88 10.67L9.94 9.69C9.95 9.54 9.88 9.32 9.78 9.21L9.16 8.45C8.77 7.99 8.94 7.48 9.52 7.33L10.47 7.09C10.62 7.05 10.8 6.91 10.88 6.78L11.41 5.96C11.74 5.45 12.26 5.45 12.59 5.96L13.12 6.78C13.2 6.91 13.38 7.05 13.53 7.09L14.48 7.33C15.06 7.48 15.23 7.99 14.84 8.45Z" fill="currentColor"></path>
                        <path opacity="0.4" d="M14.8402 8.45014L14.2202 9.21014C14.1202 9.32014 14.0502 9.54014 14.0602 9.69014L14.1202 10.6701C14.1602 11.2701 13.7302 11.5801 13.1702 11.3601L12.2602 11.0001C12.1202 10.9501 11.8802 10.9501 11.7402 11.0001L10.8302 11.3601C10.2702 11.5801 9.84024 11.2701 9.88024 10.6701L9.94023 9.69014C9.95023 9.54014 9.88023 9.32014 9.78023 9.21014L9.16023 8.45014C8.77024 7.99014 8.94024 7.48014 9.52024 7.33014L10.4702 7.09014C10.6202 7.05014 10.8002 6.91014 10.8802 6.78014L11.4102 5.96014C11.7402 5.45014 12.2602 5.45014 12.5902 5.96014L13.1202 6.78014C13.2002 6.91014 13.3802 7.05014 13.5302 7.09014L14.4802 7.33014C15.0602 7.48014 15.2302 7.99014 14.8402 8.45014Z" fill="currentColor"></path>
                    </svg>
                    <span> Reward Hub </span>
                </a>
            </div>
        </div>
    </aside>

    

    <!-- Main Content -->
    <div class="wrapper pb-4">

        <!-- Header -->
        <div class="header d-flex align-items-center">

            <!-- Mobile Toggle Button -->
            <span id="sidebar-mobile-toggle">
                <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.25 7.81V16.19C21.25 17.8608 20.7549 19.1045 19.9297 19.9297C19.1045 20.7549 17.8608 21.25 16.19 21.25H9.75V2.75H16.19C17.8608 2.75 19.1045 3.24514 19.9297 4.07033C20.7549 4.89552 21.25 6.13915 21.25 7.81Z" stroke="currentColor" stroke-width="1.5"></path>
                    <path d="M9 2V22H7.81C4.17 22 2 19.83 2 16.19V7.81C2 4.17 4.17 2 7.81 2H9Z" fill="currentColor"></path>
                </svg>
            </span>


            <!-- Collapse Button -->
            <div class="collapse-btn">
                <span id="sidebar-collapse">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.25 7.81V16.19C21.25 17.8608 20.7549 19.1045 19.9297 19.9297C19.1045 20.7549 17.8608 21.25 16.19 21.25H9.75V2.75H16.19C17.8608 2.75 19.1045 3.24514 19.9297 4.07033C20.7549 4.89552 21.25 6.13915 21.25 7.81Z" stroke="currentColor" stroke-width="1.5"></path>
                        <path d="M9 2V22H7.81C4.17 22 2 19.83 2 16.19V7.81C2 4.17 4.17 2 7.81 2H9Z" fill="currentColor"></path>
                    </svg>
                </span>
            </div>

            <div class="user-settings gap-2 gap-sm-3">

                <!-- Dropdown (Assets) -->
                <div class="dropdown profile_menu disable-sm-screen">
                    <a class="nav-link crypto-has-dropdown fw-medium" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Assets
                        <svg xmlns="http://www.w3.org/2000/svg" x="0" y="0" version="1.1" viewbox="0 0 29 29" xml:space="preserve">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2" d="m20.5 11.5-6 6-6-6"></path>
                        </svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-shows">
                        <div>
                            <a class="dropdown-item text-left" href="?r=assets">
                            <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12ZM11.9999 9L8.99985 12L11.9999 15L14.9999 12L11.9999 9Z" fill="currentColor"></path>
                            </svg>
                            Overview
                            </a>
                        </div>
                        <div>
                            <a class="dropdown-item" href="?r=spot"> 
                            <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99996 20.0302C13.3136 20.0302 15.9999 17.3439 15.9999 14.0302C15.9999 10.7166 13.3136 8.03027 9.99996 8.03027C6.68627 8.03027 4 10.7166 4 14.0302C4 17.3439 6.68627 20.0302 9.99996 20.0302ZM10.0001 12.0303L8.0001 14.0302L10.0001 16.0302L12 14.0302L10.0001 12.0303Z" fill="currentColor"></path>
                                <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd" d="M17.9431 13.0726C19.1897 12.1633 19.9997 10.6914 19.9997 9.03024C19.9997 6.26883 17.7612 4.03027 14.9998 4.03027C13.3386 4.03027 11.8667 4.84034 10.9575 6.08698C14.6102 6.52271 17.5073 9.41976 17.9431 13.0726Z" fill="currentColor"></path>
                                <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd" d="M18.811 19.7803L20.5306 21.5L19.47 22.5606L16.5054 19.596C16.1929 19.2836 16.1929 18.777 16.5054 18.4646L19.47 15.5L20.5306 16.5607L18.811 18.2803H23.0003V19.7803H18.811Z" fill="currentColor"></path>
                                <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd" d="M5.18931 5.7803L3.46966 7.49996L4.53031 8.56061L7.49493 5.59599C7.80735 5.28357 7.80735 4.77704 7.49494 4.46463L4.53031 1.5L3.46966 2.56065L5.18931 4.28031H1V5.7803H5.18931Z" fill="currentColor"></path>
                            </svg>
                            Spot
                            </a>
                        </div>
                        <div>
                            <a class="dropdown-item" onclick="JSPop('info','This Feature is not available');"> 
                            <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M6.63526 16.3363C8.65442 15.7953 9.85267 13.7198 9.31164 11.7007C8.77061 9.68151 6.69517 8.48325 4.67602 9.02428C2.65687 9.56531 1.45861 11.6408 1.99964 13.6599C2.54067 15.6791 4.61611 16.8773 6.63526 16.3363Z" fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.6034 13.3978C20.6321 12.5862 22.4295 9.47303 21.6179 6.44429C20.8064 3.41557 17.6932 1.61818 14.6645 2.42972C11.6358 3.24127 9.83842 6.35443 10.6499 9.38316C11.4615 12.4119 14.5746 14.2093 17.6034 13.3978ZM15.6444 6.08567L14.3062 8.40349L16.6241 9.74168L17.9623 7.42386L15.6444 6.08567Z" fill="currentColor"></path>
                                <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd" d="M20.958 14.9477L4.50603 19.356L4.13867 17.985L20.5906 13.5767L20.958 14.9477Z" fill="currentColor"></path>
                                <path opacity="0.4" d="M11.8706 20.4531L13.763 17.6143L15.6555 20.4531H11.8706Z" fill="currentColor"></path>
                            </svg>              
                            Margin
                            </a>
                        </div>
                        <div>
                            <a class="dropdown-item" href="?r=futures"> 
                            <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.9999 17.6361C15.0124 17.6361 17.4545 15.1941 17.4545 12.1816C17.4545 9.16914 15.0124 6.72705 11.9999 6.72705C8.98749 6.72705 6.54541 9.16914 6.54541 12.1816C6.54541 15.1941 8.98749 17.6361 11.9999 17.6361ZM12.0001 10.3634L10.1819 12.1816L12.0001 13.9998L13.8183 12.1816L12.0001 10.3634Z" fill="currentColor"></path>
                                <path opacity="0.4" d="M4.16992 9.80079C5.18954 6.44343 8.30936 4 11.9999 4C16.5185 4 20.1817 7.66312 20.1817 12.1818L22.0001 9.45454" stroke="currentColor" stroke-width="1.36364" stroke-linejoin="round"></path>
                                <path opacity="0.4" d="M19.8302 14.5626C18.8106 17.92 15.6908 20.3634 12.0003 20.3634C7.48159 20.3634 3.81847 16.7003 3.81847 12.1816L2 14.9089" stroke="currentColor" stroke-width="1.36364" stroke-linejoin="round"></path>
                            </svg>
                            Futures
                            </a>
                        </div>
                        <div>
                            <a class="dropdown-item" href="?r=trading-bots"> 
                            <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M3 10H1V14H3V10Z" fill="currentColor"></path>
                                <path opacity="0.4" d="M23 10H21V14H23V10Z" fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6 5C4.89543 5 4 5.89543 4 7V17C4 18.1046 4.89543 19 6 19H18C19.1046 19 20 18.1046 20 17V7C20 5.89543 19.1046 5 18 5H6ZM9.99991 8H7.99991V11H9.99991V8ZM14.3997 13.2004C13.7311 13.7027 12.9019 14 12.0003 14C11.0985 14 10.269 13.7027 9.6005 13.2005L8.39915 14.7995C9.40214 15.553 10.6505 16 12.0003 16C13.3502 16 14.5982 15.5528 15.601 14.7995L14.3997 13.2004ZM13.9998 8H15.9998V11H13.9998V8Z" fill="currentColor"></path>
                            </svg>
                            Trading Bots
                            </a>
                        </div>
                        <div>
                            <a class="dropdown-item" onclick="JSPop('info','This Feature is not available');"> 
                            <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M8.24423 19.9962L3 16.5L3.65836 16.1708C4.50294 15.7485 5.49706 15.7485 6.34164 16.1708L8.15542 17.0777C8.71084 17.3554 9.32329 17.5 9.94427 17.5H13L10.9487 16.8162C10.3821 16.6274 10 16.0972 10 15.5H18.1716C18.702 15.5 19.2107 15.7107 19.5858 16.0858L21 17.5L18 21.5L16 20.5H9.90833C9.31605 20.5 8.73703 20.3247 8.24423 19.9962Z" fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 14.5C12.3137 14.5 15 11.8137 15 8.5C15 5.18629 12.3137 2.5 9 2.5C5.68629 2.5 3 5.18629 3 8.5C3 11.8137 5.68629 14.5 9 14.5ZM9 6.5L7 8.5L9 10.5L11 8.5L9 6.5Z" fill="currentColor"></path>
                                <path opacity="0.4" fill-rule="evenodd" clip-rule="evenodd" d="M15.0005 13.4494C16.0742 12.0877 16.7148 10.3686 16.7148 8.5C16.7148 6.63135 16.0742 4.91234 15.0005 3.55065C15.2338 3.51727 15.4723 3.5 15.7148 3.5C18.4763 3.5 20.7148 5.73858 20.7148 8.5C20.7148 11.2614 18.4763 13.5 15.7148 13.5C15.4723 13.5 15.2338 13.4827 15.0005 13.4494Z" fill="currentColor"></path>
                            </svg>
                            Earn
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Dropdown (Orders) -->
                <div class="dropdown profile_menu disable-sm-screen">

                    <!-- menu link -->
                    <a class="nav-link crypto-has-dropdown fw-medium" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Orders
                        <svg xmlns="http://www.w3.org/2000/svg" x="0" y="0" version="1.1" viewbox="0 0 29 29" xml:space="preserve">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2" d="m20.5 11.5-6 6-6-6"></path>
                        </svg>
                    </a>

                    <!-- dropdown menu link -->
                    <ul class="profile_menu dropdown-menu dropdown-menu-shows">
                        <li>
                            <a class="dropdown-item" href="?r=order-history"> 
                                Spot Orders
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="?r=order-history"> 
                                Margin Orders
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="?r=order-history"> 
                                Funding Orders
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="?r=order-history"> 
                                Earn Orders
                            </a>
                        </li>
                    </ul>

                </div>

                <!-- Call to Action -->
                <div class="flex disable-sm-screen">
                    <a class="btn btn-editor btn-primary" href="?r=wallet" >Add Funds</a>
                </div>

                <!-- Modal (Profile) -->
                <div class="profile_menu d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle no-active d-flex align-items-center" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <img alt="" src="<?php echo $UserData['profile_img']; ?>">
                        </button>
                        <ul class="dropdown-menu">
                            <li class="wallet align-items-center">
                                <img alt="" src="<?php echo $UserData['profile_img']; ?>">
                                <div class="d-grid">
                                    <h6 class="fw-bold crypt-grayscale-100 mb-0"><?php echo Censor($UserData['mail'],'email'); ?></h6>
                                    <a class="d-flex gap-2 mb-2 verified mt-2" href="#!">
                                        <p class="crypt-grayscale-600 mb-0">UID:</p>
                                        <p class="crypt-grayscale-100 mb-0"><?php echo $UserData['uid']; ?></p>
                                    </a>
                                </div>
                            </li>
                            <li class="mt-2">
                            <a class="dropdown-item" href="?r=app">
                                <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M18.6699 2H16.7699C14.5899 2 13.4399 3.15 13.4399 5.33V7.23C13.4399 9.41 14.5899 10.56 16.7699 10.56H18.6699C20.8499 10.56 21.9999 9.41 21.9999 7.23V5.33C21.9999 3.15 20.8499 2 18.6699 2Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M7.24 13.4301H5.34C3.15 13.4301 2 14.5801 2 16.7601V18.6601C2 20.8501 3.15 22.0001 5.33 22.0001H7.23C9.41 22.0001 10.56 20.8501 10.56 18.6701V16.7701C10.57 14.5801 9.42 13.4301 7.24 13.4301Z" fill="currentColor"></path>
                                    <path d="M6.29 10.58C8.6593 10.58 10.58 8.6593 10.58 6.29C10.58 3.9207 8.6593 2 6.29 2C3.9207 2 2 3.9207 2 6.29C2 8.6593 3.9207 10.58 6.29 10.58Z" fill="currentColor"></path>
                                    <path d="M17.7099 22C20.0792 22 21.9999 20.0793 21.9999 17.71C21.9999 15.3407 20.0792 13.42 17.7099 13.42C15.3406 13.42 13.4199 15.3407 13.4199 17.71C13.4199 20.0793 15.3406 22 17.7099 22Z" fill="currentColor"></path>
                                </svg>
                                Dashboard
                            </a>
                            <a class="dropdown-item" href="?r=assets"> 
                                <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21.0602 11.8201L20.9002 11.6001C20.6202 11.2601 20.2902 10.9901 19.9102 10.7901C19.4002 10.5001 18.8202 10.3501 18.2202 10.3501H5.7702C5.1702 10.3501 4.6002 10.5001 4.0802 10.7901C3.6902 11.0001 3.3402 11.2901 3.0502 11.6501C2.4802 12.3801 2.2102 13.2801 2.3002 14.1801L2.6702 18.8501C2.8002 20.2601 2.9702 22.0001 6.1402 22.0001H17.8602C21.0302 22.0001 21.1902 20.2601 21.3302 18.8401L21.7002 14.1901C21.7902 13.3501 21.5702 12.5101 21.0602 11.8201ZM14.3902 17.3401H9.6002C9.2102 17.3401 8.9002 17.0201 8.9002 16.6401C8.9002 16.2601 9.2102 15.9401 9.6002 15.9401H14.3902C14.7802 15.9401 15.0902 16.2601 15.0902 16.6401C15.0902 17.0301 14.7802 17.3401 14.3902 17.3401Z" fill="currentColor"></path>
                                <path opacity="0.4" d="M3.37988 11.31C3.59988 11.11 3.81988 10.93 4.07988 10.79C4.58988 10.5 5.16988 10.35 5.76988 10.35H18.2299C18.8299 10.35 19.3999 10.5 19.9199 10.79C20.1799 10.93 20.4099 11.11 20.6199 11.32V10.79V9.82C20.6199 6.25 19.5299 5.16 15.9599 5.16H13.5799C13.1399 5.16 13.1299 5.15 12.8699 4.81L11.6699 3.2C11.0999 2.46 10.6499 2 9.21988 2H8.03988C4.46988 2 3.37988 3.09 3.37988 6.66V10.8V11.31Z" fill="currentColor"></path>
                                </svg>
                                My Asset
                            </a>
                            <a class="dropdown-item" href="?r=order-history"> 
                                <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M6.73049 19.7C7.55049 18.82 8.80049 18.89 9.52049 19.85L10.5305 21.2C11.3405 22.27 12.6505 22.27 13.4605 21.2L14.4705 19.85C15.1905 18.89 16.4405 18.82 17.2605 19.7C19.0405 21.6 20.4905 20.97 20.4905 18.31V7.04C20.5005 3.01 19.5605 2 15.7805 2H8.22049C4.44049 2 3.50049 3.01 3.50049 7.04V18.3C3.50049 20.97 4.96049 21.59 6.73049 19.7Z" fill="currentColor"></path>
                                    <path d="M16.2305 11.75H10.7305C10.3205 11.75 9.98047 11.41 9.98047 11C9.98047 10.59 10.3205 10.25 10.7305 10.25H16.2305C16.6405 10.25 16.9805 10.59 16.9805 11C16.9805 11.41 16.6405 11.75 16.2305 11.75Z" fill="currentColor"></path>
                                    <path d="M16.2305 7.75H10.7305C10.3205 7.75 9.98047 7.41 9.98047 7C9.98047 6.59 10.3205 6.25 10.7305 6.25H16.2305C16.6405 6.25 16.9805 6.59 16.9805 7C16.9805 7.41 16.6405 7.75 16.2305 7.75Z" fill="currentColor"></path>
                                    <path d="M7.78027 8C7.23027 8 6.78027 7.55 6.78027 7C6.78027 6.45 7.23027 6 7.78027 6C8.33027 6 8.78027 6.45 8.78027 7C8.78027 7.55 8.33027 8 7.78027 8Z" fill="currentColor"></path>
                                    <path d="M7.78027 12C7.23027 12 6.78027 11.55 6.78027 11C6.78027 10.45 7.23027 10 7.78027 10C8.33027 10 8.78027 10.45 8.78027 11C8.78027 11.55 8.33027 12 7.78027 12Z" fill="currentColor"></path>
                                </svg>
                                My Orders
                            </a>
                            <a class="dropdown-item" href="?r=settings"> 
                                <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M2 12.8801V11.1201C2 10.0801 2.85 9.22006 3.9 9.22006C5.71 9.22006 6.45 7.94006 5.54 6.37006C5.02 5.47006 5.33 4.30006 6.24 3.78006L7.97 2.79006C8.76 2.32006 9.78 2.60006 10.25 3.39006L10.36 3.58006C11.26 5.15006 12.74 5.15006 13.65 3.58006L13.76 3.39006C14.23 2.60006 15.25 2.32006 16.04 2.79006L17.77 3.78006C18.68 4.30006 18.99 5.47006 18.47 6.37006C17.56 7.94006 18.3 9.22006 20.11 9.22006C21.15 9.22006 22.01 10.0701 22.01 11.1201V12.8801C22.01 13.9201 21.16 14.7801 20.11 14.7801C18.3 14.7801 17.56 16.0601 18.47 17.6301C18.99 18.5401 18.68 19.7001 17.77 20.2201L16.04 21.2101C15.25 21.6801 14.23 21.4001 13.76 20.6101L13.65 20.4201C12.75 18.8501 11.27 18.8501 10.36 20.4201L10.25 20.6101C9.78 21.4001 8.76 21.6801 7.97 21.2101L6.24 20.2201C5.33 19.7001 5.02 18.5301 5.54 17.6301C6.45 16.0601 5.71 14.7801 3.9 14.7801C2.85 14.7801 2 13.9201 2 12.8801Z" fill="currentColor"></path>
                                    <path d="M12 15.25C13.7949 15.25 15.25 13.7949 15.25 12C15.25 10.2051 13.7949 8.75 12 8.75C10.2051 8.75 8.75 10.2051 8.75 12C8.75 13.7949 10.2051 15.25 12 15.25Z" fill="currentColor"></path>
                                </svg>
                                Account Settings
                            </a>
                            <a class="dropdown-item" href="?r=security"> 
                                <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M20.91 11.12C20.91 16.01 17.36 20.59 12.51 21.93C12.18 22.02 11.82 22.02 11.49 21.93C6.63996 20.59 3.08997 16.01 3.08997 11.12V6.73006C3.08997 5.91006 3.70998 4.98007 4.47998 4.67007L10.05 2.39007C11.3 1.88007 12.71 1.88007 13.96 2.39007L19.53 4.67007C20.29 4.98007 20.92 5.91006 20.92 6.73006L20.91 11.12Z" fill="currentColor"></path>
                                    <path d="M14.5 10.5C14.5 9.12 13.38 8 12 8C10.62 8 9.5 9.12 9.5 10.5C9.5 11.62 10.24 12.55 11.25 12.87V15.5C11.25 15.91 11.59 16.25 12 16.25C12.41 16.25 12.75 15.91 12.75 15.5V12.87C13.76 12.55 14.5 11.62 14.5 10.5Z" fill="currentColor"></path>
                                </svg>
                                Security
                            </a>
                            <a class="dropdown-item" href="?r=rewards"> 
                                <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M18 20.5H17V20.25C17 19.15 16.1 18.25 15 18.25H12.75V15.96C12.5 15.99 12.25 16 12 16C11.75 16 11.5 15.99 11.25 15.96V18.25H9C7.9 18.25 7 19.15 7 20.25V20.5H6C5.59 20.5 5.25 20.84 5.25 21.25C5.25 21.66 5.59 22 6 22H18C18.41 22 18.75 21.66 18.75 21.25C18.75 20.84 18.41 20.5 18 20.5Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M5.51979 11.64C4.85979 11.39 4.27979 10.98 3.81979 10.52C2.88979 9.49 2.27979 8.26 2.27979 6.82C2.27979 5.38 3.40979 4.25 4.84979 4.25H5.40979C5.14979 4.78 4.99979 5.37 4.99979 6V9C4.99979 9.94 5.17979 10.83 5.51979 11.64Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M21.72 6.82C21.72 8.26 21.11 9.49 20.18 10.52C19.72 10.98 19.14 11.39 18.48 11.64C18.82 10.83 19 9.94 19 9V6C19 5.37 18.85 4.78 18.59 4.25H19.15C20.59 4.25 21.72 5.38 21.72 6.82Z" fill="currentColor"></path>
                                    <path d="M15 2H9C6.79 2 5 3.79 5 6V9C5 12.87 8.13 16 12 16C15.87 16 19 12.87 19 9V6C19 3.79 17.21 2 15 2ZM14.84 8.45L14.22 9.21C14.12 9.32 14.05 9.54 14.06 9.69L14.12 10.67C14.16 11.27 13.73 11.58 13.17 11.36L12.26 11C12.12 10.95 11.88 10.95 11.74 11L10.83 11.36C10.27 11.58 9.84 11.27 9.88 10.67L9.94 9.69C9.95 9.54 9.88 9.32 9.78 9.21L9.16 8.45C8.77 7.99 8.94 7.48 9.52 7.33L10.47 7.09C10.62 7.05 10.8 6.91 10.88 6.78L11.41 5.96C11.74 5.45 12.26 5.45 12.59 5.96L13.12 6.78C13.2 6.91 13.38 7.05 13.53 7.09L14.48 7.33C15.06 7.48 15.23 7.99 14.84 8.45Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M14.8402 8.45014L14.2202 9.21014C14.1202 9.32014 14.0502 9.54014 14.0602 9.69014L14.1202 10.6701C14.1602 11.2701 13.7302 11.5801 13.1702 11.3601L12.2602 11.0001C12.1202 10.9501 11.8802 10.9501 11.7402 11.0001L10.8302 11.3601C10.2702 11.5801 9.84024 11.2701 9.88024 10.6701L9.94023 9.69014C9.95023 9.54014 9.88023 9.32014 9.78023 9.21014L9.16023 8.45014C8.77024 7.99014 8.94024 7.48014 9.52024 7.33014L10.4702 7.09014C10.6202 7.05014 10.8002 6.91014 10.8802 6.78014L11.4102 5.96014C11.7402 5.45014 12.2602 5.45014 12.5902 5.96014L13.1202 6.78014C13.2002 6.91014 13.3802 7.05014 13.5302 7.09014L14.4802 7.33014C15.0602 7.48014 15.2302 7.99014 14.8402 8.45014Z" fill="currentColor"></path>
                                </svg>
                                Reward Hub
                            </a>
                            <a class="dropdown-item" href="core/uploads/apk/MobileApp.apk" download>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.82 4.17 21.99 7.81 21.99H16.18C19.82 21.99 21.99 19.82 21.99 16.18V7.81C22 4.17 19.83 2 16.19 2Z" fill="currentColor"/>
                                    <path d="M12 16C12.41 16 12.75 15.66 12.75 15.25V7.81L14.47 9.53C14.76 9.82 15.24 9.82 15.53 9.53C15.82 9.24 15.82 8.76 15.53 8.47L12.53 5.47C12.24 5.18 11.76 5.18 11.47 5.47L8.47 8.47C8.18 8.76 8.18 9.24 8.47 9.53C8.76 9.82 9.24 9.82 9.53 9.53L11.25 7.81V15.25C11.25 15.66 11.59 16 12 16Z" fill="currentColor"/>
                                    <path d="M17 19.25H7C6.59 19.25 6.25 18.91 6.25 18.5C6.25 18.09 6.59 17.75 7 17.75H17C17.41 17.75 17.75 18.09 17.75 18.5C17.75 18.91 17.41 19.25 17 19.25Z" fill="currentColor"/>
                                </svg>
                                Download Mobile App
                            </a>
                            <a class="dropdown-item" href="?r=logout"> 
                                <svg width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M16.19 2H7.82002C4.18002 2 2.01001 4.17 2.01001 7.81V16.18C2.01001 19.82 4.18002 21.99 7.82002 21.99H16.19C19.83 21.99 22 19.82 22 16.18V7.81C22 4.17 19.83 2 16.19 2Z" fill="currentColor"></path>
                                    <path d="M9.16985 14.0899C9.35985 14.0899 9.54985 14.0199 9.69985 13.8699L14.0799 9.48992V11.9199C14.0799 12.3299 14.4199 12.6699 14.8299 12.6699C15.2399 12.6699 15.5799 12.3299 15.5799 11.9199V7.67992C15.5799 7.57992 15.5599 7.48992 15.5199 7.38992C15.4399 7.20992 15.2998 7.05993 15.1098 6.97993C15.0198 6.93993 14.9199 6.91992 14.8199 6.91992H10.5799C10.1699 6.91992 9.82985 7.25992 9.82985 7.66992C9.82985 8.07992 10.1699 8.41992 10.5799 8.41992H13.0098L8.62985 12.7999C8.33985 13.0899 8.33985 13.5699 8.62985 13.8599C8.78985 14.0199 8.97985 14.0899 9.16985 14.0899Z" fill="currentColor"></path>
                                    <path d="M18.7099 16.28C18.5799 15.89 18.1599 15.68 17.7599 15.81C14.0399 17.05 9.94993 17.05 6.22993 15.81C5.83993 15.68 5.40994 15.89 5.27994 16.28C5.14994 16.67 5.35994 17.1 5.74994 17.23C7.75994 17.9 9.86994 18.24 11.9899 18.24C14.1099 18.24 16.2199 17.9 18.2299 17.23C18.6299 17.09 18.8399 16.67 18.7099 16.28Z" fill="currentColor"></path>
                                </svg>
                                Log Out
                            </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Notification -->
                <div class="controller">
                    <a href="#!" class="notify" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNotify">
                        <svg viewbox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M18.707 8.796c0 1.256.332 1.997 1.063 2.85.553.628.73 1.435.73 2.31 0 .874-.287 1.704-.863 2.378a4.537 4.537 0 01-2.9 1.413c-1.571.134-3.143.247-4.736.247-1.595 0-3.166-.068-4.737-.247a4.532 4.532 0 01-2.9-1.413 3.616 3.616 0 01-.864-2.378c0-.875.178-1.682.73-2.31.754-.854 1.064-1.594 1.064-2.85V8.37c0-1.682.42-2.781 1.283-3.858C7.861 2.942 9.919 2 11.956 2h.09c2.08 0 4.204.987 5.466 2.625.82 1.054 1.195 2.108 1.195 3.745v.426zM9.074 20.061c0-.504.462-.734.89-.833.5-.106 3.545-.106 4.045 0 .428.099.89.33.89.833-.025.48-.306.904-.695 1.174a3.635 3.635 0 01-1.713.731 3.795 3.795 0 01-1.008 0 3.618 3.618 0 01-1.714-.732c-.39-.269-.67-.694-.695-1.173z"></path>
                        </svg>
                    </a>
                </div>

                <!-- dark/light mode -->
                <div class="mode">
                    <button class="controller m-auto" id="darkMode">
                        <span class="dark-logo">
                            <svg viewbox="0 0 512 512" width="100" fill="currentColor">
                                <path d="M283.211 512c78.962 0 151.079-35.925 198.857-94.792 7.068-8.708-.639-21.43-11.562-19.35-124.203 23.654-238.262-71.576-238.262-196.954 0-72.222 38.662-138.635 101.498-174.394 9.686-5.512 7.25-20.197-3.756-22.23A258.156 258.156 0 0 0 283.211 0c-141.309 0-256 114.511-256 256 0 141.309 114.511 256 256 256z"></path>
                            </svg>
                        </span>
                        <span class="light-logo">
                            <svg viewbox="0 0 512 512" width="100" fill="currentColor">
                                <path d="M256 160c-52.9 0-96 43.1-96 96s43.1 96 96 96 96-43.1 96-96-43.1-96-96-96zm246.4 80.5l-94.7-47.3 33.5-100.4c4.5-13.6-8.4-26.5-21.9-21.9l-100.4 33.5-47.4-94.8c-6.4-12.8-24.6-12.8-31 0l-47.3 94.7L92.7 70.8c-13.6-4.5-26.5 8.4-21.9 21.9l33.5 100.4-94.7 47.4c-12.8 6.4-12.8 24.6 0 31l94.7 47.3-33.5 100.5c-4.5 13.6 8.4 26.5 21.9 21.9l100.4-33.5 47.3 94.7c6.4 12.8 24.6 12.8 31 0l47.3-94.7 100.4 33.5c13.6 4.5 26.5-8.4 21.9-21.9l-33.5-100.4 94.7-47.3c13-6.5 13-24.7.2-31.1zm-155.9 106c-49.9 49.9-131.1 49.9-181 0-49.9-49.9-49.9-131.1 0-181 49.9-49.9 131.1-49.9 181 0 49.9 49.9 49.9 131.1 0 181z"></path>
                            </svg>
                        </span>
                    </button>
                </div>

                <!-- Hamburger Menu -->
                <div id="mobile_menu" class="close">
                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar">
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            <path d="M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            <path d="M4 6H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                        </svg>
                    </button>
                </div>

            </div>
            
        </div>
        <!-- Offcanvas Navbar -->
        <nav class="navbar py-0 navbar-dark bg-dark">
            <div class="container-fluid">
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDarkNavbar">

                    <!-- header -->
                    <div class="offcanvas-header card-line">

                        <div class="d-flex align-items-center gap-3 crypt-grayscale-100 text-uppercase mb-0 fw-bold">
                           <?php echo $WebsData['BaseName']; ?>
                        </div>

                        <button type="button" class="btn-close text-reset close-notify align-items-center justify-content-center" data-bs-dismiss="offcanvas" aria-label="Close">
                            <svg class="close-notify" width="18" height="18" viewbox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.5 4.5L4.5 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M4.5 4.5L13.5 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>

                    </div>

                    <!-- main menu -->
                    <div class="offcanvas-body">

                        <!-- Dropdown (Assets) -->
                        <div class="dropdown-menu-dark card-bs gd-bg p-2 pb-2">
                        
                            <!-- Menu Item -->
                            <ul class="d-flex flex-column profile_menu gap-2">
                                <li class="nav-item">
                                    <a class="nav-link" href="?r=app">
                                        <svg class="me-1" width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.4" d="M18.6699 2H16.7699C14.5899 2 13.4399 3.15 13.4399 5.33V7.23C13.4399 9.41 14.5899 10.56 16.7699 10.56H18.6699C20.8499 10.56 21.9999 9.41 21.9999 7.23V5.33C21.9999 3.15 20.8499 2 18.6699 2Z" fill="currentColor"></path>
                                            <path opacity="0.4" d="M7.24 13.4301H5.34C3.15 13.4301 2 14.5801 2 16.7601V18.6601C2 20.8501 3.15 22.0001 5.33 22.0001H7.23C9.41 22.0001 10.56 20.8501 10.56 18.6701V16.7701C10.57 14.5801 9.42 13.4301 7.24 13.4301Z" fill="currentColor"></path>
                                            <path d="M6.29 10.58C8.6593 10.58 10.58 8.6593 10.58 6.29C10.58 3.9207 8.6593 2 6.29 2C3.9207 2 2 3.9207 2 6.29C2 8.6593 3.9207 10.58 6.29 10.58Z" fill="currentColor"></path>
                                            <path d="M17.7099 22C20.0792 22 21.9999 20.0793 21.9999 17.71C21.9999 15.3407 20.0792 13.42 17.7099 13.42C15.3406 13.42 13.4199 15.3407 13.4199 17.71C13.4199 20.0793 15.3406 22 17.7099 22Z" fill="currentColor"></path>
                                        </svg>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="?r=marketplace"> 
                                        <svg class="me-1" width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20.9597 17.84L19.3297 18.39C18.8797 18.54 18.5197 18.89 18.3697 19.35L17.8197 20.98C17.3497 22.39 15.3697 22.36 14.9297 20.95L13.0797 15C12.7197 13.82 13.8097 12.72 14.9797 13.09L20.9397 14.94C22.3397 15.38 22.3597 17.37 20.9597 17.84Z" fill="currentColor"></path>
                                            <path opacity="0.4" d="M11.5 21C16.7467 21 21 16.7467 21 11.5C21 6.25329 16.7467 2 11.5 2C6.25329 2 2 6.25329 2 11.5C2 16.7467 6.25329 21 11.5 21Z" fill="currentColor"></path>
                                        </svg>
                                        Marketplace
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="?r=assets"> 
                                        <svg class="me-1" width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21.0602 11.8201L20.9002 11.6001C20.6202 11.2601 20.2902 10.9901 19.9102 10.7901C19.4002 10.5001 18.8202 10.3501 18.2202 10.3501H5.7702C5.1702 10.3501 4.6002 10.5001 4.0802 10.7901C3.6902 11.0001 3.3402 11.2901 3.0502 11.6501C2.4802 12.3801 2.2102 13.2801 2.3002 14.1801L2.6702 18.8501C2.8002 20.2601 2.9702 22.0001 6.1402 22.0001H17.8602C21.0302 22.0001 21.1902 20.2601 21.3302 18.8401L21.7002 14.1901C21.7902 13.3501 21.5702 12.5101 21.0602 11.8201ZM14.3902 17.3401H9.6002C9.2102 17.3401 8.9002 17.0201 8.9002 16.6401C8.9002 16.2601 9.2102 15.9401 9.6002 15.9401H14.3902C14.7802 15.9401 15.0902 16.2601 15.0902 16.6401C15.0902 17.0301 14.7802 17.3401 14.3902 17.3401Z" fill="currentColor"></path>
                                            <path opacity="0.4" d="M3.37988 11.31C3.59988 11.11 3.81988 10.93 4.07988 10.79C4.58988 10.5 5.16988 10.35 5.76988 10.35H18.2299C18.8299 10.35 19.3999 10.5 19.9199 10.79C20.1799 10.93 20.4099 11.11 20.6199 11.32V10.79V9.82C20.6199 6.25 19.5299 5.16 15.9599 5.16H13.5799C13.1399 5.16 13.1299 5.15 12.8699 4.81L11.6699 3.2C11.0999 2.46 10.6499 2 9.21988 2H8.03988C4.46988 2 3.37988 3.09 3.37988 6.66V10.8V11.31Z" fill="currentColor"></path>
                                        </svg>
                                        My Asset
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="?r=order-history"> 
                                        <svg class="me-1" width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.4" d="M6.73049 19.7C7.55049 18.82 8.80049 18.89 9.52049 19.85L10.5305 21.2C11.3405 22.27 12.6505 22.27 13.4605 21.2L14.4705 19.85C15.1905 18.89 16.4405 18.82 17.2605 19.7C19.0405 21.6 20.4905 20.97 20.4905 18.31V7.04C20.5005 3.01 19.5605 2 15.7805 2H8.22049C4.44049 2 3.50049 3.01 3.50049 7.04V18.3C3.50049 20.97 4.96049 21.59 6.73049 19.7Z" fill="currentColor"></path>
                                            <path d="M16.2305 11.75H10.7305C10.3205 11.75 9.98047 11.41 9.98047 11C9.98047 10.59 10.3205 10.25 10.7305 10.25H16.2305C16.6405 10.25 16.9805 10.59 16.9805 11C16.9805 11.41 16.6405 11.75 16.2305 11.75Z" fill="currentColor"></path>
                                            <path d="M16.2305 7.75H10.7305C10.3205 7.75 9.98047 7.41 9.98047 7C9.98047 6.59 10.3205 6.25 10.7305 6.25H16.2305C16.6405 6.25 16.9805 6.59 16.9805 7C16.9805 7.41 16.6405 7.75 16.2305 7.75Z" fill="currentColor"></path>
                                            <path d="M7.78027 8C7.23027 8 6.78027 7.55 6.78027 7C6.78027 6.45 7.23027 6 7.78027 6C8.33027 6 8.78027 6.45 8.78027 7C8.78027 7.55 8.33027 8 7.78027 8Z" fill="currentColor"></path>
                                            <path d="M7.78027 12C7.23027 12 6.78027 11.55 6.78027 11C6.78027 10.45 7.23027 10 7.78027 10C8.33027 10 8.78027 10.45 8.78027 11C8.78027 11.55 8.33027 12 7.78027 12Z" fill="currentColor"></path>
                                        </svg>
                                        Order History
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="?r=exchange"> 
                                        <svg class="me-1" width="24" height="24" viewbox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15 15.5C15 19.09 12.09 22 8.5 22C4.91 22 2 19.09 2 15.5C2 11.91 4.91 9 8.5 9C8.67 9 8.84999 9.01 9.01999 9.02C12.19 9.27 14.73 11.81 14.98 14.98C14.99 15.15 15 15.33 15 15.5Z" fill="currentColor"></path>
                                            <path opacity="0.4" d="M22 8.5C22 12.09 19.09 15 15.5 15C15.33 15 15.15 14.99 14.98 14.98C14.73 11.81 12.19 9.27 9.01999 9.02C9.00999 8.85 9 8.67 9 8.5C9 4.91 11.91 2 15.5 2C19.09 2 22 4.91 22 8.5Z" fill="currentColor"></path>
                                            <path d="M5.59 2H3C2.45 2 2 2.45 2 3V5.59C2 6.48 3.07999 6.93 3.70999 6.3L6.29999 3.71001C6.91999 3.08001 6.48 2 5.59 2Z" fill="currentColor"></path>
                                            <path d="M18.41 22H21C21.55 22 22 21.55 22 21V18.41C22 17.52 20.92 17.07 20.29 17.7L17.7 20.29C17.08 20.92 17.52 22 18.41 22Z" fill="currentColor"></path>
                                        </svg>
                                        Exchange
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Call to Action -->
                        <span class="d-grid">
                            <a class="btn btn-editor btn-primary" href="?r=wallet">Add Funds</a>
                        </span>
                    </div>

                </div>
            </div>
        </nav>