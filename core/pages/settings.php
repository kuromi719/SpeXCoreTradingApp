<?php require __DIR__ . '/inc/app_header.php'; ?>

    <div class="wrapper">
        
        <div class="d-flex flex-column pb-4 animation-element in-view">
            
            <!-- Profile Section -->
            <div class="card-bs p-4">
                <h5 class="crypt-grayscale-100 fw-bold mb-4">Profile</h5>
                
                <!-- Nickname & Avatar -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.65 20.2V20.49C15.17 21.44 13.4 22 11.5 22C9.57002 22 7.77002 21.42 6.27002 20.43V20.2C6.27002 17.87 8.60002 15.97 11.46 15.97C14.33 15.97 16.65 17.87 16.65 20.2Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M21 12.5C21 15.85 19.27 18.79 16.65 20.49V20.2C16.65 17.87 14.33 15.97 11.46 15.97C8.6 15.97 6.27 17.87 6.27 20.2V20.43C3.7 18.73 2 15.82 2 12.5C2 7.25 6.25 3 11.5 3C12.81 3 14.06 3.25999 15.2 3.73999C15.07 4.13999 15 4.56 15 5C15 5.75 15.21 6.46 15.58 7.06C15.78 7.4 16.04 7.70997 16.34 7.96997C17.04 8.60997 17.97 9 19 9C19.44 9 19.86 8.92998 20.25 8.78998C20.73 9.92998 21 11.19 21 12.5Z" fill="currentColor"></path>
                                    <path d="M21.97 2.33002C21.25 1.51002 20.18 1 19 1C17.88 1 16.86 1.46002 16.13 2.21002C15.71 2.64002 15.39 3.15999 15.2 3.73999C15.07 4.13999 15 4.56 15 5C15 5.75 15.21 6.46 15.58 7.06C15.78 7.4 16.04 7.70997 16.34 7.96997C17.04 8.60997 17.97 9 19 9C19.44 9 19.86 8.92998 20.25 8.78998C21.17 8.49998 21.94 7.87 22.42 7.06C22.63 6.72 22.79 6.32999 22.88 5.92999C22.96 5.62999 23 5.32 23 5C23 3.98 22.61 3.04002 21.97 2.33002ZM20.49 5.72998H19.75V6.51001C19.75 6.92001 19.41 7.26001 19 7.26001C18.59 7.26001 18.25 6.92001 18.25 6.51001V5.72998H17.51C17.1 5.72998 16.76 5.38998 16.76 4.97998C16.76 4.56998 17.1 4.22998 17.51 4.22998H18.25V3.52002C18.25 3.11002 18.59 2.77002 19 2.77002C19.41 2.77002 19.75 3.11002 19.75 3.52002V4.22998H20.49C20.9 4.22998 21.24 4.56998 21.24 4.97998C21.24 5.38998 20.91 5.72998 20.49 5.72998Z" fill="currentColor"></path>
                                    <path d="M11.46 14.7299C13.0119 14.7299 14.27 13.4718 14.27 11.9199C14.27 10.3679 13.0119 9.10986 11.46 9.10986C9.9081 9.10986 8.65002 10.3679 8.65002 11.9199C8.65002 13.4718 9.9081 14.7299 11.46 14.7299Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Nickname & Avatar</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Set up an avatar and nickname, it is suggested not to use your real name or the name of your social account as a nickname.
                                    <a href="#!" class="link-underline-primary"> Having trouble?</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <div class="form-check align-items-center">
                                <input class="form-check-input" type="radio" checked disabled>
                                <label class="form-check-label crypt-grayscale-100 text-sm" id="userNickname">Set up</label>
                            </div>
                            <a href="#!" class="btn btn-editor btn-dark text-sm ms-2" onclick="openProfileModal()" role="button">Edit Profile</a>
                        </div>
                    </div>
                </div>
                
                <!-- Email Authentication -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M7 3H17C20 3 22 4.5 22 8V11.14C22 11.87 21.25 12.33 20.58 12.04C19.48 11.56 18.23 11.38 16.92 11.59C14.3 12.02 12.16 14.09 11.63 16.69C11.48 17.45 11.46 18.19 11.56 18.9C11.64 19.49 11.16 20.01 10.57 20.01H7C4 20.01 2 18.51 2 15.01V8.01001C2 4.50001 4 3 7 3Z" fill="currentColor"></path>
                                    <path d="M11.9998 11.87C11.1598 11.87 10.3098 11.61 9.65978 11.08L6.52978 8.57996C6.20978 8.31996 6.14978 7.84997 6.40978 7.52997C6.66978 7.20997 7.13977 7.14997 7.45977 7.40997L10.5898 9.90997C11.3498 10.52 12.6398 10.52 13.3998 9.90997L16.5298 7.40997C16.8498 7.14997 17.3298 7.19997 17.5798 7.52997C17.8398 7.84997 17.7898 8.32996 17.4598 8.57996L14.3298 11.08C13.6898 11.61 12.8398 11.87 11.9998 11.87Z" fill="currentColor"></path>
                                    <path d="M18 13C15.24 13 13 15.23 13 18C13 20.76 15.24 23 18 23C20.77 23 23 20.76 23 18C23 15.23 20.77 13 18 13ZM20.05 17.55L19.68 17.91L17.51 20.09C17.41 20.18 17.22 20.28 17.08 20.3L16.1 20.44C15.75 20.49 15.5 20.24 15.55 19.89L15.69 18.91C15.71 18.77 15.8 18.58 15.9 18.48L18.08 16.31L18.44 15.94C18.68 15.7 18.95 15.54 19.23 15.54C19.47 15.54 19.74 15.65 20.04 15.94C20.7 16.6 20.49 17.11 20.05 17.55Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Email Authentication</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    For Login, withdraw, password retrieval, security settings change and API management verification. 
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <div class="form-check align-items-center">
                                <input class="form-check-input" type="radio" checked disabled>
                                <label class="form-check-label crypt-grayscale-100 text-sm" id="userEmail"><?php echo $UserData['mail']; ?></label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Promo Code -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M20 7H17.2C17.7 6.4 18 5.7 18 5C18 3.3 16.7 2 15 2C13.6 2 12.5 2.9 12 4C11.5 2.9 10.4 2 9 2C7.3 2 6 3.3 6 5C6 5.7 6.3 6.4 6.8 7H4C2.9 7 2 7.9 2 9V20C2 21.1 2.9 22 4 22H20C21.1 22 22 21.1 22 20V9C22 7.9 21.1 7 20 7ZM15 4C15.6 4 16 4.4 16 5C16 5.6 15.6 6 15 6H13C13 4.9 13.9 4 15 4ZM8 5C8 4.4 8.4 4 9 4C10.1 4 11 4.9 11 6H9C8.4 6 8 5.6 8 5ZM20 20H4V9H20V20Z" fill="currentColor"/>
                                <path d="M15 13H9C8.4 13 8 12.6 8 12C8 11.4 8.4 11 9 11H15C15.6 11 16 11.4 16 12C16 12.6 15.6 13 15 13Z" fill="currentColor"/>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Promo Code</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Enter promo codes to receive rewards and bonuses. Each code can be used once per account.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <a href="#!" class="btn btn-editor btn-dark text-sm ms-2" onclick="openPromoModal()" role="button">Enter Code</a>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Notifications Section -->
            <div class="card-bs p-4 mt-2">
                <h5 class="crypt-grayscale-100 fw-bold mb-4">Notifications</h5>
                
                <!-- Notification Alert -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M20.4 16.33C20.12 17.08 19.53 17.65 18.76 17.91C17.68 18.27 16.57 18.54 15.45 18.73C15.34 18.75 15.23 18.7701 15.12 18.7801C14.94 18.8101 14.76 18.83 14.58 18.85C14.36 18.8801 14.13 18.9 13.9 18.92C13.27 18.97 12.65 19 12.02 19C11.38 19 10.74 18.97 10.11 18.91C9.84 18.89 9.58 18.8601 9.32 18.82C9.17 18.8 9.02 18.7801 8.88 18.76C8.77 18.74 8.66 18.73 8.55 18.71C7.44 18.53 6.34 18.26 5.27 17.9C4.47 17.63 3.86 17.06 3.59 16.33C3.32 15.61 3.42 14.77 3.85 14.05L4.98 12.17C5.22 11.76 5.44 10.97 5.44 10.49V8.63005C5.44 5.00005 8.39 2.05005 12.02 2.05005C15.64 2.05005 18.59 5.00005 18.59 8.63005V10.49C18.59 10.97 18.81 11.76 19.06 12.17L20.19 14.05C20.6 14.75 20.68 15.57 20.4 16.33Z" fill="currentColor"></path>
                                    <path d="M12 10.7599C11.58 10.7599 11.24 10.4199 11.24 9.99989V6.89989C11.24 6.47989 11.58 6.13989 12 6.13989C12.42 6.13989 12.76 6.47989 12.76 6.89989V9.99989C12.75 10.4199 12.41 10.7599 12 10.7599Z" fill="currentColor"></path>
                                    <path d="M14.83 20.01C14.41 21.17 13.3 22 12 22C11.21 22 10.43 21.68 9.88 21.11C9.56 20.81 9.32 20.41 9.18 20C9.31 20.02 9.44 20.03 9.58 20.05C9.81 20.08 10.05 20.11 10.29 20.13C10.86 20.18 11.44 20.21 12.02 20.21C12.59 20.21 13.16 20.18 13.72 20.13C13.93 20.11 14.14 20.1 14.34 20.07C14.5 20.05 14.66 20.03 14.83 20.01Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Notification Alert</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    This will affect the language settings of E-mail and App push.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notificationAlert" checked disabled> 
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notification Preferences -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 8C20.6569 8 22 6.65685 22 5C22 3.34315 20.6569 2 19 2C17.3431 2 16 3.34315 16 5C16 6.65685 17.3431 8 19 8Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M19 9.5C16.52 9.5 14.5 7.48 14.5 5C14.5 4.09 14.77 3.25 15.24 2.55C14.22 2.2 13.13 2 12 2C6.49 2 2 6.49 2 12C2 17.51 6.49 22 12 22C17.51 22 22 17.51 22 12C22 10.87 21.8 9.78 21.45 8.76C20.75 9.23 19.91 9.5 19 9.5Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Notification Preferences</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Once configured, you will receive relevant on-site inbox notifications within the app and website.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notificationPrefs">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Auto Price Alert -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M19.21 6.36001C18.17 4.26001 16.16 2.71001 13.83 2.20001C11.39 1.66001 8.89 2.24001 6.98 3.78001C5.06 5.31001 3.97 7.60001 3.97 10.05C3.97 12.64 5.52 15.35 7.86 16.92V17.75C7.85 18.03 7.84 18.46 8.18 18.81C8.53 19.17 9.05 19.21 9.46 19.21H14.59C15.13 19.21 15.54 19.06 15.82 18.78C16.2 18.39 16.19 17.89 16.18 17.62V16.92C19.28 14.83 21.23 10.42 19.21 6.36001Z" fill="currentColor"></path>
                                    <path d="M15.26 21.9999C15.2 21.9999 15.13 21.9899 15.07 21.9699C13.06 21.3999 10.95 21.3999 8.94 21.9699C8.57 22.0699 8.18 21.8599 8.08 21.4899C7.97 21.1199 8.19 20.7299 8.56 20.6299C10.82 19.9899 13.2 19.9899 15.46 20.6299C15.83 20.7399 16.05 21.1199 15.94 21.4899C15.84 21.7999 15.56 21.9999 15.26 21.9999Z" fill="currentColor"></path>
                                    <path d="M12 13.8499C11.87 13.8499 11.74 13.8199 11.63 13.7499C11.27 13.5399 11.15 13.0799 11.35 12.7299L12.2 11.2499H11.36C10.86 11.2499 10.45 11.0299 10.23 10.6599C10.01 10.2799 10.03 9.8199 10.28 9.3799L11.35 7.5199C11.56 7.1599 12.02 7.0399 12.37 7.2399C12.73 7.4499 12.85 7.9099 12.65 8.2599L11.8 9.7499H12.64C13.14 9.7499 13.55 9.9699 13.77 10.3399C13.99 10.7199 13.97 11.1799 13.72 11.6199L12.65 13.4799C12.51 13.7199 12.26 13.8499 12 13.8499Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Auto Price Alert</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Once configured, you will receive alerts on the price changes of major and holding cryptos.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="priceAlert">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card-bs p-4 mt-2">
                <h5 class="crypt-grayscale-100 fw-bold mb-4">System Language</h5>
                
                <!-- Language Preferences -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.87 15.07L10.33 12.56L10.36 12.53C12.1 10.59 13.34 8.36 14.07 6H17V4H10V2H8V4H1V6H11.17C10.5 7.92 9.44 9.75 8 11.17C7.07 10.32 6.3 9.39 5.69 8.4H3.69C4.42 9.63 5.34 10.75 6.41 11.72L2.29 15.84L3.7 17.25L8 13L12.11 17.11L12.87 15.07ZM18.5 10H16.5L12 22H14L15.12 19H19.87L21 22H23L18.5 10ZM15.88 17L17.5 12.67L19.12 17H15.88Z" fill="currentColor"/>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Language Preferences</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Select your preferred language for the interface. Changes will be applied immediately.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <div class="gtranslate_wrapper"></div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <br><br><br><br><br><br><br><br><br>

<!-- Profile Edit Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-bs">
            <div class="modal-header">
                <h5 class="modal-title text-light">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="profileForm">
                    <div class="mb-3">
                        <label class="form-label text-light">Nickname</label>
                        <input type="text" class="form-control" id="profileNickname" placeholder="Enter nickname">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Avatar</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php for($i = 1; $i <= 10; $i++): ?>
                                <img src="core/pages/assets/images/social/ava_<?php echo $i; ?>.png" 
                                     class="avatar-option" data-avatar="ava_<?php echo $i; ?>.png" 
                                     style="width: 40px; height: 40px; border-radius: 50%; cursor: pointer; border: 2px solid transparent;">
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" id="selectedAvatar">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveProfile()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Email Change Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-bs">
            <div class="modal-header">
                <h5 class="modal-title text-light">Change Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="emailForm">
                    <div class="mb-3">
                        <label class="form-label text-light">Current Email</label>
                        <input type="email" class="form-control" id="currentEmail" value="<?php echo $UserData['mail']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">New Email</label>
                        <input type="email" class="form-control" id="newEmail" placeholder="Enter new email address">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveEmail()">Change Email</button>
            </div>
        </div>
    </div>
</div>

<!-- Promo Code Modal -->
<div class="modal fade" id="promoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-bs">
            <div class="modal-header">
                <h5 class="modal-title text-light">Enter Promo Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="promoForm">
                    <div class="mb-3">
                        <label class="form-label text-light">Promo Code</label>
                        <input type="text" class="form-control" id="promoCodeInput" placeholder="Enter your promo code" maxlength="50" style="text-transform: uppercase;">
                        <small class="text-white">Promo codes are case-insensitive</small>
                    </div>
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> How it works</h6>
                            <ul class="mb-0">
                                <li>Enter a valid promo code to receive rewards</li>
                                <li>Each code can only be used once per account</li>
                                <li>Rewards are automatically added to your balance</li>
                                <li>Some codes may have usage limits or expiration dates</li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="redeemPromoCode()" id="redeemBtn">
                    <i class="fas fa-gift me-1"></i>Redeem Code
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadUserSettings();
    
    // Settings change handlers
    $('#notificationAlert, #notificationPrefs, #priceAlert, #shortcuts').on('change', function() {
        saveNotificationSettings();
    });
    
    $('#themeColor').on('change', function() {
        updateThemePreview();
    });
    
    $('.avatar-option').on('click', function() {
        $('.avatar-option').css('border', '2px solid transparent');
        $(this).css('border', '2px solid #007bff');
        $('#selectedAvatar').val($(this).data('avatar'));
    });
});

function loadUserSettings() {
    $.ajax({
        url: 'core/ajax/settings/getUserSettings.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const settings = response.data;
                $('#notificationAlert').prop('checked', settings.notification_alert == 1);
                $('#notificationPrefs').prop('checked', settings.notification_prefs == 1);
                $('#priceAlert').prop('checked', settings.price_alert == 1);
                $('#shortcuts').prop('checked', settings.shortcuts == 1);
                $('#themeColor').val(settings.theme_color || '#facc15');
                $('#timeZone').val(settings.timezone || '24h');
                
                if (settings.nickname) {
                    $('#userNickname').text(settings.nickname);
                }
                
                updateThemePreview();
            }
        }
    });
}

function openProfileModal() {
    $('#profileNickname').val($('#userNickname').text());
    $('#profileModal').modal('show');
}

function openEmailModal() {
    $('#emailModal').modal('show');
}

function saveProfile() {
    const nickname = $('#profileNickname').val();
    const avatar = $('#selectedAvatar').val();
    
    if (!nickname.trim()) {
        JSPop('info','Please enter a nickname');
        return;
    }
    
    $.ajax({
        url: 'core/ajax/settings/updateProfile.php',
        type: 'POST',
        data: JSON.stringify({
            nickname: nickname,
            avatar: avatar
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#userNickname').text(nickname);
                $('#profileModal').modal('hide');
                JSPop('success','Profile updated successfully!');
            } else {
                JSPop('error','Error');
            }
        },
        error: function() {
            JSPop('warning','Failed to update profile data');
        }
    });
}

function saveEmail() {
    const newEmail = $('#newEmail').val();
    const currentPassword = $('#currentPassword').val();
    
    if (!newEmail.trim() || !currentPassword.trim()) {
        alert('Please fill all fields');
        return;
    }
    
    $.ajax({
        url: 'core/ajax/settings/updateEmail.php',
        type: 'POST',
        data: JSON.stringify({
            newEmail: newEmail,
            currentPassword: currentPassword
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#emailModal').modal('hide');
                alert('Email updated successfully! Please verify your new email.');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to update email');
        }
    });
}

function saveNotificationSettings() {
    const settings = {
        notification_alert: $('#notificationAlert').is(':checked') ? 1 : 0,
        notification_prefs: $('#notificationPrefs').is(':checked') ? 1 : 0,
        price_alert: $('#priceAlert').is(':checked') ? 1 : 0,
        shortcuts: $('#shortcuts').is(':checked') ? 1 : 0
    };
    
    $.ajax({
        url: 'core/ajax/settings/updateSettings.php',
        type: 'POST',
        data: JSON.stringify(settings),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log('Settings saved');
            }
        }
    });
}

function saveStyleSettings() {
    const color = $('#themeColor').val();
    
    $.ajax({
        url: 'core/ajax/settings/updateSettings.php',
        type: 'POST',
        data: JSON.stringify({
            theme_color: color
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Style settings saved!');
                updateThemePreview();
            }
        }
    });
}

function saveTimeZone() {
    const timezone = $('#timeZone').val();
    
    $.ajax({
        url: 'core/ajax/settings/updateSettings.php',
        type: 'POST',
        data: JSON.stringify({
            timezone: timezone
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Timezone setting saved!');
            }
        }
    });
}

function saveShortcuts() {
    const shortcuts = $('#shortcuts').is(':checked') ? 1 : 0;
    
    $.ajax({
        url: 'core/ajax/settings/updateSettings.php',
        type: 'POST',
        data: JSON.stringify({
            shortcuts: shortcuts
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Shortcuts setting saved!');
            }
        }
    });
}

function toggleChartIndicator() {
    const current = $('#chartIndicator').text();
    const newValue = current.includes('Green Up') ? 'Red Up / Green Down' : 'Green Up / Red Down';
    $('#chartIndicator').text(newValue);
    
    $.ajax({
        url: 'core/ajax/settings/updateSettings.php',
        type: 'POST',
        data: JSON.stringify({
            chart_indicator: newValue
        }),
        contentType: 'application/json',
        dataType: 'json'
    });
}

function updateThemePreview() {
    const color = $('#themeColor').val();
    const themes = {
        '#facc15': 'Fresh',
        '#3b82f6': 'Ocean',
        '#ef4444': 'Fire',
        '#10b981': 'Nature',
        '#8b5cf6': 'Purple'
    };
    
    $('#themeLabel').text(themes[color] || 'Custom');
}


function openPromoModal() {
    $('#promoCodeInput').val('');
    $('#promoModal').modal('show');
}

function redeemPromoCode() {
    const promoCode = $('#promoCodeInput').val().trim().toUpperCase();
    
    if (!promoCode) {
        JSPop('warning', 'Please enter a promo code');
        return;
    }
    
    if (promoCode.length < 3) {
        JSPop('warning', 'Promo code must be at least 3 characters long');
        return;
    }
    
    $('#redeemBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Processing...');
    
    $.ajax({
        url: 'core/ajax/user/UserPromoManagementAjax.php',
        type: 'POST',
        data: {
            action: 'redeemPromoCode',
            promoCode: promoCode
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#promoModal').modal('hide');
                $('#promoCodeInput').val('');
                
                let rewardText = '';
                if (response.data.reward_type === 'usd') {
                    rewardText = `$${response.data.reward_usd_value} USD`;
                } else {
                    rewardText = `${response.data.reward_amount} ${response.data.coin_symbol.toUpperCase()}`;
                }
                
                JSPop('success', `Promo code redeemed successfully! You received ${rewardText}`);
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
                
            } else {
                JSPop('error', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            JSPop('error', 'Failed to process promo code. Please try again.');
        },
        complete: function() {
            $('#redeemBtn').prop('disabled', false).html('<i class="fas fa-gift me-1"></i>Redeem Code');
        }
    });
}

$('#promoCodeInput').on('input', function() {
    this.value = this.value.toUpperCase();
});

$('#promoCodeInput').on('keypress', function(e) {
    if (e.which === 13) {
        redeemPromoCode();
    }
});

</script>

<style>
.toggle {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle:before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    border-radius: 34px;
    transition: .4s;
}

.toggle:after {
    content: "";
    position: absolute;
    top: 4px;
    left: 4px;
    width: 26px;
    height: 26px;
    background-color: white;
    border-radius: 50%;
    transition: .4s;
}

input:checked + .toggle:before {
    background-color: #2196F3;
}

input:checked + .toggle:after {
    transform: translateX(26px);
}
</style>

<?php require __DIR__ . '/inc/app_footer.php'; ?>