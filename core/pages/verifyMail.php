<?php 
require __DIR__ . '/inc/home_header.php'; 
global $spexcon;
$MailVerify = getSession('verifyMail');
$UID = getSession('UID');

if ($MailVerify<1) {
    Go('app');
}

$que = $spexcon->query("SELECT * FROM users WHERE uid='$UID'");
$fet = mysqli_fetch_assoc($que);
$Mail = $fet['mail'];

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="verify-container">
                <div class="text-center mb-5">
                    <div class="verify-icon-wrapper mb-4">
                        <i class="fas fa-shield-check verify-icon"></i>
                    </div>
                    <h2 class="fw-bold mb-2">Email Verification</h2>
                    <p>Enter the 6-digit code sent to your email</p>
                </div>

                <div id="verifyMessage" class="alert" style="display: none;"></div>

                <div class="email-info-card mb-4">
                    <div class="d-flex align-items-center justify-content-center p-3">
                        <i class="fa fa-envelope text-primary me-3" style="font-size: 1.2rem;"></i>
                        <div>
                            <small class=" d-block">Verification sent to</small>
                            <span class=" fw-semibold"><?php echo htmlspecialchars($Mail); ?></span>
                        </div>
                    </div>
                </div>

                <div class="verification-form">
                    <div class="code-input-container mb-4">
                        <div class="code-inputs d-flex justify-content-center gap-2 mb-3">
                            <input type="text" class="code-input" maxlength="1" id="digit1" autocomplete="off">
                            <input type="text" class="code-input" maxlength="1" id="digit2" autocomplete="off">
                            <input type="text" class="code-input" maxlength="1" id="digit3" autocomplete="off">
                            <input type="text" class="code-input" maxlength="1" id="digit4" autocomplete="off">
                            <input type="text" class="code-input" maxlength="1" id="digit5" autocomplete="off">
                            <input type="text" class="code-input" maxlength="1" id="digit6" autocomplete="off">
                        </div>
                        <p class="text-center small">
                            <i class="fa fa-info-circle me-1"></i>
                            Check your inbox and spam folder
                        </p>
                    </div>

                    <button class="btn btn-primary btn-verify w-100 mb-3" id="verifyBtn" disabled>
                        <span id="verifyBtnText">
                            <i class="fa fa-check-circle me-2"></i>Verify Code
                        </span>
                        <span id="verifySpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                    </button>

                    <div class="divider-container mb-3">
                        <div class="divider-line"></div>
                        <span class="divider-text">or</span>
                        <div class="divider-line"></div>
                    </div>

                    <button class="btn btn-outline-secondary w-100 mb-4" id="resendBtn" type="button">
                        <span id="resendBtnText">
                            <i class="fa fa-paper-plane me-2"></i>Resend Code
                        </span>
                        <span id="resendSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                    </button>
                </div>

                <div class="footer-actions text-center mt-2">
                    <a href="?r=login" class="btn btn-link">
                        <i class="fa fa-arrow-left me-1"></i>Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let resendCooldown = false;
    const codeInputs = $('.code-input');
    
    codeInputs.on('input', function(e) {
        const current = $(this);
        const value = this.value;
        
        this.value = value.replace(/[^0-9]/g, '');
        
        if (this.value && current.next('.code-input').length) {
            current.next('.code-input').focus();
        }
        
        checkCodeComplete();
    });
    
    codeInputs.on('keydown', function(e) {
        const current = $(this);
        
        // Backspace: move to previous input if current is empty
        if (e.key === 'Backspace' && !this.value && current.prev('.code-input').length) {
            current.prev('.code-input').focus();
        }
        
        // Enter: submit if code is complete
        if (e.key === 'Enter' && getCode().length === 6) {
            $('#verifyBtn').click();
        }
    });
    
    codeInputs.on('paste', function(e) {
        e.preventDefault();
        const paste = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        const numbers = paste.replace(/[^0-9]/g, '').slice(0, 6);
        
        numbers.split('').forEach((digit, index) => {
            if (codeInputs.eq(index).length) {
                codeInputs.eq(index).val(digit);
            }
        });
        
        checkCodeComplete();
        
        if (numbers.length === 6) {
            codeInputs.eq(5).focus();
        }
    });
    
    function getCode() {
        return codeInputs.map(function() { return this.value; }).get().join('');
    }
    
    function checkCodeComplete() {
        const code = getCode();
        $('#verifyBtn').prop('disabled', code.length !== 6);
        
        if (code.length === 6) {
            $('#verifyBtn').removeClass('btn-secondary').addClass('btn-primary');
        } else {
            $('#verifyBtn').removeClass('btn-primary').addClass('btn-secondary');
        }
    }
    
    function clearCode() {
        codeInputs.val('').first().focus();
        checkCodeComplete();
    }
    
    $('#verifyBtn').on('click', function() {
        const code = getCode();
        
        if (code.length !== 6) {
            showMessage('Please enter the complete 6-digit code.', 'error');
            return;
        }
        
        setVerifyLoadingState(true);
        
        $.ajax({
            url: 'core/ajax/user/userVerifyMail.php',
            type: 'POST',
            data: {
                action: 'verify',
                code: code
            },
            timeout: 10000,
            success: function(response) {
                setVerifyLoadingState(false);
                
                var result = parseResponse(response);
                
                if (result.status === 'success') {
                    showMessage(result.message, 'success');
                    $.ajax({
                        url: 'core/ajax/user/welcomeEmail.php',
                        type: 'POST',
                        data: {},
                        success: function(response) {
                        },
                        error: function() {
                        }
                    });
                    setTimeout(function() {
                        window.location.href = '?r=app';
                    }, 3500);
                } else {
                    showMessage(result.message, 'error');
                    clearCode();
                }
            },
            error: function(xhr, status, error) {
                setVerifyLoadingState(false);
                showMessage('Verification failed. Please try again.', 'error');
                clearCode();
            }
        });
    });
    
    $('#resendBtn').on('click', function() {
        if (resendCooldown) {
            showMessage('Please wait before requesting another code.', 'warning');
            return;
        }
        
        setResendLoadingState(true);
        
        $.ajax({
            url: 'core/ajax/user/userVerifyMail.php',
            type: 'POST',
            data: {
                action: 'resend'
            },
            timeout: 10000,
            success: function(response) {
                setResendLoadingState(false);
                
                var result = parseResponse(response);
                
                if (result.status === 'success') {
                    showMessage(result.message, 'success');
                    startCooldown();
                    clearCode();
                } else {
                    showMessage(result.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                setResendLoadingState(false);
                showMessage('Failed to send code. Please try again.', 'error');
            }
        });
    });
    
    function parseResponse(response) {
        if (typeof response === 'object') {
            return response;
        }
        
        try {
            return JSON.parse(response);
        } catch (e) {
            var jsonMatch = response.match(/\{"status".*?\}/);
            if (jsonMatch) {
                try {
                    return JSON.parse(jsonMatch[0]);
                } catch (e2) {}
            }
            
            var cleanText = response.replace(/<[^>]*>/g, '').trim();
            
            if (cleanText.toLowerCase().includes('success') || 
                cleanText.toLowerCase().includes('verified')) {
                return { status: 'success', message: cleanText };
            } else {
                return { status: 'error', message: cleanText || 'Operation failed' };
            }
        }
    }
    
    function showMessage(message, type) {
        var messageDiv = $('#verifyMessage');
        messageDiv.removeClass('alert-success alert-danger alert-warning alert-info');
        
        switch(type) {
            case 'success':
                messageDiv.addClass('alert-success');
                break;
            case 'error':
                messageDiv.addClass('alert-danger');
                break;
            case 'warning':
                messageDiv.addClass('alert-warning');
                break;
            default:
                messageDiv.addClass('alert-info');
        }
        
        messageDiv.text(message).show();
        
        setTimeout(function() {
            messageDiv.fadeOut();
        }, 5000);
    }
    
    function setVerifyLoadingState(isLoading) {
        var verifyBtn = $('#verifyBtn');
        var verifyBtnText = $('#verifyBtnText');
        var verifySpinner = $('#verifySpinner');
        
        if (isLoading) {
            verifyBtn.prop('disabled', true);
            verifyBtnText.html('<i class="fa fa-spinner fa-spin me-2"></i>Verifying...');
            verifySpinner.show();
        } else {
            verifyBtnText.html('<i class="fa fa-check-circle me-2"></i>Verify Code');
            verifySpinner.hide();
            checkCodeComplete();
        }
    }
    
    function setResendLoadingState(isLoading) {
        var resendBtn = $('#resendBtn');
        var resendBtnText = $('#resendBtnText');
        var resendSpinner = $('#resendSpinner');
        
        if (isLoading) {
            resendBtn.prop('disabled', true);
            resendBtnText.html('<i class="fa fa-spinner fa-spin me-2"></i>Sending...');
            resendSpinner.show();
        } else {
            resendBtn.prop('disabled', false);
            resendBtnText.html('<i class="fa fa-paper-plane me-2"></i>Resend Code');
            resendSpinner.hide();
        }
    }
    
    function startCooldown() {
        resendCooldown = true;
        var countdown = 60;
        var resendBtn = $('#resendBtn');
        
        var interval = setInterval(function() {
            resendBtn.find('#resendBtnText').html('<i class="fa fa-clock me-2"></i>Resend in ' + countdown + 's');
            resendBtn.prop('disabled', true);
            countdown--;
            
            if (countdown < 0) {
                clearInterval(interval);
                resendBtn.find('#resendBtnText').html('<i class="fa fa-paper-plane me-2"></i>Resend Code');
                resendBtn.prop('disabled', false);
                resendCooldown = false;
            }
        }, 1000);
    }
    
    // Focus first input on load
    $('#digit1').focus();
});
</script>

<style>
.verify-container {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 2.5rem 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.verify-icon-wrapper {
    position: relative;
    display: inline-block;
}

.verify-icon {
    font-size: 4rem;
    color: #0d6efd;
    animation: iconPulse 2s infinite;
    filter: drop-shadow(0 0 20px rgba(13, 110, 253, 0.3));
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.email-info-card {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    backdrop-filter: blur(5px);
}

.code-inputs {
    gap: 0.75rem !important;
}

.code-input {
    width: 50px;
    height: 60px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
}

.code-input:focus {
    outline: none;
    border-color: #0d6efd;
    background: rgba(13, 110, 253, 0.1);
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
    transform: scale(1.05);
}

.code-input:not(:placeholder-shown) {
    border-color: #198754;
    background: rgba(25, 135, 84, 0.1);
}

.btn-verify {
    height: 50px;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #0d6efd, #0056b3);
    border: none;
}

.btn-verify:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
}

.btn-verify:disabled {
    background: #6c757d;
    opacity: 0.5;
}

.divider-container {
    display: flex;
    align-items: center;
    margin: 1.5rem 0;
}

.divider-line {
    flex: 1;
    height: 1px;
    background: rgba(255, 255, 255, 0.2);
}

.divider-text {
    margin: 0 1rem;
    color: #6c757d;
    font-size: 0.9rem;
}

.tip-item {
    padding: 1rem 0.5rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.tip-item:hover {
    background: rgba(255, 255, 255, 0.05);
    transform: translateY(-3px);
}

.tip-item i {
    font-size: 1.5rem;
    display: block;
}

@media (max-width: 576px) {
    .verify-container {
        padding: 1.5rem 1rem;
        margin: 1rem;
    }
    
    .code-input {
        width: 40px;
        height: 50px;
        font-size: 1.25rem;
    }
    
    .code-inputs {
        gap: 0.5rem !important;
    }
    
    .verify-icon {
        font-size: 3rem;
    }
    
    .tip-item {
        padding: 0.5rem 0.25rem;
    }
    
    .tip-item i {
        font-size: 1.25rem;
    }
}

@media (max-width: 400px) {
    .code-input {
        width: 35px;
        height: 45px;
        font-size: 1.1rem;
    }
    
    .code-inputs {
        gap: 0.25rem !important;
    }
}
</style>

<br><br><br><br><br><br><br><br><br>

<?php require __DIR__ . '/inc/home_footer.php'; ?>