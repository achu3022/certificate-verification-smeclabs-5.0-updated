<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Certificates<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.875rem;
    }
    
    .modal {
        z-index: 1060 !important;
    }
    
    .modal-backdrop {
        z-index: 1040 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Error Display -->
    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger"><?= session('error') ?></div>
    <?php endif; ?>
    <?php if (session()->has('success')): ?>
        <div class="alert alert-success"><?= session('success') ?></div>
    <?php endif; ?>
    
    <!-- Alert Container for JavaScript messages -->
    <div id="alertContainer"></div>

<style>
    /* Modern Form Controls */
    .form-control, .form-select {
        border-radius: 0.5rem;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.15);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem 0 0 0.5rem;
    }
    
    /* Search Header */
    .search-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 1000;
        padding: 0.5rem 0;
        margin: 0 -1.25rem 0.5rem;
        border-bottom: 1px solid #e3e6f0;
        box-shadow: 0 0.15rem 0.75rem 0 rgba(58, 59, 69, 0.1);
    }
    
    /* Reduce card padding */
    .card {
        margin-bottom: 0.5rem !important;
    }
    
    /* Card Styling */
    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        transition: all 0.3s ease;
        position: relative;
        z-index: 10;
        margin-top: 2rem;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
    }
    
    /* Button Styling */
    .btn {
        border-radius: 0.5rem;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
        transform: translateY(-1px);
    }
    
    /* Search and Filter Section */
    #searchForm {
        position: relative;
        z-index: 1001;
    }
    
    #searchForm .form-control, 
    #searchForm .form-select {
        height: calc(2.5rem + 2px);
    }
    
    /* Table Card */
    .table-card {
        margin-top: 2rem;
        position: relative;
        z-index: 5;
    }
    
    #searchForm .input-group-merge .form-control:not(:first-child) {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        padding-left: 0.5rem;
    }
    
    /* Advanced Filters */
    #advancedFilters {
        background-color: #f8fafc;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-top: 1rem;
    }
    
    /* Status Badges */
    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 0.35rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    
    .status-badge.Pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-badge.Verified {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-badge.Rejected {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    /* Fix for sticky header */
    .table thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 10;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
        
        .form-control, .form-select {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
    }
    
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
            <a class="nav-link active" href="<?= site_url('admin/certificates') ?>">
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
<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="main-content">
    <div class="content-wrapper">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Certificate Management</h1>
                    <p class="text-muted">Manage and track all certificates</p>
                </div>
                <div class="btn-toolbar">
                    <div class="btn-group me-2">
                        <?php 
                            $currentPage = isset($pager) && $pager ? (int)$pager->getCurrentPage() : 1;
                            $perPageSel = $filters['per_page'] ?? 10;
                            $exportQuery = http_build_query(array_merge($filters, ['page' => $currentPage, 'per_page' => $perPageSel]));
                        ?>
                        <a id="exportBtn" href="<?= site_url('admin/certificates/export?' . $exportQuery) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-export me-1"></i> Export
                        </a>
                        <button type="button" class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#singleAddModal">
                            <i class="fas fa-plus me-1"></i> Add Single
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-import me-1"></i> Import
                        </button>
                    </div>
                </div>
            </div>

                <!-- Search and Filter Card -->
                <div class="search-header">
                    <form id="searchForm" action="<?= current_url() ?>" method="get" class="mb-0">
                        <div class="card mb-0">
                            <div class="card-body p-3">
                                <div class="row g-3 align-items-center">
                                    <!-- Search Input -->
                                    <div class="col-md-4">
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                            <input type="search" 
                                                   name="search" 
                                                   class="form-control form-control-lg border-start-0 ps-0" 
                                                   placeholder="Search certificates..."
                                                   value="<?= esc(service('request')->getGet('search') ?? '') ?>">
                                        </div>
                                    </div>
                                    
                                    <!-- Status Filter -->
                                    <div class="col-md-3">
                                        <select name="status" class="form-select form-select-lg">
                                            <option value="">All Status</option>
                                            <option value="Pending" <?= service('request')->getGet('status') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Verified" <?= service('request')->getGet('status') === 'Verified' ? 'selected' : '' ?>>Verified</option>
                                            <option value="Rejected" <?= service('request')->getGet('status') === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Date Range -->
                                    <div class="col-md-3">
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="far fa-calendar-alt text-muted"></i>
                                            </span>
                                            <input type="date" 
                                                   name="date_from" 
                                                   class="form-control form-select-lg border-start-0 ps-0" 
                                                   placeholder="From"
                                                   value="<?= esc(service('request')->getGet('date_from') ?? '') ?>">
                                            <span class="input-group-text bg-white border-0">to</span>
                                            <input type="date" 
                                                   name="date_to" 
                                                   class="form-control form-select-lg border-start-0 ps-0" 
                                                   placeholder="To"
                                                   value="<?= esc(service('request')->getGet('date_to') ?? '') ?>">
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="col-md-2 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                            <i class="fas fa-filter me-2"></i> Filter
                                        </button>
                                        <a href="<?= current_url() ?>" class="btn btn-outline-secondary btn-lg">
                                            <i class="fas fa-sync-alt"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Advanced Filters Toggle -->
                                <div class="mt-3">
                                    <a href="#advancedFilters" class="text-decoration-none small" data-bs-toggle="collapse">
                                        <i class="fas fa-sliders-h me-1"></i> Advanced Filters
                                    </a>
                                    
                                    <!-- Advanced Filters -->
                                    <div id="advancedFilters" class="collapse mt-3">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted mb-1">Certificate Number</label>
                                                <input type="text"
                                                       name="certificate_no"
                                                       class="form-control"
                                                       placeholder="Enter certificate number"
                                                       value="<?= esc(service('request')->getGet('certificate_no') ?? '') ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted mb-1">Admission Number</label>
                                                <input type="text"
                                                       name="admission_no"
                                                       class="form-control"
                                                       placeholder="Enter admission number"
                                                       value="<?= esc(service('request')->getGet('admission_no') ?? '') ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted mb-1">Course</label>
                                                <input type="text"
                                                       name="course"
                                                       class="form-control"
                                                       placeholder="Enter course name"
                                                       value="<?= esc(service('request')->getGet('course') ?? '') ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted mb-1">Certificates per page</label>
                                                <select name="per_page" class="form-select" onchange="$('#searchForm').submit();">
                                                    <?php $pp = service('request')->getGet('per_page') ?? 10; ?>
                                                    <option value="10" <?= $pp == 10 ? 'selected' : '' ?>>10</option>
                                                    <option value="50" <?= $pp == 50 ? 'selected' : '' ?>>50</option>
                                                    <option value="100" <?= $pp == 100 ? 'selected' : '' ?>>100</option>
                                                    <option value="500" <?= $pp == 500 ? 'selected' : '' ?>>500</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Certificates Table -->
            <div class="card table-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3" style="width: 60px;">No.</th>
                                    <th class="px-4">Certificate No</th>
                                    <th>Admission No</th>
                                    <th>Student Name</th>
                                    <th>Course</th>
                                    <th>Issue Date</th>
                                    <th>Status</th>
                                    <th class="text-end px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $counter = 1;
                                if (isset($pager) && $pager->getCurrentPage() > 1) {
                                    $counter = (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1;
                                }
                                foreach ($certificates as $cert): ?>
                                <tr>
                                    <td class="px-3 text-muted"><?= $counter++ ?></td>
                                    <td class="px-4"><?= esc($cert['certificate_no']) ?></td>
                                    <td class="text-muted"><?= esc($cert['admission_no']) ?></td>
                                    <td><?= esc($cert['student_name']) ?></td>
                                    <td><?= esc($cert['course']) ?></td>
                                    <td><?= date('d M Y', strtotime($cert['date_of_issue'])) ?></td>
                                    <td>
                                        <span class="status-badge bg-<?= $cert['status'] === 'Verified' ? 'success' : ($cert['status'] === 'Pending' ? 'warning' : 'danger') ?>">
                                            <?= esc($cert['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="btn-group">
                                            <?php if (session('role') === 'super_admin'): ?>
                                                <?php if ($cert['status'] === 'Pending'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-success approve-certificate" 
                                                            data-id="<?= $cert['id'] ?>">
                                                        Approve
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger reject-certificate" 
                                                            data-id="<?= $cert['id'] ?>">
                                                        Reject
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary view-certificate me-1" 
                                                        data-bs-toggle="modal" data-bs-target="#viewModal"
                                                        data-id="<?= $cert['id'] ?>">
                                                    View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-certificate" 
                                                        data-bs-toggle="modal" data-bs-target="#editModal"
                                                        data-id="<?= $cert['id'] ?>">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-certificate" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-id="<?= $cert['id'] ?>">
                                                    Delete
                                                </button>
                                            <?php elseif (session('role') === 'admin'): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary view-certificate me-1" 
                                                        data-bs-toggle="modal" data-bs-target="#viewModal"
                                                        data-id="<?= $cert['id'] ?>">
                                                    View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-certificate" 
                                                        data-bs-toggle="modal" data-bs-target="#editModal"
                                                        data-id="<?= $cert['id'] ?>">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-certificate" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-id="<?= $cert['id'] ?>">
                                                    Delete
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer border-0 bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing <?= count($certificates) ?> entries
                        </div>
                        <div>
                            <?= $pager->links() ?>
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
                <span class="badge bg-primary me-2">Version <?= config('App')->version ?></span>
                <span class="text-muted">Powered by  smeclabs DT</span>
            </div>
        </div>
    </footer>
</div>

<!-- Add Single Certificate Modal -->
<div class="modal fade" id="singleAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>
                    Add New Certificate
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="singleCertificateForm" action="<?= site_url('admin/certificate/create-single') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div id="singleAddAlert" class="d-none"></div>
                    <div class="mb-3">
                        <label for="certificate_no" class="form-label">Certificate Number *</label>
                        <input type="text" class="form-control" id="certificate_no" name="certificate_no" required>
                        <div class="invalid-feedback">Please enter a certificate number.</div>
                    </div>
                    <div class="mb-3">
                        <label for="admission_no" class="form-label">Admission Number *</label>
                        <input type="text" class="form-control" id="admission_no" name="admission_no" required>
                        <div class="invalid-feedback">Please enter an admission number.</div>
                    </div>
                    <div class="mb-3">
                        <label for="course" class="form-label">Course *</label>
                        <input type="text" class="form-control" id="course" name="course" required>
                        <div class="invalid-feedback">Please enter the course name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="student_name" class="form-label">Student Name *</label>
                        <input type="text" class="form-control" id="student_name" name="student_name" required>
                        <div class="invalid-feedback">Please enter the student's name.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                                <div class="invalid-feedback">Please select the start date.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                                <div class="invalid-feedback">Please select the end date.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_of_issue" class="form-label">Date of Issue *</label>
                                <input type="date" class="form-control" id="date_of_issue" name="date_of_issue" required>
                                <div class="invalid-feedback">Please select the date of issue.</div>
                            </div>
                        </div>
                    </div>
                    <?php if (session()->get('role') === 'super_admin'): ?>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="Verified">Verified</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Save Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve Confirmation Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this certificate?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmApprove">Approve</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reject this certificate?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this certificate? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- View Certificate Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Certificate Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-2"><strong>Certificate No:</strong> <span id="view_certificate_no">—</span></div>
                        <div class="mb-2"><strong>Admission No:</strong> <span id="view_admission_no">—</span></div>
                        <div class="mb-2"><strong>Student Name:</strong> <span id="view_student_name">—</span></div>
                        <div class="mb-2"><strong>Course:</strong> <span id="view_course">—</span></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2"><strong>Start Date:</strong> <span id="view_start_date">—</span></div>
                        <div class="mb-2"><strong>End Date:</strong> <span id="view_end_date">—</span></div>
                        <div class="mb-2"><strong>Date of Issue:</strong> <span id="view_date_of_issue">—</span></div>
                        <div class="mb-2"><strong>Status:</strong> <span id="view_status" class="badge bg-secondary">—</span></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    
</div>

<!-- Edit Certificate Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" action="<?= base_url('admin/certificate/update') ?>" method="POST" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Certificate Number</label>
                        <input type="text" class="form-control" id="editCertificateNo" name="certificate_no" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admission Number</label>
                        <input type="text" class="form-control" id="editAdmissionNo" name="admission_no" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student Name</label>
                        <input type="text" class="form-control" id="editStudentName" name="student_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <input type="text" class="form-control" id="editCourse" name="course" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Issue</label>
                        <input type="date" class="form-control" id="editDateOfIssue" name="date_of_issue" required>
                    </div>
                    <?php if (session('role') === 'super_admin'): ?>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="editStatus" name="status">
                            <option value="Pending">Pending</option>
                            <option value="Verified">Verified</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="importModalLabel">Import Certificates</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Please upload an Excel file with the correct format. 
                    <a href="<?= base_url('sample_certificate_import.xlsx') ?>" class="alert-link">Download sample file</a>.
                </div>
                
                <div id="importAlert" class="d-none"></div>
                
                <?= form_open_multipart(base_url('admin/certificate/import'), ['id' => 'importForm']) ?>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Select Excel File</label>
                        <input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xlsx, .xls, .csv" required>
                        <div class="form-text">Supported formats: .xlsx, .xls, .csv</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="skipHeader" name="skip_header" checked>
                            <label class="form-check-label" for="skipHeader">
                                Skip first row (header)
                            </label>
                        </div>
                    </div>
                    
                    <div class="progress mb-3 d-none" id="importProgress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="importSubmit">
                            <i class="fas fa-upload me-1"></i> Import
                        </button>
                    </div>
                <?= form_close() ?>
                
                <div class="mt-4">
                    <h6>File Format:</h6>
                    <p>Your Excel file should have the following columns in order:</p>
                    <ol class="mb-3">
                        <li>Certificate No (Required)</li>
                        <li>Admission No (Required)</li>
                        <li>Course</li>
                        <li>Student Name (Required)</li>
                        <li>Start Date (YYYY-MM-DD)</li>
                        <li>End Date (YYYY-MM-DD)</li>
                        <li>Date of Issue (YYYY-MM-DD)</li>
                        <li>Status (Pending/Verified/Rejected)</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize Select2 for better select elements
    $('select[multiple]').select2({
        placeholder: 'Select options',
        width: '100%'
    });
    
    // Function to update results
    function updateResults() {
        const formData = $('#searchForm').serialize();
        const url = $('#searchForm').attr('action') + '?' + formData;

        // Show loading state
        const submitBtn = $('#searchForm').find('button[type="submit"]');
        // Store original HTML once to avoid capturing the spinner as original
        if (!submitBtn.data('originalHtml')) {
            submitBtn.data('originalHtml', submitBtn.html());
        }
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Filtering...');

        // Show loading indicator
        const loadingIndicator = $('<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading results...</p></div>');
        $('table tbody').html(loadingIndicator);

        // Submit form via AJAX
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // Update the table with the filtered results
                    $('table tbody').html(response.table_body || '<tr><td colspan="8" class="text-center">No results found</td></tr>');

                    // Update pagination if it exists
                    if (response.pagination) {
                        $('.card-footer .d-flex .d-flex').html('<div>' + response.pagination + '</div>');
                        // Also update the main pagination container
                        if ($('.pagination').length) {
                            $('.pagination').html(response.pagination);
                        }
                    }

                    // Update result count if it exists
                    if (response.result_count) {
                        $('.card-footer .text-muted').text(response.result_count);
                    }

                    // Update URL without page reload
                    window.history.pushState({}, '', url);
                } else {
                    showAlert('error', response.message || 'An error occurred while filtering results.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                showAlert('error', 'An error occurred while filtering results. Please try again.');
            },
            complete: function() {
                // Reset button state
                const restoreHtml = submitBtn.data('originalHtml') || 'Filter';
                submitBtn.prop('disabled', false).html(restoreHtml);
                submitBtn.removeData('originalHtml');
            }
        });
    }
    
    // Show alert message
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertContainer').html(alertHtml);
    }
    
    // Status filter
    $('select[name="status"]').on('change', function() {
        updateResults();
    });
    
    // Date range filters
    $('input[name="date_from"], input[name="date_to"]').on('change', function() {
        updateResults();
    });
    
    // Certificate number filter
    $('input[name="certificate_no"]').on('keyup', function() {
        clearTimeout($(this).data('timer'));
        $(this).data('timer', setTimeout(() => {
            if ($(this).val().length === 0 || $(this).val().length >= 2) {
                updateResults();
            }
        }, 500));
    });
    
    // Admission number filter
    $('input[name="admission_no"]').on('keyup', function() {
        clearTimeout($(this).data('timer'));
        $(this).data('timer', setTimeout(() => {
            if ($(this).val().length === 0 || $(this).val().length >= 2) {
                updateResults();
            }
        }, 500));
    });
    
    // Course filter
    $('input[name="course"]').on('keyup', function() {
        clearTimeout($(this).data('timer'));
        $(this).data('timer', setTimeout(() => {
            if ($(this).val().length === 0 || $(this).val().length >= 2) {
                updateResults();
            }
        }, 500));
    });
    
    // Main search input with debounce
    let searchTimer;
    $('input[name="search"]').on('keyup', function() {
        clearTimeout(searchTimer);
        const $this = $(this);
        searchTimer = setTimeout(function() {
            if ($this.val().length === 0 || $this.val().length >= 2) {
                updateResults();
            }
        }, 500);
    });
    
    // Form submission handler (single binding)
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        updateResults();
    });
    
    // Reset all filters
    $('.reset-filters').on('click', function() {
        $('#searchForm')[0].reset();
        $('select').trigger('change');
        $('#searchForm').submit();
    });

    // Export functionality
    $('#exportBtn').on('click', function(e) {
        e.preventDefault();
        const formData = $('#searchForm').serialize();
        const params = new URLSearchParams(formData);
        // Ensure per_page reflects the current selector value explicitly
        const perPageVal = $('select[name="per_page"]').val();
        if (perPageVal) {
            params.set('per_page', perPageVal);
        }
        // Preserve current page from URL if present so export matches the visible page
        const currentPage = new URLSearchParams(window.location.search).get('page');
        if (currentPage) {
            params.set('page', currentPage);
        }
        const exportUrl = '<?= site_url('admin/certificates/export') ?>?' + params.toString();


        // Show loading state
        const originalBtnText = $(this).html();
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Exporting...');

        // Use window.open for better compatibility with downloads
        const newWindow = window.open(exportUrl, '_blank');

        // If popup blocker prevents opening, fallback to current window
        if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
            window.location.href = exportUrl;
        }

        // Reset button state after a delay
        setTimeout(() => {
            $(this).prop('disabled', false).html(originalBtnText);
            if (newWindow && !newWindow.closed) {
                newWindow.close();
            }
        }, 3000);
    });
    
    // Auto-submit form when filters change
    $('select, input[type="date"]').on('change', function() {
        $('#searchForm').submit();
    });
    
    
    // Handle file input change
    $('#excel_file').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.form-text').before(`<div class="mt-1 small">Selected: ${fileName}</div>`);
    });
    
    // Handle form submission
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submission started');
        
        const form = $(this);
        const formData = new FormData(this);
        const importUrl = form.attr('action');
        const progressBar = $('#importProgress');
        const submitBtn = form.find('button[type="submit"]');
        const importAlert = $('#importAlert');
        
        // Show loading state
        importAlert.addClass('d-none').removeClass('alert-success alert-danger').empty();
        progressBar.removeClass('d-none');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Importing...');
        
        console.log('Sending AJAX request to:', importUrl);
        
        // Create a new XHR object for better control
        const xhr = new XMLHttpRequest();
        
        // Set up the request
        xhr.open('POST', importUrl, true);
        
        // Set request headers - must be after open() and before send()
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        
        // Get CSRF token from meta tag
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
        
        // Log the token for debugging
        console.log('CSRF Token:', csrfToken ? 'Found' : 'Not found');
        
        if (csrfToken) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            formData.set('<?= csrf_token() ?>', csrfToken);
        } else {
            console.warn('CSRF token not found in meta tags');
        }
        
        // Upload progress
        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.find('.progress-bar').css('width', percentComplete + '%')
                    .attr('aria-valuenow', percentComplete)
                    .text(percentComplete.toFixed(1) + '%');
            }
        };
        
        // Handle request completion
        xhr.onload = function() {
            progressBar.addClass('d-none');
            submitBtn.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Import');
            
            try {
                const response = JSON.parse(xhr.responseText);
                console.log('Import response:', response);
                
                if (response.success) {
                    // Show success message in alert
                    importAlert.removeClass('d-none alert-danger').addClass('alert-success')
                        .html(`<i class="fas fa-check-circle me-2"></i>${response.message}`);
                    
                    // Close the modal
                    $('#importModal').modal('hide');
                    
                    // Reload the certificates table after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    let errorMessage = response.message || 'An error occurred during import.';
                    if (response.errors && response.errors.length > 0) {
                        errorMessage += '<ul class="mb-0 mt-2">';
                        response.errors.forEach(error => {
                            errorMessage += `<li>${error}</li>`;
                        });
                        errorMessage += '</ul>';
                    }
                    importAlert.removeClass('d-none alert-success').addClass('alert-danger')
                        .html(`<i class="fas fa-exclamation-circle me-2"></i>${errorMessage}`);
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                importAlert.removeClass('d-none alert-success').addClass('alert-danger')
                    .html('<i class="fas fa-exclamation-circle me-2"></i>Error processing server response');
            }
            
            // Scroll to alert
            $('html, body').animate({
                scrollTop: importAlert.offset().top - 20
            }, 500);
        };
        
        // Handle request error
        xhr.onerror = function() {
            console.error('Request failed');
            progressBar.addClass('d-none');
            submitBtn.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Import');
            
            importAlert.removeClass('d-none alert-success').addClass('alert-danger')
                .html('<i class="fas fa-exclamation-circle me-2"></i>Request failed. Please check your connection and try again.');
                
            // Scroll to alert
            $('html, body').animate({
                scrollTop: importAlert.offset().top - 20
            }, 500);
        };
        
        // Send the request
        xhr.send(formData);
        
        // Prevent default form submission
        return false;
        });
    
    // Function to show alert message
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertContainer').html(alertHtml);
    }

    // Add Certificate Form Submission
    $(document).on('submit', '#singleCertificateForm', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        const addAlert = $('#singleAddAlert');
        const importUrl = form.attr('action');
        
        // Show loading state
        addAlert.addClass('d-none').removeClass('alert-success alert-danger').empty();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...');
        
        // Clear previous validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Client-side validation
        let hasError = false;
        form.find('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                hasError = true;
            }
        });
        
        if (hasError) {
            addAlert.removeClass('d-none').addClass('alert-danger')
                .html('<i class="fas fa-exclamation-circle me-2"></i>Please fill in all required fields.');
            submitBtn.prop('disabled', false).html(originalBtnText);
            return false;
        }
        
        // Create FormData
        const formData = new FormData(this);
        
        // AJAX submission
        $.ajax({
            url: importUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    addAlert.removeClass('d-none').addClass('alert-success')
                        .html('<i class="fas fa-check-circle me-2"></i>' + (response.message || 'Certificate added successfully'));
                    form[0].reset();
                    $('#singleAddModal').modal('hide');
                    
                    // Reload the page after delay to show updated list
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    let errorMessage = response.message || 'Failed to add certificate.';
                    if (response.errors && Object.keys(response.errors).length > 0) {
                        errorMessage += '<ul class="mb-0 mt-2">';
                        for (let field in response.errors) {
                            errorMessage += '<li>' + response.errors[field] + '</li>';
                        }
                        errorMessage += '</ul>';
                        
                        // Show field-specific errors
                        for (let field in response.errors) {
                            const input = form.find('[name="' + field + '"]');
                            if (input.length) {
                                input.addClass('is-invalid');
                                input.after('<div class="invalid-feedback">' + response.errors[field] + '</div>');
                            }
                        }
                    }
                    addAlert.removeClass('d-none').addClass('alert-danger')
                        .html('<i class="fas fa-exclamation-circle me-2"></i>' + errorMessage);
                    
                    // Update CSRF token if provided
                    if (response.csrf_hash) {
                        $('meta[name="csrf-token"]').attr('content', response.csrf_hash);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Submission error:', error, xhr.responseText);
                let errorMessage = 'An error occurred while saving the certificate.';
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (resp.message) {
                        errorMessage = resp.message;
                        if (resp.errors) {
                            errorMessage += '<ul class="mb-0 mt-2">';
                            for (let key in resp.errors) {
                                errorMessage += '<li>' + resp.errors[key] + '</li>';
                            }
                            errorMessage += '</ul>';
                            
                            // Show field-specific errors
                            for (let field in resp.errors) {
                                const input = form.find('[name="' + field + '"]');
                                if (input.length) {
                                    input.addClass('is-invalid');
                                    input.after('<div class="invalid-feedback">' + resp.errors[field] + '</div>');
                                }
                            }
                        }
                    }
                } catch (e) {
                    // Handle non-JSON errors
                }
                addAlert.removeClass('d-none').addClass('alert-danger')
                    .html('<i class="fas fa-exclamation-circle me-2"></i>' + errorMessage);
                
                // Update CSRF token if provided
                if (xhr.responseJSON && xhr.responseJSON.csrf_hash) {
                    $('meta[name="csrf-token"]').attr('content', xhr.responseJSON.csrf_hash);
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
    
    
    // Reset form when modal is closed
    $('#singleAddModal').on('hidden.bs.modal', function() {
        $('#singleCertificateForm')[0].reset();
        // Remove any validation classes
        $('#singleCertificateForm .is-invalid').removeClass('is-invalid');
        $('#singleCertificateForm .invalid-feedback').remove();
    });
    
    // Reset form when modal is closed
    $('#importModal').on('hidden.bs.modal', function () {
        $('#importForm')[0].reset();
        $('#importAlert').addClass('d-none').empty();
        $('.progress-bar').css('width', '0%').attr('aria-valuenow', '0');
    });

    // Store the current certificate ID for modal actions
    let currentCertificateId = null;
    
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Handle approve button click
    $(document).on('click', '.approve-certificate', function(e) {
        e.preventDefault();
        currentCertificateId = $(this).data('id');
        $('#approveModal').modal('show');
    });
    
    // Handle reject button click
    $(document).on('click', '.reject-certificate', function(e) {
        e.preventDefault();
        currentCertificateId = $(this).data('id');
        $('#rejectModal').modal('show');
    });
    
    // Handle edit button click
    $(document).on('click', '.edit-certificate', function(e) {
        e.preventDefault();
        currentCertificateId = $(this).data('id');
        console.log('Loading certificate data for ID:', currentCertificateId);
        loadCertificateData(currentCertificateId);
    });
    
    // Handle view button click
    $(document).on('click', '.view-certificate', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        // Fetch details
        $.ajax({
            url: '<?= base_url('admin/certificate/get/') ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const c = response.data;
                    $('#view_certificate_no').text(c.certificate_no || '—');
                    $('#view_admission_no').text(c.admission_no || '—');
                    $('#view_student_name').text(c.student_name || '—');
                    $('#view_course').text(c.course || '—');
                    $('#view_start_date').text(c.start_date ? formatDate(c.start_date) : '—');
                    $('#view_end_date').text(c.end_date ? formatDate(c.end_date) : '—');
                    $('#view_date_of_issue').text(c.date_of_issue ? formatDate(c.date_of_issue) : '—');
                    const status = (c.status || '—');
                    const badgeClass = status === 'Verified' ? 'bg-success' : (status === 'Pending' ? 'bg-warning text-dark' : 'bg-danger');
                    $('#view_status').removeClass('bg-success bg-warning bg-danger text-dark bg-secondary').addClass(badgeClass).text(status);
                } else {
                    showAlert('danger', response.message || 'Failed to load certificate details');
                }
            },
            error: function() {
                showAlert('danger', 'Error loading certificate details');
            }
        });
    });

    function formatDate(d) {
        // Expecting YYYY-MM-DD or YYYY-MM-DD HH:MM:SS
        try {
            const parts = d.toString().split(' ')[0].split('-');
            if (parts.length === 3) {
                const dt = new Date(parts[0], parts[1]-1, parts[2]);
                return dt.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: '2-digit' });
            }
            return d;
        } catch (_) {
            return d;
        }
    }
    
    // Form validation
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
    
    // Handle delete button click
    $(document).on('click', '.delete-certificate', function(e) {
        e.preventDefault();
        currentCertificateId = $(this).data('id');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    });
    
    // Function to load certificate data into edit form
    function loadCertificateData(certificateId) {
        console.log('Loading certificate data...');
        $.ajax({
            url: '<?= base_url('admin/certificate/get/') ?>' + certificateId,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                console.log('Sending request to:', '<?= base_url('admin/certificate/get/') ?>' + certificateId);
            },
            success: function(response) {
                console.log('Response received:', response);
                if (response.success) {
                    const cert = response.data;
                    $('#editId').val(cert.id);
                    $('#editCertificateNo').val(cert.certificate_no);
                    $('#editStudentName').val(cert.student_name);
                    $('#editCourse').val(cert.course);
                    $('#editDateOfIssue').val(cert.date_of_issue.split(' ')[0]);
                    
                    if ($('#editStatus').length) {
                        $('#editStatus').val(cert.status);
                    }
                    
                    // Set admission number if it exists
                    if (cert.admission_no) {
                        $('#editAdmissionNo').val(cert.admission_no);
                    }
                    
                    // Show the edit modal
                    const editModal = new bootstrap.Modal(document.getElementById('editModal'), {
                        backdrop: true,
                        keyboard: true
                    });
                    editModal.show();
                    
                    // Remove any existing modal backdrops that might be causing issues
                    $('.modal-backdrop').remove();
                } else {
                    showAlert('danger', response.message || 'Failed to load certificate data');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading certificate:', error);
                showAlert('danger', 'Error loading certificate data. Please try again.');
            }
        });
    }

    // Function to update certificate status
    function updateCertificateStatus(certificateId, status) {
        $.ajax({
            url: '<?= site_url('admin/certificate/update-status') ?>',
            type: 'POST',
            data: {
                id: certificateId,
                status: status,
                '<?= csrf_token() ?>': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                if (result.success) {
                    showAlert('success', result.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('danger', result.message || 'An error occurred. Please try again.');
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred. Please try again.');
            }
        });
    }

    // Function to delete certificate
    function deleteCertificate(certificateId) {
        $.ajax({
            url: '<?= site_url('admin/certificate/delete') ?>',
            type: 'POST',
            data: {
                id: certificateId,
                '<?= csrf_token() ?>': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                if (result.success) {
                    // Hide modal (ensure instance exists), clean overlay, then refresh
                    const modalEl = document.getElementById('deleteModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.hide();
                    }
                    setTimeout(() => {
                        $('.modal-backdrop').remove();
                        document.body.classList.remove('modal-open');
                        window.location.reload();
                    }, 200);
                } else {
                    showAlert('danger', result.message || 'An error occurred. Please try again.');
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred. Please try again.');
            }
        });
    }

    // Helper function to show alerts
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Remove any existing alerts
        $('.alert-dismissible').alert('close');
        
        // Add new alert
        $('.content-wrapper').prepend(alertHtml);
        
        // Auto-close after 5 seconds
        setTimeout(() => {
            $('.alert-dismissible').alert('close');
        }, 5000);
    }

    // Confirm Approve
    $(document).on('click', '#confirmApprove', function() {
        console.log('Approve clicked for ID:', currentCertificateId);
        if (currentCertificateId) {
            updateCertificateStatus(currentCertificateId, 'Verified');
            $('#approveModal').modal('hide');
        }
    });

    // Confirm Reject
    $(document).on('click', '#confirmReject', function() {
        console.log('Reject clicked for ID:', currentCertificateId);
        if (currentCertificateId) {
            updateCertificateStatus(currentCertificateId, 'Rejected');
            $('#rejectModal').modal('hide');
        }
    });    // Confirm Delete
    $('#confirmDelete').on('click', function() {
        if (currentCertificateId) {
            deleteCertificate(currentCertificateId);
        }
    });

    // Handle edit form submission
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        // Add CSRF token
        formData.append('<?= csrf_token() ?>', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            url: '<?= base_url('admin/certificate/update') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', 'Certificate updated successfully');
                    $('#editModal').modal('hide');
                    
                    // Update the row in the table
                    const row = $(`tr[data-id="${formData.get('id')}"]`);
                    row.find('td:eq(1)').text(formData.get('certificate_no'));
                    row.find('td:eq(2)').text(formData.get('student_name'));
                    row.find('td:eq(3)').text(formData.get('course'));
                    row.find('td:eq(4)').text(new Date(formData.get('date_of_issue')).toLocaleDateString());
                    
                    // If status was updated (for super_admin)
                    if (formData.get('status')) {
                        const statusBadge = row.find('.status-badge');
                        const badgeClass = formData.get('status') === 'Verified' ? 'bg-success' :
                                         (formData.get('status') === 'Pending' ? 'bg-warning' : 'bg-danger');
                        statusBadge.removeClass('bg-success bg-warning bg-danger')
                                 .addClass(badgeClass)
                                 .text(formData.get('status'));
                    }
                    
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    let errorMsg = response.message || 'Failed to update certificate';
                    if (response.errors) {
                        errorMsg += '<ul class="mb-0 mt-2">';
                        for (let key in response.errors) {
                            errorMsg += '<li>' + key + ': ' + response.errors[key] + '</li>';
                        }
                        errorMsg += '</ul>';
                    }
                    showAlert('danger', errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error, xhr.responseText);
                let errorMsg = 'Error updating certificate. Please try again.';
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (resp.message) errorMsg = resp.message;
                } catch (e) {}
                showAlert('danger', errorMsg);
            }
        });
    });

    // Function to update certificate status
    function updateCertificateStatus(certificateId, status) {
        $.ajax({
            url: '<?= site_url('admin/certificate/update-status') ?>',
            type: 'POST',
            data: {
                id: certificateId,
                status: status,
                '<?= csrf_token() ?>': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                if (result.success) {
                    showAlert('success', result.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('danger', result.message || 'An error occurred. Please try again.');
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred. Please try again.');
            }
        });
    }

  // Duplicate deleteCertificate removed; unified implementation used above

    // Helper function to show alerts
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Remove any existing alerts
        $('.alert-dismissible').alert('close');
        
        // Add new alert
        $('.content-wrapper').prepend(alertHtml);
        
        // Auto-close after 5 seconds
        setTimeout(() => {
            $('.alert-dismissible').alert('close');
        }, 5000);
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Enhanced Pagination Styles */
    .pagination {
        margin: 1.5rem 0;
        display: flex;
        justify-content: center;
        gap: 4px;
    }
    
    .pagination .page-item {
        margin: 0;
    }
    
    .pagination .page-link {
        color: #4e73df;
        border: 1px solid #e3e6f0;
        background-color: #fff;
        padding: 0.5rem 0.9rem;
        border-radius: 6px;
        transition: all 0.2s ease-in-out;
        font-weight: 500;
        min-width: 40px;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #4e73df 0%, #3a56c9 100%);
        border-color: #3a56c9;
        color: white;
        box-shadow: 0 2px 4px rgba(78, 115, 223, 0.3);
    }
    
    .pagination .page-link:hover:not(.active) {
        background-color: #f8f9fc;
        border-color: #d1d3e2;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .pagination .page-item.disabled .page-link {
        color: #b7b9cc;
        background-color: #f8f9fc;
        border-color: #e3e6f0;
        box-shadow: none;
    }
    
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        padding: 0.5rem 0.9rem;
        border-radius: 6px;
    }
    
    .pagination .page-item .page-link i {
        font-size: 0.75rem;
    }
    
    /* Pagination info */
    .pagination-info {
        color: #6c757d;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .pagination {
            flex-wrap: wrap;
            gap: 3px;
        }
        
        .pagination .page-link {
            padding: 0.4rem 0.7rem;
            min-width: 36px;
            font-size: 0.875rem;
        }
        
        .pagination-info {
            width: 100%;
            justify-content: center;
            margin-top: 10px;
            order: 2;
        }
    }
</style>

<?= $this->section('scripts') ?>
<script>
/* Duplicate script block disabled to prevent double action binding.
   Modal-based handlers defined earlier will handle approve/reject/delete. */
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>

<?= $this->endSection() ?>