<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Manage Admins<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    body {
        background-color: #f8f9fa;
        overflow-x: hidden;
        padding: 0;
        margin: 0;
        min-height: 100vh;
        position: relative;
    }
    /* Side Panel Styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        background: linear-gradient(135deg, #0c2461 0%, #1e3799 100%);
        color: #fff;
        z-index: 1000;
        padding-top: 80px;
        transition: all 0.3s ease;
    }
    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.85);
        padding: 12px 20px;
        margin: 4px 15px;
        border-radius: 5px;
        transition: all 0.3s;
        font-weight: 500;
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        transform: translateX(5px);
    }
    .sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    .brand-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 80px;
        background: rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 20px;
        z-index: 1001;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .brand-wrapper img {
        max-height: 45px;
        width: auto;
        filter: brightness(1.1);
    }
    /* Main Content Styles */
    .main-content {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 60px;
        left: 250px;
        background-color: #f8f9fa;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .content-wrapper {
        max-width: 1800px;
        margin: 0 auto;
        padding: 25px;
        padding-bottom: 40px;
    }
    /* Card Styles */
    .table-card {
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border: none;
        margin-bottom: 30px;
    }
    .stat-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s, box-shadow 0.3s;
        overflow: hidden;
        background: #fff;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        background: #fff;
    }
    .stat-card .icon-wrapper {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        margin-bottom: 15px;
    }
    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0;
    }
    /* Footer Styles */
    .footer {
        position: fixed;
        bottom: 0;
        left: 250px;
        right: 0;
        height: 60px;
        background: #fff;
        border-top: 1px solid rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        padding: 0 25px;
        z-index: 1000;
    }
    .footer-content {
        max-width: 1800px;
        width: 100%;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    /* Responsive Design */
    @media (max-width: 992px) {
        .sidebar {
            transform: translateX(-100%);
            z-index: 1030;
        }
        .sidebar.show {
            transform: translateX(0);
        }
        .main-content {
            left: 0;
        }
        .footer {
            left: 0;
        }
        .content-wrapper {
            padding: 15px;
        }
        .toggle-sidebar {
            display: block !important;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1040;
        }
    }
    .toggle-sidebar {
        display: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Brand/Logo Section -->
<div class="brand-wrapper">
    <img src="<?= base_url('smec_white.png') ?>" alt="SMEC Logo" class="img-fluid">
</div>

<!-- Sidebar -->
<div class="sidebar">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= site_url('admin/certificates') ?>">
                <i class="fas fa-certificate"></i> Certificates
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= site_url('admin/search-logs') ?>">
                <i class="fas fa-search"></i> Search Logs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= site_url('admin/verifications') ?>">
                <i class="fas fa-building"></i> Verification Requests
            </a>
        </li>
        <?php if (session()->get('role') === 'super_admin'): ?>
        <li class="nav-item">
            <a class="nav-link active" href="<?= site_url('admin/admins') ?>">
                <i class="fas fa-users-cog"></i> Manage Admins
            </a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= site_url('admin/profile') ?>">
                <i class="fas fa-user-circle"></i> Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="<?= site_url('auth/logout') ?>">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<!-- Mobile Toggle Button -->
<button class="btn btn-primary toggle-sidebar">
    <i class="fas fa-bars"></i>
</button>

<!-- Main Content Area -->
<div class="main-content">
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Manage Administrators</h1>
                <p class="text-muted">Add and manage system administrators</p>
            </div>
            <div class="btn-toolbar">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    <i class="fas fa-user-plus me-1"></i> Add New Admin
                </button>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card table-card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="search" class="form-control border-start-0 ps-0" placeholder="Search admins...">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary btn-sm active">All</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">Super Admin</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">Admin</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">Active</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">Inactive</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admins Table -->
        <div class="card table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($admins)): ?>
                                <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <?= strtoupper(substr($admin['name'], 0, 2)) ?>
                                            </div>
                                            <?= esc($admin['name']) ?>
                                        </div>
                                    </td>
                                    <td><?= esc($admin['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $admin['role'] === 'super_admin' ? 'primary' : 'info' ?>">
                                            <?= ucfirst(str_replace('_', ' ', $admin['role'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $admin['active'] ? 'success' : 'danger' ?>">
                                            <?= $admin['active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td><?= $admin['last_login'] ? date('d M Y, H:i', strtotime($admin['last_login'])) : 'Never' ?></td>
                                    <td class="text-end px-4">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">No administrators found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing <?= isset($admins) ? count($admins) : 0 ?> entries
                    </div>
                    <div>
                        <?= isset($pager) ? $pager->links() : '' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="footer-content">
        <div class="text-muted">
            &copy; <?= date('Y') ?> SMEC Certificate Verification System
        </div>
        <div class="d-flex align-items-center">
            <span class="badge bg-primary me-2">Version 1.0</span>
            <span class="text-muted">Powered by smeclabs DT</span>
        </div>
    </div>
</footer>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Administrator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Add Administrator</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Sidebar on Mobile
    const toggleBtn = document.querySelector('.toggle-sidebar');
    const sidebar = document.querySelector('.sidebar');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
});
</script>
<?= $this->endSection() ?>