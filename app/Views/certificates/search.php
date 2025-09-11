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

                        <form id="searchForm" action="<?= site_url('certificate/search') ?>" method="post" class="slide-in">
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
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-primary">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    Search Results
                                </h5>
                                <?php if (isset($searchTerm)): ?>
                                <span class="badge bg-light text-dark px-3 py-2">
                                    Admission No: <?= esc($searchTerm) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (isset($certificates) && !empty($certificates)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="py-3 px-4 text-uppercase small fw-bold text-muted">Certificate No</th>
                                                <th class="py-3 px-4 text-uppercase small fw-bold text-muted">Status</th>
                                                <th class="py-3 px-4 text-uppercase small fw-bold text-muted text-center">Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($certificates as $cert): ?>
                                            <tr>
                                                <td class="px-4 py-3 fw-semibold"><?= esc($cert['certificate_no']) ?></td>
                                                <td class="px-4 py-3">
                                                    <span class="certificate-badge badge bg-<?= $cert['status'] === 'Verified' ? 'success' : ($cert['status'] === 'Pending' ? 'warning' : 'danger') ?> rounded-pill px-3">
                                                        <i class="fas fa-<?= $cert['status'] === 'Verified' ? 'check-circle' : ($cert['status'] === 'Pending' ? 'clock' : 'times-circle') ?> me-1"></i>
                                                        <?= esc($cert['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" class="btn btn-sm btn-primary view-details" 
                                                            data-cert-id="<?= $cert['id'] ?>" data-bs-toggle="modal" data-bs-target="#certDetailsModal">
                                                        <i class="fas fa-eye me-1"></i> View More Details
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="no-results p-5">
                                    <div class="text-center">
                                        <i class="fas fa-search fa-3x text-muted mb-4"></i>
                                        <h4 class="text-primary mb-3">No Certificates Found</h4>
                                        <p class="text-muted mb-0">
                                            No certificates were found for the admission number: <strong><?= esc($searchTerm ?? '') ?></strong>
                                        </p>
                                        <div class="mt-4">
                                            <a href="<?= site_url() ?>" class="btn btn-outline-primary rounded-pill px-4">
                                                <i class="fas fa-search me-2"></i>
                                                Try Another Search
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
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

<!-- View More Details Modal (Verification Form) -->
<div class="modal fade" id="certDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Provide Your Information
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="verificationForm" action="<?= site_url('certificate/verify') ?>" method="post" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="certificate_id" id="vf_certificate_id" value="">

                    <div class="mb-3">
                        <label for="vf_name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="vf_name" name="name" required minlength="2" maxlength="255" placeholder="Enter your full name">
                        <div class="invalid-feedback">Please enter your name (min 2 characters).</div>
                    </div>
                    <div class="mb-3">
                        <label for="vf_designation" class="form-label">Designation</label>
                        <input type="text" class="form-control" id="vf_designation" name="designation" required minlength="2" maxlength="255" placeholder="Your designation">
                        <div class="invalid-feedback">Please enter your designation.</div>
                    </div>
                    <div class="mb-3">
                        <label for="vf_company" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="vf_company" name="company_name" required minlength="2" maxlength="255" placeholder="Company name">
                        <div class="invalid-feedback">Please enter your company name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="vf_contact" class="form-label">Contact No.</label>
                        <input type="tel" class="form-control" id="vf_contact" name="contact_no" required minlength="5" maxlength="20" pattern="^[0-9+()\-\s]+$" placeholder="e.g. +1 234 567 890">
                        <div class="invalid-feedback">Please enter a valid contact number.</div>
                    </div>
                    <div class="mb-3">
                        <label for="vf_country" class="form-label">Country</label>
                        <input type="text" class="form-control" id="vf_country" name="country" required minlength="2" maxlength="100" placeholder="Country">
                        <div class="invalid-feedback">Please enter your country.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn-text"><i class="fas fa-paper-plane me-2"></i>Submit</span>
                            <span class="btn-loading d-none"><i class="fas fa-spinner fa-spin me-2"></i>Submitting...</span>
                        </button>
                    </div>
                </form>
                <div id="verificationErrors" class="alert alert-danger mt-3 d-none"></div>
            </div>
        </div>
    </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const resultsSection = document.getElementById('resultsSection');

    if (searchForm) {
        // Show results section when searching
        searchForm.addEventListener('submit', function() {
            resultsSection.classList.add('active');
        });
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                if (resultsSection) {
                    resultsSection.innerHTML = html;
                    resultsSection.classList.add('active');
                    resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request. Please try again.');
            })
            .finally(() => {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
            });
        });
    }

    // Prepare modal with certificate id
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-details')) {
            const btn = e.target.closest('.view-details');
            const certId = btn.getAttribute('data-cert-id');
            document.getElementById('vf_certificate_id').value = certId;
            // Reset previous state
            document.getElementById('verificationForm').reset();
            document.getElementById('verificationErrors').classList.add('d-none');
        }
    });

    // Handle verification form submission
    const verificationForm = document.getElementById('verificationForm');
    if (verificationForm) {
        verificationForm.addEventListener('submit', function(ev) {
            ev.preventDefault();

            // Client-side validation
            let valid = true;
            const requiredFields = ['vf_name','vf_designation','vf_company','vf_contact','vf_country'];
            requiredFields.forEach(id => {
                const el = document.getElementById(id);
                if (!el.checkValidity()) { valid = false; el.classList.add('is-invalid'); } else { el.classList.remove('is-invalid'); }
            });
            if (!valid) return;

            const submitBtn = verificationForm.querySelector('button[type="submit"]');
            submitBtn.querySelector('.btn-text').classList.add('d-none');
            submitBtn.querySelector('.btn-loading').classList.remove('d-none');
            submitBtn.disabled = true;

            fetch(verificationForm.action, {
                method: 'POST',
                body: new FormData(verificationForm),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.json())
            .then(data => {
                if (!data.success) {
                    const errBox = document.getElementById('verificationErrors');
                    errBox.classList.remove('d-none');
                    errBox.innerHTML = (data.errors) ? Object.values(data.errors).join('<br>') : (data.message || 'Validation failed');
                    return;
                }

                // Hide modal
                const modalEl = document.getElementById('certDetailsModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();

                // Render details section below
                const section = document.getElementById('verificationDetailsSection');
                const body = document.getElementById('verificationDetailsBody');
                const cert = data.certificate;
                const statusClass = cert.status === 'Verified' ? 'success' : (cert.status === 'Pending' ? 'warning' : 'danger');
                const statusIcon = cert.status === 'Verified' ? 'check-circle' : (cert.status === 'Pending' ? 'clock' : 'times-circle');

                body.innerHTML = `
                    <div class="row g-3">
                        <div class="col-12 text-center mb-2">
                            <span class="badge bg-${statusClass} rounded-pill px-3 py-2">
                                <i class="fas fa-${statusIcon} me-1"></i>${cert.status}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Certificate No</div>
                            <div class="fw-semibold">${cert.certificate_no}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Student Name</div>
                            <div class="fw-semibold">${cert.student_name}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Course</div>
                            <div class="fw-semibold">${cert.course}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Issue Date</div>
                            <div class="fw-semibold">${new Date(cert.date_of_issue).toLocaleDateString()}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Start Date</div>
                            <div>${new Date(cert.start_date).toLocaleDateString()}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">End Date</div>
                            <div>${new Date(cert.end_date).toLocaleDateString()}</div>
                        </div>
                    </div>`;
                section.style.display = 'block';
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(err => {
                const errBox = document.getElementById('verificationErrors');
                errBox.classList.remove('d-none');
                errBox.textContent = 'An error occurred. Please try again.';
            })
            .finally(() => {
                const submitBtn = verificationForm.querySelector('button[type="submit"]');
                submitBtn.querySelector('.btn-text').classList.remove('d-none');
                submitBtn.querySelector('.btn-loading').classList.add('d-none');
                submitBtn.disabled = false;
            });
        });
    }
});
</script>
<?= $this->endSection() ?>