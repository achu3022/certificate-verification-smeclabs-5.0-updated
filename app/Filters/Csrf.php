<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Csrf implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     *
     * @param RequestInterface $request
     * @param array|null      $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if ($request->getMethod() === 'post' || $request->getMethod() === 'put' || $request->getMethod() === 'patch' || $request->getMethod() === 'delete') {
            $security = Services::security();
            
            // Get token name from config
            $tokenName = $security->getTokenName();
            $headerName = config('Security')->headerName ?? 'X-CSRF-TOKEN';
            
            // Get token from various sources
            $csrfHeader = $request->getHeaderLine($headerName);
            $csrfPost = $request->getPost($tokenName);
            $csrfJson = null;
            
            // Try to get token from JSON body
            if (strpos($request->getHeaderLine('Content-Type'), 'application/json') !== false) {
                try {
                    $body = $request->getBody();
                    if ($body) {
                        $jsonData = json_decode($body, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                            $csrfJson = $jsonData[$tokenName] ?? null;
                        }
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Error parsing JSON body in CSRF filter: ' . $e->getMessage());
                }
            }
            
            // Get current hash
            $currentHash = $security->getHash();
            
            // Check all possible token sources
            $csrfValid = ($csrfHeader && hash_equals($currentHash, $csrfHeader)) || 
                        ($csrfPost && hash_equals($currentHash, $csrfPost)) || 
                        ($csrfJson && hash_equals($currentHash, $csrfJson));
            
            if (!$csrfValid) {
                return Services::response()
                    ->setJSON([
                        'success' => false,
                        'message' => 'Invalid security token. Please refresh the page and try again.',
                        'csrf_hash' => csrf_hash()
                    ])
                    ->setStatusCode(403);
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after the request
    }
}