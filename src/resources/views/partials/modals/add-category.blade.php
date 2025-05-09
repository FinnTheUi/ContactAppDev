<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm" method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_type" class="form-label">Type</label>
                        <select class="form-select" id="category_type" name="type" required>
                            <option value="business">Business</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editCategoryForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_category_name" class="form-label">Category Name</label>
                        <input type="text" name="name" id="edit_category_name" class="form-control" required maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label for="edit_category_type" class="form-label">Type</label>
                        <select name="type" id="edit_category_type" class="form-select" required>
                            <option value="business">Business</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Category</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Category Confirmation Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this category? This action cannot be undone.</p>
                <p class="text-danger" id="deleteCategoryError" style="display: none;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCategory">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addCategoryForm = document.getElementById('addCategoryForm');
        
        addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            
            // Disable submit button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const successAlert = document.createElement('div');
                    successAlert.className = 'alert alert-success alert-dismissible fade show';
                    successAlert.innerHTML = `
                        Category added successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.dashboard-container .container').prepend(successAlert);
                    
                    // Reset form and close modal
                    addCategoryForm.reset();
                    const addCategoryModal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                    addCategoryModal.hide();
                    
                    // Reload page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Error adding category');
                }
            })
            .catch(error => {
                // Show error message
                const errorAlert = document.createElement('div');
                errorAlert.className = 'alert alert-danger alert-dismissible fade show';
                errorAlert.innerHTML = `
                    ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                addCategoryForm.prepend(errorAlert);
            })
            .finally(() => {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = 'Add Category';
            });
        });

        let categoryToDelete = null;

        // Initialize Bootstrap modals
        const editCategoryModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        const deleteCategoryModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));

        // Fill the edit modal with category data
        $(document).on('click', '.edit-category-btn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const type = $(this).data('type');
            $('#edit_category_name').val(name);
            $('#edit_category_type').val(type);
            $('#editCategoryForm').attr('action', `/categories/${id}`);
        });

        // Handle delete category button click
        $(document).on('click', '.delete-category-btn', function () {
            categoryToDelete = $(this).data('id');
            $('#deleteCategoryError').hide();
            deleteCategoryModal.show();
        });

        // Handle confirm delete button click
        $('#confirmDeleteCategory').on('click', function () {
            if (!categoryToDelete) return;

            $.ajax({
                url: `/categories/${categoryToDelete}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    deleteCategoryModal.hide();
                    location.reload();
                },
                error: function(xhr) {
                    let errorMsg = 'Error deleting category. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    $('#deleteCategoryError').text(errorMsg).show();
                }
            });
        });

        // Reset categoryToDelete when modal is hidden
        $('#deleteCategoryModal').on('hidden.bs.modal', function () {
            categoryToDelete = null;
            $('#deleteCategoryError').hide();
        });
    });
</script>
