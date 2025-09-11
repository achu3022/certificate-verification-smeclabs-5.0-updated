<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

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
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        margin-bottom: 10px;
    }
    .stat-card .card-body {
        padding: 1rem 1rem;
    }
    .stat-card h3 {
        font-size: 1.35rem;
        line-height: 1.2;
    }
    .stat-card .icon-wrapper i {
        font-size: 1.1rem;
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
            <a class="nav-link active" href="<?= site_url('admin/dashboard') ?>">
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
            <a class="nav-link" href="<?= site_url('admin/admins') ?>">
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
        <!-- Welcome Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Welcome back, <?= esc($admin['name']) ?></h1>
                <p class="text-muted">Here's what's happening today</p>
            </div>
            <div class="text-end">
                <span class="badge bg-primary"><?= date('d M Y') ?></span>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 row-cols-xxl-5 g-3 mb-4">
            <div class="col">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="icon-wrapper bg-primary bg-gradient">
                            <i class="fas fa-certificate fa-2x text-white"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?= $total_certificates ?></h3>
                        <p class="text-muted mb-0">Total Certificates</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="icon-wrapper bg-success bg-gradient">
                            <i class="fas fa-check-circle fa-2x text-white"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?= $verified_certificates ?></h3>
                        <p class="text-muted mb-0">Verified Certificates</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="icon-wrapper bg-warning bg-gradient">
                            <i class="fas fa-clock fa-2x text-white"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?= $pending_certificates ?></h3>
                        <p class="text-muted mb-0">Pending Certificates</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="icon-wrapper bg-danger bg-gradient">
                            <i class="fas fa-times-circle fa-2x text-white"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?= $rejected_certificates ?></h3>
                        <p class="text-muted mb-0">Rejected Certificates</p>
                    </div>
                </div>
            </div>
            <?php if (session()->get('role') === 'super_admin' && isset($total_admins)): ?>
            <div class="col">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="icon-wrapper bg-info bg-gradient">
                            <i class="fas fa-users fa-2x text-white"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?= $total_admins ?></h3>
                        <p class="text-muted mb-0">Total Admins</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Recent Certificates -->
        <div class="card table-card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-certificate text-primary me-2"></i>Recent Certificates</h5>
                    <a href="<?= site_url('admin/certificates') ?>" class="btn btn-sm btn-primary">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Certificate No</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_certificates)): ?>
                                <?php foreach ($recent_certificates as $cert): ?>
                                <tr>
                                    <td class="px-4"><?= esc($cert['certificate_no']) ?></td>
                                    <td><?= esc($cert['student_name']) ?></td>
                                    <td><?= esc($cert['course']) ?></td>
                                    <td class="text-center">
                                        <span class="status-badge bg-<?= $cert['status'] === 'Verified' ? 'success' : ($cert['status'] === 'Pending' ? 'warning' : 'danger') ?>">
                                            <?= esc($cert['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">No certificates found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
            <span class="badge bg-primary me-2">Version Version <?= config('App')->version ?></span>
            <span class="text-muted">Powered by smeclabs DT</span>
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