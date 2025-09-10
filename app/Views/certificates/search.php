<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>smclabs Certificate Search<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .main-container {
        min-height: calc(100vh - 60px);
        padding-bottom: 80px;
        overflow-x: hidden;
    }
    .site-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to right, #2563eb, #1e40af);
        color: white;
        padding: 15px 0;
        z-index: 1000;
    }
    .search-section {
        transition: all 0.3s ease;
    }
    .results-section {
        display: none;
        margin-top: 2rem;
        padding: 2rem 0;
        border-top: 1px solid #e5e7eb;
        width: 100%;
        overflow: visible;
    }
    .results-section.active {
        display: block;
        animation: fadeInUp 0.5s ease;
    }
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
<div class="main-container bg-light py-5">
    <div class="container">
        <!-- Logo Section -->
        <div class="text-center mb-5">
            <img src="<?= base_url('smeclabs.png') ?>" alt="SMEC Labs Logo" class="img-fluid" style="max-height: 80px;">
        </div>

        <!-- Search Section -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
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
                                                <th class="py-3 px-4 text-uppercase small fw-bold text-muted">Course</th>
                                                <th class="py-3 px-4 text-uppercase small fw-bold text-muted">Issue Date</th>
                                                <th class="py-3 px-4 text-uppercase small fw-bold text-muted">Status</th>
                                                <th class="py-3 px-4 text-uppercase small fw-bold text-muted text-center">Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($certificates as $cert): ?>
                                            <tr>
                                                <td class="px-4 py-3 fw-semibold"><?= esc($cert['certificate_no']) ?></td>
                                                <td class="px-4 py-3"><?= esc($cert['course']) ?></td>
                                                <td class="px-4 py-3"><?= date('d M Y', strtotime($cert['date_of_issue'])) ?></td>
                                                <td class="px-4 py-3">
                                                    <span class="certificate-badge badge bg-<?= $cert['status'] === 'Verified' ? 'success' : ($cert['status'] === 'Pending' ? 'warning' : 'danger') ?> rounded-pill px-3">
                                                        <i class="fas fa-<?= $cert['status'] === 'Verified' ? 'check-circle' : ($cert['status'] === 'Pending' ? 'clock' : 'times-circle') ?> me-1"></i>
                                                        <?= esc($cert['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" class="btn btn-sm btn-primary view-details" 
                                                            data-cert-id="<?= $cert['id'] ?>" data-bs-toggle="modal" data-bs-target="#certDetailsModal">
                                                        <i class="fas fa-eye me-1"></i> View Details
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

<!-- Certificate Details Modal -->
<div class="modal fade" id="certDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-certificate me-2"></i>
                    Certificate Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="certDetails">
                    <!-- Details will be loaded here -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
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

    // Handle certificate details view
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-details')) {
            const btn = e.target.closest('.view-details');
            const certId = btn.getAttribute('data-cert-id');
            const detailsContainer = document.getElementById('certDetails');

            fetch(`<?= site_url('certificate/details/') ?>${certId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cert = data.data;
                    const statusClass = cert.status === 'Verified' ? 'success' : 
                                      (cert.status === 'Pending' ? 'warning' : 'danger');
                    const statusIcon = cert.status === 'Verified' ? 'check-circle' : 
                                     (cert.status === 'Pending' ? 'clock' : 'times-circle');
                    
                    detailsContainer.innerHTML = `
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-primary mb-3">${cert.course}</h4>
                            <span class="badge bg-${statusClass} rounded-pill px-4 py-2">
                                <i class="fas fa-${statusIcon} me-2"></i>${cert.status}
                            </span>
                        </div>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0 py-3">
                                <div class="text-muted small text-uppercase">Certificate Number</div>
                                <div class="fw-semibold">${cert.certificate_no}</div>
                            </div>
                            <div class="list-group-item px-0 py-3">
                                <div class="text-muted small text-uppercase">Student Name</div>
                                <div class="fw-semibold">${cert.student_name}</div>
                            </div>
                            <div class="list-group-item px-0 py-3">
                                <div class="text-muted small text-uppercase">Start Date</div>
                                <div>${new Date(cert.start_date).toLocaleDateString('en-US', { 
                                    day: 'numeric', 
                                    month: 'long', 
                                    year: 'numeric' 
                                })}</div>
                            </div>
                            <div class="list-group-item px-0 py-3">
                                <div class="text-muted small text-uppercase">End Date</div>
                                <div>${new Date(cert.end_date).toLocaleDateString('en-US', { 
                                    day: 'numeric', 
                                    month: 'long', 
                                    year: 'numeric' 
                                })}</div>
                            </div>
                            <div class="list-group-item px-0 py-3">
                                <div class="text-muted small text-uppercase">Issue Date</div>
                                <div>${new Date(cert.date_of_issue).toLocaleDateString('en-US', { 
                                    day: 'numeric', 
                                    month: 'long', 
                                    year: 'numeric' 
                                })}</div>
                            </div>
                        </div>
                        ${cert.status === 'Verified' ? `
                            <div class="alert alert-success mt-4 mb-0">
                                <i class="fas fa-shield-alt me-2"></i>
                                This certificate has been verified and is authentic.
                            </div>
                        ` : ''}
                    `;
                } else {
                    throw new Error('Failed to load certificate details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                detailsContainer.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        An error occurred while loading the certificate details. Please try again.
                    </div>
                `;
            });
        }
    });
});
</script>
<?= $this->endSection() ?>