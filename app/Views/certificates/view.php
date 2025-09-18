<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Certificate Details - SMEC Labs<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-certificate me-2"></i>
                        Certificate Verification Details
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Certificate Information</h5>
                            <dl class="row">
                                <dt class="col-sm-4">Certificate No:</dt>
                                <dd class="col-sm-8"><?= esc($certificate['certificate_no']) ?></dd>
                                
                                <dt class="col-sm-4">Student Name:</dt>
                                <dd class="col-sm-8"><?= esc($certificate['student_name']) ?></dd>
                                
                                <dt class="col-sm-4">Course:</dt>
                                <dd class="col-sm-8"><?= esc($certificate['course']) ?></dd>
                                
                                <dt class="col-sm-4">Date of Issue:</dt>
                                <dd class="col-sm-8"><?= date('d M Y', strtotime($certificate['date_of_issue'])) ?></dd>
                                
                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-<?= $certificate['status'] === 'Verified' ? 'success' : ($certificate['status'] === 'Rejected' ? 'danger' : 'warning') ?>">
                                        <?= $certificate['status'] ?: 'Pending' ?>
                                    </span>
                                </dd>
                            </dl>
                        </div>
                        
                        <?php if (!empty($verification)): ?>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Verification Details</h5>
                            <dl class="row">
                                <dt class="col-sm-4">Verified By:</dt>
                                <dd class="col-sm-8"><?= esc($verification['name']) ?></dd>
                                
                                <dt class="col-sm-4">Designation:</dt>
                                <dd class="col-sm-8"><?= esc($verification['designation']) ?></dd>
                                
                                <dt class="col-sm-4">Company:</dt>
                                <dd class="col-sm-8"><?= esc($verification['company_name']) ?></dd>
                                
                                <dt class="col-sm-4">Contact:</dt>
                                <dd class="col-sm-8"><?= esc($verification['contact_no']) ?></dd>
                                
                                <dt class="col-sm-4">Country:</dt>
                                <dd class="col-sm-8"><?= esc($verification['country']) ?></dd>
                                
                                <dt class="col-sm-4">Verified On:</dt>
                                <dd class="col-sm-8"><?= date('d M Y H:i:s', strtotime($verification['created_at'])) ?></dd>
                            </dl>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="<?= site_url() ?>" class="btn btn-primary me-2">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-primary">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
        }
        .no-print {
            display: none !important;
        }
    }
    
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header {
        border-bottom: none;
    }
    
    dt {
        font-weight: 600;
        color: #495057;
    }
    
    .border-bottom {
        border-bottom: 2px solid #e9ecef !important;
    }
</style>
<?= $this->endSection() ?>
