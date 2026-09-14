const TOKEN_KEY = 'smart_vadodara_admin_token';

let departments = [];
let categories = [];
let wards = [];
let users = [];

let editingType = null;
let editingId = null;

let masterModal = null;

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function getToken() {
    return localStorage.getItem(TOKEN_KEY);
}

function escapeHtml(value) {
    if (value === null || value === undefined) {
        return '';
    }

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function showStatus(value) {
    return value
        ? `<span class="status-badge badge-active">Active</span>`
        : `<span class="status-badge badge-inactive">Inactive</span>`;
}

function showError(message) {
    console.error(message);

    alert(message || 'Something went wrong.');
}

function getValidationMessage(errorData) {
    if (!errorData) {
        return 'Request failed.';
    }

    if (errorData.message) {
        return errorData.message;
    }

    if (errorData.errors) {
        const firstKey = Object.keys(errorData.errors)[0];

        if (
            firstKey &&
            Array.isArray(errorData.errors[firstKey]) &&
            errorData.errors[firstKey][0]
        ) {
            return errorData.errors[firstKey][0];
        }
    }

    return 'Request failed.';
}

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/

async function apiRequest(url, options = {}) {
    const token = getToken();

    if (!token) {
        window.location.href = '/admin/login';
        throw new Error('Admin authentication token not found.');
    }

    const headers = {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
        ...(options.headers || {})
    };

    if (options.body && !(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetch(url, {
        ...options,
        headers
    });

    let data = null;

    try {
        data = await response.json();
    } catch (error) {
        data = null;
    }

    if (response.status === 401) {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem('smart_vadodara_admin_user');

        window.location.href = '/admin/login';

        throw new Error('Admin session expired.');
    }

    if (!response.ok) {
        throw new Error(
            getValidationMessage(data) ||
            `Request failed with status ${response.status}.`
        );
    }

    return data;
}

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', async () => {

    const modalElement = document.getElementById('masterModal');

    if (modalElement && window.bootstrap) {
        masterModal = new bootstrap.Modal(modalElement);
    }

    await loadAll();
});

/*
|--------------------------------------------------------------------------
| Load Everything
|--------------------------------------------------------------------------
*/

async function loadAll() {
    try {
        await Promise.all([
            loadOverview(),
            loadDepartments(),
            loadCategories(),
            loadWards(),
            loadUsers()
        ]);
    } catch (error) {
        console.error(error);
        showError(error.message);
    }
}

/*
|--------------------------------------------------------------------------
| Overview
|--------------------------------------------------------------------------
*/

async function loadOverview() {
    const response = await apiRequest('/api/admin/master/overview');

    const data = response.data || {};

    document.getElementById('statDepartments').textContent =
        data.departments ?? 0;

    document.getElementById('statCategories').textContent =
        data.categories ?? 0;

    document.getElementById('statWards').textContent =
        data.wards ?? 0;

    document.getElementById('statUsers').textContent =
        data.users ?? 0;
}

/*
|--------------------------------------------------------------------------
| Departments
|--------------------------------------------------------------------------
*/

async function loadDepartments() {
    const response = await apiRequest('/api/admin/master/departments');

    departments = response.data || [];

    renderDepartments();
    refreshDepartmentSelect();
}

function renderDepartments() {
    const tbody = document.getElementById('departmentsTable');

    if (!tbody) return;

    if (!departments.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        No departments found.
                    </div>
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML = departments.map(department => `
        <tr>
            <td class="ps-3">
                <strong>#${escapeHtml(department.id)}</strong>
            </td>

            <td>
                <strong>${escapeHtml(department.name)}</strong>

                ${
                    department.description
                        ? `<div class="small text-muted">
                            ${escapeHtml(department.description)}
                           </div>`
                        : ''
                }
            </td>

            <td>
                <code>${escapeHtml(department.code)}</code>
            </td>

            <td>
                ${department.categories_count ?? 0}
            </td>

            <td>
                ${showStatus(department.is_active)}
            </td>

            <td class="text-end pe-3">

                <button
                    class="action-btn"
                    onclick="editDepartment(${department.id})"
                >
                    Edit
                </button>

                <button
                    class="action-btn danger-btn"
                    onclick="deleteDepartment(${department.id})"
                >
                    Delete
                </button>

            </td>
        </tr>
    `).join('');
}

function openDepartmentModal(id = null) {
    editingType = 'department';
    editingId = id;

    const department = id
        ? departments.find(item => Number(item.id) === Number(id))
        : null;

    document.getElementById('masterModalTitle').textContent =
        department ? 'Edit Department' : 'Add Department';

    document.getElementById('masterModalBody').innerHTML = `
        <form id="departmentForm">

            <div class="mb-3">
                <label class="form-label">
                    Department Name <span class="required">*</span>
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="departmentName"
                    value="${escapeHtml(department?.name || '')}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Department Code <span class="required">*</span>
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="departmentCode"
                    value="${escapeHtml(department?.code || '')}"
                    placeholder="e.g. ROADS"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Description
                </label>

                <textarea
                    class="form-control"
                    id="departmentDescription"
                    rows="3"
                >${escapeHtml(department?.description || '')}</textarea>
            </div>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="departmentActive"
                    ${department ? (department.is_active ? 'checked' : '') : 'checked'}
                >

                <label class="form-check-label">
                    Active
                </label>
            </div>

        </form>
    `;

    document.getElementById('masterModalSave').onclick =
        saveDepartment;

    masterModal?.show();
}

function editDepartment(id) {
    openDepartmentModal(id);
}

async function saveDepartment() {
    const name = document.getElementById('departmentName').value.trim();
    const code = document.getElementById('departmentCode').value.trim();
    const description =
        document.getElementById('departmentDescription').value.trim();

    const is_active =
        document.getElementById('departmentActive').checked;

    if (!name || !code) {
        showError('Department name and code are required.');
        return;
    }

    const payload = {
        name,
        code,
        description,
        is_active
    };

    try {
        const url = editingId
            ? `/api/admin/master/departments/${editingId}`
            : '/api/admin/master/departments';

        const method = editingId ? 'PUT' : 'POST';

        await apiRequest(url, {
            method,
            body: JSON.stringify(payload)
        });

        masterModal?.hide();

        await Promise.all([
            loadOverview(),
            loadDepartments(),
            loadCategories()
        ]);

        alert(
            editingId
                ? 'Department updated successfully.'
                : 'Department created successfully.'
        );

    } catch (error) {
        showError(error.message);
    }
}

async function deleteDepartment(id) {
    const department = departments.find(
        item => Number(item.id) === Number(id)
    );

    if (!department) return;

    const confirmed = confirm(
        `Delete department "${department.name}"?`
    );

    if (!confirmed) return;

    try {
        await apiRequest(
            `/api/admin/master/departments/${id}`,
            {
                method: 'DELETE'
            }
        );

        await Promise.all([
            loadOverview(),
            loadDepartments(),
            loadCategories()
        ]);

        alert('Department deleted successfully.');

    } catch (error) {
        showError(error.message);
    }
}

/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

async function loadCategories() {
    const response = await apiRequest('/api/admin/master/categories');

    categories = response.data || [];

    renderCategories();
}

function renderCategories() {
    const tbody = document.getElementById('categoriesTable');

    if (!tbody) return;

    if (!categories.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        No categories found.
                    </div>
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML = categories.map(category => `
        <tr>
            <td class="ps-3">
                <strong>#${escapeHtml(category.id)}</strong>
            </td>

            <td>
                <strong>${escapeHtml(category.name)}</strong>

                ${
                    category.description
                        ? `<div class="small text-muted">
                            ${escapeHtml(category.description)}
                           </div>`
                        : ''
                }
            </td>

            <td>
                <code>${escapeHtml(category.slug)}</code>
            </td>

            <td>
                ${
                    category.department
                        ? escapeHtml(category.department.name)
                        : '—'
                }
            </td>

            <td>
                ${category.incidents_count ?? 0}
            </td>

            <td>
                ${showStatus(category.is_active)}
            </td>

            <td class="text-end pe-3">

                <button
                    class="action-btn"
                    onclick="editCategory(${category.id})"
                >
                    Edit
                </button>

                <button
                    class="action-btn danger-btn"
                    onclick="deleteCategory(${category.id})"
                >
                    Delete
                </button>

            </td>
        </tr>
    `).join('');
}

function openCategoryModal(id = null) {
    editingType = 'category';
    editingId = id;

    const category = id
        ? categories.find(item => Number(item.id) === Number(id))
        : null;

    const departmentOptions = departments.map(department => `
        <option
            value="${department.id}"
            ${
                category &&
                Number(category.department_id) === Number(department.id)
                    ? 'selected'
                    : ''
            }
        >
            ${escapeHtml(department.name)}
        </option>
    `).join('');

    document.getElementById('masterModalTitle').textContent =
        category ? 'Edit Category' : 'Add Category';

    document.getElementById('masterModalBody').innerHTML = `
        <form id="categoryForm">

            <div class="mb-3">
                <label class="form-label">
                    Category Name <span class="required">*</span>
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="categoryName"
                    value="${escapeHtml(category?.name || '')}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Slug <span class="required">*</span>
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="categorySlug"
                    value="${escapeHtml(category?.slug || '')}"
                    placeholder="road-pothole"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Department <span class="required">*</span>
                </label>

                <select
                    class="form-select"
                    id="categoryDepartment"
                    required
                >
                    <option value="">Select Department</option>
                    ${departmentOptions}
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Description
                </label>

                <textarea
                    class="form-control"
                    id="categoryDescription"
                    rows="3"
                >${escapeHtml(category?.description || '')}</textarea>
            </div>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="categoryActive"
                    ${category ? (category.is_active ? 'checked' : '') : 'checked'}
                >

                <label class="form-check-label">
                    Active
                </label>
            </div>

        </form>
    `;

    document.getElementById('masterModalSave').onclick =
        saveCategory;

    masterModal?.show();
}

function editCategory(id) {
    openCategoryModal(id);
}

async function saveCategory() {
    const name = document.getElementById('categoryName').value.trim();
    const slug = document.getElementById('categorySlug').value.trim();
    const department_id =
        document.getElementById('categoryDepartment').value;

    const description =
        document.getElementById('categoryDescription').value.trim();

    const is_active =
        document.getElementById('categoryActive').checked;

    if (!name || !slug || !department_id) {
        showError(
            'Category name, slug and department are required.'
        );

        return;
    }

    const payload = {
        name,
        slug,
        department_id: Number(department_id),
        description,
        is_active
    };

    try {
        const url = editingId
            ? `/api/admin/master/categories/${editingId}`
            : '/api/admin/master/categories';

        const method = editingId ? 'PUT' : 'POST';

        await apiRequest(url, {
            method,
            body: JSON.stringify(payload)
        });

        masterModal?.hide();

        await Promise.all([
            loadOverview(),
            loadCategories()
        ]);

        alert(
            editingId
                ? 'Category updated successfully.'
                : 'Category created successfully.'
        );

    } catch (error) {
        showError(error.message);
    }
}

async function deleteCategory(id) {
    const category = categories.find(
        item => Number(item.id) === Number(id)
    );

    if (!category) return;

    const confirmed = confirm(
        `Delete category "${category.name}"?`
    );

    if (!confirmed) return;

    try {
        await apiRequest(
            `/api/admin/master/categories/${id}`,
            {
                method: 'DELETE'
            }
        );

        await Promise.all([
            loadOverview(),
            loadCategories()
        ]);

        alert('Category deleted successfully.');

    } catch (error) {
        showError(error.message);
    }
}

/*
|--------------------------------------------------------------------------
| Wards
|--------------------------------------------------------------------------
*/

async function loadWards() {
    const response = await apiRequest('/api/admin/master/wards');

    wards = response.data || [];

    renderWards();
    refreshWardSelect();
}

function renderWards() {
    const tbody = document.getElementById('wardsTable');

    if (!tbody) return;

    if (!wards.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        No wards found.
                    </div>
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML = wards.map(ward => `
        <tr>
            <td class="ps-3">
                <strong>Ward ${escapeHtml(ward.ward_no)}</strong>
            </td>

            <td>
                <strong>${escapeHtml(ward.name)}</strong>
            </td>

            <td>
                ${
                    ward.address
                        ? escapeHtml(ward.address)
                        : '—'
                }
            </td>

            <td>
                ${ward.users_count ?? 0}
            </td>

            <td>
                ${ward.incidents_count ?? 0}
            </td>

            <td>
                ${showStatus(ward.is_active)}
            </td>

            <td class="text-end pe-3">

                <button
                    class="action-btn"
                    onclick="editWard(${ward.id})"
                >
                    Edit
                </button>

                <button
                    class="action-btn danger-btn"
                    onclick="deleteWard(${ward.id})"
                >
                    Delete
                </button>

            </td>
        </tr>
    `).join('');
}

function openWardModal(id = null) {
    editingType = 'ward';
    editingId = id;

    const ward = id
        ? wards.find(item => Number(item.id) === Number(id))
        : null;

    document.getElementById('masterModalTitle').textContent =
        ward ? 'Edit Ward' : 'Add Ward';

    document.getElementById('masterModalBody').innerHTML = `
        <form id="wardForm">

            <div class="mb-3">
                <label class="form-label">
                    Ward Number <span class="required">*</span>
                </label>

                <input
                    type="number"
                    class="form-control"
                    id="wardNumber"
                    min="1"
                    value="${escapeHtml(ward?.ward_no || '')}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Ward Name <span class="required">*</span>
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="wardName"
                    value="${escapeHtml(ward?.name || '')}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Address
                </label>

                <textarea
                    class="form-control"
                    id="wardAddress"
                    rows="3"
                >${escapeHtml(ward?.address || '')}</textarea>
            </div>

            <div class="alert alert-warning small">
                GIS polygon/GeoJSON geometry is managed separately.
                This master screen does not modify ward geometry.
            </div>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="wardActive"
                    ${ward ? (ward.is_active ? 'checked' : '') : 'checked'}
                >

                <label class="form-check-label">
                    Active
                </label>
            </div>

        </form>
    `;

    document.getElementById('masterModalSave').onclick =
        saveWard;

    masterModal?.show();
}

function editWard(id) {
    openWardModal(id);
}

async function saveWard() {
    const ward_no =
        document.getElementById('wardNumber').value;

    const name =
        document.getElementById('wardName').value.trim();

    const address =
        document.getElementById('wardAddress').value.trim();

    const is_active =
        document.getElementById('wardActive').checked;

    if (!ward_no || !name) {
        showError('Ward number and name are required.');
        return;
    }

    const payload = {
        ward_no: Number(ward_no),
        name,
        address,
        is_active
    };

    try {
        const url = editingId
            ? `/api/admin/master/wards/${editingId}`
            : '/api/admin/master/wards';

        const method = editingId ? 'PUT' : 'POST';

        await apiRequest(url, {
            method,
            body: JSON.stringify(payload)
        });

        masterModal?.hide();

        await Promise.all([
            loadOverview(),
            loadWards(),
            loadUsers()
        ]);

        alert(
            editingId
                ? 'Ward updated successfully.'
                : 'Ward created successfully.'
        );

    } catch (error) {
        showError(error.message);
    }
}

async function deleteWard(id) {
    const ward = wards.find(
        item => Number(item.id) === Number(id)
    );

    if (!ward) return;

    const confirmed = confirm(
        `Delete Ward ${ward.ward_no} - ${ward.name}?`
    );

    if (!confirmed) return;

    try {
        await apiRequest(
            `/api/admin/master/wards/${id}`,
            {
                method: 'DELETE'
            }
        );

        await Promise.all([
            loadOverview(),
            loadWards(),
            loadUsers()
        ]);

        alert('Ward deleted successfully.');

    } catch (error) {
        showError(error.message);
    }
}

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/

async function loadUsers() {
    const search =
        document.getElementById('userSearch')?.value.trim() || '';

    const role =
        document.getElementById('userRoleFilter')?.value || '';

    const status =
        document.getElementById('userStatusFilter')?.value || '';

    const params = new URLSearchParams();

    if (search) {
        params.set('search', search);
    }

    if (role) {
        params.set('role', role);
    }

    if (status !== '') {
        params.set('is_active', status);
    }

    const query = params.toString();

    const url = query
        ? `/api/admin/master/users?${query}`
        : '/api/admin/master/users';

    const response = await apiRequest(url);

    users = response.data || [];

    renderUsers();
}

function renderUsers() {
    const tbody = document.getElementById('usersTable');

    if (!tbody) return;

    if (!users.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        No users found.
                    </div>
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML = users.map(user => {

        let roleLabel = 'Citizen';

        if (user.role === 'admin') {
            roleLabel = 'Admin';
        } else if (user.role === 'ward_officer') {
            roleLabel = 'Ward Officer';
        }

        const wardText = user.ward
            ? `Ward ${escapeHtml(user.ward.ward_no)} - ${escapeHtml(user.ward.name)}`
            : '—';

        return `
            <tr>

                <td>
                    <strong>${escapeHtml(user.name)}</strong>
                </td>

                <td>
                    ${escapeHtml(user.email)}
                </td>

                <td>
                    ${roleLabel}
                </td>

                <td>
                    ${wardText}
                </td>

                <td>
                    ${user.incident_assignments_count ?? 0}
                </td>

                <td>
                    ${showStatus(user.is_active)}
                </td>

                <td class="text-end">

                    <button
                        class="action-btn"
                        onclick="editUser(${user.id})"
                    >
                        Edit
                    </button>

                    <button
                        class="action-btn danger-btn"
                        onclick="deleteUser(${user.id})"
                    >
                        Delete
                    </button>

                </td>

            </tr>
        `;
    }).join('');
}

function openUserModal(id = null) {
    editingType = 'user';
    editingId = id;

    const user = id
        ? users.find(item => Number(item.id) === Number(id))
        : null;

    const wardOptions = wards.map(ward => `
        <option
            value="${ward.id}"
            ${
                user &&
                Number(user.ward_id) === Number(ward.id)
                    ? 'selected'
                    : ''
            }
        >
            Ward ${escapeHtml(ward.ward_no)} - ${escapeHtml(ward.name)}
        </option>
    `).join('');

    document.getElementById('masterModalTitle').textContent =
        user ? 'Edit User' : 'Add User';

    document.getElementById('masterModalBody').innerHTML = `
        <form id="userForm">

            <div class="mb-3">
                <label class="form-label">
                    Name <span class="required">*</span>
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="userName"
                    value="${escapeHtml(user?.name || '')}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Email <span class="required">*</span>
                </label>

                <input
                    type="email"
                    class="form-control"
                    id="userEmail"
                    value="${escapeHtml(user?.email || '')}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    class="form-control"
                    id="userPassword"
                    placeholder="${
                        user
                            ? 'Leave blank to keep current password'
                            : 'Minimum 8 characters'
                    }"
                    ${user ? '' : 'required'}
                >

                ${
                    user
                        ? `<div class="form-text">
                            Leave blank if password should not change.
                           </div>`
                        : ''
                }
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Role <span class="required">*</span>
                </label>

                <select
                    class="form-select"
                    id="userRole"
                    required
                >
                    <option
                        value="citizen"
                        ${user?.role === 'citizen' ? 'selected' : ''}
                    >
                        Citizen
                    </option>

                    <option
                        value="ward_officer"
                        ${user?.role === 'ward_officer' ? 'selected' : ''}
                    >
                        Ward Officer
                    </option>

                    <option
                        value="admin"
                        ${user?.role === 'admin' ? 'selected' : ''}
                    >
                        Admin
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Ward
                </label>

                <select
                    class="form-select"
                    id="userWard"
                >
                    <option value="">
                        No Ward
                    </option>

                    ${wardOptions}
                </select>

                <div class="form-text">
                    Ward is required when role is Ward Officer.
                </div>
            </div>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="userActive"
                    ${user ? (user.is_active ? 'checked' : '') : 'checked'}
                >

                <label class="form-check-label">
                    Active
                </label>
            </div>

        </form>
    `;

    document.getElementById('masterModalSave').onclick =
        saveUser;

    masterModal?.show();
}

function editUser(id) {
    openUserModal(id);
}

async function saveUser() {
    const name =
        document.getElementById('userName').value.trim();

    const email =
        document.getElementById('userEmail').value.trim();

    const password =
        document.getElementById('userPassword').value;

    const role =
        document.getElementById('userRole').value;

    const wardValue =
        document.getElementById('userWard').value;

    const is_active =
        document.getElementById('userActive').checked;

    if (!name || !email || !role) {
        showError('Name, email and role are required.');
        return;
    }

    if (!editingId && !password) {
        showError('Password is required for a new user.');
        return;
    }

    if (role === 'ward_officer' && !wardValue) {
        showError('Ward is required for a Ward Officer.');
        return;
    }

    const payload = {
        name,
        email,
        role,
        ward_id: wardValue ? Number(wardValue) : null,
        is_active
    };

    if (password) {
        payload.password = password;
    }

    try {
        const url = editingId
            ? `/api/admin/master/users/${editingId}`
            : '/api/admin/master/users';

        const method = editingId ? 'PUT' : 'POST';

        await apiRequest(url, {
            method,
            body: JSON.stringify(payload)
        });

        masterModal?.hide();

        await Promise.all([
            loadOverview(),
            loadUsers()
        ]);

        alert(
            editingId
                ? 'User updated successfully.'
                : 'User created successfully.'
        );

    } catch (error) {
        showError(error.message);
    }
}

async function deleteUser(id) {
    const user = users.find(
        item => Number(item.id) === Number(id)
    );

    if (!user) return;

    const confirmed = confirm(
        `Delete user "${user.name}"?`
    );

    if (!confirmed) return;

    try {
        await apiRequest(
            `/api/admin/master/users/${id}`,
            {
                method: 'DELETE'
            }
        );

        await Promise.all([
            loadOverview(),
            loadUsers()
        ]);

        alert('User deleted successfully.');

    } catch (error) {
        showError(error.message);
    }
}

/*
|--------------------------------------------------------------------------
| Select Helpers
|--------------------------------------------------------------------------
*/

function refreshDepartmentSelect() {
    /*
     * Category modal is generated dynamically,
     * therefore nothing is required here.
     */
}

function refreshWardSelect() {
    /*
     * User modal is generated dynamically,
     * therefore nothing is required here.
     */
}

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

window.openDepartmentModal = openDepartmentModal;
window.editDepartment = editDepartment;
window.deleteDepartment = deleteDepartment;

window.openCategoryModal = openCategoryModal;
window.editCategory = editCategory;
window.deleteCategory = deleteCategory;

window.openWardModal = openWardModal;
window.editWard = editWard;
window.deleteWard = deleteWard;

window.openUserModal = openUserModal;
window.editUser = editUser;
window.deleteUser = deleteUser;

window.loadUsers = loadUsers;
