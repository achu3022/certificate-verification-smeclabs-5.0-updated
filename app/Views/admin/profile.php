<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Profile<?= $this->endSection() ?>

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
        <?php if (session()->get('role') === 'super_admin'): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= site_url('admin/admins') ?>">
                <i class="fas fa-users-cog"></i> Manage Admins
            </a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link active" href="<?= site_url('admin/profile') ?>">
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
                <h1 class="h3 mb-0">Profile Settings</h1>
                <p class="text-muted">Manage your account settings and preferences</p>
            </div>
        </div>

        <div class="row">
            <!-- Profile Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Display validation errors if any -->
                        <?php if (session()->has('profile_errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    <?php foreach (session('profile_errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif ?>

                        <!-- Display success message if any -->
                        <?php if (session()->has('profile_success')): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?= session('profile_success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif ?>

                        <form action="<?= site_url('admin/profile/update-info') ?>" method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="<?= esc($admin['name'] ?? '') ?>" required minlength="3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?= esc($admin['email'] ?? '') ?>" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <div class="form-control bg-light"><?= ucfirst($admin['role'] ?? '') ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Login</label>
                                    <div class="form-control bg-light"><?= isset($admin['last_login']) ? date('d M Y, H:i', strtotime($admin['last_login'])) : 'Never' ?></div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Security Settings</h5>
                    </div>
                    <div class="card-body">
                        <!-- Display password validation errors if any -->
                        <?php if (session()->has('password_errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    <?php foreach (session('password_errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif ?>

                        <!-- Display password success message if any -->
                        <?php if (session()->has('password_success')): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?= session('password_success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif ?>

                        <form action="<?= site_url('admin/profile/update-password') ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="new_password" required minlength="8">
                                    <div class="form-text">Password must be at least 8 characters long</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-1"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Account Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="avatar-upload mb-4">
                            <img src="<?= base_url('profilepic.png') ?>" alt="Profile Picture" class="img-fluid" height="150px" width="150px">
                           
                        </div>
                       
                    </div>
                </div>

                <!-- Account Activity -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php if (!empty($activities)): ?>
                                <?php foreach ($activities as $activity): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <p class="mb-1"><?= esc($activity['description']) ?></p>
                                        <small class="text-muted"><?= time_ago($activity['created_at']) ?></small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="list-group-item text-center py-3">
                                    No recent activity
                                </div>
                            <?php endif; ?>
                        </div>
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
            <span class="badge bg-primary me-2"><?= config('App')->version ?></span>
            <span class="text-muted">Powered by  smeclabs DT</span>
        </div>
    </div>
</footer>
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