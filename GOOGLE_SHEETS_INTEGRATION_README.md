# Google Sheets Integration for Certificate Management

This integration allows your CodeIgniter certificate management system to automatically update Google Sheets when certificate statuses are changed in the web application.

## Overview

The integration consists of two main components:

1. **Google Apps Script** - Deployed as a web app that handles sheet updates
2. **CodeIgniter Helper** - Calls the Apps Script web app when status changes

## Setup Instructions

### Step 1: Deploy the Google Apps Script

1. Open Google Sheets and create a new spreadsheet or open your existing certificate spreadsheet
2. Go to **Extensions > Apps Script**
3. Replace the default code with the contents of `updated_apps_script.gs`
4. Save the project (Ctrl+S or File > Save)
5. Deploy the script as a web app:
   - Click **Deploy > New deployment**
   - Select **Web app** as deployment type
   - Set **Execute as: Me** (your Google account)
   - Set **Who has access: Anyone**
   - Click **Deploy**
   - Copy the **Web app URL** (you'll need this for the CodeIgniter configuration)

### Step 2: Configure CodeIgniter

1. Open `app/Config/GoogleSheets.php`
2. Set the `appsScriptWebAppUrl` to the URL you copied from the deployment:

```php
public $appsScriptWebAppUrl = 'https://script.google.com/macros/s/YOUR_WEB_APP_ID/exec';
```

3. Make sure the sheet structure matches the expected format:
   - Column A: Certificate No
   - Column B: Admission No
   - Column C: Course
   - Column D: Student Name
   - Column E: Start Date
   - Column F: End Date
   - Column G: Date of Issue
   - Column H: Status
   - Column I: Sync Status

### Step 3: Test the Integration

1. Run the test script to verify the setup:

```bash
php test_integration.php
```

2. Test with a real certificate:
   - Go to your admin panel
   - Find a certificate with "Pending" status
   - Change the status to "Approved" or "Verified"
   - Check if the Google Sheet is updated automatically

## How It Works

### When Admin Updates Certificate Status:

1. Admin changes certificate status in the web application
2. `Certificate.php` calls `updateGoogleSheetStatus()` function
3. The helper function makes an HTTP POST request to the Apps Script web app
4. Apps Script receives the request and updates the corresponding row in the sheet
5. Apps Script returns a JSON response indicating success/failure
6. The sync status column is updated with the result

### Apps Script Web App Features:

- **CORS enabled** - Works with cross-origin requests
- **Input validation** - Validates certificate number and status
- **Detailed logging** - Logs all requests and responses
- **Error handling** - Comprehensive error handling and reporting
- **Sync status tracking** - Updates the sync status column with timestamps

## Valid Status Values

The following status values are supported:
- `Pending`
- `Verified`
- `Rejected`
- `Approved`

## Troubleshooting

### Common Issues:

1. **"Apps Script web app URL is not configured"**
   - Make sure you've set the `$appsScriptWebAppUrl` in `GoogleSheets.php`

2. **"Certificate not found"**
   - Check if the certificate number exists in your Google Sheet
   - Ensure the certificate number matches exactly (case-sensitive)

3. **"Google Sheets integration is disabled"**
   - Make sure `$enabled = true` in `GoogleSheets.php`

4. **CORS errors**
   - The Apps Script already has CORS headers enabled
   - Check browser console for specific errors

### Debug Mode:

Enable debug logging by setting `$debug = true` in `GoogleSheets.php`. This will log detailed information about API calls and responses.

### Manual Testing:

Use the provided test script:

```bash
php test_integration.php
```

## Security Considerations

1. **Web App Access**: The Apps Script is deployed with "Anyone" access, which is necessary for the CodeIgniter app to call it
2. **Input Validation**: Both the CodeIgniter helper and Apps Script validate inputs
3. **HTTPS**: The integration uses HTTPS for secure communication
4. **No Authentication**: The current setup doesn't require authentication between the web app and Apps Script

## File Structure

```
app/
├── Config/GoogleSheets.php          # Configuration
├── Helpers/google_sheets_helper.php # Integration helper
├── Controllers/Certificate.php      # Certificate management
updated_apps_script.gs               # Google Apps Script code
test_integration.php                 # Test script
```

## Next Steps

1. Deploy the updated Apps Script
2. Configure the web app URL in CodeIgniter
3. Test with real data
4. Monitor the sync status column for any issues
5. Consider adding authentication if needed for production use

## Support

For issues or questions:
1. Check the browser console for JavaScript errors
2. Check the CodeIgniter logs for PHP errors
3. Verify the Google Sheet structure matches the expected format
4. Ensure the Apps Script is properly deployed and accessible