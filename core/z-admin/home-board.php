<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Home Board Management</h4>
                    <p class="page-title-desc">Manage home board images and content</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardModal">
                    <i class="fas fa-plus me-1"></i>Add New Board Image
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="boardTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image Preview</th>
                                    <th>Image Path</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="boardTableBody">
                                <tr><td colspan="5" class="text-center"><div class="spinner-border"></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Add Board Image Modal -->
<div class="modal fade" id="addBoardModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Board Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="boardForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="boardImage" accept="image/*" required>
                        <small class="text-muted">Supported formats: JPG, PNG, GIF. Max size: 5MB</small>
                    </div>
                    
                    <div class="mb-3" id="imagePreview" style="display: none;">
                        <label class="form-label">Preview</label>
                        <div class="border rounded p-3 text-center">
                            <img id="previewImg" src="" style="max-width: 100%; max-height: 300px;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBoardImage()">Upload Image</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadBoardImages();

    $('#addBoardModal').on('hidden.bs.modal', function() {
        resetForm();
    });
    
    // Image preview functionality
    $('#boardImage').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });
});

function loadBoardImages() {
    $.ajax({
        url: 'ajax/homeboardAjax.php',
        method: 'POST',
        data: { action: 'getBoardImages' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayBoardImages(response.data);
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        },
        error: function() {
            Swal.fire("Error!", "Failed to load board images", "error");
        }
    });
}

function displayBoardImages(images) {
    let html = '';
    if (images.length === 0) {
        html = '<tr><td colspan="5" class="text-center">No board images found</td></tr>';
    } else {
        images.forEach(image => {
            const uploadDate = new Date(image.date).toLocaleDateString() + ' ' + new Date(image.date).toLocaleTimeString();
            
            html += `
                <tr>
                    <td>${image.id}</td>
                    <td>
                        <img src="../../${image.img}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;" 
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA4MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjRjVGNUY1Ii8+CjxyZWN0IHg9IjMwIiB5PSIyMCIgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIiBmaWxsPSIjRERERERFIi8+CjwvcmVjdD4KPC9zdmc+'">
                    </td>
                    <td>
                        <span class="font-monospace small">${image.img}</span>
                    </td>
                    <td>${uploadDate}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="deleteBoardImage(${image.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                        <a href="../../${image.img}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
    }
    $('#boardTableBody').html(html);
}

function saveBoardImage() {
    const fileInput = $('#boardImage')[0];
    if (!fileInput.files[0]) {
        Swal.fire("Warning!", "Please select an image file", "warning");
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'uploadBoardImage');
    formData.append('image', fileInput.files[0]);
    
    // Show loading
    Swal.fire({
        title: 'Uploading...',
        text: 'Please wait while the image is being uploaded',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: 'ajax/homeboardAjax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire("Success!", response.message, "success");
                $('#addBoardModal').modal('hide');
                resetForm();
                loadBoardImages();
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        },
        error: function() {
            Swal.close();
            Swal.fire("Error!", "Failed to upload image", "error");
        }
    });
}

function deleteBoardImage(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will delete the board image permanently!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/homeboardAjax.php',
                method: 'POST',
                data: { action: 'deleteBoardImage', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Deleted!", response.message, "success");
                        loadBoardImages();
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                },
                error: function() {
                    Swal.fire("Error!", "Failed to delete image", "error");
                }
            });
        }
    });
}

function resetForm() {
    $('#boardForm')[0].reset();
    $('#imagePreview').hide();
    $('#previewImg').attr('src', '');
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>