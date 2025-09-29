# Google Sheets Integration for Certificate Status Updates

This document explains how to set up and use the Google Sheets integration for updating certificate statuses.

## Prerequisites

1. A Google account with access to Google Sheets
2. Google Apps Script project with the provided code deployed as a web app

## Setup Instructions

### 1. Create a Google Apps Script Project

1. Go to [Google Apps Script](https://script.google.com/)
2. Click on "New Project"
3. Replace the default code with the following:

```javascript
// =============================
// WEB APP TO UPDATE STATUS FROM WEBSITE
// =============================
function doGet(e) {
  return handleWebUpdate(e);
}

function doPost(e) {
  return handleWebUpdate(e);
}

function handleWebUpdate(e) {
  try {
    if (!e.parameter) {
      return ContentService.createTextOutput(JSON.stringify({ error: "No parameters provided" }))
                           .setMimeType(ContentService.MimeType.JSON);
    }

    var certificateNo = e.parameter.certificate_no;
    var newStatus = e.parameter.status;

    if (!certificateNo || !newStatus) {
      return ContentService.createTextOutput(JSON.stringify({ error: "Missing certificate_no or status" }))
                           .setMimeType(ContentService.MimeType.JSON);
    }

    // Normalize status
    newStatus = newStatus.charAt(0).toUpperCase() + newStatus.slice(1).toLowerCase();

    var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName(CONFIG.SHEET_NAME);
    var lastRow = sheet.getLastRow();
    var colCertNo = CONFIG.COLUMNS.CERTIFICATE_NO + 1; // Column A
    var colStatus = CONFIG.COLUMNS.STATUS + 1;          // Column H

    var updated = false;
    for (var row = CONFIG.HEADER_ROW + 1; row <= lastRow; row++) {
      var certValue = String(sheet.getRange(row, colCertNo).getValue()).trim();
      if (certValue === certificateNo) {
        sheet.getRange(row, colStatus).setValue(newStatus);
        updated = true;
        break;
      }
    }

    if (updated) {
      return ContentService.createTextOutput(JSON.stringify({ success: true, message: "Status updated" }))
                           .setMimeType(ContentService.MimeType.JSON);
    } else {
      return ContentService.createTextOutput(JSON.stringify({ error: "Certificate not found" }))
                           .setMimeType(ContentService.MimeType.JSON);
    }

  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({ error: err.toString() }))
                         .setMimeType(ContentService.MimeType.JSON);
  }
}
```

### 2. Deploy the Web App

1. Click on "Deploy" > "New deployment"
2. Select "Web app" as the deployment type
3. Set the following options:
   - Execute as: "Me"
   - Who has access: "Anyone"
4. Click "Deploy"
5. Copy the web app URL (you'll need this for the configuration)

### 3. Configure the Application

1. Open the configuration file at `app/Config/GoogleSheets.php`
2. Update the `webAppUrl` with your Google Apps Script web app URL
3. Configure other settings as needed:
   - `enabled`: Set to `true` to enable Google Sheets integration
   - `maxRetries`: Number of retry attempts for failed updates
   - `timeout`: Request timeout in seconds

## Usage

The integration will automatically update the Google Sheet whenever a certificate status is updated in the system. The following statuses are supported:

- Pending
- Verified
- Rejected

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**
   - Ensure the web app is deployed with "Anyone" access
   - Make sure you've accepted the permissions for the script

2. **Certificate Not Found**
   - Verify the certificate number exists in the Google Sheet
   - Check for leading/trailing spaces in the certificate number

3. **Connection Timeouts**
   - Increase the `timeout` value in the configuration
   - Check your server's internet connectivity

### Logs

Check the following log files for error messages:
- `writable/logs/log-YYYY-MM-DD.log`
- Google Apps Script execution logs (View > Stackdriver Logging > Logs Explorer)

## Security Considerations

1. **Web App URL**
   - Keep your web app URL private
   - Consider using environment variables for sensitive configuration

2. **Permissions**
   - The web app only needs access to the specific Google Sheet
   - Use the principle of least privilege when setting up permissions

3. **Rate Limiting**
   - Google Apps Script has daily quotas and rate limits
   - The integration includes retry logic with exponential backoff to handle temporary failures
