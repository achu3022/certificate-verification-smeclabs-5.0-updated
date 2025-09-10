<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * Instance of the Session service.
     *
     * @var \CodeIgniter\Session\Session
     */
    protected $session;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['time', 'form', 'time_helper', 'url'];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Initialize session
        $this->session = service('session');

        // Set JSON response format for AJAX requests
        if ($request->isAJAX()) {
            $response->setContentType('application/json');
        }
    }

    /**
     * Send a success response with CSRF hash
     */
    protected function successResponse($data = [], $message = 'Success', $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'csrf_hash' => csrf_hash()
        ]);
    }

    /**
     * Send an error response with CSRF hash
     */
    protected function errorResponse($message = 'Error', $code = 400, $data = [])
    {
        return $this->response->setStatusCode($code)->setJSON([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'csrf_hash' => csrf_hash()
        ]);
    }


}
