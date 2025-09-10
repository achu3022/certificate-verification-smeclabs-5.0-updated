<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CertificateModel;
use App\Models\AdminModel;
use App\Models\SearchLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends BaseController
{
    protected $certificateModel;
    protected $adminModel;
    protected $searchLogModel;
    protected $activityModel;

    public function __construct()
    {
        $this->certificateModel = new CertificateModel();
        $this->adminModel = new AdminModel();
        $this->searchLogModel = new SearchLogModel();
        $this->activityModel = new \App\Models\AdminActivityModel();
    }

    protected function verifyCSRF()
    {
        try {
            // Get the security config
            $security = config('Security');
            
            // Get token name from config
            $tokenName = $security->tokenName ?? 'csrf_test_name';
            
            // Get all possible CSRF token sources
            $csrfHeader = $this->request->getHeaderLine('X-CSRF-TOKEN');
            $csrfPost = $this->request->getPost($tokenName);
            $csrfJson = null;
            
            // Try to get token from JSON body
            if (strpos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false) {
                try {
                    $body = $this->request->getBody();
                    if ($body) {
                        $jsonData = json_decode($body, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                            $csrfJson = $jsonData[$tokenName] ?? null;
                        } else {
                            log_message('debug', 'JSON decode error: ' . json_last_error_msg());
                        }
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Error parsing JSON body: ' . $e->getMessage());
                }
            }
            
            // Log token sources for debugging
            log_message('debug', 'CSRF Sources - Header: ' . ($csrfHeader ?: 'none') .
                               ', Post: ' . ($csrfPost ?: 'none') .
                               ', JSON: ' . ($csrfJson ?: 'none') .
                               ', Current Hash: ' . csrf_hash());
            
            // Check all possible token sources against current hash
            $currentHash = csrf_hash();
            $csrfValid = ($csrfHeader && hash_equals($currentHash, $csrfHeader)) ||
                        ($csrfPost && hash_equals($currentHash, $csrfPost)) ||
                        ($csrfJson && hash_equals($currentHash, $csrfJson));
            
            if (!$csrfValid) {
                log_message('error', 'CSRF verification failed for request: ' . $this->request->getUri());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid security token. Please refresh the page and try again.',
                    'csrf_hash' => csrf_hash() // Send new token
                ])->setStatusCode(403);
            }
            
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in CSRF verification: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while verifying the security token.',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(403);
        }
    }

    public function dashboard()
    {
        $admin = $this->adminModel->find(session()->get('id'));
        
        $data = [
            'admin' => $admin,
            'total_certificates' => $this->certificateModel->countAll(),
            'verified_certificates' => $this->certificateModel->where('status', 'Verified')->countAllResults(),
            'pending_certificates' => $this->certificateModel->where('status', 'Pending')->countAllResults(),
            'recent_certificates' => $this->certificateModel->orderBy('created_at', 'DESC')->limit(10)->find(),
            'recent_searches' => $this->searchLogModel->orderBy('created_at', 'DESC')->limit(10)->find(),
        ];

        if (session()->get('role') === 'super_admin') {
            $data['total_admins'] = $this->adminModel->where('role', 'admin')->countAllResults();
        }

        return view('admin/dashboard', $data);
    }

    public function listAdmins()
    {
        $data['admins'] = $this->adminModel->where('role', 'admin')->findAll();
        return view('admin/admins/list', $data);
    }
    
    public function certificates()
    {
        $request = service('request');
        
        // If this is an AJAX request, return JSON for dynamic filtering
        if ($request->isAJAX()) {
            return $this->filterCertificatesAjax();
        }
        
        // Get filter parameters
        $filters = [
            'search' => $request->getGet('search'),
            'status' => $request->getGet('status'),
            'certificate_no' => $request->getGet('certificate_no'),
            'admission_no' => $request->getGet('admission_no'),
            'course' => $request->getGet('course'),
            'date_from' => $request->getGet('date_from'),
            'date_to' => $request->getGet('date_to'),
            'per_page' => $request->getGet('per_page') ?? 10,
        ];
        
        // Validate and set per_page
        $allowedPerPage = [10, 25, 50, 100];
        $perPage = in_array($filters['per_page'], $allowedPerPage) ? (int)$filters['per_page'] : 10;
        
        // Build the query
        $builder = $this->certificateModel;
        
        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('certificate_no', $filters['search'])
                ->orLike('admission_no', $filters['search'])
                ->orLike('student_name', $filters['search'])
                ->orLike('course', $filters['search'])
                ->groupEnd();
        }
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        if (!empty($filters['certificate_no'])) {
            $builder->like('certificate_no', $filters['certificate_no']);
        }
        
        if (!empty($filters['admission_no'])) {
            $builder->like('admission_no', $filters['admission_no']);
        }
        
        if (!empty($filters['course'])) {
            $builder->like('course', $filters['course']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('date_of_issue >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('date_of_issue <=', $filters['date_to']);
        }
        
        // Get paginated results
        $certificates = $builder->orderBy('created_at', 'DESC')->paginate($perPage);
        $pager = $this->certificateModel->pager;
        
        $data = [
            'certificates' => $certificates,
            'pager' => $pager,
            'filters' => $filters,
            'perPageOptions' => $allowedPerPage
        ];
        
        return view('admin/certificates/list', $data);
    }

    public function createAdmin()
    {
        return view('admin/admins/create');
    }

    public function storeAdmin()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[admins.email]',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->adminModel->insert([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'admin',
            'status' => 'active'
        ]);

        return redirect()->to('admin/admins')->with('success', 'Admin created successfully');
    }

    public function editAdmin($id)
    {
        if (session()->get('role') !== 'super_admin') {
            return redirect()->to('admin/admins')->with('error', 'Access denied');
        }

        $admin = $this->adminModel->find($id);
        if (!$admin || $admin['role'] === 'super_admin') {
            return redirect()->to('admin/admins')->with('error', 'Admin not found or cannot be edited');
        }

        $data = [
            'admin' => $admin
        ];

        return view('admin/admins/edit', $data);
    }

    public function updateAdmin($id)
    {
        if (session()->get('role') !== 'super_admin') {
            return redirect()->to('admin/admins')->with('error', 'Access denied');
        }

        $admin = $this->adminModel->find($id);
        if (!$admin || $admin['role'] === 'super_admin') {
            return redirect()->to('admin/admins')->with('error', 'Admin not found or cannot be updated');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[admins.email,id,' . $id . ']'
        ];

        $postData = $this->request->getPost();

        if (isset($postData['password']) && !empty($postData['password'])) {
            $rules['password'] = 'required|min_length[8]';
            $rules['confirm_password'] = 'required|matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ];

        if (!empty($this->request->getPost('password'))) {
            $updateData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->adminModel->update($id, $updateData);

        // Log the activity
        $this->activityModel->logActivity(
            session()->get('id'),
            'update_admin',
            'Updated admin: ' . $updateData['name']
        );

        return redirect()->to('admin/admins')->with('success', 'Admin updated successfully');
    }

    public function deleteAdmin($id)
    {
        $admin = $this->adminModel->find($id);
        if (!$admin || $admin['role'] === 'super_admin') {
            return redirect()->back()->with('error', 'Admin not found or cannot be deleted');
        }

        $this->adminModel->delete($id);
        return redirect()->back()->with('success', 'Admin deleted successfully');
    }

    public function activateAdmin($id)
    {
        $admin = $this->adminModel->find($id);
        if (!$admin || $admin['role'] === 'super_admin') {
            return redirect()->back()->with('error', 'Admin not found or cannot be modified');
        }

        $this->adminModel->update($id, ['status' => 'active']);
        return redirect()->back()->with('success', 'Admin activated successfully');
    }

    public function deactivateAdmin($id)
    {
        $admin = $this->adminModel->find($id);
        if (!$admin || $admin['role'] === 'super_admin') {
            return redirect()->back()->with('error', 'Admin not found or cannot be modified');
        }

        $this->adminModel->update($id, ['status' => 'inactive']);
        return redirect()->back()->with('success', 'Admin deactivated successfully');
    }

    public function createSingleCertificate()
    {
        if ($csrf = $this->verifyCSRF()) {
            if ($csrf !== true) return $csrf;
        }

        // Note: Route enforces POST; do not re-check here to avoid false 405s on some clients

        // Load the certificate model
        $certificateModel = new \App\Models\CertificateModel();
        
        // Define validation rules
        $rules = [
            'certificate_no' => 'required|max_length[50]|is_unique[certificates.certificate_no]',
            'admission_no'   => 'required|max_length[50]|is_unique[certificates.admission_no]',
            'student_name'   => 'required|min_length[2]|max_length[100]',
            'course'         => 'required|min_length[2]|max_length[100]',
            'start_date'     => 'permit_empty|valid_date[Y-m-d]',
            'end_date'       => 'permit_empty|valid_date[Y-m-d]',
            'date_of_issue'  => 'required|valid_date[Y-m-d]',
        ];

        // Add status validation for super admin
        if (session()->get('role') === 'super_admin') {
            $rules['status'] = 'required|in_list[Pending,Verified,Rejected]';
        }

        // Run validation
        if (!$this->validate($rules)) {
            $validationErrors = $this->validator->getErrors();
            $errorMsg = 'Validation failed: ' . implode(', ', $validationErrors);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg,
                    'errors' => $validationErrors,
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(400);
            }
            return redirect()->back()
                ->with('validation', $this->validator)
                ->withInput();
        }

        // Prepare data for insertion
        $data = [
            'certificate_no' => trim($this->request->getPost('certificate_no')),
            'admission_no'   => trim($this->request->getPost('admission_no')),
            'student_name'   => trim($this->request->getPost('student_name')),
            'course'         => trim($this->request->getPost('course')),
            'start_date'     => $this->request->getPost('start_date') ?: null,
            'end_date'       => $this->request->getPost('end_date') ?: null,
            'date_of_issue'  => $this->request->getPost('date_of_issue'),
            'status'         => $this->request->getPost('status') ?? 'Pending',
        ];

        try {
            // Insert the certificate
            $saved = $certificateModel->insert($data);
            
            if ($saved === false) {
                $errors = $certificateModel->errors();
                log_message('error', 'Failed to save certificate. Errors: ' . print_r($errors, true));
                $errorMsg = 'Failed to save certificate: ' . implode(' ', $errors);
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $errorMsg,
                        'csrf_hash' => csrf_hash()
                    ])->setStatusCode(500);
                }
                return redirect()->back()
                    ->with('error', $errorMsg)
                    ->withInput();
            }

            // Get the inserted certificate ID
            $certificateId = $certificateModel->getInsertID();
            
            // Log the activity (if activity logging is enabled and model exists)
            try {
                if (class_exists('App\Models\ActivityLogModel')) {
                    $activityModel = new \App\Models\ActivityLogModel();
                    $activityData = [
                        'admin_id'   => session()->get('id'),
                        'action'     => 'Created certificate: ' . $data['certificate_no'],
                        'ip_address' => $this->request->getIPAddress(),
                        'user_agent' => $this->request->getUserAgent()->getAgentString(),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $activityModel->insert($activityData);
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the certificate creation
                log_message('error', 'Failed to log activity: ' . $e->getMessage());
            }
            
            log_message('info', 'Certificate created successfully. ID: ' . $certificateId);
            
            // Set success message and redirect
            $successMsg = 'Certificate added successfully';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $successMsg,
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return redirect()->to('admin/certificates')
                ->with('success', $successMsg);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in createSingleCertificate: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            $errorMsg = 'An error occurred while saving the certificate: ' . $e->getMessage();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg,
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(500);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMsg);
        }
    }
    /**
     * Handle AJAX requests for filtering certificates in admin
     */
    protected function filterCertificatesAjax()
    {
        $request = service('request');

        // Get filter parameters
        $filters = [
            'search' => $request->getGet('search'),
            'status' => $request->getGet('status'),
            'certificate_no' => $request->getGet('certificate_no'),
            'admission_no' => $request->getGet('admission_no'),
            'course' => $request->getGet('course'),
            'date_from' => $request->getGet('date_from'),
            'date_to' => $request->getGet('date_to'),
            'per_page' => $request->getGet('per_page') ?? 10,
        ];

        // Build the query
        $builder = $this->certificateModel;

        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('certificate_no', $filters['search'])
                ->orLike('admission_no', $filters['search'])
                ->orLike('student_name', $filters['search'])
                ->orLike('course', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        if (!empty($filters['certificate_no'])) {
            $builder->like('certificate_no', $filters['certificate_no']);
        }

        if (!empty($filters['admission_no'])) {
            $builder->like('admission_no', $filters['admission_no']);
        }

        if (!empty($filters['course'])) {
            $builder->like('course', $filters['course']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('date_of_issue >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('date_of_issue <=', $filters['date_to']);
        }

        // Get paginated results
        $allowedPerPage = [10, 25, 50, 100, 500];
        $perPage = in_array((int)$filters['per_page'], $allowedPerPage) ? (int)$filters['per_page'] : 10;
        $currentPage = (int)($request->getGet('page') ?? 1);
        $certificates = $builder->orderBy('created_at', 'DESC')->paginate($perPage, 'default', $currentPage);
        $pager = $this->certificateModel->pager;

        // Generate table body HTML
        $tableBody = '';
        $counter = 1;
        if (isset($pager) && $pager->getCurrentPage() > 1) {
            $counter = (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1;
        }

        foreach ($certificates as $cert) {
            $tableBody .= '<tr>';
            $tableBody .= '<td class="px-3 text-muted">' . $counter++ . '</td>';
            $tableBody .= '<td class="px-4">' . esc($cert['certificate_no']) . '</td>';
            $tableBody .= '<td class="text-muted">' . esc($cert['admission_no']) . '</td>';
            $tableBody .= '<td>' . esc($cert['student_name']) . '</td>';
            $tableBody .= '<td>' . esc($cert['course']) . '</td>';
            $tableBody .= '<td>' . date('d M Y', strtotime($cert['date_of_issue'])) . '</td>';
            $tableBody .= '<td><span class="status-badge bg-' . ($cert['status'] === 'Verified' ? 'success' : ($cert['status'] === 'Pending' ? 'warning' : 'danger')) . '">' . esc($cert['status']) . '</span></td>';
            $tableBody .= '<td class="text-end px-4">';

            // Action buttons
            $tableBody .= '<div class="btn-group">';

            if (session()->get('role') === 'super_admin') {
                if ($cert['status'] === 'Pending') {
                    $tableBody .= '<button type="button" class="btn btn-sm btn-outline-success approve-certificate" data-id="' . $cert['id'] . '">Approve</button>';
                    $tableBody .= '<button type="button" class="btn btn-sm btn-outline-danger reject-certificate" data-id="' . $cert['id'] . '">Reject</button>';
                }
                $tableBody .= '<button type="button" class="btn btn-sm btn-outline-primary edit-certificate" data-bs-toggle="modal" data-bs-target="#editModal" data-id="' . $cert['id'] . '">Edit</button>';
                $tableBody .= '<button type="button" class="btn btn-sm btn-outline-danger delete-certificate" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="' . $cert['id'] . '">Delete</button>';
            } elseif (session()->get('role') === 'admin') {
                $tableBody .= '<button type="button" class="btn btn-sm btn-outline-primary edit-certificate" data-bs-toggle="modal" data-bs-target="#editModal" data-id="' . $cert['id'] . '">Edit</button>';
                $tableBody .= '<button type="button" class="btn btn-sm btn-outline-danger delete-certificate" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="' . $cert['id'] . '">Delete</button>';
            }

            $tableBody .= '</div>';
            $tableBody .= '</td>';
            $tableBody .= '</tr>';
        }

        // Prepare the response
        $response = [
            'success' => true,
            'table_body' => $tableBody,
            'pagination' => $pager->links(),
            'result_count' => 'Showing ' . count($certificates) . ' of ' . $pager->getTotal() . ' results',
        ];

        return $this->response->setJSON($response);
    }

    /**
     * Export filtered certificates to Excel
     */
    public function exportCertificates()
    {
        try {
            $request = service('request');

            // Get filter parameters (same as filtering)
            $filters = [
                'search' => $request->getGet('search'),
                'status' => $request->getGet('status'),
                'certificate_no' => $request->getGet('certificate_no'),
                'admission_no' => $request->getGet('admission_no'),
                'course' => $request->getGet('course'),
                'date_from' => $request->getGet('date_from'),
                'date_to' => $request->getGet('date_to'),
                'per_page' => $request->getGet('per_page') ?? 10,
            ];

            // Validate and set per_page
            $allowedPerPage = [10, 50, 100, 500];
            $perPage = in_array($filters['per_page'], $allowedPerPage) ? (int)$filters['per_page'] : 10;

            $currentPage = $request->getGet('page') ?? 1;

            // Build the query (same logic as filtering)
            $builder = $this->certificateModel;

            // Apply filters
            if (!empty($filters['search'])) {
                $builder->groupStart()
                    ->like('certificate_no', $filters['search'])
                    ->orLike('admission_no', $filters['search'])
                    ->orLike('student_name', $filters['search'])
                    ->orLike('course', $filters['course'])
                    ->groupEnd();
            }

            if (!empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }

            if (!empty($filters['certificate_no'])) {
                $builder->like('certificate_no', $filters['certificate_no']);
            }

            if (!empty($filters['admission_no'])) {
                $builder->like('admission_no', $filters['admission_no']);
            }

            if (!empty($filters['course'])) {
                $builder->like('course', $filters['course']);
            }

            if (!empty($filters['date_from'])) {
                $builder->where('date_of_issue >=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $builder->where('date_of_issue <=', $filters['date_to']);
            }

            // Get paginated results for export (respect per_page and current page)
            $certificates = $builder->paginate($perPage, 'default', $currentPage);

            // Create spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'Certificate No');
            $sheet->setCellValue('B1', 'Admission No');
            $sheet->setCellValue('C1', 'Student Name');
            $sheet->setCellValue('D1', 'Course');
            $sheet->setCellValue('E1', 'Date of Issue');
            $sheet->setCellValue('F1', 'Status');

            // Style the header row
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4e73df'],
                ],
            ];
            $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

            if (empty($certificates)) {
                // Add a message row if no data
                $sheet->setCellValue('A2', 'No certificates found matching the current filters');
            } else {
                // Add data rows
                $row = 2;
                foreach ($certificates as $cert) {
                    $sheet->setCellValue('A' . $row, $cert['certificate_no'] ?? '');
                    $sheet->setCellValue('B' . $row, $cert['admission_no'] ?? '');
                    $sheet->setCellValue('C' . $row, $cert['student_name'] ?? '');
                    $sheet->setCellValue('D' . $row, $cert['course'] ?? '');
                    $sheet->setCellValue('E' . $row, isset($cert['date_of_issue']) ? date('d M Y', strtotime($cert['date_of_issue'])) : '');
                    $sheet->setCellValue('F' . $row, $cert['status'] ?? '');
                    $row++;
                }
            }

            // Auto-size columns
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create filename with timestamp
            $filename = 'certificates_export_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Clear any previous output
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');

            // Create Excel writer and output
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function searchLogs()
    {
        $filters = [
            'search' => $this->request->getGet('search') ?? '',
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'per_page' => $this->request->getGet('per_page') ?? 10,
        ];

        // Validate and set per_page
        $allowedPerPage = [10, 50, 100, 500];
        $perPage = in_array($filters['per_page'], $allowedPerPage) ? (int)$filters['per_page'] : 10;

        $searchTerm = $filters['search'];
        $status = $filters['status'];
        $dateFrom = $filters['date_from'];
        $dateTo = $filters['date_to'];
        
        // Build the query
        $query = $this->searchLogModel->builder();
        
        // Apply filters
        if (!empty($searchTerm)) {
            $query->like('search_term', $searchTerm);
        }
        
        if ($status !== null && $status !== '') {
            $query->where('found', $status === 'found' ? 1 : 0);
        }
        
        if (!empty($dateFrom)) {
            $query->where('DATE(created_at) >=', date('Y-m-d', strtotime($dateFrom)));
        }
        
        if (!empty($dateTo)) {
            $query->where('DATE(created_at) <=', date('Y-m-d', strtotime($dateTo)));
        }
        
        // Get total count for pagination
        $total = $query->countAllResults(false);
        $page = (int)($this->request->getVar('page') ?? 1);
        $offset = ($page - 1) * $perPage;
        
        // Apply ordering and pagination
        $searches = $query->select('*')
                         ->orderBy('created_at', 'DESC')
                         ->get($perPage, $offset)
                         ->getResultArray();
        
        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        $data = [
            'searches' => $searches,
            'pager' => $pager,
            'searchTerm' => $searchTerm,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $total,
            'offset' => $offset,
            'perPage' => $perPage
        ];

        return view('admin/search_logs', $data);
    }

    public function exportSearchLogs()
    {
        try {
            $request = service('request');

            // Get filter parameters (same as searchLogs)
            $filters = [
                'search' => $request->getGet('search'),
                'status' => $request->getGet('status'),
                'date_from' => $request->getGet('date_from'),
                'date_to' => $request->getGet('date_to'),
            ];

            // Build the query (same logic as searchLogs)
            $builder = $this->searchLogModel;

            // Apply filters
            if (!empty($filters['search'])) {
                $builder->like('search_term', $filters['search']);
            }

            if (!empty($filters['status'])) {
                $builder->where('found', $filters['status'] === 'found' ? 1 : 0);
            }

            if (!empty($filters['date_from'])) {
                $builder->where('DATE(created_at) >=', date('Y-m-d', strtotime($filters['date_from'])));
            }

            if (!empty($filters['date_to'])) {
                $builder->where('DATE(created_at) <=', date('Y-m-d', strtotime($filters['date_to'])));
            }

            // Get all matching records (no pagination for export)
            $searches = $builder->orderBy('created_at', 'DESC')->findAll();

            // Create spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Search Term');
            $sheet->setCellValue('C1', 'IP Address');
            $sheet->setCellValue('D1', 'User Agent');
            $sheet->setCellValue('E1', 'Found');
            $sheet->setCellValue('F1', 'Created At');

            // Style the header row
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4e73df'],
                ],
            ];
            $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

            // Add data rows
            $row = 2;
            foreach ($searches as $search) {
                $sheet->setCellValue('A' . $row, $search['id'] ?? '');
                $sheet->setCellValue('B' . $row, $search['search_term'] ?? '');
                $sheet->setCellValue('C' . $row, $search['ip_address'] ?? '');
                $sheet->setCellValue('D' . $row, $search['user_agent'] ?? '');
                $sheet->setCellValue('E' . $row, ($search['found'] ?? 0) ? 'Yes' : 'No');
                $sheet->setCellValue('F' . $row, isset($search['created_at']) ? date('d M Y, H:i:s', strtotime($search['created_at'])) : '');
                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create filename with timestamp
            $filename = 'search_logs_export_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Clear any previous output
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');

            // Create Excel writer and output
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Export search logs error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
    
    public function deleteSearchLog($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ])->setStatusCode(405);
        }
        
        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Log ID is required'
            ])->setStatusCode(400);
        }
        
        try {
            $log = $this->searchLogModel->find($id);
            
            if (!$log) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Log not found'
                ])->setStatusCode(404);
            }
            
            // Delete the log
            $this->searchLogModel->delete($id);
            
            // Log the activity
            $this->activityModel->logActivity(
                session()->get('id'),
                'delete_search_log',
                'Deleted search log for: ' . $log['search_term']
            );
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Log deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to delete search log: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete log: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function profile()
    {
        $id = session()->get('id');
        $data = [
            'admin' => $this->adminModel->find($id),
            'activities' => $this->activityModel->getRecentActivities($id, 5)
        ];

        return view('admin/profile', $data);
    }

    public function updateProfileInfo()
    {
        $id = session()->get('id');
        $admin = $this->adminModel->find($id);

        if (!$admin) {
            return redirect()->back()->with('profile_errors', ['Administrator not found']);
        }

        $rules = [
            'name' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('profile_errors', $this->validator->getErrors());
        }

        $updateData = [
            'name' => $this->request->getPost('name')
        ];

        $this->adminModel->update($id, $updateData);
        
        // Log the activity
        $this->activityModel->logActivity(
            $id,
            'profile_update',
            'Updated profile information'
        );
        
        return redirect()->back()->with('profile_success', 'Profile information updated successfully');
    }

    public function updatePassword()
    {
        $id = session()->get('id');
        $admin = $this->adminModel->find($id);

        if (!$admin) {
            return redirect()->back()->with('password_errors', ['Administrator not found']);
        }

        $rules = [
            'current_password' => 'required|verify_password[' . $admin['password'] . ']',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('password_errors', $this->validator->getErrors());
        }

        $updateData = [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)
        ];

        $this->adminModel->update($id, $updateData);

        // Log the activity
        $this->activityModel->logActivity(
            $id,
            'password_update',
            'Changed account password'
        );
        
        return redirect()->back()->with('password_success', 'Password updated successfully');
    }
}
