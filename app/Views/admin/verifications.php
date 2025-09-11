<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Verification Requests<?= $this->endSection() ?>

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
            <a class="nav-link active" href="<?= site_url('admin/verifications') ?>">
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

<div class="main-content">
  <div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-0">Verification Requests</h1>
        <p class="text-muted">Details submitted by organizations requesting certificate verification</p>
      </div>
      <div class="text-end">
        <span class="badge bg-primary">Total: <?= esc($total ?? 0) ?></span>
      </div>
    </div>

    <div class="card table-card mb-4">
      <div class="card-body">
        <form method="get" action="<?= site_url('admin/verifications') ?>" id="verifyFilters">
          <div class="row g-3">
            <div class="col-md-6 col-lg-4">
              <label class="form-label small text-muted mb-1">Search</label>
              <input type="text" name="search" class="form-control" value="<?= esc($search ?? '') ?>" placeholder="Name, company, designation, contact, country, certificate no">
            </div>
            <div class="col-md-2">
              <label class="form-label small text-muted mb-1">Per Page</label>
              <select name="per_page" class="form-select" onchange="document.getElementById('verifyFilters').submit();">
                <?php foreach (($perPageOptions ?? [10,20,50,100]) as $opt): ?>
                  <option value="<?= $opt ?>" <?= (isset($perPage) && (int)$perPage === (int)$opt) ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button class="btn btn-primary w-100" type="submit"><i class="fas fa-filter me-1"></i> Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card table-card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th class="px-4">Submitted At</th>
                <th>Certificate No</th>
                <th>Student</th>
                <th>Requester</th>
                <th>Designation</th>
                <th>Company</th>
                <th>Contact</th>
                <th>Country</th>
                
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($records)): ?>
                <?php foreach ($records as $r): ?>
                  <tr>
                    <td class="px-4"><?= !empty($r['created_at']) ? date('d M Y, H:i', strtotime($r['created_at'])) : '' ?></td>
                    <td><?= esc($r['certificate_no'] ?? '') ?></td>
                    <td><?= esc($r['student_name'] ?? '') ?></td>
                    <td><?= esc($r['name'] ?? '') ?></td>
                    <td><?= esc($r['designation'] ?? '') ?></td>
                    <td><?= esc($r['company_name'] ?? '') ?></td>
                    <td><?= esc($r['contact_no'] ?? '') ?></td>
                    <td><?= esc($r['country'] ?? '') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="text-center py-4">No verification requests found</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer border-0">
        <div class="d-flex justify-content-between align-items-center">
          <div class="text-muted small">Showing <?= isset($offset) ? $offset + 1 : 0 ?> to <?= isset($records) ? ($offset + count($records)) : 0 ?> of <?= esc($total ?? 0) ?> entries</div>
          <div><?= isset($pager) ? $pager->links() : '' ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="footer">
  <div class="footer-content d-flex justify-content-between w-100">
    <div class="text-muted">&copy; <?= date('Y') ?> SMEC Certificate Verification System</div>
    <div class="d-flex align-items-center">
      <span class="badge bg-primary me-2">Version <?= config('App')->version ?></span>
      <span class="text-muted">Powered by smeclabs DT</span>
    </div>
  </div>
</footer>

<?= $this->endSection() ?>
