<?php require __DIR__ . '/inc/home_header.php'; ?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="crypt-login-form mt-5">
            <div class="d-flex mb-4">
                <h3 class="fw-bold">Create Account</h3>
            </div>
            <div id="loginMessage" class="alert" style="display: none;"></div>
            <form class="needs-validation" name="password_strength" id="registerForm" novalidate="" method='POST'>
                <div class="d-flex flex-column">
                    <div class="d-flex flex-column mb-3">
                        <label for="email" class="form-label text-light">Email <span class="crypt-grayscale-600">*</span></label>
                        <input type="email" class="form-control py-2 mb-0" name="email" id="email" placeholder="name@example.com" required="">
                        <div class="invalid-feedback mt-0">This field is required.</div>
                    </div>
                    <div class="d-flex flex-column mb-3">
                        <label for="phone" class="form-label text-light">Phone Number <span class="crypt-grayscale-600">*</span></label>
                        <input type="tel" class="form-control py-2 mb-0" name="phone" id="phone" placeholder="XXXXXXXXX" required="">
                        <div class="invalid-feedback mt-0">This field is required.</div>
                    </div>
                    <div class="d-flex flex-column mb-3">
                        <label for="password" class="form-label text-light">Password <span class="crypt-grayscale-600">*</span></label>
                        <div class="input-group-append">
                            <input type="password" class="form-control form-controls py-2" name="password" id="password" value="" placeholder="Enter a password" autocomplete="off" required="">
                            <div class="eye fright"><i class="fa fa-eye" id="eye" onclick="toggle()"></i></div>
                            <span id="passstrength"></span>
                            <div class="text-muted crypt-grayscale-600 text-sm">Use 8 or more characters with a mix of letters, numbers &amp; symbols.</div>
                        </div>
                    </div>
                    <div class="d-flex flex-column mb-3">
                        <label for="invc" class="form-label text-light">Invitation Code</label>
                        <input type="text" class="form-control py-2 mb-0" name="invc" id="invc" placeholder="C-XXXXX" 
                        <?php 
                        if(isset($_GET['c']) && !empty($_GET['c'])) {
                            echo "value='" . htmlspecialchars($_GET['c']) . "'";
                        } 
                        ?> 
                        >
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" name="remember_me" id="remember_me" required="">
                            <label class="form-check-label text-secondary" for="remember_me">
                                I have read and agree to the <a href="#!" class="link-primary fw-bold mb-0">Terms of Use</a>.
                            </label>
                            <div class="invalid-feedback mt-0"> You must agree before submitting.</div>
                        </div>
                    </div>
                    <button class="btn btn-primary rounded-pill text-center mt-4" type="submit" id="registerBtn">
                        <span id="registerBtnText">Create Account</span>
                        <span id="registerSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                    </button>
                </div>
            </form>
            <div class="d-flex flex-row gap-2 mt-4 justify-content-center">
                <p class="crypt-grayscale-500">Already have an account?</p>
                <a href="?r=login" class="link-primary fw-bold">Login</a>
            </div>
        </div>
    </div>
</div>

    <script>

        $(document).ready(function() {
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                
                var email = $('#email').val().trim();
                var phone = $('#phone').val().trim();
                var password = $('#password').val().trim();
                var invc = $('#invc').val().trim();
                
                if (!$('#remember_me').is(':checked')) {
                    showMessage('You must agree to the Terms of Use.', 'error');
                    return;
                }
                
                if (!email || !password || !phone) {
                    showMessage('Please fill all required fields.', 'error');
                    return;
                }
                
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showMessage('Please enter a valid email address.', 'error');
                    return;
                }
                
                if (password.length < 6) {
                    showMessage('Password must be at least 6 characters long.', 'error');
                    return;
                }
                
                setLoadingState(true);
                
                $.ajax({
                    url: 'core/ajax/user/userRegister.php',
                    type: 'POST',
                    data: {
                        email: email,
                        phone: phone,
                        password: password,
                        invc: invc
                    },
                    timeout: 10000,
                    success: function(response) {
                        setLoadingState(false);
                        console.log('Raw response:', response);
                        
                        var result = parseResponse(response);
                        
                        if (result.status === 'success') {
                            triggerConfettiEffect();
                            showMessage(result.message, 'success');
                            setTimeout(function() {
                                window.location.href = '?r=verifyMail';
                            }, 3200);
                        } else {
                            showMessage(result.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        setLoadingState(false);
                        console.error('AJAX Error:', xhr.responseText);
                        
                        if (status === 'timeout') {
                            showMessage('Request timeout. Please try again.', 'error');
                        } else if (xhr.status === 500) {
                            showMessage('Server error occurred. Please check your input.', 'error');
                        } else {
                            showMessage('An error occurred. Please try again.', 'error');
                        }
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
                            // parse error
                        }
                    }
                    
                    var cleanText = response.replace(/<[^>]*>/g, '').trim();
                    
                    if (cleanText.toLowerCase().includes('successfully') || 
                        cleanText.toLowerCase().includes('registered')) {
                        return {
                            status: 'success',
                            message: cleanText
                        };
                    } else {
                        return {
                            status: 'error',
                            message: cleanText || 'Registration failed'
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
                var registerBtn = $('#registerBtn');
                var registerBtnText = $('#registerBtnText');
                var registerSpinner = $('#registerSpinner');
                
                if (isLoading) {
                    registerBtn.prop('disabled', true);
                    registerBtnText.text('Please wait...');
                    registerSpinner.show();
                } else {
                    registerBtn.prop('disabled', false);
                    registerBtnText.text('Create Account');
                    registerSpinner.hide();
                }
            }
        });

    </script>

    <!-- ? -->
    <br><br><br><br><br><br><br><br>


<?php require __DIR__ . '/inc/home_footer.php'; ?>