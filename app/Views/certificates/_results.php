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
                <tr class="fade-in">
                    <td class="px-4 py-3 fw-semibold"><?= esc($cert['certificate_no']) ?></td>
                    <td class="px-4 py-3"><?= esc($cert['course']) ?></td>
                    <td class="px-4 py-3"><?= date('d M Y', strtotime($cert['date_of_issue'])) ?></td>
                    <td class="px-4 py-3">
                        <span class="badge bg-<?= $cert['status'] === 'Verified' ? 'success' : ($cert['status'] === 'Pending' ? 'warning' : 'danger') ?> rounded-pill px-3">
                            <i class="fas fa-<?= $cert['status'] === 'Verified' ? 'check-circle' : ($cert['status'] === 'Pending' ? 'clock' : 'times-circle') ?> me-1"></i>
                            <?= esc($cert['status']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill view-details" 
                                data-cert-id="<?= $cert['id'] ?>" data-bs-toggle="modal" data-bs-target="#certDetailsModal">
                            <i class="fas fa-eye me-1"></i> View
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