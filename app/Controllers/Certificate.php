<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CertificateModel;
use App\Models\SearchLogModel;
use App\Models\CertificateVerificationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Certificate extends BaseController
{
    protected $certificateModel;
    protected $searchLogModel;
    protected $verificationModel;

    public function __construct()
    {
        $this->certificateModel = new CertificateModel();
        $this->searchLogModel = new SearchLogModel();
        $this->verificationModel = new \App\Models\CertificateVerificationModel();
        helper(['form', 'url', 'text']);
    }
    
    /**
     * Search certificates by admission number
     */
    /**
     * Search certificates by admission number
     */
    public function search()
    {
        // Get search term from POST or GET
        $searchTerm = $this->request->getGet('admission_no') ?? $this->request->getPost('admission_no');
        $format = $this->request->getGet('format');
        
        // Only force JSON if explicitly requested via format=json
        // Otherwise, default to HTML view
        if ($this->request->isAJAX() && $format !== 'json') {
            $format = 'html';
        }
        
        // Initialize data array with default values
        $data = [
            'searchTerm' => $searchTerm,
            'certificates' => [],
            'message' => null,
            'success' => false
        ];
        
        if (!empty($searchTerm)) {
            try {
                // Trim the search term to remove any extra whitespace
                $searchTerm = trim($searchTerm);
                
                // Search for certificates by admission number (exact match and partial match)
                $certificates = $this->certificateModel
                    ->groupStart()
                        ->where('admission_no', $searchTerm)
                        ->orLike('admission_no', $searchTerm)
                    ->groupEnd()
                    ->orderBy('date_of_issue', 'DESC')
                    ->findAll();
                
                // Log the search query for debugging
                log_message('debug', 'Search term: ' . $searchTerm . ', Found certificates: ' . count($certificates));
                
                // Group certificates by admission number
                $groupedCertificates = [];
                foreach ($certificates as $cert) {
                    $admissionNo = $cert['admission_no'];
                    if (!isset($groupedCertificates[$admissionNo])) {
                        $groupedCertificates[$admissionNo] = [
                            'admission_no' => $admissionNo,
                            'student_name' => $cert['student_name'],
                            'certificates' => [],
                            'certificate_count' => 0,
                            'status_counts' => [
                                'Verified' => 0,
                                'Pending' => 0,
                                'Rejected' => 0
                            ]
                        ];
                    }
                    
                    $groupedCertificates[$admissionNo]['certificates'][] = $cert;
                    $groupedCertificates[$admissionNo]['certificate_count']++;
                    $groupedCertificates[$admissionNo]['status_counts'][$cert['status']]++;
                }
                
                // Convert to indexed array for the view
                $data['certificates'] = array_values($groupedCertificates);
                $found = !empty($data['certificates']);
                $data['success'] = true;
                
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
                
                // Return JSON only if explicitly requested via format=json
                if ($format === 'json') {
                    return $this->response->setJSON([
                        'success' => $data['success'],
                        'certificates' => $data['certificates'],
                        'message' => $data['message'],
                        'csrf_hash' => csrf_hash()
                    ]);
                }
                
                // Default to HTML view with the data
                return view('certificates/search', $data);
                
            } catch (\Exception $e) {
                log_message('error', 'Certificate search error: ' . $e->getMessage());
                $data['message'] = 'An error occurred while searching for certificates.';
                $data['success'] = false;
                
                if ($format === 'json' || $this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $data['message'],
                        'csrf_hash' => csrf_hash()
                    ])->setStatusCode(500);
                }
            }
        } else if ($format === 'json') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please provide an admission number to search',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(400);
        }
        
        // Regular request - return full view
        return view('certificates/search', $data);
    }

    /**
     * Handle public verification form submission
     * Saves into certificate_verifications and returns certificate details
     */
    /**
     * Get client IP address
     */
    public function getClientIp()
    {
        $ip = $this->request->getIPAddress();
        
        return $this->response->setJSON([
            'success' => true,
            'ip' => $ip,
            'user_agent' => $this->request->getUserAgent()->getAgentString()
        ]);
    }
    
    /**
     * Handle public verification form submission
     */
    public function verify()
    {
        // Get raw input for debugging
        $rawInput = file_get_contents('php://input');
        log_message('debug', 'Raw input: ' . $rawInput);
        
        // Get POST data directly
        $input = $this->request->getPost();
        
        // If no POST data, try to parse as JSON
        if (empty($input) && !empty($rawInput)) {
            $input = json_decode($rawInput, true) ?: [];
        }
        
        // Ensure certificate_id is properly set and is a number
        $certId = (int) ($input['certificate_id'] ?? 0);
        
        // Log the processed input data for debugging
        log_message('debug', 'Processed input: ' . print_r($input, true));
        
        // Verify CSRF
        $csrfToken = $input[csrf_token()] ?? '';
        if (!hash_equals(csrf_hash(), $csrfToken)) {
            log_message('error', 'CSRF token validation failed. Expected: ' . csrf_hash() . ', Got: ' . $csrfToken);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'CSRF token validation failed',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(403);
        }
        
        // Set certificate ID
        $input['certificate_id'] = $certId;

        try {
            // First check if certificate exists
            $certId = (int) ($input['certificate_id'] ?? 0);
            
            if ($certId <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid certificate ID',
                    'errors' => ['certificate_id' => 'The certificate ID is required and must be a positive number'],
                    'input_data' => $input,
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(400);
            }
            
            $certificate = $this->certificateModel->find($certId);
            
            if (!$certificate) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate not found',
                    'errors' => ['certificate_id' => 'No certificate found with the provided ID'],
                    'input_data' => $input,
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(404);
            }
            
            $rules = [
                'name' => 'required|min_length[2]|max_length[255]',
                'designation' => 'required|min_length[2]|max_length[255]',
                'company_name' => 'required|min_length[2]|max_length[255]',
                'contact_no' => 'required|min_length[5]|max_length[20]',
                'country' => 'required|min_length[2]|max_length[100]',
                'ip_address' => 'permit_empty',
                'user_agent' => 'permit_empty'
            ];
            
            // Set default values if not provided
            $input['ip_address'] = $input['ip_address'] ?? $this->request->getIPAddress();
            $input['user_agent'] = $input['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
            
            // Validate input
            $validation = \Config\Services::validation();
            $validation->setRules($rules);
            
            if (!$validation->run($input)) {
                $errors = $validation->getErrors();
                log_message('error', 'Validation failed: ' . print_r($errors, true));
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors,
                    'input_data' => $input,
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(400);
            }

            $certId = (int) ($input['certificate_id'] ?? 0);
            
            // Log certificate lookup
            log_message('debug', 'Looking up certificate with ID: ' . $certId);
            
            $certificate = $this->certificateModel->find($certId);
            
            if (!$certificate) {
                log_message('error', 'Certificate not found with ID: ' . $certId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate not found',
                    'csrf_hash' => csrf_hash(),
                    'certificate_id' => $certId
                ])->setStatusCode(404);
            }
            
            // Prepare verification data
            $verificationData = [
                'certificate_id' => $certId,
                'name' => trim($input['name'] ?? ''),
                'designation' => trim($input['designation'] ?? ''),
                'company_name' => trim($input['company_name'] ?? ''),
                'contact_no' => trim($input['contact_no'] ?? ''),
                'country' => trim($input['country'] ?? ''),
                'ip_address' => $input['ip_address'] ?? $this->request->getIPAddress(),
                'user_agent' => $input['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Log the verification data being saved
            log_message('debug', 'Saving verification data: ' . print_r($verificationData, true));
            
            // Save verification
            $verificationModel = new CertificateVerificationModel();
            $verificationModel->save($verificationData);
            
            // Log success
            log_message('info', 'Verification saved successfully for certificate ID: ' . $certId);

            // Return certificate details for rendering on the page
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Verification submitted successfully',
                'certificate' => [
                    'id' => $certificate['id'],
                    'certificate_no' => $certificate['certificate_no'],
                    'student_name' => $certificate['student_name'],
                    'course' => $certificate['course'],
                    'start_date' => $certificate['start_date'],
                    'end_date' => $certificate['end_date'],
                    'date_of_issue' => $certificate['date_of_issue'],
                    'status' => $certificate['status'],
                ],
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Verification submission failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while submitting verification',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);
        }
    }

    /**
     * View certificate details
     */
    public function view($id = null)
    {
        if (empty($id)) {
            return redirect()->back()->with('error', 'Certificate ID is required');
        }

        $certificate = $this->certificateModel->find($id);
        if (!$certificate) {
            return redirect()->back()->with('error', 'Certificate not found');
        }

        // Get verification details if any
        $verification = $this->verificationModel->where('certificate_id', $id)
            ->orderBy('created_at', 'DESC')
            ->first();

        return view('certificates/view', [
            'certificate' => $certificate,
            'verification' => $verification,
            'title' => 'Certificate Details'
        ]);
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

        // Set error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        log_message('debug', '=== STARTING IMPORT PROCESS ===');

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

            log_message('debug', 'Checking for uploaded file...');
            $file = $this->request->getFile('excel_file');
            log_message('debug', 'File data: ' . print_r($file, true));

            if (!$file) {
                log_message('error', 'No file found in request');
                throw new \RuntimeException('No file was uploaded.');
            }
            
            if (!$file->isValid()) {
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
            
            $filePath = $file->getPathname();
            log_message('debug', 'Loading file from: ' . $filePath);
            
            if (!file_exists($filePath)) {
                throw new \RuntimeException("File not found at: " . $filePath);
            }
            
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            log_message('debug', 'Loaded ' . count($rows) . ' rows from spreadsheet');
            
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
            log_message('debug', 'Starting to process rows...');
            
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
                    
                    // Check for duplicate certificate number (only check certificate_no since admission_no can be duplicated)
                    $exists = $this->certificateModel
                        ->where('certificate_no', $data['certificate_no'])
                        ->first();
                        
                    if ($exists) {
                        $skipped[] = "Row $rowNumber: Duplicate certificate number: " . $data['certificate_no'];
                        continue;
                    }
                    
                    // Save the record
                    log_message('debug', 'Attempting to save row: ' . print_r($data, true));
                    
                    try {
                        if ($this->certificateModel->save($data)) {
                            $imported++;
                            log_message('debug', 'Successfully imported row ' . $rowNumber);
                        } else {
                            $errorMsg = "Row $rowNumber: Failed to save - " . implode(', ', $this->certificateModel->errors());
                            $skipped[] = $errorMsg;
                            log_message('error', $errorMsg);
                        }
                    } catch (\Exception $e) {
                        $errorMsg = "Row $rowNumber: Exception - " . $e->getMessage();
                        $skipped[] = $errorMsg;
                        log_message('error', $errorMsg);
                        log_message('error', $e->getTraceAsString());
                    }
                    
                } catch (\Exception $e) {
                    $skipped[] = "Row $rowNumber: " . $e->getMessage();
                    continue;
                }
            }
            
            log_message('debug', 'Import completed. Success: ' . $imported . ', Skipped: ' . count($skipped));
            
            if ($imported === 0 && empty($skipped)) {
                $errorMsg = 'No valid data found to import. Please check the file format and try again.';
                log_message('error', $errorMsg);
                throw new \RuntimeException($errorMsg);
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

            // Get form data (excluding certificate_no as it should not be editable)
            $data = [
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
     * Update certificate status
     */
    public function updateStatus()
    {
        try {
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

            $id = $this->request->getPost('id');
            $status = $this->request->getPost('status');

            if (!$id || !$status) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate ID and status are required',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(400);
            }

            // Validate status
            if (!in_array($status, ['Pending', 'Verified', 'Rejected'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid status value',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(400);
            }

            // Check if certificate exists
            $certificate = $this->certificateModel->find($id);
            if (!$certificate) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate not found',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(404);
            }
            
            // Get the current status before update
            $oldStatus = $certificate['status'] ?? null;
            $certificateNo = $certificate['certificate_no'] ?? null;

            // Update status in the database
            if ($this->certificateModel->update($id, ['status' => $status])) {
                log_message('info', "Certificate {$id} status updated from {$oldStatus} to {$status}");
                
                // Only update Google Sheet if the status has actually changed
                if ($certificateNo && $oldStatus !== $status) {
                    // Load the helper
                    helper('google_sheets');
                    
                    // Update Google Sheet
                    $sheetUpdate = updateGoogleSheetStatus($certificateNo, $status);
                    
                    if (!$sheetUpdate['success']) {
                        // Log the error but don't fail the request
                        log_message('error', 'Google Sheets update failed: ' . $sheetUpdate['message']);
                        // You could also send an email notification here if needed
                    }
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Certificate status updated successfully',
                    'sheet_updated' => $sheetUpdate['success'] ?? false,
                    'sheet_message' => $sheetUpdate['message'] ?? '',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update certificate status',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);

        } catch (\Exception $e) {
            log_message('error', 'Error updating certificate status: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating the certificate status',
                'error' => $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete certificate
     */
    public function delete()
    {
        try {
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

            $id = $this->request->getPost('id');

            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate ID is required',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(400);
            }

            // Check if certificate exists
            $certificate = $this->certificateModel->find($id);
            if (!$certificate) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Certificate not found',
                    'csrf_hash' => csrf_hash()
                ])->setStatusCode(404);
            }

            // Delete certificate
            if ($this->certificateModel->delete($id)) {
                log_message('info', "Certificate {$id} deleted");
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Certificate deleted successfully',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete certificate',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);

        } catch (\Exception $e) {
            log_message('error', 'Error deleting certificate: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting the certificate',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get all certificates for an admission number (flat array for modal display)
     */
    public function getAllByAdmission()
    {
        // Get admission number from query parameter
        $admissionNo = $this->request->getGet('admission_no');
        
        // Log the raw parameter received
        log_message('debug', 'getAllByAdmission called with parameter: ' . var_export($admissionNo, true));
        
        if (!$admissionNo) {
            log_message('debug', 'getAllByAdmission: No admission number provided');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admission number is required',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(400);
        }

        try {
            // Trim the admission number to remove any extra whitespace
            $admissionNo = trim($admissionNo);
            
            // URL decode the admission number in case it's encoded
            $admissionNo = urldecode($admissionNo);
            log_message('debug', 'getAllByAdmission - After URL decode: ' . $admissionNo);
            
            // Get all certificates for this admission number (exact match)
            $builder = $this->certificateModel->builder();
            $builder->where('admission_no', $admissionNo);
            $query = $builder->getCompiledSelect();
            log_message('debug', 'getAllByAdmission - SQL Query: ' . $query);
            
            $certificates = $this->certificateModel
                ->where('admission_no', $admissionNo)
                ->orderBy('date_of_issue', 'DESC')
                ->findAll();
            
            // Log the query for debugging
            log_message('debug', 'getAllByAdmission - Admission No: ' . $admissionNo . ', Found certificates: ' . count($certificates));
            
            // Log first few characters of each admission_no in database for comparison
            if (empty($certificates)) {
                $allAdmissions = $this->certificateModel->select('admission_no')->findAll(5);
                log_message('debug', 'Sample admission numbers in DB: ' . json_encode(array_column($allAdmissions, 'admission_no')));
            }

            if (empty($certificates)) {
                return $this->response->setJSON([
                    'success' => true,
                    'certificates' => [],
                    'message' => 'No certificates found for this admission number',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // Format dates for frontend display
            foreach ($certificates as &$cert) {
                if ($cert['start_date']) {
                    $cert['start_date'] = date('Y-m-d', strtotime($cert['start_date']));
                }
                if ($cert['end_date']) {
                    $cert['end_date'] = date('Y-m-d', strtotime($cert['end_date']));
                }
                if ($cert['date_of_issue']) {
                    $cert['date_of_issue'] = date('Y-m-d', strtotime($cert['date_of_issue']));
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'certificates' => $certificates,
                'count' => count($certificates),
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error retrieving certificates for admission ' . $admissionNo . ': ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while retrieving certificates',
                'csrf_hash' => csrf_hash()
            ])->setStatusCode(500);
        }
    }


    /**
     * Sync certificate data from Google Sheets
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function syncFromSheet()
    {
        // Rate limiting check (max 60 requests per minute per IP)
        $throttler = \Config\Services::throttler();
        if ($throttler->check('syncFromSheet-' . $this->request->getIPAddress(), 60, MINUTE) === false) {
            log_message('warning', 'Rate limit exceeded for IP: ' . $this->request->getIPAddress());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Too many requests. Please try again later.',
                'code'    => 429
            ])->setStatusCode(429);
        }

        // Verify request content type
        if (!$this->request->is('json')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid content type. Expected application/json',
                'code'    => 415
            ])->setStatusCode(415);
        }

        $data = $this->request->getJSON(true);
        
        // Basic validation
        if (empty($data)) {
            log_message('error', 'Empty request body received in syncFromSheet');
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Request body cannot be empty',
                'code'    => 400
            ])->setStatusCode(400);
        }

        // Required fields validation
        $requiredFields = ['certificate_no', 'admission_no', 'student_name', 'course', 'date_of_issue'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            log_message('error', 'Missing required fields: ' . implode(', ', $missingFields));
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields),
                'code'    => 400,
                'missing_fields' => $missingFields
            ])->setStatusCode(400);
        }

        // Validate date formats
        $dateFields = ['start_date', 'end_date', 'date_of_issue'];
        foreach ($dateFields as $dateField) {
            if (!empty($data[$dateField]) && !strtotime($data[$dateField])) {
                log_message('error', "Invalid date format for field: $dateField");
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => "Invalid date format for $date_field. Expected YYYY-MM-DD",
                    'code'    => 400,
                    'field'   => $dateField
                ])->setStatusCode(400);
            }
        }

        $model = new CertificateModel();
        $certificateNo = $data['certificate_no'];
        
        // Log the sync attempt
        log_message('info', "Processing sync for certificate: $certificateNo");
        
        try {
            // Check if certificate already exists
            $existing = $model->find($certificateNo);
            
            // Sanitize and prepare data
            $allowedFields = array_flip($model->allowedFields);
            $syncData = array_intersect_key($data, $allowedFields);
            
            // Format dates to Y-m-d
            foreach ($dateFields as $dateField) {
                if (!empty($syncData[$dateField])) {
                    $syncData[$dateField] = date('Y-m-d', strtotime($syncData[$dateField]));
                }
            }
            
            // Handle existing record
            if ($existing) {
                // Check if there are actual changes
                $changes = [];
                foreach ($syncData as $key => $value) {
                    if (array_key_exists($key, $existing) && $existing[$key] != $value) {
                        $changes[$key] = $value;
                    }
                }
                
                if (empty($changes)) {
                    log_message('info', "No changes detected for certificate: $certificateNo");
                    return $this->response->setJSON([
                        'status'  => 'no_changes',
                        'message' => 'No changes detected',
                        'code'    => 200
                    ]);
                }
                
                // Update existing record
                $model->update($certificateNo, $changes);
                
                log_message('info', "Updated certificate: $certificateNo");
                
                return $this->response->setJSON([
                    'status'   => 'updated',
                    'message'  => 'Certificate updated successfully',
                    'code'     => 200,
                    'changes'  => $changes
                ]);
            } 
            // Handle new record
            else {
                // Set default status if not provided
                if (!isset($syncData['status'])) {
                    $syncData['status'] = 'Pending';
                }
                
                // Insert new record
                $model->insert($syncData);
                
                log_message('info', "Created new certificate: $certificateNo");
                
                return $this->response->setJSON([
                    'status'  => 'inserted',
                    'message' => 'Certificate created successfully',
                    'code'    => 201
                ])->setStatusCode(201);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error in syncFromSheet: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'An error occurred while processing your request',
                'code'    => 500,
                'error'   => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ])->setStatusCode(500);
        }
    }
}
