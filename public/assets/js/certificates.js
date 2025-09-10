class CertificateManager {
    constructor() {
        this.baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Single event listener for all certificate actions
        document.addEventListener('click', (e) => {
            const button = e.target.closest('.certificate-action');
            if (!button) return;

            const action = button.dataset.action;
            const certificateId = button.dataset.certificateId;
            
            switch(action) {
                case 'approve':
                    this.confirmAction('approve', certificateId, 'Are you sure you want to approve this certificate?');
                    break;
                case 'reject':
                    this.confirmAction('reject', certificateId, 'Are you sure you want to reject this certificate?');
                    break;
                case 'delete':
                    this.confirmAction('delete', certificateId, 'Are you sure you want to delete this certificate? This cannot be undone.');
                    break;
                case 'edit':
                    this.loadEditForm(button.dataset);
                    break;
            }
        });

        // Handle form submission
        const editForm = document.getElementById('editCertificateForm');
        if (editForm) {
            editForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(e.target);
            });
        }
    }

    confirmAction(action, certificateId, message) {
        if (confirm(message)) {
            const method = action === 'delete' ? 'delete' : 'updateStatus';
            const data = action === 'delete' 
                ? { id: certificateId }
                : { id: certificateId, status: action === 'approve' ? 'Verified' : 'Rejected' };
            
            this.sendRequest(method, data);
        }
    }

    loadEditForm(data) {
        // Populate the edit form with data from data attributes
        document.getElementById('editId').value = data.certificateId;
        document.getElementById('editCertificateNo').value = data.certificateNo;
        document.getElementById('editStudentName').value = data.studentName;
        document.getElementById('editCourse').value = data.course;
        document.getElementById('editDateOfIssue').value = data.date.split(' ')[0];
        
        const statusField = document.getElementById('editStatus');
        if (statusField) {
            statusField.value = data.status;
        }

        // Show the modal
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    }

    handleFormSubmit(form) {
        const formData = new FormData(form);
        this.sendRequest('update', formData);
    }

    async sendRequest(action, data) {
        try {
            const formData = new FormData();
            if (data instanceof FormData) {
                formData = data;
            } else {
                for (const [key, value] of Object.entries(data)) {
                    formData.append(key, value);
                }
            }
            formData.append('csrf_token', this.csrfToken);

            const response = await fetch(`${this.baseUrl}/admin/certificate/${action}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                this.showAlert('success', result.message || 'Action completed successfully');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showAlert('danger', result.message || 'Action failed');
            }
        } catch (error) {
            console.error('Request failed:', error);
            this.showAlert('danger', 'An error occurred while processing your request');
        }
    }

    showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        const existingAlerts = document.querySelectorAll('.alert-dismissible');
        existingAlerts.forEach(alert => alert.remove());
        
        document.querySelector('.content-wrapper').insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const newAlert = document.querySelector('.alert-dismissible');
            if (newAlert) {
                newAlert.remove();
            }
        }, 5000);
    }
}

// Initialize the certificate manager when the DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new CertificateManager();
});
