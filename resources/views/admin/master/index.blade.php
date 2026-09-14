<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Master Management | Smart Vadodara Connect</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f7fb;
            color: #1f2937;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .topbar {
            background: #111827;
            color: #fff;
            min-height: 68px;
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
        }

        .brand small {
            display: block;
            font-size: 11px;
            color: #9ca3af;
            font-weight: 400;
        }

        .back-btn {
            color: #fff;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,.2);
            padding: 8px 14px;
            border-radius: 8px;
        }

        .back-btn:hover {
            color: #fff;
            background: rgba(255,255,255,.08);
        }

        .page-wrap {
            padding: 25px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 750;
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: #6b7280;
            margin-bottom: 25px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 18px;
            height: 100%;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        }

        .stat-label {
            font-size: 13px;
            color: #6b7280;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 750;
            margin-top: 4px;
        }

        .master-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
            overflow: hidden;
        }

        .master-card-header {
            padding: 17px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .master-card-title {
            font-size: 17px;
            font-weight: 700;
        }

        .master-card-body {
            padding: 20px;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: .03em;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge {
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .action-btn {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 7px;
            padding: 5px 9px;
            font-size: 12px;
            margin-right: 3px;
        }

        .action-btn:hover {
            background: #f3f4f6;
        }

        .danger-btn {
            color: #b91c1c;
        }

        .empty-state {
            padding: 35px;
            text-align: center;
            color: #9ca3af;
        }

        .loading {
            opacity: .55;
            pointer-events: none;
        }

        .nav-tabs .nav-link {
            color: #4b5563;
            font-weight: 600;
        }

        .nav-tabs .nav-link.active {
            color: #111827;
        }

        .required {
            color: #dc2626;
        }

        .modal-header {
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-footer {
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 768px) {
            .page-wrap {
                padding: 15px;
            }

            .master-card-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<header class="topbar d-flex align-items-center">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
        <div class="brand">
            Smart Vadodara Connect
            <small>Admin Master Management</small>
        </div>

        <a href="/admin/dashboard" class="back-btn">
            ← Command Center
        </a>
    </div>
</header>

<main class="page-wrap">

    <div class="container-fluid">

        <div class="page-title">
            Master Management
        </div>

        <div class="page-subtitle">
            Manage departments, civic categories, wards and system users.
        </div>

        <!-- Overview -->
        <div class="row g-3 mb-4">

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Departments</div>
                    <div class="stat-number" id="statDepartments">—</div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Categories</div>
                    <div class="stat-number" id="statCategories">—</div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Wards</div>
                    <div class="stat-number" id="statWards">—</div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Users</div>
                    <div class="stat-number" id="statUsers">—</div>
                </div>
            </div>

        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="masterTabs">

            <li class="nav-item">
                <button
                    class="nav-link active"
                    data-bs-toggle="tab"
                    data-bs-target="#departmentsPane"
                    type="button"
                >
                    Departments
                </button>
            </li>

            <li class="nav-item">
                <button
                    class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#categoriesPane"
                    type="button"
                >
                    Categories
                </button>
            </li>

            <li class="nav-item">
                <button
                    class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#wardsPane"
                    type="button"
                >
                    Wards
                </button>
            </li>

            <li class="nav-item">
                <button
                    class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#usersPane"
                    type="button"
                >
                    Users
                </button>
            </li>

        </ul>

        <div class="tab-content">

            <!-- Departments -->
            <div class="tab-pane fade show active" id="departmentsPane">

                <div class="master-card">

                    <div class="master-card-header">

                        <div>
                            <div class="master-card-title">
                                Departments
                            </div>
                            <small class="text-muted">
                                Civic departments and service ownership
                            </small>
                        </div>

                        <button
                            class="btn btn-dark btn-sm"
                            onclick="openDepartmentModal()"
                        >
                            + Add Department
                        </button>

                    </div>

                    <div class="master-card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="ps-3">ID</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Categories</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="departmentsTable"></tbody>
                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Categories -->
            <div class="tab-pane fade" id="categoriesPane">

                <div class="master-card">

                    <div class="master-card-header">

                        <div>
                            <div class="master-card-title">
                                Civic Categories
                            </div>
                            <small class="text-muted">
                                Complaint classification categories
                            </small>
                        </div>

                        <button
                            class="btn btn-dark btn-sm"
                            onclick="openCategoryModal()"
                        >
                            + Add Category
                        </button>

                    </div>

                    <div class="master-card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-hover">

                                <thead>
                                    <tr>
                                        <th class="ps-3">ID</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Department</th>
                                        <th>Incidents</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="categoriesTable"></tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Wards -->
            <div class="tab-pane fade" id="wardsPane">

                <div class="master-card">

                    <div class="master-card-header">

                        <div>
                            <div class="master-card-title">
                                Wards
                            </div>
                            <small class="text-muted">
                                Municipal ward master
                            </small>
                        </div>

                        <button
                            class="btn btn-dark btn-sm"
                            onclick="openWardModal()"
                        >
                            + Add Ward
                        </button>

                    </div>

                    <div class="master-card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-hover">

                                <thead>
                                    <tr>
                                        <th class="ps-3">Ward</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Users</th>
                                        <th>Incidents</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="wardsTable"></tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Users -->
            <div class="tab-pane fade" id="usersPane">

                <div class="master-card">

                    <div class="master-card-header">

                        <div>
                            <div class="master-card-title">
                                System Users
                            </div>
                            <small class="text-muted">
                                Citizens, Ward Officers and Administrators
                            </small>
                        </div>

                        <button
                            class="btn btn-dark btn-sm"
                            onclick="openUserModal()"
                        >
                            + Add User
                        </button>

                    </div>

                    <div class="master-card-body">

                        <div class="row g-2 mb-3">

                            <div class="col-md-4">
                                <input
                                    type="text"
                                    id="userSearch"
                                    class="form-control form-control-sm"
                                    placeholder="Search name or email..."
                                    oninput="loadUsers()"
                                >
                            </div>

                            <div class="col-md-3">
                                <select
                                    id="userRoleFilter"
                                    class="form-select form-select-sm"
                                    onchange="loadUsers()"
                                >
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="ward_officer">Ward Officer</option>
                                    <option value="citizen">Citizen</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select
                                    id="userStatusFilter"
                                    class="form-select form-select-sm"
                                    onchange="loadUsers()"
                                >
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-hover">

                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Ward</th>
                                        <th>Assignments</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="usersTable"></tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<!-- Generic Modal -->
<div
    class="modal fade"
    id="masterModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="masterModalTitle">
                    Master
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body" id="masterModalBody"></div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    class="btn btn-dark"
                    id="masterModalSave"
                >
                    Save
                </button>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>

@vite('resources/js/admin/master.js')

</body>
</html>
