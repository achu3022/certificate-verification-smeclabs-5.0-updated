<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get the API key from the header
        $apiKey = $request->getHeaderLine('X-API-KEY');
        
        // Validate the API key (replace with your actual validation logic)
        if ($apiKey !== 'smeC3rt!f1c4t3_2025_XyZ@123_AbCdEf') {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Invalid API key']);
        }
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
