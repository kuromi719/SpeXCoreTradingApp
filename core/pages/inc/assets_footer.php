    <div class="modal fade" id="mysteryBoxDropModal" tabindex="-1" aria-labelledby="mysteryBoxDropModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #1a1a1a; border: none; border-radius: 15px;">
                <div class="modal-body text-center p-5" id="mysteryBoxDropContent">
                    
                    <div id="dropFoundPhase">
                        <h3 class="text-white mb-4">🎁 Mystery Box Dropped!</h3>
                        <p class="text-white mb-4">You found a mystery box! Double tap to open it and claim your reward.</p>
                        
                        <div class="mystery-drop-container">
                            <div class="mystery-drop-box" id="mysteryDropBox">
                                <div class="drop-box-emoji">🎁</div>
                                <div class="drop-box-name" id="dropBoxName">Loading...</div>
                                <div class="drop-box-description" id="dropBoxDescription">Loading...</div>
                            </div>
                            <div class="tap-instruction">Double tap the box to open!</div>
                        </div>
                    </div>
                    
                    <div id="openingPhase" style="display: none;">
                        <h3 class="text-white mb-4">Opening Mystery Box...</h3>
                        <br>
                        <div class="opening-animation">
                            <div class="opening-box-emoji" id="openingEmoji">🎁</div>
                        </div>
                    </div>
                    
                    <div id="rewardRevealPhase" style="display: none;">
                        <h3 class="text-white mb-4">🎉 Congratulations!</h3>
                        <div class="reward-showcase">
                            <div class="reward-rarity-badge" id="rewardRarityBadge"></div>
                            <div class="reward-icon-display" id="rewardIconDisplay"></div>
                            <div class="reward-amount-display" id="rewardAmountDisplay"></div>
                            <div class="reward-description-text" id="rewardDescriptionText"></div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer" style="border: none; background: transparent;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeMysteryModal">Close</button>
                    <button type="button" class="btn btn-success" id="claimRewardBtn" style="display: none;">Claim Reward</button>
                </div>
            </div>
        </div>
    </div>

    <div id="mysteryBoxDropNotification" class="mystery-drop-notification" style="display: none;">
        <div class="drop-notification-content">
            <div class="notification-icon">🎁</div>
            <div class="notification-text">
                <h5>Mystery Box Dropped!</h5>
                <p>A mystery box appeared! Click to claim it.</p>
            </div>
            <div class="notification-actions">
                <button class="btn btn-primary btn-sm" onclick="openMysteryDropModal()">Open Box</button>
                <button class="btn btn-secondary btn-sm" onclick="hideMysteryDropNotification()">Later</button>
            </div>
        </div>
    </div>

    <script>
    let currentDropData = null;
    let clickCount = 0;
    let clickTimer = null;
    let isProcessingClaim = false;

    $(document).ready(function() {
        initializeMysteryBoxSystem();
        
        setInterval(function() {
            checkForMysteryBoxDrops();
        }, 30000);
        
        setTimeout(checkForMysteryBoxDrops, 2000);
    });

    function initializeMysteryBoxSystem() {
        $.ajax({
            url: 'core/ajax/mysterybox/mysterybox_handler.php',
            type: 'POST',
            data: { action: 'checkSystem' },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.is_active) {
                    //console.log('Mystery Box System is ACTIVE');
                } else {
                    //console.log('Mystery Box System is INACTIVE');
                }
            }
        });
    }

    function checkForMysteryBoxDrops() {
        $.ajax({
            url: 'core/ajax/mysterybox/mysterybox_handler.php',
            type: 'POST',
            data: { action: 'checkDrops' },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.hasUnclaimedDrops) {
                    console.log(`Found ${response.data.count} unclaimed drops!`);
                    showMysteryDropNotification();
                }
            },
            error: function() {
                console.log('Error checking for mystery drops');
            }
        });
    }

    function showMysteryDropNotification() {
        $('#mysteryBoxDropNotification').fadeIn(500);
        
        setTimeout(() => {
            if ($('#mysteryBoxDropNotification').is(':visible')) {
                hideMysteryDropNotification();
            }
        }, 15000);
    }

    function hideMysteryDropNotification() {
        $('#mysteryBoxDropNotification').fadeOut(500);
    }

    function openMysteryDropModal() {
        hideMysteryDropNotification();
        $('#mysteryBoxDropModal').modal('show');
        loadUnclaimedDrop();
    }

    function loadUnclaimedDrop() {
        $('#dropFoundPhase').show();
        $('#openingPhase').hide();
        $('#rewardRevealPhase').hide();
        $('#claimRewardBtn').hide();
        
        $.ajax({
            url: 'core/ajax/mysterybox/mysterybox_handler.php',
            type: 'POST',
            data: { action: 'getUnclaimedDrop' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentDropData = response.data;
                    displayDropBox(response.data);
                    setupDoubleClickHandler();
                } else {
                    JSPop('info', 'No mystery boxes available');
                    $('#mysteryBoxDropModal').modal('hide');
                }
            },
            error: function() {
                JSPop('error', 'Failed to load mystery box');
                $('#mysteryBoxDropModal').modal('hide');
            }
        });
    }

    function displayDropBox(dropData) {
        $('#dropBoxName').text(dropData.box_name);
        $('#dropBoxDescription').text(dropData.box_description);
        
        const rarityClass = `rarity-${dropData.rarity}`;
        $('#mysteryDropBox').attr('class', `mystery-drop-box ${rarityClass}`);
    }

    function setupDoubleClickHandler() {
        const dropBox = $('#mysteryDropBox');
        
        dropBox.off('click').on('click', function() {
            clickCount++;
            
            if (clickCount === 1) {
                clickTimer = setTimeout(() => {
                    clickCount = 0;
                }, 400);
            } else if (clickCount === 2) {
                clearTimeout(clickTimer);
                clickCount = 0;
                startOpeningAnimation();
            }
        });
    }

    function startOpeningAnimation() {
        if (isProcessingClaim) return;
        
        $('#dropFoundPhase').fadeOut(300, function() {
            $('#openingPhase').fadeIn(300);
            
            const openingEmoji = $('#openingEmoji')[0];
            openingEmoji.style.animation = 'mysteryBoxOpening 2s ease-in-out';
            
            setTimeout(claimMysteryBoxReward, 2000);
        });
    }

    function claimMysteryBoxReward() {
        if (isProcessingClaim) return;
        isProcessingClaim = true;
        
        $.ajax({
            url: 'core/ajax/mysterybox/mysterybox_handler.php',
            type: 'POST',
            data: { action: 'claimDrop' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showRewardReveal(response.data);
                    triggerConfettiEffect();
                    
                    JSPop('success', `MysteryBox Claimed Successfully!`);
                } else {
                    JSPop('error', response.message);
                    $('#mysteryBoxDropModal').modal('hide');
                }
                isProcessingClaim = false;
            },
            error: function() {
                JSPop('error', 'Failed to claim reward');
                $('#mysteryBoxDropModal').modal('hide');
                isProcessingClaim = false;
            }
        });
    }

    function showRewardReveal(rewardData) {
        $('#openingPhase').fadeOut(300, function() {
            const rarityClass = `rarity-${rewardData.rarity}`;
            $('#rewardRarityBadge').attr('class', `reward-rarity-badge ${rarityClass}`).text(rewardData.rarity.toUpperCase());
            
            let rewardIcon = '';
            let rewardText = '';
            let rewardDescription = '';
            
            if (rewardData.reward_type === 'usd') {
                rewardIcon = '💵';
                rewardText = `$${rewardData.reward_usd_value}`;
                rewardDescription = `You received ${rewardText} USD credit to your account!`;
            } else {
                rewardIcon = '💰';
                rewardText = `${rewardData.reward_amount} ${rewardData.coin_symbol}`;
                rewardDescription = `You received ${rewardText} cryptocurrency!`;
            }
            
            $('#rewardIconDisplay').text(rewardIcon);
            $('#rewardAmountDisplay').text(rewardText);
            $('#rewardDescriptionText').text(rewardDescription);
            
            $('#rewardRevealPhase').fadeIn(500);
            $('#claimRewardBtn').show();
        });
    }

    function triggerConfettiEffect() {
        confetti({
            particleCount: 150,
            spread: 70,
            origin: { y: 0.3 },
            colors: ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7', '#dda0dd', '#98d8c8']
        });
        
        setTimeout(() => {
            confetti({
                particleCount: 100,
                spread: 100,
                origin: { y: 0.4 },
                colors: ['#fd79a8', '#fdcb6e', '#e17055', '#00b894', '#6c5ce7']
            });
        }, 300);
        
        setTimeout(() => {
            confetti({
                particleCount: 80,
                spread: 60,
                origin: { y: 0.5 },
                colors: ['#a29bfe', '#fd79a8', '#00cec9', '#55a3ff']
            });
        }, 600);
    }

    $('#closeMysteryModal, #claimRewardBtn').click(function() {
        $('#mysteryBoxDropModal').modal('hide');
        currentDropData = null;
        isProcessingClaim = false;
        
        setTimeout(checkForMysteryBoxDrops, 1000);
    });

    function simulateMysteryDrop() {
        $.ajax({
            url: 'core/ajax/mysterybox/mysterybox_handler.php',
            type: 'POST',
            data: { action: 'simulateDrop' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    JSPop('success', 'Mystery box simulated! Check for drops.');
                    setTimeout(checkForMysteryBoxDrops, 1000);
                } else {
                    JSPop('error', response.message);
                }
            }
        });
    }

    window.openMysteryBox = openMysteryDropModal;

    </script>

    <style>

    .mystery-drop-notification {
        position: fixed;
        top: 80px;
        right: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        z-index: 9998;
        max-width: 350px;
        animation: mysteryDropSlide 0.6s ease-out;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .drop-notification-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .notification-icon {
        font-size: 3em;
        animation: mysteryBoxBounce 2s infinite;
    }

    .notification-text h5 {
        margin: 0 0 5px 0;
        font-size: 1.2em;
        font-weight: bold;
    }

    .notification-text p {
        margin: 0 0 10px 0;
        font-size: 0.9em;
        opacity: 0.9;
    }

    .notification-actions .btn {
        margin-right: 8px;
        font-size: 0.85em;
        padding: 6px 12px;
    }

    .mystery-drop-container {
        margin: 2em 0;
        position: relative;
    }

    .mystery-drop-box {
        background: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
        border-radius: 20px;
        padding: 3em 2em;
        cursor: pointer;
        transition: all 0.4s ease;
        border: 3px solid transparent;
        position: relative;
        overflow: hidden;
        max-width: 400px;
        margin: 0 auto;
    }

    .mystery-drop-box::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%; 
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: mysteryBoxShine 3s infinite;
        pointer-events: none;
    }

    .mystery-drop-box:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    }

    .mystery-drop-box.rarity-common {
        border-color: #6c757d;
        box-shadow: 0 0 20px rgba(108, 117, 125, 0.3);
    }

    .mystery-drop-box.rarity-rare {
        border-color: #007bff;
        box-shadow: 0 0 20px rgba(0, 123, 255, 0.4);
    }

    .mystery-drop-box.rarity-epic {
        border-color: #6f42c1;
        box-shadow: 0 0 20px rgba(111, 66, 193, 0.5);
    }

    .mystery-drop-box.rarity-legendary {
        border-color: #fd7e14;
        box-shadow: 0 0 25px rgba(253, 126, 20, 0.6);
        background: linear-gradient(135deg, #fd7e14 0%, #ff6b35 100%);
    }

    .drop-box-emoji {
        font-size: 5em;
        margin-bottom: 0.5em;
        animation: mysteryBoxFloat 3s ease-in-out infinite;
    }

    .drop-box-name {
        color: white;
        font-weight: bold;
        font-size: 1.4em;
        margin-bottom: 0.5em;
    }

    .drop-box-description {
        color: #adb5bd;
        font-size: 1em;
        line-height: 1.4;
    }

    .tap-instruction {
        color: #ffc107;
        font-size: 1.1em;
        margin-top: 1.5em;
        animation: mysteryBoxPulse 2s infinite;
        font-weight: 500;
    }

    .opening-animation {
        margin: 2em 0;
    }

    .opening-box-emoji {
        font-size: 8em;
        display: inline-block;
    }

    .reward-showcase {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 3em 2em;
        margin: 1em 0;
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .reward-showcase::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.15), transparent);
        animation: rewardShine 2s infinite;
    }

    .reward-rarity-badge {
        display: inline-block;
        padding: 0.8em 1.5em;
        border-radius: 30px;
        font-weight: bold;
        font-size: 1em;
        margin-bottom: 1.5em;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .reward-rarity-badge.rarity-common { 
        background: #6c757d; 
        color: white; 
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
    }

    .reward-rarity-badge.rarity-rare { 
        background: #007bff; 
        color: white; 
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
    }

    .reward-rarity-badge.rarity-epic { 
        background: #6f42c1; 
        color: white; 
        box-shadow: 0 4px 15px rgba(111, 66, 193, 0.4);
    }

    .reward-rarity-badge.rarity-legendary { 
        background: #fd7e14; 
        color: white; 
        box-shadow: 0 4px 15px rgba(253, 126, 20, 0.4);
    }

    .reward-icon-display {
        font-size: 5em;
        margin: 1em 0;
        animation: rewardIconBounce 1s ease-out;
    }

    .reward-amount-display {
        font-size: 2.5em;
        font-weight: bold;
        color: white;
        margin: 0.5em 0;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .reward-description-text {
        font-size: 1.2em;
        color: rgba(255, 255, 255, 0.9);
        margin-top: 1em;
        line-height: 1.5;
    }

    /* Animations */
    @keyframes mysteryDropSlide {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes mysteryBoxBounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-12px); }
        60% { transform: translateY(-6px); }
    }

    @keyframes mysteryBoxFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    @keyframes mysteryBoxPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(1.05); }
    }

    @keyframes mysteryBoxShine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    @keyframes mysteryBoxOpening {
        0% { transform: scale(1) rotate(0deg); }
        25% { transform: scale(1.1) rotate(-5deg); }
        50% { transform: scale(1.2) rotate(5deg); }
        75% { transform: scale(1.3) rotate(-10deg); }
        100% { transform: scale(1.5) rotate(15deg); opacity: 0; }
    }

    @keyframes rewardShine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    @keyframes rewardIconBounce {
        0% { transform: scale(0.3) rotate(-180deg); opacity: 0; }
        50% { transform: scale(1.1) rotate(-90deg); opacity: 0.7; }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }

    </style>
    

    <style>

        .jspop-dialog-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(3px);
        }

        .jspop-dialog-overlay.show {
            opacity: 1;
        }

        .jspop-dialog-box {
            background: #2d3436;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            min-width: 420px;
            max-width: 550px;
            transform: scale(0.8) translateY(-30px);
            transition: all 0.3s ease;
            border: 1px solid #636e72;
            overflow: hidden;
        }

        .jspop-dialog-overlay.show .jspop-dialog-box {
            transform: scale(1) translateY(0);
        }

        .jspop-dialog-header {
            padding: 18px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            border-bottom: 1px;
            solid rgba(255, 255, 255, 0.1);
    }

    .jspop-dialog-header h5 {
        margin: 0;
        font-size: 16px;
    }

    .jspop-dialog-header i {
        font-size: 18px;
    }

    .jspop-dialog-body {
        padding: 24px;
        color: #fff;
        line-height: 1.6;
        font-size: 15px;
    }

    .jspop-dialog-body div {
        margin: 0;
    }

    .jspop-dialog-footer {
        padding: 18px 24px;
        border-top: 1px solid #636e72;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background: rgba(0, 0, 0, 0.1);
    }

    .jspop-dialog-footer .btn {
        min-width: 80px;
        font-weight: 500;
        border-radius: 6px;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }

    .jspop-dialog-footer .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .jspop-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .jspop-notification {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease;
        min-width: 300px;
    }

    .jspop-notification.jspop-success {
        background: linear-gradient(135deg, #00b894, #00a085);
        color: white;
    }

    .jspop-notification.jspop-error {
        background: linear-gradient(135deg, #e17055, #d63031);
        color: white;
    }

    .jspop-notification.jspop-warning {
        background: linear-gradient(135deg, #fdcb6e, #e17055);
        color: white;
    }

    .jspop-notification.jspop-info {
        background: linear-gradient(135deg, #74b9ff, #0984e3);
        color: white;
    }

    .jspop-content {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .jspop-message {
        flex: 1;
    }

    .jspop-close {
        background: none;
        border: none;
        color: inherit;
        font-size: 18px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .jspop-close:hover {
        opacity: 1;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .gift-box-container {
        margin: 2em 0;
        position: relative;
    }

    .gift-box-emoji {
        font-size: 8em;
        animation: joggle 4.5s ease-in-out infinite;
        display: inline-block;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
    }

    .gift-box-emoji:hover {
        transform: scale(1.1);
    }

    .gift-box-emoji.opening {
        animation: none;
        transform: scale(1.5) rotate(12deg);
        transition: transform 500ms ease;
    }

    .tap-instruction {
        color: #888;
        font-size: 1.2em;
        margin-top: 1em;
        animation: pulse 2s infinite;
    }

    .gift-result {
        animation: fadeInUp 0.5s ease;
    }

    .reward-display {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 2em;
        margin: 1em 0;
        position: relative;
        overflow: hidden;
    }

    .reward-display::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: shine 2s infinite;
    }

    .rarity-badge {
        display: inline-block;
        padding: 0.5em 1em;
        border-radius: 25px;
        font-weight: bold;
        font-size: 0.9em;
        margin-bottom: 1em;
        text-transform: uppercase;
    }

    .rarity-common { background: #6c757d; color: white; }
    .rarity-rare { background: #007bff; color: white; }
    .rarity-epic { background: #6f42c1; color: white; }
    .rarity-legendary { background: #fd7e14; color: white; }

    .reward-icon {
        font-size: 4em;
        margin: 0.5em 0;
    }

    .reward-amount {
        font-size: 2em;
        font-weight: bold;
        color: white;
        margin: 0.5em 0;
    }

    .reward-description {
        font-size: 1.2em;
        color: rgba(255,255,255,0.9);
    }

    .mystery-box-card, .dropped-box-card {
        background: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
        border-radius: 15px;
        padding: 2em;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .mystery-box-card:hover, .dropped-box-card:hover {
        transform: translateY(-5px);
        border-color: #007bff;
        box-shadow: 0 10px 25px rgba(0,123,255,0.3);
    }

    .mystery-box-card.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .mystery-box-card.disabled:hover {
        transform: none;
        border-color: transparent;
        box-shadow: none;
    }

    .dropped-box-card {
        border-color: #28a745;
        background: linear-gradient(135deg, #1e3c32 0%, #28a745 100%);
    }

    .dropped-box-card:hover {
        border-color: #20c997;
        box-shadow: 0 10px 25px rgba(32,201,151,0.3);
    }

    .box-emoji {
        font-size: 4em;
        margin-bottom: 0.5em;
    }

    .box-name {
        color: white;
        font-weight: bold;
        font-size: 1.3em;
        margin-bottom: 0.5em;
    }

    .box-price {
        color: #28a745;
        font-size: 1.5em;
        font-weight: bold;
        margin-bottom: 0.5em;
    }

    .box-description {
        color: #adb5bd;
        font-size: 0.9em;
        margin-bottom: 1em;
    }

    .box-availability {
        color: #ffc107;
        font-size: 0.8em;
        font-weight: bold;
    }

    .box-unavailable {
        color: #dc3545;
        font-size: 0.8em;
        font-weight: bold;
    }

    .box-expires {
        color: #ffc107;
        font-size: 0.9em;
        font-weight: bold;
        margin-top: 0.5em;
    }

    .mystery-drop-notification {
        position: fixed;
        top: 80px;
        right: 20px;
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        color: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        z-index: 9998;
        max-width: 350px;
        animation: dropNotificationSlide 0.5s ease;
    }

    .drop-notification-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .drop-icon {
        font-size: 3em;
        animation: bounce 2s infinite;
    }

    .drop-text h5 {
        margin: 0 0 5px 0;
        font-size: 1.2em;
    }

    .drop-text p {
        margin: 0 0 10px 0;
        font-size: 0.9em;
        opacity: 0.9;
    }

    .drop-notification-content .btn {
        margin-right: 5px;
        font-size: 0.8em;
    }

    @keyframes joggle {
        0%, 33%, 100% { transform: rotate(0deg); }
        3.33% { transform: rotate(-10deg); }
        6.67% { transform: rotate(12deg); }
        10% { transform: rotate(-10deg); }
        13.33% { transform: rotate(9deg); }
        16.67% { transform: rotate(0deg); }
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes shine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }

    @keyframes dropNotificationSlide {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    </style>


    <div class="offcanvas offcanvas-end" tabindex="0" id="offcanvasNotify">
        <div class="offcanvas-header card-line">
            <h5 class="d-flex align-items-center gap-3 crypt-grayscale-100 mb-0">
                Notifications 
                <span class="crypt-grayscale-500 text-sm mb-0" id="notification-count">0</span>
            </h5>
            <button type="button" class="btn-close text-reset close-notify align-items-center justify-content-center" data-bs-dismiss="offcanvas" aria-label="Close">
                <svg class="close-notify" width="18" height="18" viewbox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.5 4.5L4.5 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M4.5 4.5L13.5 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
        <div class="offcanvas-body" id="notifications-container">
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

  <script>
    $(document).ready(function() {
        loadNotifications();
        
        $('#offcanvasNotify').on('show.bs.offcanvas', function() {
            loadNotifications();
        });
        
        $(document).on('click', '.close-notification', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const notificationId = $(this).data('notification-id');
            const $alert = $(this).closest('.alert');
            
            closeNotification(notificationId, $alert);
        });
        
        setInterval(function() {
            if ($('#offcanvasNotify').hasClass('show')) {
                loadNotifications();
            } else {
                updateNotificationBadge();
            }
        }, 15000);
    });

    function loadNotifications() {
        $.ajax({
            url: 'core/ajax/user/userPushs.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#notifications-container').html(response.html);
                    updateNotificationCount(response.count);
                    updateHeaderBadge(response.count);
                } else {
                    showNotificationError(response.error);
                }
            },
            error: function(xhr, status, error) {
                showNotificationError('Failed to load notifications');
            }
        });
    }

    function closeNotification(notificationId, $alert) {
        $alert.addClass('closing');
        
        $.ajax({
            url: 'core/ajax/user/userPushs.php',
            type: 'POST',
            data: {
                action: 'close',
                id: notificationId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $alert.fadeOut(300, function() {
                        $(this).remove();
                        updateNotificationCountFromDOM();
                        
                        if ($('#notifications-container .alert').length === 0) {
                            showEmptyState();
                        }
                    });
                } else {
                    $alert.removeClass('closing');
                    console.error('Failed to close notification:', response.error);
                }
            },
            error: function(xhr, status, error) {
                $alert.removeClass('closing');
                console.error('Error closing notification:', error);
            }
        });
    }

    function updateNotificationCount(count) {
        $('#notification-count').text(count);
    }

    function updateNotificationCountFromDOM() {
        const count = $('#notifications-container .alert').length;
        updateNotificationCount(count);
        updateHeaderBadge(count);
    }

    function updateHeaderBadge(count) {
        const $badge = $('.notification-badge');
        
        if (count > 0) {
            if ($badge.length === 0) {
                $('[data-bs-target="#offcanvasNotify"]').append(
                    '<span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning"><div class="crypt-grayscale">' +
                    (count > 99 ? '99+' : count) + 
                    '</div></span>'
                );
            } else {
                $badge.text(count > 99 ? '99+' : count);
            }
        } else {
            $badge.remove();
        }
    }

    function updateNotificationBadge() {
        $.ajax({
            url: 'core/ajax/user/userPushs.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateHeaderBadge(response.count);
                }
            },
            error: function() {
                console.error('Failed to update notification badge');
            }
        });
    }

    function showNotificationError(message) {
        $('#notifications-container').html(`
            <div class="text-center py-5">
                <div class="text-danger mb-3">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2"/>
                        <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <p class="text-danger mb-2">Error loading notifications</p>
                <small class="crypt-grayscale-600">${message}</small>
                <br>
                <button class="btn btn-sm btn-outline-primary mt-3" onclick="loadNotifications()">
                    <i class="fas fa-refresh"></i> Retry
                </button>
            </div>
        `);
    }

    function showEmptyState() {
        $('#notifications-container').html(`
            <div class="text-center py-5">
                <div class="mb-3">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <p class="crypt-grayscale-500 mb-0">No notifications</p>
                <small class="crypt-grayscale-600">You're all caught up!</small>
            </div>
        `);
    }
  </script>
  <br><br><br><br><br><br><br><br>
  </section>
  <!-- Main Content End -->

  <?php 
    if (!empty(FetchWebSettings()['tawk_to'])) {
        echo FetchWebSettings()['tawk_to'];
    }
  ?>

<script>

    function JSPop(type, message, confirm = '0', yesCallback = null, noCallback = null) {
        if (confirm === '1') {
            const existingDialog = document.getElementById('jspop-dialog');
            if (existingDialog) {
                existingDialog.remove();
            }
            
            const dialogId = 'jspop-dialog';
            const yesButtonId = 'jspop-btnYes-' + Date.now();
            const noButtonId = 'jspop-btnNo-' + Date.now();
            
            const dialogHTML = `
                <div id="${dialogId}" class="jspop-dialog-overlay">
                    <div class="jspop-dialog-box">
                        <div class="jspop-dialog-header ${getConfirmHeaderClass(type)}">
                            <i class="${getConfirmIcon(type)}"></i>
                            <h5>${getConfirmTitle(type)}</h5>
                        </div>
                        <div class="jspop-dialog-body">
                            <div>${message}</div>
                        </div>
                        <div class="jspop-dialog-footer">
                            <button type="button" class="btn btn-outline-secondary" id="${noButtonId}">Close</button>
                            <button type="button" class="btn ${getConfirmButtonClass(type)}" id="${yesButtonId}">OK</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', dialogHTML);
            
            const dialog = document.getElementById(dialogId);
            const yesButton = document.getElementById(yesButtonId);
            const noButton = document.getElementById(noButtonId);
            
            function closeDialog() {
                dialog.classList.remove('show');
                setTimeout(() => {
                    if (dialog.parentNode) {
                        dialog.remove();
                    }
                }, 300);
            }
            
            yesButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeDialog();
                
                setTimeout(() => {
                    if (yesCallback && typeof yesCallback === 'function') {
                        try {
                            yesCallback();
                        } catch (error) {
                            console.error('JSPop yes callback error:', error);
                        }
                    }
                }, 350);
            });

            noButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeDialog();
                
                setTimeout(() => {
                    if (noCallback && typeof noCallback === 'function') {
                        try {
                            noCallback();
                        } catch (error) {
                            console.error('JSPop no callback error:', error);
                        }
                    }
                }, 350);
            });

            dialog.addEventListener('click', function(e) {
                if (e.target === dialog) {
                    closeDialog();
                    setTimeout(() => {
                        if (noCallback && typeof noCallback === 'function') {
                            try {
                                noCallback();
                            } catch (error) {
                                console.error('JSPop overlay callback error:', error);
                            }
                        }
                    }, 350);
                }
            });

            const escHandler = function(e) {
                if (e.key === 'Escape') {
                    closeDialog();
                    setTimeout(() => {
                        if (noCallback && typeof noCallback === 'function') {
                            try {
                                noCallback();
                            } catch (error) {
                                console.error('JSPop escape callback error:', error);
                            }
                        }
                    }, 350);
                    document.removeEventListener('keydown', escHandler);
                }
            };
            document.addEventListener('keydown', escHandler);
            
            setTimeout(() => {
                dialog.classList.add('show');
            }, 10);
            
        } else {
            const popupContainer = document.getElementById('popup-container') || createPopupContainer();
            
            const popup = document.createElement('div');
            popup.className = `jspop-notification jspop-${type}`;
            popup.innerHTML = `
                <div class="jspop-content">
                    <i class="${getPopupIcon(type)}"></i>
                    <span class="jspop-message">${message}</span>
                    <button class="jspop-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                </div>
            `;
            
            popupContainer.appendChild(popup);
            
            setTimeout(() => {
                if (popup.parentNode) {
                    popup.remove();
                }
            }, 4000);
        }
    }

    function getConfirmHeaderClass(type) {
        switch(type) {
            case 'success': return 'bg-success text-white';
            case 'error': return 'bg-danger text-white';
            case 'warning': return 'bg-warning text-dark';
            case 'info': return 'bg-info text-white';
            default: return 'bg-primary text-white';
        }
    }

    function getConfirmButtonClass(type) {
        switch(type) {
            case 'success': return 'btn-success';
            case 'error': return 'btn-danger';
            case 'warning': return 'btn-warning';
            case 'info': return 'btn-info';
            default: return 'btn-primary';
        }
    }

    function getConfirmIcon(type) {
        switch(type) {
            case 'success': return 'fa fa-check-circle';
            case 'error': return 'fa fa-exclamation-triangle';
            case 'warning': return 'fa fa-exclamation-circle';
            case 'info': return 'fa fa-info-circle';
            default: return 'fa fa-bell';
        }
    }

    function getConfirmTitle(type) {
        switch(type) {
            case 'success': return 'Success';
            case 'error': return 'Error';
            case 'warning': return 'Warning';
            case 'info': return 'Information';
            default: return 'Notification';
        }
    }

    function getPopupIcon(type) {
        switch(type) {
            case 'success': return 'fa fa-check-circle';
            case 'error': return 'fa fa-times-circle';
            case 'warning': return 'fa fa-exclamation-triangle';
            case 'info': return 'fa fa-info-circle';
            default: return 'fa fa-bell';
        }
    }

    function createPopupContainer() {
        const container = document.createElement('div');
        container.id = 'popup-container';
        container.className = 'jspop-container';
        document.body.appendChild(container);
        return container;
    }

  </script>

  <style>

    .jspop-dialog-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(3px);
    }

    .jspop-dialog-overlay.show {
        opacity: 1;
    }

    .jspop-dialog-box {
        background: #2d3436;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        min-width: 420px;
        max-width: 550px;
        transform: scale(0.8) translateY(-30px);
        transition: all 0.3s ease;
        border: 1px solid #636e72;
        overflow: hidden;
    }

    .jspop-dialog-overlay.show .jspop-dialog-box {
        transform: scale(1) translateY(0);
    }

    .jspop-dialog-header {
        padding: 18px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .jspop-dialog-header h5 {
        margin: 0;
        font-size: 16px;
    }

    .jspop-dialog-header i {
        font-size: 18px;
    }

    .jspop-dialog-body {
        padding: 24px;
        color: #fff;
        line-height: 1.6;
        font-size: 15px;
    }

    .jspop-dialog-body div {
        margin: 0;
    }

    .jspop-dialog-footer {
        padding: 18px 24px;
        border-top: 1px solid #636e72;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background: rgba(0, 0, 0, 0.1);
    }

    .jspop-dialog-footer .btn {
        min-width: 80px;
        font-weight: 500;
        border-radius: 6px;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }

    .jspop-dialog-footer .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    /* Existing notification styles */
    .jspop-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .jspop-notification {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease;
        min-width: 300px;
    }

    .jspop-notification.jspop-success {
        background: linear-gradient(135deg, #00b894, #00a085);
        color: white;
    }

    .jspop-notification.jspop-error {
        background: linear-gradient(135deg, #e17055, #d63031);
        color: white;
    }

    .jspop-notification.jspop-warning {
        background: linear-gradient(135deg, #fdcb6e, #e17055);
        color: white;
    }

    .jspop-notification.jspop-info {
        background: linear-gradient(135deg, #74b9ff, #0984e3);
        color: white;
    }

    .jspop-content {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .jspop-message {
        flex: 1;
    }

    .jspop-close {
        background: none;
        border: none;
        color: inherit;
        font-size: 18px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .jspop-close:hover {
        opacity: 1;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

  </style>

  <script>window.gtranslateSettings = {"default_language":"en","wrapper_selector":".gtranslate_wrapper"}</script>
  <script src="https://cdn.gtranslate.net/widgets/latest/popup.js" defer></script> 

  <script src="core/pages/assets/npm/apexcharts%403.41.0/dist/apexcharts.min.js"></script>
  <script src="core/pages/assets/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="core/pages/assets/ajax/libs/popper.js/2.11.8/umd/popper.min.js"></script>
  <script src="core/pages/assets/bootstrap/js/bootstrap.js"></script>
  <script src="core/pages/assets/js/main.js"></script>

</body>
</html>
