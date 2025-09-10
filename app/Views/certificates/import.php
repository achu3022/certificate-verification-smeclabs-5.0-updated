<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Import Certificates</h3>
        </div>
        <div class="card-body">
            <?php if (session()->has('success')): ?>
                <div class="alert alert-success">
                    <?= session('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger">
                    <?= session('error') ?>
                </div>
            <?php endif; ?>

            <?php $validation = \Config\Services::validation(); ?>
            
            <?php if (session()->has('skipped')): ?>
                <div class="alert alert-warning">
                    <h5>Skipped Rows:</h5>
                    <ul class="mb-0">
                        <?php foreach (session('skipped') as $message): ?>
                            <li><?= esc($message) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Upload Excel File</h5>
                            <p class="text-muted">
                                Upload an Excel file containing certificate information. 
                                <a href="<?= base_url('sample_certificate_import.xlsx') ?>" download>Download sample file</a>.
                            </p>
                            
                            <?= form_open_multipart('certificate/import', ['id' => 'importForm']) ?>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="excel_file" name="excel_file" accept=".xlsx, .xls, .csv" required>
                                        <label class="custom-file-label" for="excel_file">Choose Excel file</label>
                                        <?php if ($validation->hasError('excel_file')): ?>
                                            <div class="invalid-feedback d-block">
                                                <?= $validation->getError('excel_file') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    <i class="fas fa-upload mr-1"></i> Upload and Import
                                </button>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">File Format</h5>
                            <p class="card-text">Your Excel file should have the following columns in order:</p>
                            <ol class="pl-3">
                                <li>Certificate No (Required)</li>
                                <li>Admission No (Required)</li>
                                <li>Course</li>
                                <li>Student Name (Required)</li>
                                <li>Start Date (YYYY-MM-DD)</li>
                                <li>End Date (YYYY-MM-DD)</li>
                                <li>Date of Issue (YYYY-MM-DD)</li>
                                <li>Status (Pending/Verified/Rejected)</li>
                            </ol>
                            <p class="text-muted small">
                                <strong>Note:</strong> The first row will be treated as header and skipped.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add file name display script -->
<script>
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = document.getElementById("excel_file").files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});
</script>
<?= $this->endSection() ?>
