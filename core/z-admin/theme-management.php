<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="page-title">Theme Management</h6>
                <p class="mb-0 text-muted">Manage and customize website themes</p>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary me-2" onclick="openCreateModal()">
                        <i class="fas fa-plus me-1"></i>Create New Theme
                    </button>
                    <button type="button" class="btn btn-success" onclick="openImportModal()">
                        <i class="fas fa-upload me-1"></i>Import Theme
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-palette text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalThemes">0</h5>
                            <p class="mb-0 text-white-50">Total Themes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="activeThemes">0</h5>
                            <p class="mb-0 text-white-50">Active Theme</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-star text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="defaultThemes">0</h5>
                            <p class="mb-0 text-white-50">Default Theme</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="lastUpdated">Never</h5>
                            <p class="mb-0 text-white-50">Last Updated</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Themes List -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title">Available Themes</h4>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="sortThemes" onchange="loadThemes()">
                            <option value="theme_name,ASC">Name (A-Z)</option>
                            <option value="theme_name,DESC">Name (Z-A)</option>
                            <option value="created_at,DESC">Newest First</option>
                            <option value="updated_at,DESC">Recently Updated</option>
                        </select>
                        <button class="btn btn-outline-secondary btn-sm" onclick="loadThemes()">
                            <i class="fas fa-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row" id="themesContainer">
                <!-- Themes will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Theme Editor Modal -->
<div class="modal fade" id="themeEditorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    <span id="modalTitle">Edit Theme</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="themeForm">
                    <input type="hidden" id="themeId" name="id">
                    
                    <div id="formFieldsContainer">
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-warning w-100" onclick="resetToDefaults()">
                                <i class="fas fa-undo me-1"></i>Reset to Defaults
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-info w-100" onclick="duplicateTheme()">
                                <i class="fas fa-copy me-1"></i>Duplicate Theme
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-secondary w-100" onclick="exportTheme()">
                                <i class="fas fa-download me-1"></i>Export Theme
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="saveTheme()">
                    <i class="fas fa-save me-1"></i>Save Theme
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Import Theme Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Import Theme
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="importForm">
                    <div class="mb-3">
                        <label for="importFormat" class="form-label">Import Format</label>
                        <select class="form-select" id="importFormat" name="format">
                            <option value="json">JSON File</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="themeData" class="form-label">Theme Data</label>
                        <textarea class="form-control" id="themeData" name="theme_data" rows="8" 
                                  placeholder="Paste your theme JSON data here..."></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" id="importFile" accept=".json" 
                               onchange="loadFileContent(this)">
                        <div class="form-text">Or select a JSON file to import</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="importTheme()">
                    <i class="fas fa-upload me-1"></i>Import Theme
                </button>
            </div>
        </div>
    </div>
</div>

<style>

.modal-xl .modal-body {
    max-height: 80vh;
    overflow-y: auto;
    padding: 1rem 1.5rem;
}

.field-group {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.field-group h6 {
    color: #495057;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
    margin-bottom: 20px;
    font-weight: 600;
    font-size: 1.1rem;
}

.field-group .row {
    margin-left: 0;
    margin-right: 0;
}

.field-group .row > [class*="col-"] {
    padding-left: 10px;
    padding-right: 10px;
}

.color-input-group input[type="color"] {
    width: 100%;
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    cursor: pointer;
}

.field-group h6 i.fa-info-circle { color: #17a2b8; }
.field-group h6 i.fa-cog { color: #6c757d; }
.field-group h6 i.fa-font { color: #e83e8c; }
.field-group h6 i.fa-moon { color: #6f42c1; }
.field-group h6 i.fa-sun { color: #fd7e14; }
.field-group h6 i.fa-magic { color: #20c997; }

.color-input-group {
    width: 100%;
}

.color-input-group input[type="color"] {
    width: 100%;
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    cursor: pointer;
}

.form-control {
    width: 100%;
}

.mb-3 {
    margin-bottom: 1rem !important;
}

/* Modal body scroll düzeltmesi */
.modal-xl .modal-body {
    max-height: 75vh;
    overflow-y: auto;
    padding: 1rem 1.5rem;
}

.tab-content {
    padding: 0;
}

.tab-pane {
    padding: 0;
}
.theme-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    height: 100%;
}

.theme-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.theme-card.active {
    border-color: #28a745;
    background: linear-gradient(135deg, #28a74508, #ffffff);
}

.theme-card.default {
    border-color: #007bff;
    background: linear-gradient(135deg, #007bff08, #ffffff);
}

.color-swatch {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    display: inline-block;
}
</style>

<script>
// Global variables
let currentThemeId = null;
let fieldGroups = {};
let allThemes = [];

// Initialize the page
$(document).ready(function() {
    loadThemeStats();
    loadThemes();
    loadFieldGroups();
});

// Load theme statistics
function loadThemeStats() {
    $.ajax({
        url: 'ajax/theme-management.php',
        type: 'POST',
        data: {action: 'get_theme_stats'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const stats = response.stats;
                $('#totalThemes').text(stats.total_themes || 0);
                $('#activeThemes').text(stats.active_themes || 0);
                $('#defaultThemes').text(stats.default_themes || 0);
                $('#lastUpdated').text(stats.last_updated_theme || 'Never');
            }
        },
        error: function() {
            console.error('Failed to load theme stats');
        }
    });
}

// Load all themes
function loadThemes() {
    const sortOption = $('#sortThemes').val().split(',');
    const orderBy = sortOption[0];
    const orderDirection = sortOption[1];

    $.ajax({
        url: 'ajax/theme-management.php',
        type: 'POST',
        data: {
            action: 'get_themes',
            order_by: orderBy,
            order_direction: orderDirection
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allThemes = response.themes;
                displayThemes(allThemes);
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Failed to load themes', 'error');
        }
    });
}

// Display themes in cards
function displayThemes(themes) {
    let html = '';
    
    if (themes.length === 0) {
        html = `
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-palette fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No themes found</p>
                    <button class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus me-1"></i>Create Your First Theme
                    </button>
                </div>
            </div>
        `;
    } else {
        themes.forEach(function(theme) {
            const statusClass = theme.is_active == 1 ? 'active' : (theme.is_default == 1 ? 'default' : '');
            const statusBadge = theme.is_active == 1 ? 
                '<span class="badge bg-success theme-status-badge">Active</span>' : 
                (theme.is_default == 1 ? '<span class="badge bg-primary theme-status-badge">Default</span>' : '');

            html += `
                <div class="col-md-6 col-xl-4 mb-3">
                    <div class="card theme-card ${statusClass}">
                        <div class="card-body position-relative">
                            ${statusBadge}
                            
                            <h6 class="card-title mb-2">${theme.theme_name}</h6>
                            <p class="card-text text-muted small mb-3">${theme.theme_description || 'No description available'}</p>
                            
                            <div class="d-flex gap-1 mb-3">
                                <div class="color-swatch" style="background-color: ${theme.dark_brand_color}" title="Brand Color"></div>
                                <div class="color-swatch" style="background-color: ${theme.dark_card_color}" title="Card Color"></div>
                                <div class="color-swatch" style="background-color: ${theme.dark_color_bg}" title="Background"></div>
                                <div class="color-swatch" style="background-color: ${theme.light_brand_color}" title="Light Brand"></div>
                                <div class="color-swatch" style="background-color: ${theme.light_card_color}" title="Light Card"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    ${formatDate(theme.updated_at)}
                                </small>
                                
                                <div class="btn-group btn-group-sm">
                                    ${theme.is_active != 1 ? 
                                        `<button class="btn btn-outline-success" onclick="activateTheme(${theme.id})" title="Activate">
                                            <i class="fas fa-play"></i>
                                        </button>` : ''
                                    }
                                    <button class="btn btn-outline-primary" onclick="editTheme(${theme.id})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    ${theme.is_default != 1 ? 
                                        `<button class="btn btn-outline-danger" onclick="deleteTheme(${theme.id})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>` : ''
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#themesContainer').html(html);
}

// Load field groups for form generation
function loadFieldGroups() {
    $.ajax({
        url: 'ajax/theme-management.php',
        type: 'POST',
        data: {action: 'get_field_groups'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                fieldGroups = response.field_groups;
            }
        }
    });
}

// Open create modal
function openCreateModal() {
    currentThemeId = null;
    resetForm();
    generateFormFields();
    $('#modalTitle').text('Create New Theme');
    $('#themeEditorModal').modal('show');
}

// Edit existing theme
function editTheme(themeId) {
    $.ajax({
        url: 'ajax/theme-management.php',
        type: 'POST',
        data: {action: 'get_theme', id: themeId},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                currentThemeId = themeId;
                const theme = response.theme;
                
                generateFormFields(theme);
                $('#modalTitle').text('Edit Theme: ' + theme.theme_name);
                $('#themeId').val(theme.id);
                $('#themeEditorModal').modal('show');
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        }
    });
}

function generateFormFields(themeData = null) {
    let html = '';
    
    if (fieldGroups.basic) {
        html += generateFieldGroup('basic', fieldGroups.basic, themeData);
    }
    
    if (fieldGroups.root) {
        html += generateFieldGroup('root', fieldGroups.root, themeData);
    }
    
    if (fieldGroups.fonts) {
        html += generateFieldGroup('fonts', fieldGroups.fonts, themeData);
    }
    
    if (fieldGroups.dark) {
        html += generateFieldGroup('dark', fieldGroups.dark, themeData);
    }
    
    if (fieldGroups.light) {
        html += generateFieldGroup('light', fieldGroups.light, themeData);
    }
    
    if (fieldGroups.gsap) {
        html += generateFieldGroup('gsap', fieldGroups.gsap, themeData);
    }
    
    $('#formFieldsContainer').html(html);
}

// Generate field group HTML - DÜZELTILMIŞ VERSİYON
function generateFieldGroup(groupKey, group, themeData) {
    if (!group) return '';
    
    let html = `<div class="field-group">`;
    html += `<h6><i class="fas fa-${getGroupIcon(groupKey)} me-2"></i>${group.title}</h6>`;
    
    html += '<div class="row">';
    
    Object.keys(group.fields).forEach(function(fieldName) {
        const field = group.fields[fieldName];
        const value = themeData ? (themeData[fieldName] || field.default || '') : (field.default || '');
        
        html += '<div class="col-md-6 mb-3">';
        html += `<label for="${fieldName}" class="form-label">${field.label}</label>`;
        
        if (field.type === 'color') {
            html += `
                <div class="color-input-group">
                    <input type="color" class="form-control" id="${fieldName}" name="${fieldName}" value="${value}">
                </div>
            `;
        } else if (field.type === 'textarea') {
            html += `<textarea class="form-control" id="${fieldName}" name="${fieldName}" rows="2" placeholder="${field.label}">${value}</textarea>`;
        } else {
            html += `<input type="${field.type}" class="form-control" id="${fieldName}" name="${fieldName}" value="${value}" placeholder="${field.label}" ${field.required ? 'required' : ''}>`;
        }
        
        html += '</div>';
    });
    
    html += '</div>';
    html += '</div>';
    
    return html;
}

// Get icon for field group
function getGroupIcon(groupKey) {
    const icons = {
        'basic': 'info-circle',
        'root': 'cog',
        'fonts': 'font',
        'dark': 'moon',
        'light': 'sun',
        'gsap': 'magic'
    };
    return icons[groupKey] || 'cog';
}

// Save theme
function saveTheme() {
    const formData = new FormData(document.getElementById('themeForm'));
    const action = currentThemeId ? 'update_theme' : 'create_theme';
    formData.append('action', action);

    // Validate theme name
    const themeName = formData.get('theme_name');
    if (!themeName || themeName.trim() === '') {
        Swal.fire('Error!', 'Theme name is required', 'error');
        return;
    }

    $.ajax({
        url: 'ajax/theme-management.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Success!', response.message, 'success');
                $('#themeEditorModal').modal('hide');
                loadThemes();
                loadThemeStats();
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Failed to save theme', 'error');
        }
    });
}

// Activate theme
function activateTheme(themeId) {
    Swal.fire({
        title: 'Activate Theme?',
        text: 'This will change the active theme across the website',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, activate it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/theme-management.php',
                type: 'POST',
                data: {action: 'activate_theme', id: themeId},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Activated!', 'Theme activated successfully!', 'success');
                        loadThemes();
                        loadThemeStats();
                        
                        // Reload page to apply new theme
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        }
    });
}

// Delete theme
function deleteTheme(themeId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/theme-management.php',
                type: 'POST',
                data: {action: 'delete_theme', id: themeId},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success');
                        loadThemes();
                        loadThemeStats();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        }
    });
}

// Reset to defaults
function resetToDefaults() {
    if (!currentThemeId) return;
    
    Swal.fire({
        title: 'Reset to defaults?',
        text: 'This will reset all theme values to default settings',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reset!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/theme-management.php',
                type: 'POST',
                data: {action: 'reset_theme', id: currentThemeId},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Reset!', response.message, 'success');
                        editTheme(currentThemeId); // Reload form
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        }
    });
}

// Duplicate theme
function duplicateTheme() {
    if (!currentThemeId) return;
    
    const newName = prompt('Enter name for duplicated theme:');
    
    if (!newName || newName.trim() === '') {
        Swal.fire('Error!', 'Theme name is required', 'error');
        return;
    }
    
    $.ajax({
        url: 'ajax/theme-management.php',
        type: 'POST',
        data: {
            action: 'duplicate_theme', 
            id: currentThemeId, 
            new_name: newName.trim()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Duplicated!', response.message, 'success');
                loadThemes();
                loadThemeStats();
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Failed to duplicate theme', 'error');
        }
    });
}

// Export theme
function exportTheme() {
    if (!currentThemeId) return;
    
    Swal.fire({
        title: 'Export Theme',
        html: `
            <div class="mb-3">
                <label for="exportFormat" class="form-label">Export Format:</label>
                <select class="form-select" id="exportFormat">
                    <option value="json">JSON Format</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        preConfirm: () => {
            const format = document.getElementById('exportFormat').value;
            return format;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/theme-management.php',
                type: 'POST',
                data: {
                    action: 'export_theme',
                    id: currentThemeId,
                    format: result.value
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Download file
                        downloadFile(response.data, response.filename, result.value === 'css' ? 'text/css' : 'application/json');
                        Swal.fire('Exported!', 'Theme exported successfully!', 'success');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        }
    });
}

// Open import modal
function openImportModal() {
    $('#importModal').modal('show');
}

// Import theme
function importTheme() {
    const formData = new FormData(document.getElementById('importForm'));
    formData.append('action', 'import_theme');

    const themeData = formData.get('theme_data');
    if (!themeData || themeData.trim() === '') {
        Swal.fire('Error!', 'Please enter theme data or select a file', 'error');
        return;
    }

    $.ajax({
        url: 'ajax/theme-management.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
           if (response.success) {
               Swal.fire('Imported!', response.message, 'success');
               $('#importModal').modal('hide');
               $('#importForm')[0].reset();
               loadThemes();
               loadThemeStats();
           } else {
               Swal.fire('Error!', response.message, 'error');
           }
       },
       error: function() {
           Swal.fire('Error!', 'Failed to import theme', 'error');
       }
   });
}

// Load file content for import
function loadFileContent(input) {
   const file = input.files[0];
   if (file) {
       if (file.type !== 'application/json') {
           Swal.fire('Error!', 'Please select a valid JSON file', 'error');
           input.value = '';
           return;
       }
       
       const reader = new FileReader();
       reader.onload = function(e) {
           $('#themeData').val(e.target.result);
       };
       reader.onerror = function() {
           Swal.fire('Error!', 'Failed to read file', 'error');
       };
       reader.readAsText(file);
   }
}

// Download file helper
function downloadFile(content, filename, mimeType) {
   const blob = new Blob([content], { type: mimeType });
   const url = window.URL.createObjectURL(blob);
   const a = document.createElement('a');
   a.href = url;
   a.download = filename;
   a.style.display = 'none';
   document.body.appendChild(a);
   a.click();
   window.URL.revokeObjectURL(url);
   document.body.removeChild(a);
}

// Reset form
function resetForm() {
   document.getElementById('themeForm').reset();
   $('#themeId').val('');
   currentThemeId = null;
}

// Format date helper
function formatDate(dateString) {
   if (!dateString) return 'Never';
   const date = new Date(dateString);
   return date.toLocaleDateString('tr-TR', {
       year: 'numeric',
       month: 'short',
       day: 'numeric'
   });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>