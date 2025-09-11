<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Search Logs<?= $this->endSection() ?>

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
    .pagination .page-item:not(:last-child) {
        margin-right: 5px;
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
            <a class="nav-link active" href="<?= site_url('admin/search-logs') ?>">
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
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Search Logs</h1>
                <p class="text-muted">Track all certificate verification searches</p>
            </div>
            <div class="btn-toolbar">
                <a href="<?= site_url('admin/search-logs/export?' . http_build_query([
                    'search' => $searchTerm ?? '',
                    'status' => $status ?? '',
                    'date_from' => $dateFrom ?? '',
                    'date_to' => $dateTo ?? ''
                ])) ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-export me-1"></i> Export Logs
                </a>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card table-card mb-4">
            <div class="card-body">
                <form action="<?= site_url('admin/search-logs') ?>" method="get" id="searchLogsForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Search Term</label>
                            <input type="text" name="search" class="form-control" value="<?= esc($searchTerm ?? '') ?>" placeholder="Search term...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="found" <?= (isset($status) && $status === 'found') ? 'selected' : '' ?>>Found</option>
                                <option value="not_found" <?= (isset($status) && $status === 'not_found') ? 'selected' : '' ?>>Not Found</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="<?= esc($dateFrom ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="<?= esc($dateTo ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Per Page</label>
                            <select name="per_page" class="form-select" onchange="document.getElementById('searchLogsForm').submit();">
                                <option value="10" <?= ($perPage ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                                <option value="50" <?= ($perPage ?? 10) == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= ($perPage ?? 10) == 100 ? 'selected' : '' ?>>100</option>
                                <option value="500" <?= ($perPage ?? 10) == 500 ? 'selected' : '' ?>>500</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Logs Table -->
        <div class="card table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Search Term</th>
                                <th>IP Address</th>
                                <th>Date & Time</th>
                                <th>Result</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($searches)): ?>
                                <?php foreach ($searches as $search): ?>
                                <tr>
                                    <td class="px-4"><?= esc($search['search_term'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-laptop"></i> <?= esc($search['ip_address'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td><?= !empty($search['created_at']) ? date('d M Y, H:i:s', strtotime($search['created_at'])) : 'N/A' ?></td>
                                    <td>
                                        <?php $found = $search['found'] ?? false; ?>
                                        <span class="badge bg-<?= $found ? 'success' : 'danger' ?>">
                                            <?= $found ? 'Found' : 'Not Found' ?>
                                        </span>
                                    </td>
                                    <td class="text-end px-4">
                                        <button class="btn btn-sm btn-outline-info view-details" 
                                                title="View Details"
                                                data-search-term="<?= esc($search['search_term'] ?? '') ?>"
                                                data-ip="<?= esc($search['ip_address'] ?? '') ?>"
                                                data-user-agent="<?= esc($search['user_agent'] ?? '') ?>"
                                                data-created="<?= !empty($search['created_at']) ? date('d M Y, H:i:s', strtotime($search['created_at'])) : 'N/A' ?>">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger ms-1 delete-log" 
                                                title="Delete"
                                                data-id="<?= $search['id'] ?? '' ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">No search logs found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing <?= isset($offset) ? $offset + 1 : 0 ?> to <?= min(($offset ?? 0) + count($searches), $total ?? 0) ?> of <?= $total ?? 0 ?> entries
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
            <span class="badge bg-primary me-2">Version <?= config('App')->version ?></span>
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

    // View Details Modal
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', function() {
            const searchTerm = this.dataset.searchTerm || 'N/A';
            const ip = this.dataset.ip || 'N/A';
            const userAgent = this.dataset.userAgent || 'N/A';
            const createdAt = this.dataset.created || 'N/A';
            
            // Set modal content
            document.querySelector('#searchDetailsModal .search-term').textContent = searchTerm;
            document.querySelector('#searchDetailsModal .ip-address').textContent = ip;
            document.querySelector('#searchDetailsModal .user-agent').textContent = userAgent;
            document.querySelector('#searchDetailsModal .created-at').textContent = createdAt;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('searchDetailsModal'));
            modal.show();
        });
    });

    // Delete Log
    document.querySelectorAll('.delete-log').forEach(button => {
        button.addEventListener('click', function() {
            const logId = this.dataset.id;
            
            if (confirm('Are you sure you want to delete this log entry?')) {
                fetch(`<?= site_url('admin/search-logs/delete/') ?>${logId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to see changes
                        window.location.reload();
                    } else {
                        alert('Failed to delete log: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the log');
                });
            }
        });
    });
});
</script>

<!-- Search Details Modal -->
<div class="modal fade" id="searchDetailsModal" tabindex="-1" aria-labelledby="searchDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchDetailsModalLabel">Search Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Search Term:</strong>
                    <p class="search-term"></p>
                </div>
                <div class="mb-3">
                    <strong>IP Address:</strong>
                    <p class="ip-address"></p>
                </div>
                <div class="mb-3">
                    <strong>User Agent:</strong>
                    <p class="user-agent small text-muted"></p>
                </div>
                <div class="mb-3">
                    <strong>Date & Time:</strong>
                    <p class="created-at"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>