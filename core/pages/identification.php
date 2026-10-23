<?php 
require __DIR__ . '/inc/app_header.php';

global $spexcon;
$identificationQuery = $spexcon->prepare("SELECT * FROM user_identifications WHERE user_id = ?");
$identificationQuery->bind_param("i", $UserUID);
$identificationQuery->execute();
$identification = $identificationQuery->get_result()->fetch_assoc();
?>

<div class="main-content">
    <div class="wrapper pb-2">
        
        <div class="row g-2 animation-element in-view">
            <div class="col-12">
                <div class="card-bs p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h3 class="fw-bold crypt-grayscale-100 mb-1">Identity Verification</h3>
                            <p class="crypt-grayscale-600 mb-0">Complete your identity verification to access all features</p>
                        </div>
                        <div class="text-end">
                            <?php if ($identification): ?>
                                <?php if ($identification['status'] === 'PENDING'): ?>
                                    <span class="badge bg-warning fs-6">Under Review</span>
                                <?php elseif ($identification['status'] === 'APPROVED'): ?>
                                    <span class="badge bg-success fs-6">Verified</span>
                                <?php else: ?>
                                    <span class="badge bg-danger fs-6">Rejected</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-secondary fs-6">Not Submitted</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($identification && $identification['status'] === 'APPROVED'): ?>
                        <!-- Approved Status -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <svg class="text-success" width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor"/>
                                    <path d="M10.5803 15.5801C10.3803 15.5801 10.1903 15.5001 10.0503 15.3601L7.22027 12.5301C6.93027 12.2401 6.93027 11.7601 7.22027 11.4701C7.51027 11.1801 7.99027 11.1801 8.28027 11.4701L10.5803 13.7701L15.7203 8.63008C16.0103 8.34008 16.4903 8.34008 16.7803 8.63008C17.0703 8.92008 17.0703 9.40008 16.7803 9.69008L11.1103 15.3601C10.9703 15.5001 10.7803 15.5801 10.5803 15.5801Z" fill="currentColor"/>
                                </svg>
                            </div>
                            <h4 class="text-success mb-2">Identity Verified Successfully!</h4>
                            <p class="crypt-grayscale-600 mb-4">Your identity has been verified and approved.</p>
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="card-border p-3 text-start">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <small class="crypt-grayscale-500">Full Name:</small>
                                                <p class="fw-bold mb-1"><?php echo $identification['first_name'] . ' ' . $identification['last_name']; ?></p>
                                            </div>
                                            <div class="col-6">
                                                <small class="crypt-grayscale-500">Identity Number:</small>
                                                <p class="fw-bold mb-1"><?php echo $identification['identity_number']; ?></p>
                                            </div>
                                            <div class="col-6">
                                                <small class="crypt-grayscale-500">Birth Date:</small>
                                                <p class="fw-bold mb-1"><?php echo date('d/m/Y', strtotime($identification['birth_date'])); ?></p>
                                            </div>
                                            <div class="col-6">
                                                <small class="crypt-grayscale-500">Verified On:</small>
                                                <p class="fw-bold mb-1"><?php echo date('d/m/Y', strtotime($identification['approved_at'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    <?php elseif ($identification && $identification['status'] === 'REJECTED'): ?>
                        <!-- Rejected Status -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <svg class="text-danger" width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor"/>
                                    <path d="M13.0601 12.0001L15.3601 9.70011C15.6501 9.41011 15.6501 8.93011 15.3601 8.64011C15.0701 8.35011 14.5901 8.35011 14.3001 8.64011L12.0001 10.9401L9.70011 8.64011C9.41011 8.35011 8.93011 8.35011 8.64011 8.64011C8.35011 8.93011 8.35011 9.41011 8.64011 9.70011L10.9401 12.0001L8.64011 14.3001C8.35011 14.5901 8.35011 15.0701 8.64011 15.3601C8.79011 15.5101 8.99011 15.5801 9.18011 15.5801C9.37011 15.5801 9.57011 15.5101 9.72011 15.3601L12.0201 13.0601L14.3201 15.3601C14.4701 15.5101 14.6701 15.5801 14.8601 15.5801C15.0501 15.5801 15.2501 15.5101 15.4001 15.3601C15.6901 15.0701 15.6901 14.5901 15.4001 14.3001L13.0601 12.0001Z" fill="currentColor"/>
                                </svg>
                            </div>
                            <h4 class="text-danger mb-2">Identity Verification Rejected</h4>
                            <p class="crypt-grayscale-600 mb-4">Your identity verification was rejected. Please resubmit with correct information.</p>
                            
                            <?php if ($identification['admin_notes']): ?>
                                <div class="alert alert-danger" role="alert">
                                    <strong>Admin Notes:</strong> <?php echo $identification['admin_notes']; ?>
                                </div>
                            <?php endif; ?>
                            
                            <button class="btn btn-primary" onclick="resubmitIdentification()">Resubmit Identity</button>
                        </div>
                    
                    <?php elseif ($identification && $identification['status'] === 'PENDING'): ?>
                        <!-- Pending Status -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <div class="spinner-border text-warning" style="width: 4rem; height: 4rem;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <h4 class="text-warning mb-2">Identity Under Review</h4>
                            <p class="crypt-grayscale-600 mb-4">Your identity verification is being reviewed. This process usually takes 1-3 business days.</p>
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="card-border p-3 text-start">
                                        <h6 class="fw-bold mb-3">Submitted Information</h6>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <small class="crypt-grayscale-500">Full Name:</small>
                                                <p class="fw-bold mb-1"><?php echo $identification['first_name'] . ' ' . $identification['last_name']; ?></p>
                                            </div>
                                            <div class="col-6">
                                                <small class="crypt-grayscale-500">Identity Number:</small>
                                                <p class="fw-bold mb-1"><?php echo $identification['identity_number']; ?></p>
                                            </div>
                                            <div class="col-6">
                                                <small class="crypt-grayscale-500">Birth Date:</small>
                                                <p class="fw-bold mb-1"><?php echo date('d/m/Y', strtotime($identification['birth_date'])); ?></p>
                                            </div>
                                            <div class="col-6">
                                                <small class="crypt-grayscale-500">Submitted:</small>
                                                <p class="fw-bold mb-1"><?php echo date('d/m/Y H:i', strtotime($identification['submitted_at'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    <?php else: ?>
                        <!-- Identity Verification Form -->
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="card-border p-4">
                                    <div class="text-center mb-4">
                                        <div class="mb-3">
                                            <svg class="text-primary" width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.4" d="M12.1207 12.78C12.0507 12.77 11.9607 12.77 11.8807 12.78C10.1207 12.72 8.7207 11.28 8.7207 9.51C8.7207 7.7 10.1807 6.23 12.0007 6.23C13.8107 6.23 15.2807 7.7 15.2807 9.51C15.2707 11.28 13.8807 12.72 12.1207 12.78Z" fill="currentColor"/>
                                                <path opacity="0.4" d="M18.7398 19.3801C16.9598 21.0101 14.5998 22.0001 11.9998 22.0001C9.39977 22.0001 7.03977 21.0101 5.25977 19.3801C5.35977 18.4401 5.95977 17.5201 7.02977 16.8001C9.76977 14.9801 14.2498 14.9801 16.9698 16.8001C18.0398 17.5201 18.6398 18.4401 18.7398 19.3801Z" fill="currentColor"/>
                                                <path d="M12.1207 12.78C12.0507 12.77 11.9607 12.77 11.8807 12.78C10.1207 12.72 8.7207 11.28 8.7207 9.51C8.7207 7.7 10.1807 6.23 12.0007 6.23C13.8107 6.23 15.2807 7.7 15.2807 9.51C15.2707 11.28 13.8807 12.72 12.1207 12.78Z" fill="currentColor"/>
                                                <path d="M18.7398 19.3801C16.9598 21.0101 14.5998 22.0001 11.9998 22.0001C9.39977 22.0001 7.03977 21.0101 5.25977 19.3801C5.35977 18.4401 5.95977 17.5201 7.02977 16.8001C9.76977 14.9801 14.2498 14.9801 16.9698 16.8001C18.0398 17.5201 18.6398 18.4401 18.7398 19.3801Z" fill="currentColor"/>
                                            </svg>
                                        </div>
                                        <h5 class="fw-bold crypt-grayscale-100 mb-2">Identity Verification</h5>
                                        <p class="crypt-grayscale-600">Please provide the following information to verify your identity</p>
                                    </div>
                                    
                                    <form id="identificationForm" enctype="multipart/form-data">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label text-light">First Name *</label>
                                                <input type="text" class="form-control" id="firstName" name="firstName" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-light">Last Name *</label>
                                                <input type="text" class="form-control" id="lastName" name="lastName" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-light">Identity Number *</label>
                                                <input type="text" class="form-control" id="identityNumber" name="identityNumber" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-light">Serial Number</label>
                                                <input type="text" class="form-control" id="serialNumber" name="serialNumber">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-light">Birth Date *</label>
                                                <input type="date" class="form-control" id="birthDate" name="birthDate" required>
                                            </div>
                                            <div class="col-md-6"></div>
                                            
                                            <!-- Front Image Upload -->
                                            <div class="col-md-6">
                                                <label class="form-label text-light">Identity Card Front *</label>
                                                <label for="frontImage" class="upload-area d-block" id="frontUploadArea" style="cursor: pointer;">
                                                    <input type="file" class="d-none" id="frontImage" name="frontImage" accept="image/*" required>
                                                    <div class="upload-content text-center" id="frontUploadContent">
                                                        <svg class="text-primary mb-2" width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.4" d="M22 11.1V16.9C22 20 20 22 16.9 22H7.1C4 22 2 20 2 16.9V11.1C2 8 4 6 7.1 6H16.9C20 6 22 8 22 11.1Z" fill="currentColor"/>
                                                            <path d="M12.75 8.25V14.8L14.13 13.42C14.42 13.13 14.9 13.13 15.19 13.42C15.48 13.71 15.48 14.19 15.19 14.48L12.53 17.14C12.39 17.28 12.2 17.35 12 17.35C11.8 17.35 11.61 17.28 11.47 17.14L8.81 14.48C8.52 14.19 8.52 13.71 8.81 13.42C9.1 13.13 9.58 13.13 9.87 13.42L11.25 14.8V8.25C11.25 7.84 11.59 7.5 12 7.5C12.41 7.5 12.75 7.84 12.75 8.25Z" fill="currentColor"/>
                                                            <path d="M8.5 2.75C8.5 2.34 8.84 2 9.25 2H14.75C15.16 2 15.5 2.34 15.5 2.75C15.5 3.16 15.16 3.5 14.75 3.5H9.25C8.84 3.5 8.5 3.16 8.5 2.75Z" fill="currentColor"/>
                                                        </svg>
                                                        <p class="mb-1 text-light">Click to upload front image</p>
                                                        <small class="crypt-grayscale">PNG, JPG up to 5MB</small>
                                                    </div>
                                                    <div class="upload-preview d-none" id="frontPreview">
                                                        <img id="frontPreviewImage" src="" alt="Front Preview" class="img-fluid rounded">
                                                        <button type="button" class="btn btn-sm btn-danger remove-btn" onclick="removeImage('front')">×</button>
                                                    </div>
                                                </label>
                                            </div>
                                            
                                            <!-- Back Image Upload -->
                                            <div class="col-md-6">
                                                <label class="form-label text-light">Identity Card Back *</label>
                                                <label for="backImage" class="upload-area d-block" id="backUploadArea" style="cursor: pointer;">
                                                    <input type="file" class="d-none" id="backImage" name="backImage" accept="image/*" required>
                                                    <div class="upload-content text-center" id="backUploadContent">
                                                        <svg class="text-primary mb-2" width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.4" d="M22 11.1V16.9C22 20 20 22 16.9 22H7.1C4 22 2 20 2 16.9V11.1C2 8 4 6 7.1 6H16.9C20 6 22 8 22 11.1Z" fill="currentColor"/>
                                                            <path d="M12.75 8.25V14.8L14.13 13.42C14.42 13.13 14.9 13.13 15.19 13.42C15.48 13.71 15.48 14.19 15.19 14.48L12.53 17.14C12.39 17.28 12.2 17.35 12 17.35C11.8 17.35 11.61 17.28 11.47 17.14L8.81 14.48C8.52 14.19 8.52 13.71 8.81 13.42C9.1 13.13 9.58 13.13 9.87 13.42L11.25 14.8V8.25C11.25 7.84 11.59 7.5 12 7.5C12.41 7.5 12.75 7.84 12.75 8.25Z" fill="currentColor"/>
                                                            <path d="M8.5 2.75C8.5 2.34 8.84 2 9.25 2H14.75C15.16 2 15.5 2.34 15.5 2.75C15.5 3.16 15.16 3.5 14.75 3.5H9.25C8.84 3.5 8.5 3.16 8.5 2.75Z" fill="currentColor"/>
                                                        </svg>
                                                        <p class="mb-1 text-light">Click to upload back image</p>
                                                        <small class="crypt-grayscale">PNG, JPG up to 5MB</small>
                                                    </div>
                                                    <div class="upload-preview d-none" id="backPreview">
                                                        <img id="backPreviewImage" src="" alt="Back Preview" class="img-fluid rounded">
                                                        <button type="button" class="btn btn-sm btn-danger remove-btn" onclick="removeImage('back')">×</button>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <div class="alert alert-info" role="alert">
                                                <strong>Important Notes:</strong>
                                                <ul class="mb-0 mt-2">
                                                    <li>Make sure all information is clearly visible</li>
                                                    <li>Photos should be high quality and well-lit</li>
                                                    <li>Identity card should be placed on a flat surface</li>
                                                    <li>Verification process takes 1-3 business days</li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary px-5" id="submitBtn">
                                                <span id="submitText">Submit for Verification</span>
                                                <span id="submitSpinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#frontUploadArea').on('click', function() {
        $('#frontImage').click();console.log('Front area clicked');
    });
    
    $('#backUploadArea').on('click', function() {
        $('#backImage').click();
    });
    
    $('#frontImage').on('change', function() {
        handleFileUpload(this, 'front');
    });
    
    $('#backImage').on('change', function() {
        handleFileUpload(this, 'back');
    });
    
    $('#identificationForm').on('submit', function(e) {
        e.preventDefault();
        submitIdentification();
    });
    
    $('#serialNumber').on('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    $('#firstName, #lastName').on('input', function() {
        this.value = this.value.replace(/[^a-zA-ZğüşıöçĞÜŞİÖÇ\s]/g, '');
    });
});

function handleFileUpload(input, type) {
    const file = input.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        JSPop('warning','Please select a valid image file');
        input.value = '';
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        JSPop('info','File size must be less than 5MB');
        input.value = '';
        return;
    }
    
    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        $(`#${type}PreviewImage`).attr('src', e.target.result);
        $(`#${type}UploadContent`).addClass('d-none');
        $(`#${type}Preview`).removeClass('d-none');
    };
    reader.readAsDataURL(file);
}

function removeImage(type) {
    $(`#${type}Image`).val('');
    $(`#${type}PreviewImage`).attr('src', '');
    $(`#${type}UploadContent`).removeClass('d-none');
    $(`#${type}Preview`).addClass('d-none');
}

function submitIdentification() {
    const formData = new FormData();
    const frontImage = $('#frontImage')[0].files[0];
    const backImage = $('#backImage')[0].files[0];
    
    // Validate required fields
    if (!$('#firstName').val().trim()) {
        JSPop('info','First name is required');
        return;
    }
    
    if (!$('#lastName').val().trim()) {
        JSPop('info','Last name is required');
        return;
    }
    
    if (!$('#identityNumber').val().trim()) {
        JSPop('info','Identity number is required');
        return;
    }
    
    if (!$('#birthDate').val()) {
        JSPop('info','Birth date is required');
        return;
    }
    
    if (!frontImage) {
        JSPop('info','Front image of identity card is required');
        return;
    }
    
    if (!backImage) {
        JSPop('info','Back image of identity card is required');
        return;
    }
    
    // Prepare form data
    formData.append('firstName', $('#firstName').val().trim());
    formData.append('lastName', $('#lastName').val().trim());
    formData.append('identityNumber', $('#identityNumber').val().trim());
    formData.append('serialNumber', $('#serialNumber').val().trim());
    formData.append('birthDate', $('#birthDate').val());
    formData.append('frontImage', frontImage);
    formData.append('backImage', backImage);
    
    // Show loading state
    $('#submitBtn').prop('disabled', true);
    $('#submitText').text('Submitting...');
    $('#submitSpinner').removeClass('d-none');
    
    $.ajax({
        url: 'core/ajax/identification/submit.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                JSPop('success','Identity verification submitted successfully!');
                location.reload();
            } else {
                JSPop('error','Error');
            }
        },
        error: function() {
            JSPop('error','Failed to submit identity verification');
        },
        complete: function() {
            $('#submitBtn').prop('disabled', false);
            $('#submitText').text('Submit for Verification');
            $('#submitSpinner').addClass('d-none');
        }
    });
}

function resubmitIdentification() {
    if (confirm('Are you sure you want to resubmit your identity verification?')) {
        $.ajax({
            url: 'core/ajax/identification/resubmit.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    JSPop('error','Error');
                }
            }
        });
    }
}
</script>

<style>
.upload-area {
    border: 2px dashed #6c757d;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-area:hover {
    border-color: #007bff;
    background-color: rgba(0, 123, 255, 0.1);
}

.upload-area.dragover {
    border-color: #007bff;
    background-color: rgba(0, 123, 255, 0.1);
}

.upload-preview {
    position: relative;
    width: 100%;
}

.upload-preview img {
    max-width: 100%;
    max-height: 200px;
    object-fit: cover;
}

.remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    line-height: 1;
}

.alert ul {
    margin-bottom: 0;
}

.alert ul li {
    margin-bottom: 0.5rem;
}
</style>
<br><br><br><br><br><br><br><br>
<?php require __DIR__ . '/inc/app_footer.php'; ?>