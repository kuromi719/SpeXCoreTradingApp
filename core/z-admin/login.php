<?php require __DIR__ . '/inc/login_header.php'; ?>

<div class="container-xxl">
    <div class="row vh-100 d-flex justify-content-center">
        <div class="col-12 align-self-center">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 mx-auto">
                        <div class="card">
                            <div class="card-body p-0 bg-black auth-header-box rounded-top">
                                <div class="text-center p-3">
                                    <img src="assets/logo.png" width='100' alt="Z-Logo">
                                    <h4 class="mt-1 mb-1 fw-semibold text-white fs-18">Z-Admin</h4>
                                    <p class="text-muted fw-medium mb-0">Access. Manage. Control.</p>
                                </div>
                            </div>
                            <br>
                            <div id='alert' class='container'></div>

                            <div class="card-body pt-0">
                                <form class="my-4" id='Login'>
                                    <div class="form-group mb-2">
                                        <label class="form-label" for="username">Username</label>
                                        <input type="text" class="form-control" id="username" name="username"
                                            placeholder="Username">
                                    </div><!--end form-group-->

                                    <div class="form-group">
                                        <label class="form-label" for="userpassword">Password</label>
                                        <input type="password" class="form-control" name="password" id="userpassword"
                                            placeholder="Password">
                                    </div><!--end form-group-->

                                 

                                    <div class="form-group mb-0 row">
                                        <div class="col-12">
                                            <div class="d-grid mt-3">
                                                <button class="btn btn-primary" type="button">Log in <i
                                                        class="fas fa-sign-in-alt ms-1"></i></button>
                                            </div>
                                        </div><!--end col-->
                                    </div> <!--end form-group-->

                                </form><!--end form-->

                                <script>

                                $(document).ready(function() {
                                    $("#alert").hide();
                                    
                                    $(".btn-primary").click(function() {
                                        var username = $("#username").val();
                                        var password = $("#userpassword").val();
                                        
                                        if(username == "" || password == "") {
                                            $("#alert").html('<div class="alert alert-danger shadow-sm" role="alert"><div class="d-inline-flex justify-content-center align-items-center thumb-xs bg-danger rounded-circle mx-auto me-1"><i class="fas fa-exclamation align-self-center mb-0 text-white"></i></div><strong>Error!</strong> Username or Password cannot be a empty.</div>').fadeIn();
                                            return false;
                                        }
                                        
                                        $.ajax({
                                            type: "POST",
                                            url: "ajax/login-ajax.php",
                                            data: {
                                                username: username,
                                                password: password
                                            },
                                            dataType: "json",
                                            success: function(response) {
                                                if(response.status == "success") {
                                                    $("#alert").html('<div class="alert alert-success shadow-sm" role="alert"><div class="d-inline-flex justify-content-center align-items-center thumb-xs bg-success rounded-circle mx-auto me-1"><i class="fas fa-check align-self-center mb-0 text-white"></i></div><strong>Login Successfully!</strong> Redirecting...</div>').fadeIn();
                                                    setTimeout(function() {
                                                        window.location.href = "home.php";
                                                    }, 3000);
                                                } else {
                                                    $("#alert").html('<div class="alert alert-danger shadow-sm" role="alert"><div class="d-inline-flex justify-content-center align-items-center thumb-xs bg-danger rounded-circle mx-auto me-1"><i class="fas fa-exclamation align-self-center mb-0 text-white"></i></div><strong>Error!</strong> ' + response.message + '</div>').fadeIn();
                                                }
                                            },
                                            error: function() {
                                                $("#alert").html('<div class="alert alert-danger shadow-sm" role="alert"><div class="d-inline-flex justify-content-center align-items-center thumb-xs bg-danger rounded-circle mx-auto me-1"><i class="fas fa-exclamation align-self-center mb-0 text-white"></i></div><strong>Error!</strong> Connection error.</div>').fadeIn();
                                            }
                                        });
                                    });
                                });

                                </script>

                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end card-body-->
        </div><!--end col-->
    </div><!--end row-->
</div><!-- container -->
<script>window.gtranslateSettings = {"default_language":"en","wrapper_selector":".gtranslate_wrapper","flag_size":24}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/popup.js" defer></script>
</body>
<!--end body-->
</html>