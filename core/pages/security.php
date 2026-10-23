<?php require __DIR__ . '/inc/app_header.php'; ?>

<div class="main-content">
    <div class="wrapper pb-2">
        
        <div class="d-flex flex-column pb-4 animation-element in-view">
            
            <!-- Privacy Settings Section -->
            <div class="card-bs p-4">
                <h5 class="crypt-grayscale-100 fw-bold mb-4">Privacy Settings</h5>
                
                <!-- Data Sharing -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M12.0012 2.91992C8.71124 2.91992 6.02124 5.59992 6.02124 8.89992V11.1999C6.02124 12.2999 5.72124 13.7099 5.22124 14.6299L3.71124 17.0799C2.96124 18.3999 3.41124 19.9799 4.83124 20.5199C9.30124 22.1499 14.7012 22.1499 19.1712 20.5199C20.5012 20.0199 20.9812 18.5399 20.2912 17.0799L18.7812 14.6299C18.2812 13.7099 17.9812 12.2999 17.9812 11.1999V8.89992C17.9812 5.60992 15.2912 2.91992 12.0012 2.91992Z" fill="currentColor"></path>
                                    <path d="M13.8809 3.19994C13.5609 3.11994 13.2409 3.04994 12.9109 2.99994C11.9509 2.87994 11.0309 2.94994 10.1709 3.19994C10.4609 2.45994 11.1809 1.93994 12.0209 1.93994C12.8609 1.93994 13.5809 2.45994 13.8809 3.19994Z" fill="currentColor"></path>
                                    <path d="M12.02 22.8101C10.76 22.8101 9.54004 22.1601 8.87004 21.1801C8.56004 20.7401 8.24004 20.2801 8.24004 19.7801H15.79C15.79 20.2701 15.47 20.7401 15.17 21.1801C14.49 22.1601 13.28 22.8101 12.02 22.8101Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Don't Share Personal Data</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Prevent sharing your personal information with third-party companies for marketing or analytics purposes.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="noDataSharing" checked>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Analytics & Cookies -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.83 4.17 22 7.81 22H16.18C19.82 22 21.99 19.83 21.99 16.19V7.81C22 4.17 19.83 2 16.19 2Z" fill="currentColor"></path>
                                    <path d="M8.57001 15.27L15.11 8.72998C15.4 8.43998 15.4 7.95998 15.11 7.66998C14.82 7.37998 14.34 7.37998 14.05 7.66998L7.50001 14.22C7.21001 14.51 7.21001 14.99 7.50001 15.28C7.65001 15.42 7.84001 15.49 8.04001 15.49C8.23001 15.49 8.42001 15.42 8.57001 15.27Z" fill="currentColor"></path>
                                    <path d="M14.5 14.79C13.95 14.79 13.5 15.24 13.5 15.79C13.5 16.34 13.95 16.79 14.5 16.79C15.05 16.79 15.5 16.34 15.5 15.79C15.5 15.24 15.05 14.79 14.5 14.79Z" fill="currentColor"></path>
                                    <path d="M9.5 7.20999C8.95 7.20999 8.5 7.65999 8.5 8.20999C8.5 8.75999 8.95 9.20999 9.5 9.20999C10.05 9.20999 10.5 8.75999 10.5 8.20999C10.5 7.65999 10.05 7.20999 9.5 7.20999Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Analytics & Performance Cookies</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Allow collection of anonymous analytics data to improve our platform performance and user experience.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="analyticsCookies">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Data Retention -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor"></path>
                                    <path d="M15.71 15.18L13.31 13.69C12.61 13.24 12.06 12.22 12.06 11.4V7.84C12.06 7.42 12.4 7.09 12.81 7.09C13.22 7.09 13.56 7.43 13.56 7.84V11.4C13.56 11.69 13.79 12.13 14.02 12.3L16.42 13.79C16.78 14.02 16.89 14.46 16.66 14.82C16.5 15.05 16.25 15.18 15.99 15.18C15.85 15.18 15.7 15.13 15.71 15.18Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Data Retention Period</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Choose how long your personal data should be retained after account closure.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <select class="form-select" id="dataRetention" style="width: auto;">
                                <option value="30">30 Days</option>
                                <option value="90" selected>90 Days</option>
                                <option value="365">1 Year</option>
                                <option value="immediate">Delete Immediately</option>
                            </select>
                            <a href="#!" class="btn btn-editor btn-dark text-sm ms-2" onclick="saveDataRetention()" role="button">Save</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Security Section -->
            <div class="card-bs p-4 mt-2">
                <h5 class="crypt-grayscale-100 fw-bold mb-4">Account Security</h5>
                
                <!-- Two-Factor Authentication -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.83 4.17 22 7.81 22H16.18C19.82 22 21.99 19.83 21.99 16.19V7.81C22 4.17 19.83 2 16.19 2Z" fill="currentColor"></path>
                                    <path d="M11.28 14.48C10.93 14.83 10.37 14.83 10.02 14.48L8.47 12.93C8.12 12.58 8.12 12.02 8.47 11.67C8.82 11.32 9.38 11.32 9.73 11.67L10.65 12.59L14.27 8.97C14.62 8.62 15.18 8.62 15.53 8.97C15.88 9.32 15.88 9.88 15.53 10.23L11.28 14.48Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Two-Factor Authentication</h6>
                                    <span id="2faStatus" class="badge bg-danger ms-2">Disabled</span>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Add an extra layer of security to your account with two-factor authentication using Google Authenticator.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <button type="button" class="btn btn-primary text-sm ms-2" id="2faButton" onclick="setup2FA()">Enable 2FA</button>
                        </div>
                    </div>
                </div>

                <!-- Login Notifications -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M16.0401 2H7.9601C4.9901 2 3.0001 3.99 3.0001 6.96V17.04C3.0001 20.01 4.9901 22 7.9601 22H16.0401C19.0101 22 21.0001 20.01 21.0001 17.04V6.96C21.0001 3.99 19.0101 2 16.0401 2Z" fill="currentColor"></path>
                                    <path d="M8.29006 6.29004C7.90006 6.68004 7.90006 7.31004 8.29006 7.70004C8.68006 8.09004 9.31006 8.09004 9.70006 7.70004C10.0901 7.31004 10.0901 6.68004 9.70006 6.29004C9.31006 5.90004 8.68006 5.90004 8.29006 6.29004Z" fill="currentColor"></path>
                                    <path d="M10.93 12.0001H15.66C16.1 12.0001 16.45 11.65 16.45 11.21C16.45 10.77 16.1 10.42 15.66 10.42H10.93C10.49 10.42 10.14 10.77 10.14 11.21C10.14 11.65 10.49 12.0001 10.93 12.0001Z" fill="currentColor"></path>
                                    <path d="M8.29006 16.71C8.68006 17.1 9.31006 17.1 9.70006 16.71C10.0901 16.32 10.0901 15.69 9.70006 15.3C9.31006 14.91 8.68006 14.91 8.29006 15.3C7.90006 15.69 7.90006 16.32 8.29006 16.71Z" fill="currentColor"></path>
                                    <path d="M10.93 17.58H15.66C16.1 17.58 16.45 17.23 16.45 16.79C16.45 16.35 16.1 16 15.66 16H10.93C10.49 16 10.14 16.35 10.14 16.79C10.14 17.23 10.49 17.58 10.93 17.58Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Login Notifications</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Get notified via email when someone logs into your account from a new device.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="loginNotifications" checked disabled>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Session Management -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M22 16.19V7.81C22 4.17 19.83 2 16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.83 4.17 22 7.81 22H16.18C19.82 22 21.99 19.83 21.99 16.19Z" fill="currentColor"></path>
                                    <path d="M19.2 12.21H17.24C15.23 12.21 13.62 10.6 13.62 8.59V6.63C13.62 6.22 13.28 5.88 12.87 5.88C12.46 5.88 12.12 6.22 12.12 6.63V8.59C12.12 11.42 14.41 13.71 17.24 13.71H19.2C19.61 13.71 19.95 13.37 19.95 12.96C19.95 12.55 19.61 12.21 19.2 12.21Z" fill="currentColor"></path>
                                    <path d="M11.76 10.59L14.67 7.68C14.96 7.39 14.96 6.91 14.67 6.62C14.38 6.33 13.9 6.33 13.61 6.62L10.7 9.53C10.41 9.82 10.41 10.3 10.7 10.59C10.85 10.74 11.04 10.81 11.23 10.81C11.42 10.81 11.61 10.74 11.76 10.59Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Active Sessions</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Manage and terminate active sessions on different devices for enhanced security.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <a href="#!" class="btn btn-editor btn-dark text-sm ms-2" onclick="viewActiveSessions()" role="button">Manage Sessions</a>
                        </div>
                    </div>
                </div>
                
                <!-- Password Security -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-primary">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M6 10V8C6 5.79 7.79 4 10 4H14C16.21 4 18 5.79 18 8V10C19.1 10 20 10.9 20 12V18C20 19.1 19.1 20 18 20H6C4.9 20 4 19.1 4 18V12C4 10.9 4.9 10 6 10ZM8 8V10H16V8C16 6.9 15.1 6 14 6H10C8.9 6 8 6.9 8 8Z" fill="currentColor"></path>
                                    <path d="M12 17C12.55 17 13 16.55 13 16C13 15.45 12.55 15 12 15C11.45 15 11 15.45 11 16C11 16.55 11.45 17 12 17Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-light fs-6 fw-bold mb-0">Password Security</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Change your password regularly and use a strong, unique password for your account.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-primary">
                            <a href="#!" class="btn btn-editor btn-dark text-sm ms-2" onclick="changePassword()" role="button">Change Password</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Control Section -->
            <div class="card-bs p-4 mt-2">
                <h5 class="crypt-grayscale-100 fw-bold mb-4">Data Control</h5>
                
                <!-- Account Deletion -->
                <div class="row align-items-center justify-content-between mb-4 mb-sm-3">
                    <div class="col-12 col-md-6">
                        <div class="d-sm-flex gap-3 text-danger">
                            <div class="flex mb-2">
                                <svg class="p-2 card-bs-tabs text-danger" width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M17.9801 14.95L15.9301 16.99C15.8001 17.12 15.6901 17.35 15.6601 17.53L15.5401 18.5C15.4401 19.25 16.0601 19.87 16.8101 19.77L17.7701 19.65C17.9501 19.62 18.1801 19.51 18.3101 19.38L20.3601 17.33C21.0901 16.6 21.4401 15.73 20.3601 14.65C19.2801 13.57 18.7001 14.22 17.9801 14.95Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M20.8401 9.00006C20.8401 13.3901 17.3901 17.0001 13.0401 17.0001C8.69006 17.0001 5.24006 13.3901 5.24006 9.00006C5.24006 4.61006 8.69006 1.00006 13.0401 1.00006C17.3901 1.00006 20.8401 4.61006 20.8401 9.00006Z" fill="currentColor"></path>
                                    <path d="M14.8601 23H4.25C3.84 23 3.5 22.66 3.5 22.25C3.5 21.84 3.84 21.5 4.25 21.5H14.86C15.27 21.5 15.61 21.84 15.61 22.25C15.61 22.66 15.27 23 14.8601 23Z" fill="currentColor"></path>
                                    <path d="M10.33 12.5H15.67C16.08 12.5 16.42 12.16 16.42 11.75C16.42 11.34 16.08 11 15.67 11H10.33C9.92 11 9.58 11.34 9.58 11.75C9.58 12.16 9.92 12.5 10.33 12.5Z" fill="currentColor"></path>
                                    <path d="M10.33 8.5H15.67C16.08 8.5 16.42 8.16 16.42 7.75C16.42 7.34 16.08 7 15.67 7H10.33C9.92 7 9.58 7.34 9.58 7.75C9.58 8.16 9.92 8.5 10.33 8.5Z" fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="flex">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-danger fs-6 fw-bold mb-0">Delete My Account</h6>
                                </div>
                                <p class="crypt-grayscale-500 mt-2">
                                    Permanently delete your account and all associated data. This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-sm-end align-items-center gap-2 text-danger">
                            <a href="#!" class="btn btn-danger text-sm ms-2" onclick="deleteAccount()" role="button">Delete Account</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Login History Section -->
            <div class="card-bs p-4 mt-2">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="crypt-grayscale-100 fw-bold mb-0">Recent Login Activity</h5>
                    <a href="#!" class="btn btn-outline-primary btn-sm" onclick="refreshLoginHistory()">Refresh</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Device / Browser</th>
                                <th>Location</th>
                                <th>IP Address</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="loginHistoryTable">
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Two-Factor Authentication Setup Modal -->
<div class="modal fade" id="twoFAModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content 
                bg-body-tertiary 
                text-body 
                dark-bg-body-tertiary 
                dark-text-body
                text-center
                ">
      <div class="modal-header border-0 justify-content-center">
        <h5 class="modal-title text-body">Setup Two-Factor Authentication</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex flex-column align-items-center">
        
        <!-- Üst blok -->
        <div class="mb-4 p-3 bg-body-secondary rounded w-100" style="max-width: 400px;">
          <h6>Step 1: Download Google Authenticator App</h6>
          <p>Download Google Authenticator App</p>
          <div class="d-flex justify-content-center gap-3 mb-3">
            <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank">
              <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Download_on_the_App_Store_RGB_blk.svg" alt="App Store" style="height: 40px;">
            </a>
            <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">
              <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play" style="height: 40px;">
            </a>
          </div>

          <h6>Step 2: Scan QR Code</h6>
          <div class="text-center mb-3 p-3 bg-white rounded d-inline-block mx-auto" style="width: 200px; height: 200px;">
            <img id="qrCode" src="" alt="QR Code" style="width: 100%; height: 100%;">
          </div>

          <h6>Or enter this key manually:</h6>
          <div>
            <code id="secretKey" class="bg-body rounded p-2 d-block text-center" style="user-select: all;"></code>
          </div>
        </div>

        <!-- Alt blok -->
        <div class="p-3 bg-body-secondary rounded w-100" style="max-width: 400px;">
          <h6>Step 3: Enter Verification Code</h6>
          <p>Enter the 6-digit code from your Google Authenticator app:</p>
          <input type="text" id="verificationCode" maxlength="6" pattern="[0-9]{6}" placeholder="000000" class="form-control form-control-lg text-center mb-3">
          <button type="button" class="btn btn-success w-100" onclick="verify2FA()">Verify & Enable 2FA</button>
        </div>

      </div>
    </div>
  </div>
</div>


<!-- Change Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-bs">
            <div class="modal-header">
                <h5 class="modal-title text-light">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    <div class="mb-3">
                        <label class="form-label text-light">New Password</label>
                        <input type="password" class="form-control" id="newPassword" required>
                        <div class="form-text">Password must be at least 8 characters long</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePassword()">Change Password</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadSecuritySettings();
    loadLoginHistory();
    
    // Settings change handlers
    $('#noDataSharing, #marketingEmails, #analyticsCookies, #loginNotifications').on('change', function() {
        saveSecuritySettings();
    });
});

function loadSecuritySettings() {
    $.ajax({
        url: 'core/ajax/security/getSecuritySettings.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const settings = response.data;
                $('#noDataSharing').prop('checked', settings.no_data_sharing == 1);
                $('#marketingEmails').prop('checked', settings.marketing_emails == 1);
                $('#analyticsCookies').prop('checked', settings.analytics_cookies == 1);
                $('#loginNotifications').prop('checked', settings.login_notifications == 1);
                $('#dataRetention').val(settings.data_retention || '90');
                
                if (settings.two_factor_enabled) {
                    $('#2faStatus').removeClass('bg-danger').addClass('bg-success').text('Enabled');
                    $('#2faButton').text('Disable 2FA').attr('onclick', 'disable2FA()');
                }
                
                if (settings.last_password_change) {
                    $('#lastPasswordChange').text(new Date(settings.last_password_change).toLocaleDateString());
                }
            }
        }
    });
}

function loadSecuritySettings() {
    $.ajax({
        url: 'core/ajax/security/getSecuritySettings.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const settings = response.data;
                $('#noDataSharing').prop('checked', settings.no_data_sharing == 1);
                $('#analyticsCookies').prop('checked', settings.analytics_cookies == 1);
                $('#loginNotifications').prop('checked', settings.login_notifications == 1);
                $('#dataRetention').val(settings.data_retention || '90');
                
                if (settings.two_factor_enabled == 1 || settings.has_2fa) {
                    $('#2faStatus').removeClass('bg-danger').addClass('bg-success').text('Enabled');
                    $('#2faButton').text('Disable 2FA').removeClass('btn-primary').addClass('btn-danger').attr('onclick', 'disable2FA()');
                } else {
                    $('#2faStatus').removeClass('bg-success').addClass('bg-danger').text('Disabled');
                    $('#2faButton').text('Enable 2FA').removeClass('btn-danger').addClass('btn-primary').attr('onclick', 'setup2FA()');
                }
            }
        }
    });
}


function saveDataRetention() {
    const retention = $('#dataRetention').val();
    
    $.ajax({
        url: 'core/ajax/security/updateSecuritySettings.php',
        type: 'POST',
        data: JSON.stringify({
            data_retention: retention
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success','Data retention setting updated!');
            }
        }
    });
}

function changePassword() {
    $('#passwordModal').modal('show');
}

function savePassword() {
    const newPassword = $('#newPassword').val();
    const confirmPassword = $('#confirmPassword').val();
    
    if (!newPassword || !confirmPassword) {
        JSPop('info','Please fill all field');
        return;
    }
    
    if (newPassword.length < 8) {
        JSPop('warning','New password must be at least 8 characters');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        JSPop('warning','New passwords do not match');
        return;
    }
    
    JSPop('warning', 
        'Change Password<br><br>Are you sure you want to change your password?<br><br>You will be logged out from all devices.', 
        '1',
        function() {
            $.ajax({
                url: 'core/ajax/security/changePassword.php',
                type: 'POST',
                data: JSON.stringify({
                    newPassword: newPassword
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        JSPop('success','Password changed successfully!');
                        $('#passwordModal').modal('hide');
                        $('#passwordForm')[0].reset();
                        loadSecuritySettings();
                    } else {
                        JSPop('error', response.message || 'Error changing password');
                    }
                },
                error: function(xhr, status, error) {
                    JSPop('error', 'Network error occurred while changing password');
                }
            });
        },
        function() {
        }
    );
}

function setup2FA() {
    $.ajax({
        url: 'core/ajax/security/setup2FA.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log(response);
                if (response.qr_code) {
                    $('#qrCode').attr('src', response.qr_code);
                }
                $('#secretKey').text(response.secret);
                $('#twoFAModal').modal('show');
            } else {
                JSPop('error', response.message || 'Error setting up 2FA');
            }
        }
    });
}

function verify2FA() {
    const code = $('#verificationCode').val();
    
    if (!code || code.length !== 6) {
        JSPop('warning', 'Please enter a valid 6-digit code');
        return;
    }
    
    $.ajax({
        url: 'core/ajax/security/verify2FA.php',
        type: 'POST',
        data: JSON.stringify({
            code: code
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success', '2FA enabled successfully!');
                $('#twoFAModal').modal('hide');
                $('#verificationCode').val('');
                loadSecuritySettings();
            } else {
                JSPop('error', response.message || 'Invalid verification code');
            }
        }
    });
}

function disable2FA() {
    JSPop('warning', 
        'Are you sure you want to disable Two-Factor Authentication?<br><br>This will make your account less secure.', 
        '1',
        function() {
            $.ajax({
                url: 'core/ajax/security/disable2FA.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        JSPop('success', '2FA disabled successfully!');
                        loadSecuritySettings();
                    } else {
                        JSPop('error', response.message || 'Error disabling 2FA');
                    }
                },
                error: function(xhr, status, error) {
                    JSPop('error', 'Network error occurred while disabling 2FA');
                }
            });
        },
        function() {
        }
    );
}

function loadLoginHistory() {
    $.ajax({
        url: 'core/ajax/security/getLoginHistory.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderLoginHistory(response.data);
            }
        }
    });
}

function renderLoginHistory(history) {
    if (!history || history.length === 0) {
        $('#loginHistoryTable').html('<tr><td colspan="5" class="text-center text-muted">No login history available</td></tr>');
        return;
    }
    
    let html = '';
    history.forEach(item => {
        const statusBadge = item.status === 'SUCCESS' ? 
            '<span class="badge bg-success">Success</span>' : 
            '<span class="badge bg-danger">Failed</span>';
            
        html += `
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <svg width="16" height="16" fill="currentColor" class="text-primary">
                            <path d="M12 1a11 11 0 1 0 11 11A11 11 0 0 0 12 1zm0 20a9 9 0 1 1 9-9 9 9 0 0 1-9 9z"/>
                        </svg>
                        <div>
                            <div class="fw-medium">${item.device || 'Unknown Device'}</div>
                            <small class="text-muted">${item.browser || 'Unknown Browser'}</small>
                        </div>
                    </div>
                </td>
                <td>${item.location || 'Unknown'}</td>
                <td><code>${item.ip_address}</code></td>
                <td>${new Date(item.login_time).toLocaleString()}</td>
                <td>${statusBadge}</td>
            </tr>
        `;
    });
    
    $('#loginHistoryTable').html(html);
}

function refreshLoginHistory() {
    $('#loginHistoryTable').html('<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>');
    loadLoginHistory();
}

function viewActiveSessions() {
    JSPop('info','Active sessions management feature coming soon!');
}

function requestDataDownload() {
    JSPop('info', 
        'Request Data Download<br><br>Are you sure you want to request a download of all your personal data?<br><br>You will receive an email with download link within 24 hours.', 
        '1',
        function() {
            $.ajax({
                url: 'core/ajax/security/requestDataDownload.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        JSPop('success', 'Data download request submitted! You will receive an email with download link within 24 hours.');
                    } else {
                        JSPop('error', response.message || 'Error submitting data download request');
                    }
                },
                error: function(xhr, status, error) {
                    JSPop('error', 'Network error occurred while requesting data download');
                }
            });
        },
        function() {
        }
    );
}

function deleteAccount() {
    JSPop('error', 
        'Type "DELETE" in the input field below to confirm account deletion:<br><br>' +
        '<input type="text" id="deleteConfirmInput" class="form-control mt-2" placeholder="Type DELETE here" style="background: #2d3436; border: 1px solid #636e72; color: #fff;">' +
        '<br><small class="text-danger">This action cannot be undone!</small>', 
        '1',
        function() {
            const confirmInput = document.getElementById('deleteConfirmInput');
            const confirmText = confirmInput ? confirmInput.value.trim() : '';
            
            if (confirmText === 'DELETE') {
                $.ajax({
                    url: 'core/ajax/security/deleteAccount.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            JSPop('success', 'Account deleted successfully! Redirecting...');
                            setTimeout(() => {
                                window.location.href = '?r=logout';
                            }, 2000);
                        } else {
                            JSPop('error', response.message || 'Error deleting account');
                        }
                    },
                    error: function(xhr, status, error) {
                        JSPop('error', 'Network error occurred while deleting account');
                    }
                });
            } else {
                JSPop('warning', 'Account deletion cancelled. Please type "DELETE" exactly to confirm.');
            }
        },
        function() {
        }
    );
}
</script>

<?php require __DIR__ . '/inc/app_footer.php'; ?>