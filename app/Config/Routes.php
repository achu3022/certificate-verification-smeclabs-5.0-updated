<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Certificate::index');
$routes->match(['GET', 'POST'], 'certificate/search', 'Certificate::search');
$routes->get('certificate/details/(:num)', 'Certificate::details/$1');
$routes->get('certificate/all', 'Certificate::getAllByAdmission');
$routes->post('certificate/verify', 'Certificate::verify');
$routes->get('get-client-ip', 'Certificate::getClientIp');

// Auth Routes
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/authenticate', 'Auth::authenticate');
$routes->get('auth/logout', 'Auth::logout');

// Admin Routes (Protected)
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('certificates', 'Admin::certificates');
    $routes->get('search-logs', 'Admin::searchLogs');
    $routes->get('verifications', 'Admin::verifications');
    $routes->post('search-logs/delete/(:num)', 'Admin::deleteSearchLog/$1');
    $routes->get('search-logs/export', 'Admin::exportSearchLogs');
    $routes->get('profile', 'Admin::profile');
    $routes->post('profile/update-info', 'Admin::updateProfileInfo');
    $routes->post('profile/update-password', 'Admin::updatePassword');
    
    // Certificate management routes
    $routes->get('certificate/import', 'Certificate::import');
    $routes->post('certificate/import', 'Certificate::import');
    $routes->get('certificate/get/(:num)', 'Certificate::get/$1');
    $routes->post('certificate/update', 'Certificate::update');
    $routes->post('certificate/update-status', 'Certificate::updateStatus');
    $routes->post('certificate/delete', 'Certificate::delete');
    $routes->post('certificate/create-single', 'Admin::createSingleCertificate');
    $routes->get('certificates/export', 'Admin::exportCertificates');
    
    // Legacy routes for backward compatibility
    $routes->get('certificate/(:num)', 'Certificate::get/$1');
    $routes->post('certificate/approve/(:num)', 'Certificate::updateStatus/$1');
    $routes->post('certificate/reject/(:num)', 'Certificate::updateStatus/$1');
    $routes->post('certificate/edit/(:num)', 'Certificate::update/$1');
});

// Super Admin Routes (Protected)
$routes->group('admin', ['filter' => 'auth:super_admin'], function($routes) {
    $routes->get('admins', 'Admin::listAdmins');
    $routes->get('admins/create', 'Admin::createAdmin');
    $routes->post('admins/store', 'Admin::storeAdmin');
    $routes->get('admins/edit/(:num)', 'Admin::editAdmin/$1');
    $routes->post('admins/update/(:num)', 'Admin::updateAdmin/$1');
    $routes->post('admins/delete/(:num)', 'Admin::deleteAdmin/$1');
    $routes->post('admins/activate/(:num)', 'Admin::activateAdmin/$1');
    $routes->post('admins/deactivate/(:num)', 'Admin::deactivateAdmin/$1');
});
// API Routes
$routes->group('api', function($routes) {
    // Certificate sync endpoint with only API auth (for testing)
    $routes->post('certificates/sync', 'Certificate::syncFromSheet', [
        'namespace' => 'App\Controllers',
        'filter' => 'apiAuth'  // Only API auth first for testing
    ]);
});
