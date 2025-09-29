<?php

// Ensure APPPATH is defined
if (!defined('APPPATH')) {
    // If not defined, try to include the bootstrap file
    @include_once __DIR__ . '/../../bootstrap.php';
    
    // If still not defined, try to include the main index.php
    if (!defined('APPPATH')) {
        @include_once __DIR__ . '/../../public/index.php';
    }
}

use CodeIgniter\HTTP\ResponseInterface;
use Google\Client as Google_Client;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Service\Sheets\ValueRange;

if (!function_exists('getGoogleSheetsService')) {
    /**
     * Get Google Sheets service client
     * 
     * @param string $keyFilePath Path to the service account key file
     * @return Google_Service_Sheets|false Google Sheets service or false on failure
     */
    function getGoogleSheetsService($keyFilePath) {
        if (!file_exists($keyFilePath)) {
            log_message('error', 'Google service account key file not found: ' . $keyFilePath);
            return false;
        }
        
        try {
            $client = new Google_Client();
            $client->setAuthConfig($keyFilePath);
            $client->setScopes([
                Google_Service_Sheets::SPREADSHEETS
            ]);
            
            // Optional: Set the application name
            $client->setApplicationName('Certificate Verification Portal');
            
            // Create Google Sheets service
            return new Google_Service_Sheets($client);
            
        } catch (\Exception $e) {
            log_message('error', 'Error initializing Google Sheets service: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('updateGoogleSheetStatus')) {
    /**
     * Update certificate status in Google Sheet via Apps Script web app
     *
     * @param string $certificateNo The certificate number to update
     * @param string $newStatus The new status to set
     * @return array Response array with 'success' and 'message' keys
     */
    function updateGoogleSheetStatus(string $certificateNo, string $newStatus): array
    {
        // Get the Google Sheets configuration
        $config = config('GoogleSheets');

        // Check if Google Sheets integration is enabled
        if (!$config->enabled) {
            log_message('debug', 'Google Sheets integration is disabled in config');
            return [
                'success' => false,
                'message' => 'Google Sheets integration is disabled'
            ];
        }

        // Check if required configuration is set
        if (empty($config->appsScriptWebAppUrl)) {
            log_message('error', 'Apps Script web app URL is not configured');
            return [
                'success' => false,
                'message' => 'Apps Script web app URL is not configured'
            ];
        }

        // Validate certificate number
        if (empty($certificateNo) || strlen(trim($certificateNo)) === 0) {
            log_message('error', 'Certificate number is empty or invalid');
            return [
                'success' => false,
                'message' => 'Certificate number is required'
            ];
        }

        // Validate status
        $validStatuses = ['Pending', 'Verified', 'Rejected', 'Approved'];
        if (empty($newStatus) || !in_array($newStatus, $validStatuses)) {
            log_message('error', 'Invalid status provided: ' . $newStatus);
            return [
                'success' => false,
                'message' => 'Invalid status. Valid statuses are: ' . implode(', ', $validStatuses)
            ];
        }

        log_message('debug', "Updating Google Sheet via Apps Script for certificate: {$certificateNo} to status: {$newStatus}");

        try {
            // Prepare the data to send to Apps Script
            $postData = [
                'certificate_no' => $certificateNo,
                'status' => $newStatus,
                'source' => 'web_app'
            ];

            // Initialize cURL
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $config->appsScriptWebAppUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $config->timeout);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For development - set to true in production

            // Set headers
            $headers = [
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: CodeIgniter-GoogleSheets-Integration/1.0'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Execute the request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Check for cURL errors
            if ($curlError) {
                throw new \Exception('cURL Error: ' . $curlError);
            }

            // Check HTTP response code
            if ($httpCode !== 200) {
                throw new \Exception("HTTP Error: {$httpCode}. Response: {$response}");
            }

            // Parse JSON response
            $decodedResponse = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }

            // Log the response for debugging
            log_message('debug', 'Apps Script response: ' . print_r($decodedResponse, true));

            // Check if the update was successful
            if (isset($decodedResponse['success']) && $decodedResponse['success'] === true) {
                log_message('info', "Successfully updated Google Sheet for certificate {$certificateNo} to status: {$newStatus}");
                return [
                    'success' => true,
                    'message' => $decodedResponse['message'] ?? 'Google Sheet updated successfully'
                ];
            } else {
                $errorMessage = $decodedResponse['message'] ?? 'Unknown error from Apps Script';
                log_message('error', 'Apps Script update failed: ' . $errorMessage);
                return [
                    'success' => false,
                    'message' => 'Apps Script Error: ' . $errorMessage
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'Error updating Google Sheet via Apps Script: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error updating Google Sheet: ' . $e->getMessage()
            ];
        }
    }
}
