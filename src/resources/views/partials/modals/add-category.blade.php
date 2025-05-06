<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Manage Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('categories.store') }}" class="mb-4 d-flex flex-wrap gap-3 align-items-end">
                    @csrf
                    <div class="mb-2 flex-grow-1">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" name="name" id="category_name" class="form-control" required maxlength="50">
                    </div>
                    <div class="mb-2">
                        <label for="category_type" class="form-label">Type</label>
                        <select name="type" id="category_type" class="form-select" required>
                            <option value="business">Business</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Add Category</button>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ ucfirst($category->type) }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary edit-category-btn" 
                                        data-id="{{ $category->id }}" 
                                        data-name="{{ $category->name }}" 
                                        data-type="{{ $category->type }}"
                                        data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                                        Edit
                                    </button>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger ms-1">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fill the edit modal with category data
        $(document).on('click', '.edit-category-btn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const type = $(this).data('type');
            $('#edit_category_name').val(name);
            $('#edit_category_type').val(type);
            $('#editCategoryForm').attr('action', `/categories/${id}`);
        });
    });
</script>
