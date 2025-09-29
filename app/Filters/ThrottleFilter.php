<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Cache\CacheInterface;

class ThrottleFilter implements FilterInterface
{
    protected $cache;
    
    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }
    
    public function before(RequestInterface $request, $arguments = null)
    {
        $limit = $arguments[0] ?? 60;     // Requests per minute
        $minutes = $arguments[1] ?? 1;    // Time window in minutes
        
        $ip = $request->getIPAddress();
        $key = 'throttle_' . md5($ip . $request->uri->getPath());
        
        // Get current count
        $count = $this->cache->get($key) ?? 0;
        
        // Check if over limit
        if ($count >= $limit) {
            return service('response')
                ->setStatusCode(429)
                ->setJSON(['error' => 'Too many requests. Please try again later.']);
        }
        
        // Increment count
        $this->cache->save($key, $count + 1, $minutes * 60);
        
        return $request;
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
