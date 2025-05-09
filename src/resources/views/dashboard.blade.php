<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Contact Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.12/dist/cropper.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2D6CDF;
            --primary-hover: #1A4FA0;
            --success-color: #10B981;
            --danger-color: #EF4444;
            --warning-color: #F59E0B;
            --text-primary: #1A202C;
            --text-secondary: #64748B;
            --bg-light: #F8FAFC;
            --border-color: #E2E8F0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
            color: var(--text-primary);
        }

        .navbar {
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            height: 72px; /* Fixed height for navbar */
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            color: var(--primary-color);
        }

        .nav-link {
            color: var(--text-secondary);
            font-weight: 500;
            transition: all 0.2s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            color: var(--primary-color);
            background: rgba(45, 108, 223, 0.1);
        }

        .btn {
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .card {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease;
            overflow: hidden; /* Ensure content doesn't overflow */
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-header {
            background: none;
            border-bottom: 1px solid var(--border-color);
            padding: 1.75rem 2rem;
            margin: 0; /* Remove any margin */
        }

        .card-body {
            padding: 2rem;
            margin: 0; /* Remove any margin */
        }

        .card-title {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1.35rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-title i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .badge {
            font-weight: 500;
            padding: 0.625rem 0.875rem;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .badge-primary {
            background: rgba(45, 108, 223, 0.1);
            color: var(--primary-color);
        }

        .table {
            margin: 0;
            width: 100%;
            table-layout: fixed;
        }

        .table th {
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 2px solid var(--border-color);
            padding: 1.25rem 1rem;
            white-space: nowrap;
            font-size: 0.95rem;
        }

        .table td {
            color: var(--text-secondary);
            padding: 1.25rem 1rem;
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .action-buttons {
            white-space: nowrap;
            width: 100px; /* Fixed width for action column */
        }

        .action-buttons .btn {
            padding: 0.625rem;
            width: 36px;
            height: 36px;
            margin: 0 0.25rem;
        }

        .btn-edit {
            background: rgba(45, 108, 223, 0.1);
            color: var(--primary-color);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .btn-edit:hover {
            background: rgba(45, 108, 223, 0.2);
            color: var(--primary-hover);
        }

        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #DC2626;
        }

        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background-color: var(--bg-light);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(45, 108, 223, 0.1);
            background-color: #ffffff;
        }

        .form-control.is-invalid {
            border-color: var(--danger-color);
            background-image: none;
        }

        .form-control.is-valid {
            border-color: var(--success-color);
            background-image: none;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            color: var(--danger-color);
        }

        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .dataTables_wrapper {
            padding: 0;
            margin: 0;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1.5rem;
            padding: 0;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            padding: 0.625rem 1rem;
            font-size: 0.95rem;
            background-color: var(--bg-light);
            width: 300px;
            height: 42px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            padding: 0.625rem 2.5rem 0.625rem 1rem;
            font-size: 0.95rem;
            background-color: var(--bg-light);
            height: 42px;
        }

        .dataTables_wrapper .dataTables_info {
            padding: 1.5rem 0 0 0;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: none;
            padding: 0.625rem 1rem;
            margin: 0 0.25rem;
            border-radius: 8px;
            color: var(--text-secondary) !important;
            font-size: 0.95rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color) !important;
            color: #ffffff !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: rgba(45, 108, 223, 0.1) !important;
            color: var(--primary-color) !important;
            border: none;
        }

        .category-filter {
            margin: 0;
            padding: 0;
        }

        .category-filter select {
            margin: 0;
        }

        .avatar-circle {
            width: 100px;
            height: 100px;
            background: rgba(45, 108, 223, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .avatar-circle:hover {
            transform: scale(1.05);
        }

        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-circle label {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .avatar-circle label:hover {
            background: var(--primary-hover);
        }

        @media (max-width: 1200px) {
            .container {
                max-width: 100%;
                padding: 0 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem 0;
            }

            .container {
                padding: 0 1rem;
            }

            .card-header {
                padding: 1.25rem;
            }

            .card-body {
                padding: 1.25rem;
            }

            .card-header .d-flex.flex-wrap {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.75rem !important;
            }

            .card-header .category-filter {
                width: 100%;
            }

            .card-header .btn,
            .card-header .form-select {
                width: 100%;
                margin-bottom: 0.25rem;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
            }

            .table th,
            .table td {
                padding: 1rem 0.75rem;
            }
        }

        /* Loading Spinner */
        .spinner-border {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }

        /* Tooltip Styles */
        .tooltip {
            font-size: 0.875rem;
        }

        .tooltip-inner {
            background-color: var(--text-primary);
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
        }

        /* Animation for alerts */
        .alert {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Table Row Hover Effect */
        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(45, 108, 223, 0.02);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        /* Custom Scrollbar for Table */
        .dataTables_scrollBody {
            scrollbar-width: thin;
            scrollbar-color: var(--border-color) var(--bg-light);
        }

        .dataTables_scrollBody::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .dataTables_scrollBody::-webkit-scrollbar-track {
            background: var(--bg-light);
        }

        .dataTables_scrollBody::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        .dashboard-container {
            padding: 2rem 0;
            min-height: calc(100vh - 72px); /* Subtract navbar height */
            background: var(--bg-light);
            margin-top: 0; /* Remove any margin */
        }

        .container {
            max-width: 1400px;
            padding: 0 2rem;
            margin: 0 auto; /* Center the container */
        }

        /* Adjust responsive breakpoints */
        @media (max-width: 1200px) {
            .container {
                max-width: 100%;
                padding: 0 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem 0;
            }

            .container {
                padding: 0 1rem;
            }

            .card-header {
                padding: 1.25rem;
            }

            .card-body {
                padding: 1.25rem;
            }
        }

        /* Ensure proper spacing in the header section */
        .card-header .d-flex {
            margin: 0;
            padding: 0;
        }

        .card-header .card-title {
            margin: 0;
            padding: 0;
        }

        /* Adjust category filter spacing */
        .category-filter {
            margin: 0;
            padding: 0;
        }

        .category-filter select {
            margin: 0;
        }

        /* Ensure proper button spacing in header */
        .card-header .btn {
            margin: 0;
        }

        .card-header .btn + .btn {
            margin-left: 0.5rem;
        }

        /* Adjust DataTables elements spacing */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            margin: 0;
            padding: 0;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1.5rem;
        }

        /* Ensure table doesn't cause horizontal scroll */
        .table {
            margin: 0;
            width: 100%;
            table-layout: fixed;
        }

        .table th,
        .table td {
            padding: 1.25rem 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Adjust action buttons to prevent overflow */
        .action-buttons {
            white-space: nowrap;
            width: 100px; /* Fixed width for action column */
        }

        .action-buttons .btn {
            padding: 0.625rem;
            width: 36px;
            height: 36px;
            margin: 0 0.25rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-person-lines-fill"></i>
                Contact Manager
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button type="button" class="btn btn-link nav-link" data-bs-toggle="modal" data-bs-target="#profileModal">
                            <i class="bi bi-person-circle"></i> Profile
                        </button>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
                            <h5 class="card-title">
                                <i class="bi bi-person-lines-fill"></i>
                                Contacts
                                <span class="badge bg-primary ms-2">{{ $contactsCount }}</span>
                            </h5>
                            <div class="d-flex flex-wrap gap-2 align-items-center ms-auto">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recentContactsModal">
                                    <i class="bi bi-clock-history"></i>
                                    <span class="d-none d-md-inline">Recent Contacts</span>
                                </button>
                                <div class="category-filter d-flex align-items-center gap-2 mb-0">
                                    <select id="categoryFilter" class="form-select" style="min-width: 160px;">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                                        <i class="bi bi-gear"></i>
                                        <span class="d-none d-md-inline">Manage Categories</span>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addContactModal">
                                    <i class="bi bi-plus-lg"></i>
                                    <span class="d-none d-md-inline">Add Contact</span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="contactsTable" class="table">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contacts as $contact)
                                    <tr>
                                        <td></td>
                                        <td>{{ $contact->name }}</td>
                                        <td>{{ $contact->phone }}</td>
                                        <td>{{ $contact->email }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $contact->category->name }}</span>
                                        </td>
                                        <td class="action-buttons">
                                            <button class="btn btn-edit edit-contact-btn" data-id="{{ $contact->id }}" title="Edit Contact">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-delete delete-contact-btn" data-id="{{ $contact->id }}" title="Delete Contact">
                                                <i class="bi bi-trash"></i>
                                            </button>
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
    </div>

    <!-- Add Contact Modal -->
    <div class="modal fade" id="addContactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addContactForm" method="POST" action="{{ route('contacts.store') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   maxlength="20" pattern="[A-Za-z0-9\s]+" required
                                   title="Name can only contain letters, numbers, and spaces (max 20 characters)">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   pattern="^(\+63|09)\d{9}$" required
                                   title="Please enter a valid Philippine mobile number starting with 09 or +63">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   maxlength="30" required
                                   title="Please enter a valid email address (max 30 characters)">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-muted" style="font-size:0.9em;">(Optional)</span></label>
                            <select class="form-select select-dropdown" id="category_id" name="category_id">
                                <option value="">No Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Contact</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Contact Modal -->
    <div class="modal fade" id="editContactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editContactForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category_id" class="form-label">Category <span class="text-muted" style="font-size:0.9em;">(Optional)</span></label>
                            <select class="form-control" id="edit_category_id" name="category_id">
                                <option value="">No Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Contact</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Categories Modal -->
    <div class="modal fade" id="manageCategoriesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">All Categories</h6>
                        <button type="button" class="btn btn-primary btn-sm" id="addCategoryBtn">
                            <i class="bi bi-plus"></i> Add Category
                        </button>
                    </div>
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ ucfirst($category->type) }}</td>
                                <td>
                                    <button class="btn btn-edit edit-category-btn" data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-type="{{ $category->type }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-delete delete-category-btn" data-id="{{ $category->id }}">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('partials.modals.add-category')

    <!-- Recent Contacts Modal -->
    <div class="modal fade" id="recentContactsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recent Contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentContacts as $contact)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td>{{ $contact->email }}</td>
                                <td><span class="badge badge-primary">{{ $contact->category->name }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="edit_category_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category_type" class="form-label">Type</label>
                            <select class="form-select" id="edit_category_type" name="type" required>
                                <option value="business">Business</option>
                                <option value="personal">Personal</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-circle me-2"></i>
                        User Profile
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column - Profile Image and Stats -->
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div class="avatar-circle mb-3 position-relative mx-auto" style="width: 150px; height: 150px;">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ asset(Auth::user()->profile_image) }}" 
                                         alt="Profile Image" 
                                         class="rounded-circle"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100" style="background: #EFF6FF;">
                                        <i class="bi bi-person-circle" style="font-size: 4rem; color: #2D6CDF;"></i>
                                    </div>
                                @endif
                                <div class="position-absolute bottom-0 end-0 d-flex gap-1">
                                    <label for="profile_image" class="bg-primary text-white rounded-circle p-2" style="cursor: pointer; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-camera"></i>
                                    </label>
                                    @if(Auth::user()->profile_image)
                                    <button type="button" class="btn-remove-image bg-danger text-white rounded-circle p-2 border-0" style="cursor: pointer; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                            <p class="text-muted mb-3">{{ Auth::user()->email }}</p>
                            
                            <!-- Account Stats -->
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Total Contacts</span>
                                        <span class="badge bg-primary">{{ $contactsCount }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Categories</span>
                                        <span class="badge bg-primary">{{ count($categories) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Member Since</span>
                                        <span class="text-primary">{{ Auth::user()->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Profile Form -->
                        <div class="col-md-8">
                            <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">
                                <input type="hidden" name="remove_image" id="remove_image" value="0">
                                
                                <!-- Personal Information -->
                                <div class="card border-0 bg-light mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="bi bi-person me-2"></i>
                                            Personal Information
                                        </h6>
                                        <div class="mb-3">
                                            <label for="profile_name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="profile_name" name="name" value="{{ Auth::user()->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="profile_email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="profile_email" name="email" value="{{ Auth::user()->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="profile_phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="profile_phone" name="phone" value="{{ Auth::user()->phone }}" pattern="^(\+63|09)\d{9}$">
                                            <small class="form-text text-muted">Enter a valid Philippine mobile number starting with 09 or +63</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Change Password -->
                                <div class="card border-0 bg-light mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="bi bi-key me-2"></i>
                                            Change Password
                                        </h6>
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="current_password" name="current_password">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="new_password" name="new_password">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                            <div class="password-strength mt-2">
                                                <div class="password-strength-bar" id="passwordStrength"></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="bi bi-check2 me-2"></i>
                                        Save Changes
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Cropper Modal -->
    <div class="modal fade" id="cropperModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-crop me-2"></i>
                        Crop Profile Image
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <img id="cropperImage" src="" alt="Image to crop" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-2"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="cropButton">
                        <i class="bi bi-check2 me-2"></i>
                        Crop & Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Contact Confirmation Modal -->
    <div class="modal fade" id="deleteContactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this contact? This action cannot be undone.</p>
                    <p class="text-danger" id="deleteContactError" style="display: none;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteContact">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.12/dist/cropper.min.js"></script>
    <script>
        // Initialize DataTable
        function initializeDataTable() {
            if ($.fn.DataTable.isDataTable('#contactsTable')) {
                $('#contactsTable').DataTable().destroy();
            }
            
            return $('#contactsTable').DataTable({
                language: {
                    search: "",
                    searchPlaceholder: "Search contacts...",
                    info: "Showing _START_ to _END_ of _TOTAL_ pages",
                    infoEmpty: "Showing 0 to 0 of 0 pages",
                    lengthMenu: "Show _MENU_ entries per page",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                columnDefs: [
                    { orderable: false, searchable: false, targets: 0 },
                    { orderable: false, searchable: false, targets: -1 }
                ],
                order: [[1, 'asc']],
                drawCallback: function() {
                    this.api().column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
            });
        }

        // Initialize all event handlers
        function initializeEventHandlers(table) {
            // Category filter functionality
            $('#categoryFilter').on('change', function() {
                var categoryId = $(this).val();
                table.column(4).search(categoryId ? $(this).find('option:selected').text() : '', true, false).draw();
            });

            // Add Contact Form Submit with double submission prevention and validation
            $('#addContactForm').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $submitButton = $form.find('button[type="submit"]');
                
                // Clear previous error messages
                $('.error-message').remove();
                $('.is-invalid').removeClass('is-invalid');
                
                // Validate form
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }
                
                // Disable the submit button
                $submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');
                
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        // Show success message
                        var successAlert = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Contact added successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('.dashboard-container .container').prepend(successAlert);
                        
                        // Reset form and close modal
                        $form[0].reset();
                        $form.removeClass('was-validated');
                        $('#addContactModal').modal('hide');
                        
                        // Reload after a short delay to show the success message
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        // Re-enable the submit button
                        $submitButton.prop('disabled', false).text('Add Contact');
                        
                        if (xhr.status === 422) {
                            const response = xhr.responseJSON;
                            if (response.errors) {
                                // Handle validation errors
                                Object.keys(response.errors).forEach(function(key) {
                                    const input = $form.find(`[name="${key}"]`);
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(response.errors[key][0]);
                                });
                            } else if (response.error) {
                                // Show general error message
                                $form.prepend(`
                                    <div class="alert alert-danger error-message">
                                        ${response.error}
                                    </div>
                                `);
                            }
                        } else {
                            // Show general error message
                            $form.prepend(`
                                <div class="alert alert-danger error-message">
                                    Error adding contact. Please try again.
                                </div>
                            `);
                        }
                    }
                });
            });

            // Add input validation on blur
            $('#addContactForm input').on('blur', function() {
                if (this.checkValidity()) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                }
            });

            // Format phone number as user types
            $('#phone').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length > 0) {
                    if (value.startsWith('09')) {
                        value = value.substring(0, 11);
                    } else if (value.startsWith('63')) {
                        value = '+63' + value.substring(2, 12);
                    } else {
                        value = '09' + value.substring(0, 9);
                    }
                }
                $(this).val(value);
            });

            // Edit Contact Form Submit
            $('#editContactForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editContactModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error updating contact. Please try again.');
                    }
                });
            });

            // Event delegation for edit button
            $('#contactsTable').on('click', '.edit-contact-btn', function() {
                var id = $(this).data('id');
                $.get(`/contacts/${id}/edit`, function(contact) {
                    $('#edit_name').val(contact.name);
                    $('#edit_phone').val(contact.phone);
                    $('#edit_email').val(contact.email);
                    $('#edit_category_id').val(contact.category_id);
                    $('#editContactForm').attr('action', `/contacts/${id}`);
                    $('#editContactModal').modal('show');
                });
            });

            // Event delegation for delete button
            $('#contactsTable').on('click', '.delete-contact-btn', function() {
                const contactId = $(this).data('id');
                $('#deleteContactError').hide();
                $('#confirmDeleteContact').data('id', contactId);
                const deleteContactModal = new bootstrap.Modal(document.getElementById('deleteContactModal'));
                deleteContactModal.show();
            });

            // Handle confirm delete contact button click
            $('#confirmDeleteContact').on('click', function() {
                const contactId = $(this).data('id');
                const button = $(this);
                
                // Disable button and show loading state
                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...');
                
                $.ajax({
                    url: `/contacts/${contactId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        const deleteContactModal = bootstrap.Modal.getInstance(document.getElementById('deleteContactModal'));
                        deleteContactModal.hide();
                        location.reload();
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error deleting contact. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        $('#deleteContactError').text(errorMsg).show();
                        button.prop('disabled', false).text('Delete');
                    }
                });
            });

            // Reset error message and backdrop when modal is hidden
            $('#deleteContactModal').on('hidden.bs.modal', function() {
                $('#deleteContactError').hide();
                $('#confirmDeleteContact').prop('disabled', false).text('Delete');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });

            // Category management handlers
            $('#manageCategoriesModal').on('click', '.edit-category-btn', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var type = $(this).data('type');
                $('#edit_category_name').val(name);
                $('#edit_category_type').val(type);
                $('#editCategoryForm').attr('action', '/categories/' + id);
                $('#editCategoryModal').modal('show');
            });

            $('#editCategoryForm').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        $('#editCategoryModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        let msg = 'Error updating category. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).join('\n');
                        }
                        alert(msg);
                    }
                });
            });

            $('#manageCategoriesModal').on('click', '.delete-category-btn', function() {
                var id = $(this).data('id');
                $('#deleteCategoryError').hide();
                $('#confirmDeleteCategory').data('id', id);
                const deleteCategoryModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
                deleteCategoryModal.show();
            });

            $('#confirmDeleteCategory').on('click', function() {
                const categoryId = $(this).data('id');
                const button = $(this);
                
                // Disable button and show loading state
                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...');
                
                $.ajax({
                    url: `/categories/${categoryId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        const deleteCategoryModal = bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal'));
                        deleteCategoryModal.hide();
                        location.reload();
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error deleting category. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        $('#deleteCategoryError').text(errorMsg).show();
                        button.prop('disabled', false).text('Delete');
                    }
                });
            });

            // Reset error message and backdrop when category delete modal is hidden
            $('#deleteCategoryModal').on('hidden.bs.modal', function() {
                $('#deleteCategoryError').hide();
                $('#confirmDeleteCategory').prop('disabled', false).text('Delete');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });

            // Profile image handlers
            let cropper;
            $('#profile_image').on('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        $('#cropperImage').attr('src', e.target.result);
                        $('#cropperModal').modal('show');
                        
                        if (cropper) {
                            cropper.destroy();
                        }
                        
                        cropper = new Cropper(document.getElementById('cropperImage'), {
                            aspectRatio: 1,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 1,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                        });
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('#cropButton').on('click', function() {
                const canvas = cropper.getCroppedCanvas({
                    width: 300,
                    height: 300
                });
                
                canvas.toBlob(function(blob) {
                    const formData = new FormData();
                    formData.append('profile_image', blob, 'profile.jpg');
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    formData.append('_method', 'PUT');
                    formData.append('name', $('#profile_name').val());
                    formData.append('email', $('#profile_email').val());
                    formData.append('phone', $('#profile_phone').val());
                    
                    $('#cropButton').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...');
                    
                    $.ajax({
                        url: $('#profileForm').attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#cropperModal').modal('hide');
                            if (response.profile_image) {
                                $('.avatar-circle img').attr('src', response.profile_image);
                            }
                            location.reload();
                        },
                        error: function(xhr) {
                            $('#cropButton').prop('disabled', false).text('Crop & Save');
                            let msg = 'Error updating profile image. ';
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                msg += xhr.responseJSON.error;
                            } else if (xhr.status === 413) {
                                msg += 'The image file is too large. Please choose an image smaller than 2MB.';
                            } else if (xhr.status === 422) {
                                if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    msg = Object.values(xhr.responseJSON.errors).join('\n');
                                } else {
                                    msg += 'Invalid image format. Please use JPEG, PNG, JPG, or GIF.';
                                }
                            } else {
                                msg += 'Please try again.';
                            }
                            alert(msg);
                            console.error('Upload error:', xhr.responseText);
                        }
                    });
                }, 'image/jpeg', 0.9);
            });

            $('.btn-remove-image').on('click', function() {
                if (confirm('Are you sure you want to remove your profile image?')) {
                    $('#remove_image').val('1');
                    $('#profileForm').submit();
                }
            });

            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#profileModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        let msg = 'Error updating profile. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).join('\n');
                        }
                        alert(msg);
                    }
                });
            });

            // Add event listener for the cancel buttons
            $('.btn-secondary[data-bs-dismiss="modal"]').on('click', function() {
                const modal = $(this).closest('.modal');
                modal.on('hidden.bs.modal', function() {
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                });
            });
        }

        // Main initialization
        $(function() {
            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable and event handlers
            var table = initializeDataTable();
            initializeEventHandlers(table);

            // Initialize all modals
            const manageCategoriesModal = new bootstrap.Modal(document.getElementById('manageCategoriesModal'));
            const addCategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            const editCategoryModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            const deleteCategoryModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));

            // Handle Add Category button click
            document.getElementById('addCategoryBtn').addEventListener('click', function() {
                manageCategoriesModal.hide();
                addCategoryModal.show();
            });
        });
    </script>
</body>

</html>
