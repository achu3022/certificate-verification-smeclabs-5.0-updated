<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CertificateModel;
use App\Models\SearchLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class Certificate extends BaseController
{
    protected $certificateModel;
    protected $searchLogModel;

    public function __construct()
    {
        $this->certificateModel = new CertificateModel();
        $this->searchLogModel = new SearchLogModel();
        helper(['form', 'url']);
    }

    public function createSingle()
    {
        if ($csrf = $this->verifyCSRF()) {
            if ($csrf !== true) return $csrf;
        }

        $rules = [
            'certificate_no' => 'required|is_unique[certificates.certificate_no]',
            'admission_no' => 'required',
            'student_name' => 'required|min_length[2]',
            'course' => 'required|min_length[2]',
            'start_date' => 'permit_empty|valid_date[Y-m-d]',
            'end_date' => 'permit_empty|valid_date[Y-m-d]',
            'date_of_issue' => 'required|valid_date[Y-m-d]',
        ];

        if (session()->get('role') === 'super_admin') {
            $rules['status'] = 'required|in_list[Pending,Verified,Rejected]';
        }

        if (!$this->validate($rules)) {
            $validationErrors = $this->validator->getErrors();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validationErrors,
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(400);
            } else {
                $errorMsg = 'Validation failed: ' . implode(', ', $validationErrors);
                session()->setFlashdata('error', $errorMsg);
                return redirect()->back();
            }
        }

        $data = [
            'certificate_no' => $this->request->getPost('certificate_no'),
            'admission_no' => $this->request->getPost('admission_no'),
            'student_name' => $this->request->getPost('student_name'),
            'course' => $this->request->getPost('course'),
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
            'date_of_issue' => $this->request->getPost('date_of_issue'),
            'status' => session()->get('role') === 'super_admin' ? $this->request->getPost('status') : 'Pending',
        ];

        if ($this->certificateModel->insert($data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Certificate created successfully',
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                session()->setFlashdata('success', 'Certificate created successfully');
                return redirect()->to('/admin/certificates');
            }
        } else {
            $errorMsg = 'Failed to create certificate';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg,
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(500);
            } else {
                session()->setFlashdata('error', $errorMsg);
                return redirect()->back();
            }
        }
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

    public function index()
    {
        $request = service('request');
        
        // If it's an AJAX request, return JSON response
        if ($request->isAJAX()) {
            return $this->filterCertificates();
        }
        
        // Check if there's a search term in the URL
        $searchTerm = $request->getGet('search');
        $admissionNo = $request->getGet('admission_no');
        
        $data = [
            'certificates' => [],
            'pager' => null,
            'searchTerm' => $searchTerm,
            'admissionNo' => $admissionNo
        ];
        
        // Only fetch certificates if there's a search term or admission number
        if (!empty($searchTerm) || !empty($admissionNo)) {
            $builder = $this->certificateModel;
            
            if (!empty($searchTerm)) {
                $builder->groupStart()
                    ->like('certificate_no', $searchTerm)
                    ->orLike('admission_no', $searchTerm)
                    ->orLike('student_name', $searchTerm)
                    ->orLike('course', $searchTerm)
                    ->groupEnd();
            }
            
            if (!empty($admissionNo)) {
                $builder->like('admission_no', $admissionNo);
            }
            
            $data['certificates'] = $builder->paginate(10);
            $data['pager'] = $this->certificateModel->pager;
        }
        
        return view('certificates/search', $data);
    }
    
    /**
     * Handle AJAX requests for filtering certificates
     */
    protected function filterCertificates()
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
        $perPage = 10;
        $currentPage = $request->getGet('page') ?? 1;
        $certificates = $builder->paginate($perPage, 'default', $currentPage);
        $pager = $this->certificateModel->pager;
        
        // Prepare the response
        $response = [
            'success' => true,
            'table_body' => view_cell('App\Libraries\CertificateCell::generateTableBody', ['certificates' => $certificates]),
            'pagination' => $pager->links(),
            'result_count' => 'Showing ' . count($certificates) . ' of ' . $pager->getTotal() . ' results',
        ];
        
        return $this->response->setJSON($response);
    }
    
    /**
     * Get certificate by ID
     */
    public function get($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Certificate ID is required'
            ])->setStatusCode(400);
        }
        
        $certificate = $this->certificateModel->find($id);
        
        if (!$certificate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Certificate not found'
            ])->setStatusCode(404);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $certificate
        ]);
    }
    
    public function ajaxGetCertificate($id)
    {
        $certificate = $this->certificateModel->find($id);
        
        if (!$certificate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Certificate not found'
            ])->setStatusCode(404);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $certificate
        ]);
    }
    
    public function ajaxUpdateStatus()
    {
        if ($csrf = $this->verifyCSRF()) {
            if ($csrf !== true) return $csrf;
        }
        
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        
        if (!$id || !$status) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid request parameters'
                ])->setStatusCode(400);
            } else {
                session()->setFlashdata('error', 'Invalid request parameters');
                return redirect()->back();
            }
        }
        
        $updated = $this->certificateModel->update($id, ['status' => $status]);
        
        if ($updated) {
            // Log the activity
            log_message('info', "Certificate {$id} status updated to {$status}");
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Certificate status updated successfully'
                ]);
            } else {
                session()->setFlashdata('success', 'Certificate status updated successfully');
                return redirect()->to('/admin/certificates');
            }
        }
        
        $errorMsg = 'Failed to update certificate status';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMsg
            ])->setStatusCode(500);
        } else {
            session()->setFlashdata('error', $errorMsg);
            return redirect()->back();
        }
    }
    
    public function ajaxUpdateCertificate()
    {
        if ($csrf = $this->verifyCSRF()) {
            if ($csrf !== true) return $csrf;
        }
        
        $id = $this->request->getPost('id');
        $rules = [
            'certificate_no' => 'required|is_unique[certificates.certificate_no,id,' . $id . ']',
            'admission_no' => 'required',
            'student_name' => 'required|min_length[2]',
            'course' => 'required|min_length[2]',
            'start_date' => 'permit_empty|valid_date[Y-m-d]',
            'end_date' => 'permit_empty|valid_date[Y-m-d]',
            'date_of_issue' => 'required|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            $validationErrors = $this->validator->getErrors();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validationErrors
                ])->setStatusCode(400);
            } else {
                $errorMsg = 'Validation failed: ' . implode(', ', $validationErrors);
                session()->setFlashdata('error', $errorMsg);
                return redirect()->back();
            }
        }

        $data = [
            'certificate_no' => $this->request->getPost('certificate_no'),
            'admission_no' => $this->request->getPost('admission_no'),
            'student_name' => $this->request->getPost('student_name'),
            'course' => $this->request->getPost('course'),
            'start_date' => $this->request->getPost('start_date') ?: null,
            'end_date' => $this->request->getPost('end_date') ?: null,
            'date_of_issue' => $this->request->getPost('date_of_issue')
        ];
        
        if (!$id) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate ID is required'
                ])->setStatusCode(400);
            } else {
                session()->setFlashdata('error', 'Certificate ID is required');
                return redirect()->back();
            }
        }
        
        $updated = $this->certificateModel->update($id, $data);
        
        if ($updated) {
            log_message('info', "Certificate {$id} updated");
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Certificate updated successfully'
                ]);
            } else {
                session()->setFlashdata('success', 'Certificate updated successfully');
                return redirect()->to('/admin/certificates');
            }
        }
        
        $errorMsg = 'Failed to update certificate';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMsg
            ])->setStatusCode(500);
        } else {
            session()->setFlashdata('error', $errorMsg);
            return redirect()->back();
        }
    }
    
    public function ajaxDeleteCertificate()
    {
        if ($csrf = $this->verifyCSRF()) {
            if ($csrf !== true) return $csrf;
        }
        
        $id = $this->request->getPost('id');
        
        if (!$id) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate ID is required'
                ])->setStatusCode(400);
            } else {
                session()->setFlashdata('error', 'Certificate ID is required');
                return redirect()->back();
            }
        }
        
        $deleted = $this->certificateModel->delete($id);
        
        if ($deleted) {
            log_message('info', "Certificate {$id} deleted");
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Certificate deleted successfully'
                ]);
            } else {
                session()->setFlashdata('success', 'Certificate deleted successfully');
                return redirect()->to('/admin/certificates');
            }
        }
        
        $errorMsg = 'Failed to delete certificate';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMsg
            ])->setStatusCode(500);
        } else {
            session()->setFlashdata('error', $errorMsg);
            return redirect()->back();
        }
    }

    public function import()
    {
        // Set JSON response for AJAX requests
        $response = [
            'success' => false,
            'message' => 'Initial response',
            'errors' => []
        ];

        try {
            // Debug log the request
            $method = $this->request->getMethod();
            $headers = $this->request->getHeaders();
            
            log_message('debug', '=== IMPORT DEBUG ===');
            log_message('debug', 'Request Method: ' . $method);
            log_message('debug', 'Is AJAX: ' . ($this->request->isAJAX() ? 'Yes' : 'No'));
            log_message('debug', 'Headers: ' . print_r($headers, true));
            
            // Log specific headers we care about
            foreach (['x-requested-with', 'x-csrf-token', 'content-type', 'accept'] as $header) {
                $value = $this->request->getHeaderLine($header);
                log_message('debug', "Header {$header}: " . ($value ?: 'Not set'));
            }
            
            // Check if this is a POST request (case-insensitive check)
            if (strtoupper($method) !== 'POST') {
                log_message('error', 'Invalid request method: ' . $method);
                throw new \RuntimeException('Invalid request method. Expected POST, got ' . $method);
            }

            // Temporarily disabled for debugging
            // if (!$this->request->isAJAX()) {
            //     throw new \RuntimeException('This endpoint only accepts AJAX requests.');
            // }

            $file = $this->request->getFile('excel_file');

            if (!$file || !$file->isValid()) {
                throw new \RuntimeException($file ? $file->getErrorString() : 'No file uploaded');
            }
            
            $extension = strtolower($file->getClientExtension());
            if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
                throw new \RuntimeException('Invalid file format. Please upload Excel files only (xlsx, xls, csv).');
            }
            
            // Load the PhpSpreadsheet classes
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            
            if ($extension === 'xls') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } elseif ($extension === 'csv') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }
            
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            if (empty($rows)) {
                throw new \RuntimeException('The uploaded file is empty');
            }
            
            // Check if we should skip the header row
            $skipHeader = $this->request->getPost('skip_header') !== null;
            if ($skipHeader) {
                array_shift($rows);
            }
            
            $imported = 0;
            $skipped = [];
            
            foreach ($rows as $index => $row) {
                $rowNumber = $index + ($skipHeader ? 2 : 1);
                
                // Skip empty rows
                if (empty(array_filter($row, function($value) { 
                    return $value !== null && $value !== ''; 
                }))) {
                    $skipped[] = "Row $rowNumber: Empty row";
                    continue;
                }
                
                try {
                    // Prepare data for insertion
                    $data = [
                        'certificate_no' => trim($row[0] ?? ''),
                        'admission_no' => trim($row[1] ?? ''),
                        'course' => !empty($row[2]) ? trim($row[2]) : null,
                        'student_name' => trim($row[3] ?? ''),
                        'start_date' => !empty($row[4]) ? $this->parseDate($row[4]) : null,
                        'end_date' => !empty($row[5]) ? $this->parseDate($row[5]) : null,
                        'date_of_issue' => !empty($row[6]) ? $this->parseDate($row[6]) : date('Y-m-d'),
                        'status' => !empty($row[7]) ? ucfirst(strtolower(trim($row[7]))) : 'Pending'
                    ];
                    
                    // Validate required fields
                    if (empty($data['certificate_no']) || empty($data['admission_no']) || empty($data['student_name'])) {
                        $skipped[] = "Row $rowNumber: Missing required fields";
                        continue;
                    }
                    
                    // Check for duplicates
                    $exists = $this->certificateModel
                        ->where('certificate_no', $data['certificate_no'])
                        ->orWhere('admission_no', $data['admission_no'])
                        ->first();
                        
                    if ($exists) {
                        $skipped[] = "Row $rowNumber: Duplicate certificate or admission number";
                        continue;
                    }
                    
                    // Save the record
                    if ($this->certificateModel->save($data)) {
                        $imported++;
                    } else {
                        $skipped[] = "Row $rowNumber: Failed to save - " . implode(', ', $this->certificateModel->errors());
                    }
                    
                } catch (\Exception $e) {
                    $skipped[] = "Row $rowNumber: " . $e->getMessage();
                    continue;
                }
            }
            
            if ($imported === 0 && empty($skipped)) {
                throw new \RuntimeException('No valid data found to import');
            }
            
            $message = "Successfully imported $imported certificates.";
            if (!empty($skipped)) {
                $message .= ' ' . count($skipped) . ' row(s) were skipped.';
                $response['errors'] = $skipped;
            }
            
            // Set success message in session and redirect
            session()->setFlashdata('success', $message);
            
            // If this is an AJAX request, return JSON response
            if ($this->request->isAJAX()) {
                $response['success'] = true;
                $response['message'] = $message;
                return $this->response->setJSON($response);
            }
            
            // For regular form submission, redirect to certificates list
            return redirect()->to('/admin/certificates')->with('import_errors', $skipped);
            
        } catch (\Exception $e) {
            $errorMsg = 'Error: ' . $e->getMessage();
            log_message('error', 'Import Error: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            
            if ($this->request->isAJAX()) {
                $response['message'] = $errorMsg;
                return $this->response->setJSON($response);
            }
            
            return redirect()->back()->with('error', $errorMsg);
        }
    }
    
    /**
     * Parse date from various formats to Y-m-d
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return date('Y-m-d');
        }
        
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }
        
        if (is_numeric($date)) {
            // Handle Excel timestamp (days since 1900-01-01)
            $timestamp = ($date - 25569) * 86400; // Convert to Unix timestamp
            return date('Y-m-d', $timestamp);
        }
        
        // Try to parse the date string
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
        
        return date('Y-m-d');
    }
    
    /**
     * Get certificate details by ID
     */
    /**
     * Get certificate details by ID via AJAX
     */
    public function details($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Certificate ID is required',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(400);
        }

        try {
            $certificate = $this->certificateModel->find($id);
            
            if (!$certificate) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate not found',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(404);
            }
            
            // Format dates for frontend display
            $certificate['start_date'] = date('Y-m-d', strtotime($certificate['start_date']));
            $certificate['end_date'] = date('Y-m-d', strtotime($certificate['end_date']));
            $certificate['date_of_issue'] = date('Y-m-d', strtotime($certificate['date_of_issue']));

            return $this->response->setJSON([
                'success' => true,
                'data' => $certificate,
                'csrf_hash' => csrf_hash()
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error retrieving certificate details: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while retrieving certificate details',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update certificate details
     */
    public function update()
    {
        try {
            // Debug logging
            log_message('debug', 'Update request received');
            log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));
            log_message('debug', 'Headers: ' . print_r($this->request->headers(), true));
            
            // Verify CSRF token
            $csrfCheck = $this->verifyCSRF();
            if ($csrfCheck !== true) {
                return $csrfCheck;
            }
            
            // Check if user is logged in and has permission
            if (!session()->get('isLoggedIn')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(403);
            }

            // Get and validate ID
            $id = $this->request->getPost('id');
            if (!$id) {
                log_message('error', 'Certificate ID is missing from request');
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Certificate ID is required',
                        'csrf_hash' => csrf_hash()
                    ])->setStatusCode(400);
                } else {
                    session()->setFlashdata('error', 'Certificate ID is required');
                    return redirect()->back();
                }
            }
            
            // Check if certificate exists
            $certificate = $this->certificateModel->find($id);
            if (!$certificate) {
                log_message('error', "Certificate not found with ID: {$id}");
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Certificate not found',
                        'csrf_hash' => csrf_hash()
                    ])->setStatusCode(404);
                } else {
                    session()->setFlashdata('error', 'Certificate not found');
                    return redirect()->back();
                }
            }

            $rules = [
                'certificate_no' => 'required|is_unique[certificates.certificate_no,id,' . $id . ']',
                'admission_no' => 'required',
                'student_name' => 'required|min_length[2]',
                'course' => 'required|min_length[2]',
                'start_date' => 'permit_empty|valid_date[Y-m-d]',
                'end_date' => 'permit_empty|valid_date[Y-m-d]',
                'date_of_issue' => 'required|valid_date[Y-m-d]',
            ];

            // Only super admin can update status
            if (session()->get('role') === 'super_admin') {
                $rules['status'] = 'required|in_list[Pending,Verified,Rejected]';
            }

            if (!$this->validate($rules)) {
                $validationErrors = $this->validator->getErrors();
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validationErrors,
                        'csrf_hash' => csrf_hash()
                    ])->setStatusCode(400);
                } else {
                    $errorMsg = 'Validation failed: ' . implode(', ', $validationErrors);
                    session()->setFlashdata('error', $errorMsg);
                    return redirect()->back();
                }
            }

            // Get form data
            $data = [
                'certificate_no' => trim($this->request->getPost('certificate_no')),
                'admission_no' => trim($this->request->getPost('admission_no')),
                'student_name' => trim($this->request->getPost('student_name')),
                'course' => trim($this->request->getPost('course')),
                'start_date' => $this->request->getPost('start_date') ?: null,
                'end_date' => $this->request->getPost('end_date') ?: null,
                'date_of_issue' => $this->request->getPost('date_of_issue')
            ];

            // Debug log the data
            log_message('debug', 'Update data: ' . print_r($data, true));

            // Only super admin can update status
            if (session()->get('role') === 'super_admin') {
                $status = $this->request->getPost('status');
                if ($status) {
                    $data['status'] = $status;
                }
            }

            // Perform update
            if ($this->certificateModel->update($id, $data)) {
                log_message('info', "Certificate {$id} updated successfully");
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Certificate updated successfully',
                        'csrf_hash' => csrf_hash()
                    ]);
                } else {
                    session()->setFlashdata('success', 'Certificate updated successfully');
                    return redirect()->to('/admin/certificates');
                }
            }

            // If update failed, check for validation errors
            $errors = $this->certificateModel->errors();
            log_message('error', 'Update failed. Validation errors: ' . print_r($errors, true));
            $errorMsg = $errors ? implode(', ', $errors) : 'Failed to update certificate';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg,
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(500);
            } else {
                session()->setFlashdata('error', $errorMsg);
                return redirect()->back();
            }

        } catch (\Exception $e) {
            log_message('error', 'Error updating certificate: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'An error occurred while updating the certificate',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(500);
            } else {
                session()->setFlashdata('error', 'An error occurred while updating the certificate');
                return redirect()->back();
            }
        }
    }

    /**
     * Search certificates by admission number
     */
    public function search()
    {
        // Get search term from POST or GET
        $searchTerm = $this->request->getPost('admission_no') ?? $this->request->getGet('admission_no');
        
        // Initialize data array with default values
        $data = [
            'searchTerm' => $searchTerm,
            'certificates' => [],
            'message' => null
        ];
        
        if (!empty($searchTerm)) {
            try {
                // Search for certificates by admission number first
                $data['certificates'] = $this->certificateModel->where('admission_no', $searchTerm)
                                                             ->orderBy('id', 'DESC')
                                                             ->findAll();
                
                $found = !empty($data['certificates']);
                
                // Log the search with found status and current timestamp
                $db = \Config\Database::connect();
                $currentTime = date('Y-m-d H:i:s');
                $db->query("INSERT INTO search_logs (search_term, ip_address, user_agent, found, created_at) VALUES (?, ?, ?, ?, ?)", 
                    [
                        $searchTerm,
                        $this->request->getIPAddress(),
                        $this->request->getUserAgent()->getAgentString(),
                        $found ? 1 : 0,
                        $currentTime
                    ]
                );
                                                             
                if (!$found) {
                    $data['message'] = 'No certificates found for this admission number.';
                }
                
                // For AJAX requests, return partial view
                if ($this->request->isAJAX()) {
                    return view('certificates/_results', $data);
                }
                
            } catch (\Exception $e) {
                log_message('error', 'Certificate search error: ' . $e->getMessage());
                
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'An error occurred while searching for certificates.',
                        'csrf_hash' => csrf_hash()
                    ])->setStatusCode(500);
                }
                
                $data['message'] = 'An error occurred while searching for certificates.';
            }
        }
        
        // Regular request - return full view
        return view('certificates/search', $data);
    }
    
    /**
     * Update certificate status (Approve/Reject)
     */
    public function updateStatus()
    {
        try {
            // Verify CSRF token
            $csrfCheck = $this->verifyCSRF();
            if ($csrfCheck !== true) {
                return $csrfCheck;
            }
            
            // Debug logging
            log_message('debug', 'Update Status Request - POST data: ' . print_r($this->request->getPost(), true));
            log_message('debug', 'Update Status Request - Headers: ' . print_r($this->request->headers(), true));
            log_message('debug', 'Update Status Request - Session: ' . print_r(session()->get(), true));
            
            // Check if user is logged in and is a super admin
            if (!session()->get('isLoggedIn') || session()->get('role') !== 'super_admin') {
                log_message('error', 'Unauthorized access attempt - Role: ' . session()->get('role'));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(403);
            }
            
            $id = $this->request->getPost('id');
            $status = $this->request->getPost('status');
            
            // Validate input parameters
            if (empty($id) || empty($status)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Missing required parameters',
                        'csrf_hash' => csrf_hash()
                    ])->setStatusCode(400);
                } else {
                    session()->setFlashdata('error', 'Missing required parameters');
                    return redirect()->back();
                }
            }
            
            // Validate status value
            if (!in_array($status, ['Verified', 'Rejected', 'Pending'])) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid status value',
                        'csrf_hash' => csrf_hash()
                    ])->setStatusCode(400);
                } else {
                    session()->setFlashdata('error', 'Invalid status value');
                    return redirect()->back();
                }
            }
            
            // Check if certificate exists
            $certificate = $this->certificateModel->find($id);
            if (!$certificate) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Certificate not found',
                        'csrf_hash' => csrf_hash()
                    ])->setStatusCode(404);
                } else {
                    session()->setFlashdata('error', 'Certificate not found');
                    return redirect()->back();
                }
            }
            
            // Prepare update data
            $updateData = [
                'status' => $status,
                'verified_by' => session()->get('id'),
                'verified_at' => date('Y-m-d H:i:s')
            ];
            
            // Update status
            if ($this->certificateModel->update($id, $updateData)) {
                log_message('info', "Certificate {$id} status updated to {$status} by admin ID " . session()->get('id'));
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Certificate ' . strtolower($status) . ' successfully',
                        'csrf_hash' => csrf_hash()
                    ]);
                } else {
                    session()->setFlashdata('success', 'Certificate ' . strtolower($status) . ' successfully');
                    return redirect()->to('/admin/certificates');
                }
            } else {
                $errors = $this->certificateModel->errors();
                $errorMsg = $errors ? implode(', ', $errors) : 'Failed to update certificate status';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $errorMsg,
                        'csrf_hash' => csrf_hash()
                    ])->setStatusCode(500);
                } else {
                    session()->setFlashdata('error', $errorMsg);
                    return redirect()->back();
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating certificate status: ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'An error occurred while updating the certificate status',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(500);
            } else {
                session()->setFlashdata('error', 'An error occurred while updating the certificate status');
                return redirect()->back();
            }
        }
    }
    
    /**
     * Delete a certificate
     */
    public function delete()
    {
        // Verify CSRF token
        $csrfCheck = $this->verifyCSRF();
        if ($csrfCheck !== true) {
            return $csrfCheck;
        }
        
        // Check if user is logged in and is an admin
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'super_admin'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(403);
            } else {
                session()->setFlashdata('error', 'Unauthorized access');
                return redirect()->back();
            }
        }
        
        $id = $this->request->getPost('id');
        
        if (empty($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate ID is required'
                ])->setStatusCode(400);
            } else {
                session()->setFlashdata('error', 'Certificate ID is required');
                return redirect()->back();
            }
        }
        
        $certificate = $this->certificateModel->find($id);
        
        if (!$certificate) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate not found'
                ])->setStatusCode(404);
            } else {
                session()->setFlashdata('error', 'Certificate not found');
                return redirect()->back();
            }
        }
        
        // Delete the certificate
        $this->certificateModel->delete($id);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Certificate deleted successfully'
            ]);
        } else {
            session()->setFlashdata('success', 'Certificate deleted successfully');
            return redirect()->to('/admin/certificates');
        }
    }
}
