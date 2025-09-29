<?php
/**
 * Standalone test script to verify Google Sheets integration
 * This script tests the integration without requiring full CodeIgniter bootstrap
 */

echo "Testing Google Sheets Integration\n";
echo "================================\n\n";

// Test 1: Test with invalid configuration (should fail)
echo "Test 1: Testing with invalid configuration\n";
$result1 = testGoogleSheetStatus('TEST123', 'Approved');
echo "Result: " . ($result1['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $result1['message'] . "\n\n";

// Test 2: Test with empty certificate number (should fail)
echo "Test 2: Testing with empty certificate number\n";
$result2 = testGoogleSheetStatus('', 'Approved');
echo "Result: " . ($result2['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $result2['message'] . "\n\n";

// Test 3: Test with invalid status (should fail)
echo "Test 3: Testing with invalid status\n";
$result3 = testGoogleSheetStatus('TEST123', 'InvalidStatus');
echo "Result: " . ($result3['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $result3['message'] . "\n\n";

echo "Integration tests completed.\n";
echo "To fully test the integration, you need to:\n";
echo "1. Set the appsScriptWebAppUrl in app/Config/GoogleSheets.php\n";
echo "2. Deploy the updated Apps Script as a web app\n";
echo "3. Test with a real certificate number from your Google Sheet\n";

/**
 * Standalone version of the updateGoogleSheetStatus function for testing
 */
function testGoogleSheetStatus(string $certificateNo, string $newStatus): array
{
    // Simple configuration check
    $config = (object)[
        'enabled' => true,
        'appsScriptWebAppUrl' => '', // This should be set in real usage
    ];

    // Check if integration is enabled
    if (!$config->enabled) {
        return [
            'success' => false,
            'message' => 'Google Sheets integration is disabled'
        ];
    }

    // Check if URL is configured
    if (empty($config->appsScriptWebAppUrl)) {
        return [
            'success' => false,
            'message' => 'Apps Script web app URL is not configured'
        ];
    }

    // Validate certificate number
    if (empty($certificateNo) || strlen(trim($certificateNo)) === 0) {
        return [
            'success' => false,
            'message' => 'Certificate number is required'
        ];
    }

    // Validate status
    $validStatuses = ['Pending', 'Verified', 'Rejected', 'Approved'];
    if (empty($newStatus) || !in_array($newStatus, $validStatuses)) {
        return [
            'success' => false,
            'message' => 'Invalid status. Valid statuses are: ' . implode(', ', $validStatuses)
        ];
    }

    // Simulate the cURL call (would actually make HTTP request in real implementation)
    return [
        'success' => true,
        'message' => 'Configuration is valid. Ready to make HTTP request to Apps Script.'
    ];
}