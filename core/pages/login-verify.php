<?php require __DIR__ . '/inc/home_header.php'; ?>
<?php 
global $spexcon;
$Via2Fa = getSession('LoginVia2Fa');
$UID = getSession('UID');

if ($Via2Fa<1) {
    Go('app');
}

$UserData = FetchUserData($UID);
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="crypt-login-form mt-5" style="max-width: 400px; width: 100%;">
            <div class="mb-4 text-center">
                <h3 class="fw-bold ">Two-Factor Authentication</h3>
                <p class="text-center">
                    Hello, <strong><?php echo htmlspecialchars($UserData['mail']); ?></strong><br>
                    For your account's security, Two-Factor Authentication (2FA) is enabled. <br>
                    Please enter the 6-digit code from your authenticator app to continue.
                </p>
            </div>
            
            <div id="twoFAMessage" class="alert" style="display: none;"></div>
            
            <form id="twoFAForm" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="twoFACode" class="form-label text-body">Authentication Code</label>
                    <input type="text" 
                           class="form-control form-control-lg text-center" 
                           id="twoFACode" 
                           name="twoFACode" 
                           maxlength="6" 
                           pattern="\d{6}" 
                           placeholder="000000" 
                           autocomplete="one-time-code" 
                           required>
                    <div class="invalid-feedback mt-0">Please enter the 6-digit code.</div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 rounded-pill" id="verifyBtn">
                    <span id="verifyBtnText">Verify & Continue</span>
                    <span id="verifySpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                </button>
            </form>
            
            <div class="text-center mt-3">
                <a href="?r=logout" class="link-danger small">Cancel and Logout</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#twoFAForm').on('submit', function(e) {
        e.preventDefault();

        var $input = $('#twoFACode');
        var code = $input.val().trim();
        var $messageDiv = $('#twoFAMessage');
        var $btn = $('#verifyBtn');
        var $btnText = $('#verifyBtnText');
        var $spinner = $('#verifySpinner');

        if (!/^\d{6}$/.test(code)) {
            $input.addClass('is-invalid');
            showMessage('Please enter a valid 6-digit code.', 'error');
            return;
        } else {
            $input.removeClass('is-invalid');
        }

        setLoadingState(true);

        $.ajax({
            url: 'core/ajax/user/user2FaLogin.php',
            type: 'POST',
            dataType: 'json',
            data: { code: code },
            timeout: 7000,
            success: function(response) {
                if (response.message === 'Code verified') {
                    showMessage(response.message || '2FA Verified. Redirecting...', 'success');
                    setTimeout(function() {
                        window.location.href = '?r=app';
                    }, 2600);
                } else {
                    showMessage(response.message || 'Invalid code. Please try again.', 'error');
                    setLoadingState(false);
                }
            },
            error: function(xhr, status) {
                if (status === 'timeout') {
                    showMessage('Request timed out. Please try again.', 'error');
                } else {
                    showMessage('An error occurred. Please try again.', 'error');
                }
                setLoadingState(false);
            }
        });

        function setLoadingState(isLoading) {
            if (isLoading) {
                $btn.prop('disabled', true);
                $btnText.text('Verifying...');
                $spinner.show();
            } else {
                $btn.prop('disabled', false);
                $btnText.text('Verify & Continue');
                $spinner.hide();
            }
        }

        function showMessage(message, type) {
            $messageDiv.removeClass('alert-success alert-danger').addClass(type === 'success' ? 'alert-success' : 'alert-danger');
            $messageDiv.text(message).fadeIn();

            if (type === 'error') {
                setTimeout(function() {
                    $messageDiv.fadeOut();
                }, 5000);
            }
        }
    });
});
</script>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<?php require __DIR__ . '/inc/home_footer.php'; ?>
