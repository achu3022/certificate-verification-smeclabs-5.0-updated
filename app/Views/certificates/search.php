<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>smclabs Certificate Search<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .main-container {
        min-height: calc(100vh - 60px);
        padding-bottom: 60px; /* reduced */
        overflow-x: hidden;
    }
    .site-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to right, #2563eb, #1e40af);
        color: white;
        padding: 10px 0; /* reduced */
        z-index: 1000;
    }
    .search-section {
        transition: all 0.3s ease;
    }
    .results-section {
        display: none;
        margin-top: 1rem; /* reduced */
        padding: 1rem 0; /* reduced */
        border-top: 1px solid #e5e7eb;
        width: 100%;
    }
    
    /* Certificate Popover Styling */
    .popover.certificate-popover {
        max-width: 350px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .popover.certificate-popover .popover-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        color: #0d6efd;
    }
    
    .popover.certificate-popover .popover-body {
        padding: 0.75rem;
    }
    
    .certificate-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .certificate-item:last-child {
        border-bottom: none;
    }
    
    .certificate-no {
        font-weight: 600;
        color: #212529;
    }
    
    .course-name {
        color: #6c757d;
        font-size: 0.85rem;
    }
    
    .issue-date {
        color: #6c757d;
        font-size: 0.75rem;
    }
        overflow: visible;
    }
    /* Narrower max widths for search and results sections */
    .search-section,
    #resultsSection,
    #verificationDetailsSection {
        max-width: 840px;
        margin-left: auto;
        margin-right: auto;
    }
    /* Tighter paddings across components */
    .card .card-body { padding: 0.75rem !important; }
    .card-header { padding: 0.5rem 0.75rem !important; }
    .table td, .table th { padding: 0.5rem 0.5rem !important; }
    .list-group-item { padding: 0.5rem 0 !important; }
    h3.mb-4 { margin-bottom: 0.75rem !important; }
    .results-section.active {
        display: block;
        animation: fadeInUp 0.5s ease;
    }
    /* Ensure details section clears fixed footer and is close to results */
    #verificationDetailsSection { margin-top: 8px; margin-bottom: 120px; }
    .no-results {
        text-align: center;
        padding: 3rem 0;
        background: #f8fafc;
        border-radius: 1rem;
        border: 1px dashed #e2e8f0;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .certificate-badge {
        transition: all 0.2s ease;
    }
    .certificate-badge:hover {
        transform: scale(1.05);
    }
    
    /* Enhanced Modal Styling */
    .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 1.5rem 2rem;
        position: relative;
    }
    
    .modal-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        opacity: 0.9;
    }
    
    .modal-header * {
        position: relative;
        z-index: 1;
    }
    
    .modal-title {
        font-weight: 700;
        font-size: 1.4rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .modal-body {
        padding: 2rem;
        background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
    }
    
    /* Enhanced Certificate Cards in Modal */
    #allCertificatesList .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        position: relative;
    }
    
    #allCertificatesList .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    }
    
    #allCertificatesList .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }
    
    #allCertificatesList .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: none;
        padding: 1.5rem 2rem 1rem;
        position: relative;
    }
    
    #allCertificatesList .card-header h6 {
        font-weight: 700;
        font-size: 1.1rem;
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    #allCertificatesList .badge {
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    #allCertificatesList .badge.bg-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }
    
    #allCertificatesList .badge.bg-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    }
    
    #allCertificatesList .badge.bg-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    }
    
    #allCertificatesList .card-body {
        padding: 1.5rem 2rem 2rem;
    }
    
    #allCertificatesList .form-label {
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    #allCertificatesList .card-body p {
        font-weight: 500;
        color: #1f2937;
        font-size: 1rem;
        padding: 0.5rem 0;
    }
    
    #allCertificatesList .card-body .fw-semibold {
        font-weight: 700;
        color: #111827;
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Student Information Header Enhancement */
    .modal-body .card.bg-light {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        position: relative;
        overflow: hidden;
    }
    
    .modal-body .card.bg-light::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
    }
    
    .modal-body .card.bg-light .card-body {
        padding: 1.5rem 2rem;
        background: transparent;
    }
    
    .modal-body .card.bg-light h6 {
        font-weight: 700;
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 1.1rem;
    }
    
    .modal-body .card.bg-light .badge {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        padding: 0.6rem 1rem;
        font-weight: 700;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    /* Modal Footer Enhancement */
    .modal-footer {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: none;
        padding: 1.5rem 2rem;
    }
    
    .modal-footer .btn {
        border-radius: 50px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .modal-footer .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    
    /* Icon Enhancements */
    #allCertificatesList .fas.fa-certificate {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 1.2rem;
    }
    
    #allCertificatesList .far.fa-calendar-alt {
        color: #6366f1;
    }
    
    /* Animation for modal appearance */
    .modal.fade .modal-dialog {
        transform: scale(0.8) translateY(-50px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .modal.show .modal-dialog {
        transform: scale(1) translateY(0);
    }
    
    /* Loading spinner enhancement */
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3em;
    }
    
    .spinner-border.text-primary {
        border-color: rgba(37, 99, 235, 0.2);
        border-top-color: #2563eb;
    }
    
    /* Scrollbar styling for modal body */
    .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    
    .modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    .modal-body::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        border-radius: 10px;
    }
    
    .modal-body::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-container bg-light py-3">
    <div class="container">
        <!-- Logo Section -->
        <div class="text-center mb-3">
            <img src="<?= base_url('smeclabs.png') ?>" alt="SMEC Labs Logo" class="img-fluid" style="max-height: 48px;">
        </div>

        <!-- Search Section -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="search-section card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="text-center mb-4 fw-bold text-primary">
                            Certificate Verification Portal
                        </h3>
                        <div class="text-center mb-4">
                            <p class="text-muted mb-0">Enter your admission number to verify certificates</p>
                        </div>

                        <form id="searchForm" action="<?= site_url('certificate/search') ?>" method="get" class="slide-in">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control form-control-lg rounded-3" 
                                           id="admission_no" name="admission_no" 
                                           placeholder="Enter admission number"
                                           value="<?= isset($searchTerm) ? esc($searchTerm) : '' ?>" 
                                           required>
                                    <label for="admission_no">
                                        <i class="fas fa-id-card me-2"></i>
                                        Admission Number
                                    </label>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Please enter your student admission number
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg rounded-3">
                                    <i class="fas fa-search me-2"></i>
                                    Search Certificates
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Results Section -->
                <div id="resultsSection" class="results-section <?= (isset($searchTerm) || isset($admissionNo)) ? 'active' : '' ?>">
                    <?php if (isset($searchTerm) || isset($admissionNo) || isset($certificates)): ?>
                        <?php if (empty($certificates) && (isset($searchTerm) || isset($admissionNo))): ?>
                            <div class="no-results">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h4 class="mb-3">No certificates found</h4>
                                <?php if (!empty($searchTerm)): ?>
                                    <p class="text-muted">No certificates were found for: <strong><?= esc($searchTerm) ?></strong></p>
                                <?php endif; ?>
                                <?php if (!empty($admissionNo)): ?>
                                    <p class="text-muted">No certificates were found for admission number: <strong><?= esc($admissionNo) ?></strong></p>
                                <?php endif; ?>
                                <a href="<?= site_url() ?>" class="btn btn-outline-primary mt-3">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Search
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="card shadow-lg border-0 rounded-4">
                                <div class="card-header bg-white border-0 py-4">
                                    <div class="text-center mb-4">
                                        <h4 class="text-primary mb-3">
                                            <i class="fas fa-graduation-cap me-2"></i>
                                            Search Results
                                        </h4>
                                        <?php if (isset($searchTerm)): ?>
                                        <div class="d-flex justify-content-center">
                                            <span class="badge bg-light text-dark px-4 py-2 fs-6">
                                                <i class="fas fa-id-card me-2"></i>
                                                Admission No: <?= esc($searchTerm) ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Student Name</th>
                                                    <th>Certificate Number</th>
                                                    <th>Course</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-end pe-4">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $studentShown = [];
                                                foreach ($certificates as $group): 
                                                    foreach ($group['certificates'] as $index => $cert): 
                                                        $isFirstForStudent = !in_array($group['admission_no'], $studentShown);
                                                        if ($isFirstForStudent) {
                                                            $studentShown[] = $group['admission_no'];
                                                        }
                                                ?>
                                                <?php if ($isFirstForStudent): ?>
                                                <tr>
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm bg-light rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                <i class="fas fa-user text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0 fw-semibold"><?= esc($group['student_name']) ?></h6>
                                                                <small class="text-muted">Adm: <?= esc($group['admission_no']) ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php 
                                                        $totalCerts = count($group['certificates']);
                                                        $firstCert = $group['certificates'][0];
                                                        ?>
                                                        <div class="d-flex align-items-center">
                                                            <span><?= esc($firstCert['certificate_no'] ?? 'N/A') ?></span>
                                                            <?php if ($totalCerts > 1): ?>
                                                                <span class="badge bg-info ms-2">+<?= $totalCerts - 1 ?> more</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php 
                                                        $courses = array_unique(array_column($group['certificates'], 'course'));
                                                        if (count($courses) > 1): ?>
                                                            <span><?= esc($courses[0]) ?></span>
                                                            <small class="text-muted d-block">+<?= count($courses) - 1 ?> other course(s)</small>
                                                        <?php else: ?>
                                                            <?= esc($courses[0] ?? 'N/A') ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <?php 
                                                        $statuses = array_column($group['certificates'], 'status');
                                                        $statusCounts = array_count_values($statuses);
                                                        $primaryStatus = array_keys($statusCounts, max($statusCounts))[0] ?? 'Pending';
                                                        $statusClass = [
                                                            'Verified' => 'success',
                                                            'Pending' => 'warning',
                                                            'Rejected' => 'danger'
                                                        ][$primaryStatus] ?? 'secondary';
                                                        ?>
                                                        <span class="badge bg-<?= $statusClass ?>">
                                                            <?= $primaryStatus ?>
                                                            <?php if ($totalCerts > 1): ?>
                                                                <small class="d-block mt-1"><?= $statusCounts[$primaryStatus] ?>/<?= $totalCerts ?></small>
                                                            <?php endif; ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end align-middle">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-primary view-details"
                                                                data-cert-id="<?= $firstCert['id'] ?>"
                                                                data-cert-no="<?= esc($firstCert['certificate_no']) ?>">
                                                            <i class="fas fa-search me-1"></i> Verify
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="welcome-message text-center py-5">
                            <div class="welcome-icon mb-4">
                                <i class="fas fa-search fa-4x text-primary"></i>
                            </div>
                            <h3 class="mb-3">Welcome to Certificate Verification</h3>
                            <p class="text-muted mb-4">Please enter an admission number or search term to verify certificates</p>
                            <div class="d-flex justify-content-center">
                                <div class="search-tips p-4 bg-light rounded-3" style="max-width: 600px;">
                                    <h5 class="mb-3">Search Tips:</h5>
                                    <ul class="text-start">
                                        <li>Enter the full or partial admission number</li>
                                        <li>Search by student name, course, or certificate number</li>
                                        <li>Make sure there are no extra spaces in your search</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Details Section rendered after submission (moved here to match width) -->
                <div id="verificationDetailsSection" class="mt-3" style="display:none;">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-certificate me-2"></i>Certificate Details</h5>
                        </div>
                        <div class="card-body" id="verificationDetailsBody">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Fixed Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">© <?= date('Y') ?> SMEC Labs. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-white-50">
                        Powered by  smeclabs DT
                       
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- Certificate Verification Form Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Verify Certificate</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verificationForm" action="<?= site_url('certificate/verify') ?>" method="POST" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="certificate_id" id="verification_certificate_id" value="">
                <input type="hidden" name="ip_address" id="ip_address" value="">
                <input type="hidden" name="user_agent" id="user_agent" value="">
                
                <div class="modal-body">
                    <!-- Error message container -->
                    <div id="verificationErrors" class="alert alert-danger d-none" role="alert"></div>
                    
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <h6 class="mb-1">Certificate Verification</h6>
                                <p class="mb-0">You are verifying certificate: <strong id="certificate-number-display">-</strong></p>
                            </div>
                        </div>
                    </div>
                    
                    <p class="mb-4">Please provide your details to verify this certificate:</p>
                    
                    <div class="mb-3">
                        <label for="vf_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="vf_name" name="name" required 
                               placeholder="Enter your full name">
                        <div class="invalid-feedback">Please provide your full name</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="vf_designation" class="form-label">Designation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="vf_designation" name="designation" required
                               placeholder="Your job title">
                        <div class="invalid-feedback">Please provide your designation</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="vf_company" class="form-label">Company/Organization <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="vf_company" name="company_name" required
                               placeholder="Your company or organization name">
                        <div class="invalid-feedback">Please provide your company/organization name</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="vf_contact" class="form-label">Contact Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="vf_contact" name="contact_no" required
                               placeholder="Your contact number">
                        <div class="invalid-feedback">Please provide a valid contact number</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="vf_country" class="form-label">Country <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="vf_country" name="country" required
                               placeholder="Your country">
                        <div class="invalid-feedback">Please provide your country</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-text">View Certificate</span>
                        <span class="spinner-border spinner-border-sm d-none btn-loading" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="verificationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <i class="fas fa-check-circle me-2"></i> Verification successful! Loading certificate details...
        </div>
    </div>
</div>

<!-- View More Details Modal (Certificate Details) -->
<div class="modal fade" id="certDetailsModal" tabindex="-1" aria-hidden="true" data-admission-no="">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-certificate me-2"></i>
                    Certificate Details - <span id="modalStudentName">Student</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <!-- Student Information Header -->
                <div class="card mb-4 border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-1 text-primary">Student Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Student Name:</small>
                                        <p class="mb-0 fw-semibold" id="studentName">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Admission Number:</small>
                                        <p class="mb-0 fw-semibold" id="admissionNo">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex align-items-center justify-content-end">
                                    <i class="fas fa-certificate text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Total Certificates</small>
                                        <span class="badge bg-primary fs-6" id="certificateCount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- All Certificates with Full Details -->
                <div id="allCertificatesList">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 mb-0 text-muted">Loading certificate details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    const searchForm = document.getElementById('searchForm');
    const resultsSection = document.getElementById('resultsSection');
    const certDetailsModalEl = document.getElementById('certDetailsModal');
    const verificationModalEl = document.getElementById('verificationModal');
    let certDetailsModal = null;
    let verificationModalInstance = null;
    
    // Initialize modals
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        if (certDetailsModalEl) {
            certDetailsModal = new bootstrap.Modal(certDetailsModalEl);
        }
        if (verificationModalEl) {
            verificationModalInstance = new bootstrap.Modal(verificationModalEl);
            console.log('Verification modal initialized');
        }
    } else {
        console.error('Bootstrap not found');
    }
    
    // Handle form submission
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = searchForm.querySelector('input[name="admission_no"]');
            if (searchInput && searchInput.value.trim() === '') {
                e.preventDefault();
                return false;
            }
            // Ensure we're not doing an AJAX request
            e.preventDefault();
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                const originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Searching...';
                
                // Revert button state after form submission
                setTimeout(() => {
                    // Submit the form normally to get HTML response
                    this.submit();
                    
                    // Revert button state in case form submission fails
                    setTimeout(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }, 5000);
                }, 100);
            } else {
                // Fallback if button not found
                this.submit();
            }
            return false;
        });
    }

    // Initialize popovers for certificate numbers with custom styling
    if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                container: 'body',
                trigger: 'focus',
                html: true,
                customClass: 'certificate-popover',
                sanitize: false
            });
        });
    }

    // Handle verify button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-details')) {
            e.preventDefault();
            const button = e.target.closest('.view-details');
            const certId = button.getAttribute('data-cert-id');
            const certNo = button.getAttribute('data-cert-no');
            
            console.log('Verify button clicked:', { certId, certNo });
            
            // Set certificate ID in the form
            const certIdInput = document.getElementById('verification_certificate_id');
            if (certIdInput && certId) {
                certIdInput.value = certId;
            }
            
            // Update certificate number display
                const certNoDisplay = document.getElementById('certificate-number-display');
                if (certNoDisplay && certNo) {
                    certNoDisplay.textContent = certNo;
                }
                
                // Clear any previous errors
                const errorDiv = document.getElementById('verificationErrors');
                if (errorDiv) {
                    errorDiv.classList.add('d-none');
                    errorDiv.innerHTML = '';
                }
                
                // Reset form
                const form = document.getElementById('verificationForm');
                if (form) {
                    form.reset();
                    if (certIdInput) certIdInput.value = certId; // Set again after reset
                }
                
                // Show modal
                if (verificationModalInstance) {
                    console.log('Opening verification modal');
                    verificationModalInstance.show();
                } else {
                    console.error('Modal instance not found, trying to reinitialize');
                    // Try to reinitialize
                    if (verificationModalEl && typeof bootstrap !== 'undefined') {
                        verificationModalInstance = new bootstrap.Modal(verificationModalEl);
                        verificationModalInstance.show();
                    }
                }
        }
    });

    // Handle verification form submission
    const verificationForm = document.getElementById('verificationForm');
    if (verificationForm) {
        verificationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...';
            
            // Submit the form
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const toast = new bootstrap.Toast(document.getElementById('verificationToast'));
                    toast.show();
                    
                    // Close the verification modal
                    if (verificationModalInstance) {
                        verificationModalInstance.hide();
                    }
                    
                    // Show certificate details
                    if (data.certificate) {
                        loadCertificateDetails(data.certificate.id, data.certificate.admission_no);
                    }
                } else {
                    // Show error message
                    const errorDiv = document.getElementById('verificationErrors');
                    if (errorDiv) {
                        errorDiv.classList.remove('d-none');
                        errorDiv.innerHTML = data.message || 'Verification failed. Please check your details and try again.';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorDiv = document.getElementById('verificationErrors');
                if (errorDiv) {
                    errorDiv.classList.remove('d-none');
                    errorDiv.innerHTML = 'An error occurred. Please try again later.';
                }
            })
            .finally(() => {
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });
    }
    
    // Function to load all certificates for an admission number
    function loadAllCertificates(admissionNo) {
        const certificatesList = document.getElementById('allCertificatesList');
        if (!certificatesList) {
            console.error('Certificate list element not found');
            return;
        }
        
        console.log('Loading certificates for admission number:', admissionNo);
        
        certificatesList.innerHTML = [
            '<div class="text-center py-4">',
            '    <div class="spinner-border text-primary" role="status">',
            '        <span class="visually-hidden">Loading...</span>',
            '    </div>',
            '    <p class="mt-2 mb-0 text-muted">Loading certificates...</p>',
            '</div>'
        ].join('');
        
        fetch('/certificate/all?admission_no=' + encodeURIComponent(admissionNo), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            method: 'GET',
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            console.log('Response received:', data);
            if (data.success && data.certificates && data.certificates.length > 0) {
                console.log('Found ' + data.certificates.length + ' certificates');
                let html = [];
                
                data.certificates.forEach((cert, index) => {
                    const statusClass = cert.status === 'Verified' ? 'success' : (cert.status === 'Pending' ? 'warning' : 'danger');
                    const statusIcon = cert.status === 'Verified' ? 'check-circle' : (cert.status === 'Pending' ? 'clock' : 'times-circle');
                    const issueDate = cert.date_of_issue ? new Date(cert.date_of_issue).toLocaleDateString() : 'N/A';
                    const startDate = cert.start_date ? new Date(cert.start_date).toLocaleDateString() : 'N/A';
                    const endDate = cert.end_date ? new Date(cert.end_date).toLocaleDateString() : 'N/A';
                    
                    const certHtml = [
                        '<div class="card mb-4 border-0 shadow-sm certificate-card" style="animation: fadeInUp 0.6s ease ' + (index * 0.1) + 's both;">',
                        '    <div class="card-header bg-white border-bottom">',
                        '        <div class="d-flex justify-content-between align-items-center">',
                        '            <div>',
                        '                <h6 class="mb-0 text-primary">',
                        '                    <i class="fas fa-certificate me-2"></i>',
                        '                    Certificate #' + (index + 1),
                        '                </h6>',
                        '            </div>',
                        '            <span class="badge bg-' + statusClass + ' rounded-pill">',
                        '                <i class="fas fa-' + statusIcon + ' me-1"></i> ' + (cert.status || 'Unknown'),
                        '            </span>',
                        '        </div>',
                        '    </div>',
                        '    <div class="card-body">',
                        '        <div class="row">',
                        '            <div class="col-md-6">',
                        '                <div class="mb-3">',
                        '                    <label class="form-label text-muted small mb-1">Certificate Number</label>',
                        '                    <p class="mb-0 fw-semibold">' + (cert.certificate_no || 'N/A') + '</p>',
                        '                </div>',
                        '                <div class="mb-3">',
                        '                    <label class="form-label text-muted small mb-1">Course</label>',
                        '                    <p class="mb-0">' + (cert.course || 'N/A') + '</p>',
                        '                </div>',
                        '                <div class="mb-3">',
                        '                    <label class="form-label text-muted small mb-1">Date of Issue</label>',
                        '                    <p class="mb-0">',
                        '                        <i class="far fa-calendar-alt me-1 text-muted"></i>' + issueDate,
                        '                    </p>',
                        '                </div>',
                        '            </div>',
                        '            <div class="col-md-6">',
                        '                <div class="mb-3">',
                        '                    <label class="form-label text-muted small mb-1">Course Duration</label>',
                        '                    <div class="d-flex align-items-center">',
                        '                        <small class="text-muted me-2">From:</small>',
                        '                        <span class="me-3">' + startDate + '</span>',
                        '                        <small class="text-muted me-2">To:</small>',
                        '                        <span>' + endDate + '</span>',
                        '                    </div>',
                        '                </div>',
                        '            </div>',
                        '        </div>',
                        '    </div>',
                        '</div>'
                    ].join('');
                    
                    html.push(certHtml);
                });
                
                certificatesList.innerHTML = html.join('');
                document.getElementById('certificateCount').textContent = data.certificates.length;
            } else {
                console.log('No certificates found or empty response');
                certificatesList.innerHTML = [
                    '<div class="text-center py-5">',
                    '    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>',
                    '    <h6 class="text-muted">No Certificates Found</h6>',
                    '    <p class="mb-0 text-muted">No certificates were found for this admission number.</p>',
                    '</div>'
                ].join('');
                document.getElementById('certificateCount').textContent = '0';
            }
        })
        .catch(error => {
            console.error('Error loading certificates:', error);
            certificatesList.innerHTML = [
                '<div class="alert alert-danger m-3">',
                '    <i class="fas fa-exclamation-triangle me-2"></i>',
                '    Error loading certificates. Please try again later.',
                '</div>'
            ].join('');
            document.getElementById('certificateCount').textContent = '0';
        });
    }

    // Handle modal close event to reset content
    if (certDetailsModalEl) {
        certDetailsModalEl.addEventListener('hidden.bs.modal', function () {
            // Reset modal content when closed
            const resetFields = ['modalStudentName', 'studentName', 'admissionNo'];
            resetFields.forEach(field => {
                const el = document.getElementById(field);
                if (el) el.textContent = field === 'modalStudentName' ? 'Student' : '-';
            });
            
            // Reset certificates list
            const certificatesList = document.getElementById('allCertificatesList');
            if (certificatesList) {
                certificatesList.innerHTML = [
                    '<div class="text-center py-5">',
                    '    <div class="spinner-border text-primary" role="status">',
                    '        <span class="visually-hidden">Loading...</span>',
                    '    </div>',
                    '    <p class="mt-3 mb-0 text-muted">Loading certificate details...</p>',
                    '</div>'
                ].join('');
            }
            
            const certCount = document.getElementById('certificateCount');
            if (certCount) certCount.textContent = '0';
        });
    }

    // Show results section when searching
    if (searchForm && resultsSection) {
        searchForm.addEventListener('submit', function() {
            resultsSection.classList.add('active');
        });
    }


    // Function to get client IP and user agent
    async function getClientInfo() {
        try {
            // Get IP address from a free API
            const response = await fetch('https://api.ipify.org?format=json');
            const data = await response.json();
            
            // Update hidden form fields
            const ipInput = document.getElementById('ip_address');
            const userAgentInput = document.getElementById('user_agent');
            
            if (ipInput) ipInput.value = data.ip || '';
            if (userAgentInput) userAgentInput.value = navigator.userAgent || '';
            
            return {
                ip: data.ip || 'unknown',
                userAgent: navigator.userAgent || 'unknown'
            };
        } catch (error) {
            console.error('Error getting client info:', error);
            return {
                ip: 'unknown',
                userAgent: navigator.userAgent || 'unknown'
            };
        }
    }


    // Function to show error message
    function showError(message) {
        const errorDiv = document.getElementById('verificationErrors');
        if (errorDiv) {
            errorDiv.innerHTML = message;
            errorDiv.classList.remove('d-none');
            // Auto-hide after 5 seconds
            setTimeout(() => {
                errorDiv.classList.add('d-none');
            }, 5000);
        }
    }
    
    // Function to load and display certificate details
    function loadCertificateDetails(certId, admissionNo = null) {
        if (!certId) {
            console.error('No certificate ID provided');
            return;
        }
        
        // Initialize the modal if it hasn't been shown yet
        const certDetailsModal = new bootstrap.Modal(document.getElementById('certDetailsModal'));
        
        // Show loading overlay instead of replacing content
        const modalBody = document.querySelector('#certDetailsModal .modal-body');
        let loadingOverlay = null;
        if (modalBody) {
            // Create loading overlay
            loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75';
            loadingOverlay.style.zIndex = '1050';
            loadingOverlay.innerHTML = [
                '<div class="text-center">',
                '    <div class="spinner-border text-primary" role="status">',
                '        <span class="visually-hidden">Loading...</span>',
                '    </div>',
                '    <p class="mt-2 mb-0 text-muted">Loading certificate details...</p>',
                '</div>'
            ].join('');
            
            // Add overlay to modal body
            modalBody.style.position = 'relative';
            modalBody.appendChild(loadingOverlay);
        }
        
        // Show the modal
        certDetailsModal.show();
        
        // Get client info
        async function getClientInfo() {
            try {
                // First try to get IP from the server-side
                const ipResponse = await fetch('<?= site_url("get-client-ip") ?>');
                if (ipResponse.ok) {
                    const data = await ipResponse.json();
                    return {
                        ip: data.ip || 'unknown',
                        userAgent: navigator.userAgent || 'unknown'
                    };
                }
                // Fallback to public IP service
                const publicIpResponse = await fetch('https://api.ipify.org?format=json');
                const publicIpData = await publicIpResponse.json();
                return {
                    ip: publicIpData.ip || 'unknown',
                    userAgent: navigator.userAgent || 'unknown'
                };
            } catch (error) {
                console.error('Error getting client info:', error);
                return {
                    ip: 'unknown',
                    userAgent: navigator.userAgent || 'unknown'
                };
            }
        }
        
        // Fetch certificate details
        fetch('/certificate/details/' + certId, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to load certificate details');
            }
            
            // The certificate data is in the 'data' property of the response
            const cert = data.data || {};
            
            console.log('Certificate data received:', cert); // Debug log
            
            // Safe function to update element text if it exists
            const updateElementText = (id, text) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = text;
                } else {
                    console.warn(`Element with ID '${id}' not found`);
                }
            };
            
            // Wait for the modal to be fully shown before updating content
            const modalShown = new Promise((resolve) => {
                const modal = document.getElementById('certDetailsModal');
                if (modal) {
                    const handler = () => {
                        modal.removeEventListener('shown.bs.modal', handler);
                        resolve();
                    };
                    modal.addEventListener('shown.bs.modal', handler);
                } else {
                    resolve();
                }
            });
            
            // Remove loading overlay
            if (loadingOverlay && loadingOverlay.parentNode) {
                loadingOverlay.parentNode.removeChild(loadingOverlay);
            }
            
            // Update modal header and student information
            updateElementText('modalStudentName', cert.student_name || 'Student');
            updateElementText('studentName', cert.student_name || '-');
            updateElementText('admissionNo', cert.admission_no || '-');
            
            // Load all certificates for this admission number if provided
            if (admissionNo || cert.admission_no) {
                loadAllCertificates(admissionNo || cert.admission_no);
            }
        })
        .catch(error => {
            console.error('Error loading certificate details:', error);
            
            // Remove loading overlay
            if (loadingOverlay && loadingOverlay.parentNode) {
                loadingOverlay.parentNode.removeChild(loadingOverlay);
            }
            
            const modalBody = document.querySelector('#certDetailsModal .modal-body');
            if (modalBody) {
                // Create error message overlay instead of replacing content
                const errorOverlay = document.createElement('div');
                errorOverlay.className = 'position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-90';
                errorOverlay.style.zIndex = '1050';
                errorOverlay.innerHTML = [
                    '<div class="alert alert-danger">',
                    '    <i class="fas fa-exclamation-triangle me-2"></i>',
                    '    Error loading certificate details. Please try again later.',
                    '</div>'
                ].join('');
                modalBody.appendChild(errorOverlay);
            }
        });
    }
});
</script>
<?= $this->endSection() ?>