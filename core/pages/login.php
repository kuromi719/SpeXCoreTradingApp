<?php require __DIR__ . '/inc/home_header.php';
$adminReque = $_SESSION['AdminReque'] ?? null;
$requestedUser = $_SESSION['RequestedUser'] ?? null;
?>


    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="crypt-login-form mt-5">
                <div class="d-flex justify-content-between mb-4 text-center">
                    <h3 class="fw-bold">Log in</h3>
                </div>
                
                <div id="loginMessage" class="alert" style="display: none;"></div>
                
                <form class="needs-validation" id="loginForm" method="POST" novalidate="" >
                    <div class="d-flex flex-column">
                        <div class="d-flex flex-column mb-3">
                            <label for="email" class="form-label text-light">Email <span class="crypt-grayscale-600">*</span></label>
                            <input type="email" class="form-control py-2" name="email" id="email" placeholder="Email" required="">
                            <div class="invalid-feedback mt-0">This field is required.</div>
                        </div>
                        <div class="d-flex flex-column">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label text-light">Password <span class="crypt-grayscale-600">*</span></label>
                                <a href="#!" data-bs-toggle="modal" data-bs-target="#ResetPassword" class="link-primary text-sm text-decoration-none">Forgot password?</a>
                            </div>
                            <div class="input-group-append">
                                <input type="password" class="form-control form-controls py-2" name="password" id="password" value="" placeholder="Login Password" required="">
                                <div class="eye fright"><i class="fa fa-eye" id="eye" onclick="toggle()"></i></div>
                                <div class="invalid-feedback mt-0">This field is required.</div>
                            </div>
                        </div>
                        <button class="btn btn-primary rounded-pill text-center mt-4" type="submit" id="loginBtn">
                            <span id="loginBtnText">Continue</span>
                            <span id="loginSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                        </button>
                    </div>
                </form>
                <div class="d-flex flex-row gap-2 mt-4 justify-content-center">
                    <p class="crypt-grayscale-500">Don't have an account?</p>
                    <a href="?r=register" class="link-primary fw-bold">Sign up</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                var email = $('#email').val().trim();
                var password = $('#password').val().trim();
                
                if (!email || !password) {
                    showMessage('Please fill all fields.', 'error');
                    return;
                }
                
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showMessage('Please enter a valid email address.', 'error');
                    return;
                }
                
                setLoadingState(true);
                
                $.ajax({
                    url: 'core/ajax/user/userLogin.php',
                    type: 'POST',
                    data: {
                        email: email,
                        password: password
                    },
                    timeout: 5000,
                    success: function(response) {
                        setLoadingState(false);
                        console.log('Raw response:', response);
                        
                        var result = parseResponse(response);
                        
                        if (result.status === 'success') {
                            showMessage(result.message, 'success');
                            setTimeout(function() {
                                window.location.href = '?r=app';
                            }, 2000);
                        } else {
                            showMessage(result.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        setLoadingState(false);
                        if (status === 'timeout') {
                            showMessage('Request timeout. Please try again.', 'error');
                        } else {
                            showMessage('An error occurred. Please try again.', 'error');
                        }
                        console.error('AJAX Error:', error);
                    }
                });
            });
            
            function parseResponse(response) {
                try {
                    var jsonData = JSON.parse(response);
                    return jsonData;
                } catch (e) {
                    console.log('Response is not JSON, parsing as HTML/text');
                    
                    var jsonMatch = response.match(/\{"status".*?\}/);
                    if (jsonMatch) {
                        try {
                            return JSON.parse(jsonMatch[0]);
                        } catch (e2) {

                        }
                    }
                    
                    var cleanText = response.replace(/<[^>]*>/g, '').trim();
                    
                    if (cleanText.toLowerCase().includes('successfully') || 
                        cleanText.toLowerCase().includes('logged in')) {
                        return {
                            status: 'success',
                            message: cleanText
                        };
                    } else {
                        return {
                            status: 'error',
                            message: cleanText || 'Login failed'
                        };
                    }
                }
            }
            
            function showMessage(message, type) {
                var messageDiv = $('#loginMessage');
                messageDiv.removeClass('alert-success alert-danger alert-warning');
                
                if (type === 'success') {
                    messageDiv.addClass('alert-success');
                } else {
                    messageDiv.addClass('alert-danger');
                }
                
                messageDiv.text(message).show();
                
                setTimeout(function() {
                    messageDiv.fadeOut();
                }, 5000);
            }
            
            function setLoadingState(isLoading) {
                var loginBtn = $('#loginBtn');
                var loginBtnText = $('#loginBtnText');
                var loginSpinner = $('#loginSpinner');
                
                if (isLoading) {
                    loginBtn.prop('disabled', true);
                    loginBtnText.text('Please wait...');
                    loginSpinner.show();
                } else {
                    loginBtn.prop('disabled', false);
                    loginBtnText.text('Continue');
                    loginSpinner.hide();
                }
            }
        });
    </script>


    <div class="modal fade" id="ResetPassword" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex">
                    <h5 class="d-flex align-items-center gap-3 crypt-grayscale-100 mb-0">Password Reset</h5>
                    <button type="button" class="btn-close text-reset close-notify align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close">
                        <svg class="close-notify" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.5 4.5L4.5 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M4.5 4.5L13.5 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="resetPasswordForm">
                        <div class="d-flex flex-column mb-3">
                            <label for="emailreset" class="form-label text-light">E-Mail <span class="crypt-grayscale-600">*</span></label>
                            <input type="email" class="form-control py-2" name="email" id="emailreset" placeholder="E-Mail" required>
                            <div class="invalid-feedback mt-0">This field is required.</div>
                        </div>
                        
                        <!-- Success/Error Messages -->
                        <div id="resetMessage" style="display: none;"></div>
                        
                        <button class="btn btn-primary rounded-pill text-center" type="submit" id="resetBtn">
                            <span id="resetBtnText">Reset</span>
                            <span id="resetLoader" style="display: none;">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Sending...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {

        $('#resetPasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            const email = $('#emailreset').val().trim();
            const $form = $(this);
            const $emailInput = $('#emailreset');
            const $resetBtn = $('#resetBtn');
            const $resetBtnText = $('#resetBtnText');
            const $resetLoader = $('#resetLoader');
            const $messageDiv = $('#resetMessage');
            
            if (!email) {
                showValidationError($emailInput, 'E-Mail field is required.');
                return;
            }
            
            if (!isValidEmail(email)) {
                showValidationError($emailInput, 'Please enter a valid email address.');
                return;
            }
            
            clearValidationError($emailInput);
            
            $resetBtn.prop('disabled', true);
            $resetBtnText.hide();
            $resetLoader.show();
            $messageDiv.hide();
            
            $.ajax({
                url: 'core/ajax/user/resetPasswordWithMail.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    email: email
                },
                timeout: 30000, // 30 seconds timeout
                success: function(response) {
                    if (response.status === 'success') {
                        showMessage('success', response.message);
                        $form[0].reset();
                        
                        setTimeout(function() {
                            $('#ResetPassword').modal('hide');
                        }, 3000);
                    } else {
                        showMessage('error', response.message || 'An error occurred while processing your request.');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Network error occurred. Please try again.';
                    
                    if (status === 'timeout') {
                        errorMessage = 'Request timed out. Please try again.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    showMessage('error', errorMessage);
                    console.error('Ajax Error:', error);
                },
                complete: function() {
                    $resetBtn.prop('disabled', false);
                    $resetBtnText.show();
                    $resetLoader.hide();
                }
            });
        });
        
        $('#emailreset').on('input', function() {
            clearValidationError($(this));
        });
        
        $('#ResetPassword').on('hidden.bs.modal', function() {
            $('#resetPasswordForm')[0].reset();
            clearValidationError($('#emailreset'));
            $('#resetMessage').hide();
        });

        function showValidationError($input, message) {
            $input.addClass('is-invalid');
            $input.siblings('.invalid-feedback').text(message).show();
        }
        
        function clearValidationError($input) {
            $input.removeClass('is-invalid');
            $input.siblings('.invalid-feedback').hide();
        }
        
        function showMessage(type, message) {
            const $messageDiv = $('#resetMessage');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'text-success' : 'text-danger';
            const icon = type === 'success' ? '✓' : '⚠';
            
            $messageDiv.html(`
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <span class="${iconClass} me-2">${icon}</span>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `).show();
        }
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
    </script>

    <div class="modal fade" id="autoLoginModal" tabindex="-1" aria-labelledby="autoLoginModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="autoLoginModalLabel">Please Wait</h5>
            </div>
            <div class="modal-body text-center">
                <p>Logging you in as user...</p>
                <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
            </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const a = <?php echo json_encode($adminReque !== null); ?>;
            const b = <?php echo json_encode($requestedUser !== null); ?>;

            if (a && b) {
                const m = document.getElementById('autoLoginModal');
                const modalInstance = new bootstrap.Modal(m);
                modalInstance.show();

                setTimeout(() => {
                fetch('core/ajax/user/LoginViaReq.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                    modalInstance.hide();
                    window.location.href = '?r=app';
                    } else {
                    m.querySelector('.modal-body p').textContent = 'Login failed: ' + d.message;
                    }
                })
                .catch(() => {
                    m.querySelector('.modal-body p').textContent = 'Network error. Please try again.';
                });
                }, 5000);
            }
        });
    </script>

    <!-- ? -->
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> 


<?php require __DIR__ . '/inc/home_footer.php'; ?>