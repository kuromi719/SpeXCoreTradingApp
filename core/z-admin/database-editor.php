<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">Universal Database Editor [DEVELOPERS ONLY]</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Z-Admin</a></li>
                        <li class="breadcrumb-item active">Database Editor</li>
                    </ol>
                </div>
            </div>
            <br><br>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                If you don't know about this part, DEFINITELY do not touch anything. (It can be destroy the System)
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tables List -->
        <div class="col-xl-3 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Database Tables</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-search p-3 border-bottom">
                        <input type="text" class="form-control" id="tableSearch" placeholder="Search tables...">
                    </div>
                    <div class="table-list" style="max-height: 600px; overflow-y: auto;">
                        <div class="list-group list-group-flush" id="tablesList">
                            <div class="text-center p-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Data -->
        <div class="col-xl-9 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0" id="currentTableName">Select a table</h4>
                        <small class="text-muted" id="tableInfo">Choose a table from the left to view and edit data</small>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" id="addRecordBtn" disabled>
                            <i class="fas fa-plus me-1"></i>Add Record
                        </button>
                        <button type="button" class="btn btn-info" id="refreshTableBtn" disabled>
                            <i class="fas fa-sync me-1"></i>Refresh
                        </button>
                        <button type="button" class="btn btn-warning" id="exportTableBtn" disabled>
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="tableContainer">
                        <div class="text-center p-5">
                            <i class="fas fa-database fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No table selected</h5>
                            <p class="text-muted">Select a table from the sidebar to start editing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Record Modal -->
<div class="modal fade" id="recordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordModalTitle">Add Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="recordForm">
                    <div id="recordFields">
                        <!-- Fields will be dynamically generated -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveRecordBtn">Save Record</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this record? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Record ID:</strong> <span id="deleteRecordId"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let currentTable = '';
    let currentColumns = [];
    let editingRecordId = null;
    
    loadTables();
    
    // Load tables list
    function loadTables() {
        $.ajax({
            url: 'ajax/database_editor.php',
            method: 'POST',
            data: { action: 'getTables' },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    displayTables(response.tables);
                } else {
                    showAlert('error', 'Error loading tables: ' + response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to load tables');
            }
        });
    }
    
    // Display tables in sidebar
    function displayTables(tables) {
        let html = '';
        tables.forEach(function(table) {
            html += `
                <div class="list-group-item list-group-item-action table-item" data-table="${table.name}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${table.name}</h6>
                            <small class="text-muted">${table.rows} rows</small>
                        </div>
                        <i class="fas fa-table text-primary"></i>
                    </div>
                </div>
            `;
        });
        $('#tablesList').html(html);
    }
    
    // Table search
    $('#tableSearch').on('keyup', function() {
        let searchTerm = $(this).val().toLowerCase();
        $('.table-item').each(function() {
            let tableName = $(this).data('table').toLowerCase();
            if(tableName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Table selection
    $(document).on('click', '.table-item', function() {
        $('.table-item').removeClass('active');
        $(this).addClass('active');
        
        currentTable = $(this).data('table');
        loadTableData(currentTable);
        
        // Enable buttons
        $('#addRecordBtn, #refreshTableBtn, #exportTableBtn').prop('disabled', false);
    });
    
    // Load table data
    function loadTableData(tableName) {
        $('#currentTableName').text(tableName);
        $('#tableInfo').text('Loading table data...');
        
        $.ajax({
            url: 'ajax/database_editor.php',
            method: 'POST',
            data: { 
                action: 'getTableData',
                table: tableName
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    currentColumns = response.columns;
                    displayTableData(response.data, response.columns, tableName);
                    $('#tableInfo').text(`${response.data.length} records found`);
                } else {
                    showAlert('error', 'Error loading table data: ' + response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to load table data');
            }
        });
    }
    
    // Display table data
    function displayTableData(data, columns, tableName) {
        let html = `
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
        `;
        
        // Table headers
        columns.forEach(function(col) {
            let typeIcon = getTypeIcon(col.type);
            html += `
                <th>
                    <div class="d-flex align-items-center">
                        <i class="${typeIcon} me-1"></i>
                        ${col.name}
                        ${col.key === 'PRI' ? '<i class="fas fa-key text-warning ms-1" title="Primary Key"></i>' : ''}
                        ${col.null === 'NO' ? '<i class="fas fa-exclamation-circle text-danger ms-1" title="Required"></i>' : ''}
                    </div>
                    <small class="text-muted">${col.type}</small>
                </th>
            `;
        });
        html += '<th width="120">Actions</th></tr></thead><tbody>';
        
        // Table rows
        if(data.length === 0) {
            html += `
                <tr>
                    <td colspan="${columns.length + 1}" class="text-center p-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <div class="text-muted">No records found</div>
                    </td>
                </tr>
            `;
        } else {
            data.forEach(function(row) {
                html += '<tr>';
                columns.forEach(function(col) {
                    let value = row[col.name];
                    if(value === null) {
                        value = '<span class="text-muted font-style-italic">NULL</span>';
                    } else if(typeof value === 'string' && value.length > 50) {
                        value = value.substring(0, 50) + '...';
                    }
                    html += `<td>${value}</td>`;
                });
                
                // Get primary key value
                let primaryKey = columns.find(col => col.key === 'PRI');
                let pkValue = primaryKey ? row[primaryKey.name] : '';
                
                html += `
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary edit-btn" data-id="${pkValue}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger delete-btn" data-id="${pkValue}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                html += '</tr>';
            });
        }
        
        html += '</tbody></table></div>';
        $('#tableContainer').html(html);
    }
    
    // Get type icon
    function getTypeIcon(type) {
        if(type.includes('int')) return 'fas fa-hashtag';
        if(type.includes('varchar') || type.includes('text')) return 'fas fa-font';
        if(type.includes('decimal') || type.includes('float')) return 'fas fa-calculator';
        if(type.includes('timestamp') || type.includes('date')) return 'fas fa-calendar';
        if(type.includes('enum')) return 'fas fa-list';
        return 'fas fa-database';
    }
    
    // Add record button
    $('#addRecordBtn').click(function() {
        editingRecordId = null;
        $('#recordModalTitle').text(`Add New Record - ${currentTable}`);
        generateRecordForm();
        $('#recordModal').modal('show');
    });
    
    // Edit record button
    $(document).on('click', '.edit-btn', function() {
        editingRecordId = $(this).data('id');
        $('#recordModalTitle').text(`Edit Record - ${currentTable}`);
        loadRecordForEdit(editingRecordId);
        $('#recordModal').modal('show');
    });
    
    // Delete record button
    $(document).on('click', '.delete-btn', function() {
        let recordId = $(this).data('id');
        $('#deleteRecordId').text(recordId);
        $('#confirmDeleteBtn').data('id', recordId);
        $('#deleteModal').modal('show');
    });
    
    // Generate record form
    function generateRecordForm() {
        let html = '<div class="row">';
        
        currentColumns.forEach(function(col, index) {
            if(col.extra === 'auto_increment') return; // Skip auto increment fields
            
            let colClass = index % 2 === 0 ? 'col-md-6' : 'col-md-6';
            let required = col.null === 'NO' ? 'required' : '';
            let readonly = col.key === 'PRI' && editingRecordId ? 'readonly' : '';
            
            html += `
                <div class="${colClass} mb-3">
                    <label class="form-label">
                        ${col.name}
                        ${col.null === 'NO' ? '<span class="text-danger">*</span>' : ''}
                        <small class="text-muted">(${col.type})</small>
                    </label>
            `;
            
            // Generate appropriate input based on type
            if(col.type.includes('text') || col.type.includes('longtext')) {
                html += `<textarea class="form-control" name="${col.name}" id="field_${col.name}" ${required} ${readonly}></textarea>`;
            } else if(col.type.includes('enum')) {
                let enumValues = col.type.match(/enum\(([^)]+)\)/)[1].split(',');
                html += `<select class="form-control" name="${col.name}" id="field_${col.name}" ${required} ${readonly}>`;
                html += `<option value="">Select...</option>`;
                enumValues.forEach(function(val) {
                    let cleanVal = val.replace(/'/g, '');
                    html += `<option value="${cleanVal}">${cleanVal}</option>`;
                });
                html += `</select>`;
            } else if(col.type.includes('timestamp') || col.type.includes('datetime')) {
                html += `<input type="datetime-local" class="form-control" name="${col.name}" id="field_${col.name}" ${required} ${readonly}>`;
            } else if(col.type.includes('date')) {
                html += `<input type="date" class="form-control" name="${col.name}" id="field_${col.name}" ${required} ${readonly}>`;
            } else if(col.type.includes('decimal') || col.type.includes('float')) {
                html += `<input type="number" step="0.01" class="form-control" name="${col.name}" id="field_${col.name}" ${required} ${readonly}>`;
            } else if(col.type.includes('int')) {
                html += `<input type="number" class="form-control" name="${col.name}" id="field_${col.name}" ${required} ${readonly}>`;
            } else {
                html += `<input type="text" class="form-control" name="${col.name}" id="field_${col.name}" ${required} ${readonly}>`;
            }
            
            html += '</div>';
        });
        
        html += '</div>';
        $('#recordFields').html(html);
    }
    
    // Load record for editing
    function loadRecordForEdit(recordId) {
        $.ajax({
            url: 'ajax/database_editor.php',
            method: 'POST',
            data: {
                action: 'getRecord',
                table: currentTable,
                id: recordId
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    generateRecordForm();
                    // Fill form with data
                    Object.keys(response.data).forEach(function(key) {
                        let value = response.data[key];
                        let field = $(`#field_${key}`);
                        
                        if(field.is('select')) {
                            field.val(value);
                        } else if(field.attr('type') === 'datetime-local' && value) {
                            // Convert timestamp to datetime-local format
                            let date = new Date(value);
                            let formatted = date.toISOString().slice(0, 16);
                            field.val(formatted);
                        } else {
                            field.val(value);
                        }
                    });
                } else {
                    showAlert('error', 'Error loading record: ' + response.message);
                }
            }
        });
    }
    
    // Save record
    $('#saveRecordBtn').click(function() {
        let formData = new FormData(document.getElementById('recordForm'));
        formData.append('action', editingRecordId ? 'updateRecord' : 'insertRecord');
        formData.append('table', currentTable);
        if(editingRecordId) {
            formData.append('id', editingRecordId);
        }
        
        $.ajax({
            url: 'ajax/database_editor.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    showAlert('success', editingRecordId ? 'Record updated successfully!' : 'Record added successfully!');
                    $('#recordModal').modal('hide');
                    loadTableData(currentTable);
                } else {
                    showAlert('error', 'Error saving record: ' + response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to save record');
            }
        });
    });
    
    // Confirm delete
    $('#confirmDeleteBtn').click(function() {
        let recordId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/database_editor.php',
            method: 'POST',
            data: {
                action: 'deleteRecord',
                table: currentTable,
                id: recordId
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    showAlert('success', 'Record deleted successfully!');
                    $('#deleteModal').modal('hide');
                    loadTableData(currentTable);
                } else {
                    showAlert('error', 'Error deleting record: ' + response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to delete record');
            }
        });
    });
    
    // Refresh table
    $('#refreshTableBtn').click(function() {
        if(currentTable) {
            loadTableData(currentTable);
            showAlert('info', 'Table refreshed');
        }
    });
    
    // Export table
    $('#exportTableBtn').click(function() {
        if(currentTable) {
            window.open(`ajax/database_editor.php?action=exportTable&table=${currentTable}`, '_blank');
        }
    });
    
    function showAlert(type, message) {
        let iconType = {
            'success': 'success',
            'error': 'error', 
            'info': 'info',
            'warning': 'warning'
        };
        
        Swal.fire({
            title: message,
            icon: iconType[type],
            timer: 3000,
            button: false
        });
    }
});
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>