// =============================
// CONFIGURATION
// =============================
var CONFIG = {
  API_URL: 'https://certificates.smeclabs.info/api/certificates/sync',
  API_KEY: 'smeC3rt!f1c4t3_2025_XyZ@123_AbCdEf',
  SHEET_NAME: 'Sheet1', // Change if your sheet name is different
  HEADER_ROW: 1,
  // Column indices (0-based)
  COLUMNS: {
    CERTIFICATE_NO: 0,    // A
    ADMISSION_NO: 1,      // B
    COURSE: 2,            // C
    STUDENT_NAME: 3,      // D
    START_DATE: 4,        // E
    END_DATE: 5,          // F
    DATE_OF_ISSUE: 6,     // G
    STATUS: 7,            // H
    SYNC_STATUS: 8        // I
  }
};


// =============================
// TRIGGER - RUNS ON EDIT
// =============================
function onEdit(e) {
  if (!e || !e.range) return;

  var sheet = e.source.getActiveSheet();
  if (sheet.getName() !== CONFIG.SHEET_NAME) return;
  if (e.range.getRow() <= CONFIG.HEADER_ROW) return; // Skip header

  var row = e.range.getRow();
  var col = e.range.getColumn();
  var statusCell = sheet.getRange(row, CONFIG.COLUMNS.SYNC_STATUS + 1);

  try {
    // Only trigger if STATUS column was edited
    if (col !== CONFIG.COLUMNS.STATUS + 1) return;

    // Normalize status (e.g. pending → Pending)
    var rawStatus = String(e.range.getValue()).trim();
    var normalizedStatus = normalizeStatus(rawStatus);

    // Force value back to normalized form
    sheet.getRange(row, CONFIG.COLUMNS.STATUS + 1).setValue(normalizedStatus);

    // Only sync if status = Pending
    if (normalizedStatus !== "Pending") {
      statusCell.setValue(new Date().toLocaleString() + ' - ❌ Status must be "Pending" to sync');
      return;
    }

    // Read row data
    var data = sheet.getRange(row, 1, 1, 9).getValues()[0];

    // Prepare payload
    var payload = {
      certificate_no: String(data[CONFIG.COLUMNS.CERTIFICATE_NO] || '').trim(),
      admission_no: String(data[CONFIG.COLUMNS.ADMISSION_NO] || '').trim(),
      course: String(data[CONFIG.COLUMNS.COURSE] || '').trim(),
      student_name: String(data[CONFIG.COLUMNS.STUDENT_NAME] || '').trim(),
      start_date: formatDateForAPI(data[CONFIG.COLUMNS.START_DATE]),
      end_date: formatDateForAPI(data[CONFIG.COLUMNS.END_DATE]),
      date_of_issue: formatDateForAPI(data[CONFIG.COLUMNS.DATE_OF_ISSUE]),
      status: "Pending" // Always send Pending to API
    };

    // Validate required fields
    var missing = [];
    if (!payload.certificate_no) missing.push("Certificate No");
    if (!payload.admission_no) missing.push("Admission No");
    if (!payload.course) missing.push("Course");
    if (!payload.student_name) missing.push("Student Name");
    if (!payload.start_date) missing.push("Start Date");
    if (!payload.end_date) missing.push("End Date");
    if (!payload.date_of_issue) missing.push("Date of Issue");

    if (missing.length > 0) {
      statusCell.setValue(new Date().toLocaleString() + " - ❌ Missing: " + missing.join(", "));
      return;
    }

    // API call
    console.log("Sending data:", payload);
    var response = makeApiCall(payload);
    console.log("API Response:", response);

    // Update sync status (but DO NOT overwrite Status column)
    updateStatus(sheet, row, response);

  } catch (error) {
    console.error("Error in onEdit:", error);
    statusCell.setValue(new Date().toLocaleString() + ' - ❌ Error: ' + error.toString().substr(0, 50));
  }
}


// =============================
// API CALL
// =============================
function makeApiCall(data) {
  var options = {
    method: "POST",
    contentType: "application/json",
    payload: JSON.stringify(data),
    muteHttpExceptions: true,
    headers: {
      'X-API-KEY': CONFIG.API_KEY,
      'X-Requested-With': 'XMLHttpRequest'
    }
  };

  try {
    var response = UrlFetchApp.fetch(CONFIG.API_URL, options);
    var result = {
      status: response.getResponseCode(),
      content: response.getContentText(),
      headers: response.getAllHeaders()
    };
    try {
      result.data = JSON.parse(result.content);
    } catch (e) {
      result.data = null;
    }
    return result;
  } catch (error) {
    return {
      error: error.toString(),
      message: error.message,
      status: 0
    };
  }
}


// =============================
// UPDATE SYNC STATUS COLUMN
// =============================
function updateStatus(sheet, row, response) {
  var statusCell = sheet.getRange(row, CONFIG.COLUMNS.SYNC_STATUS + 1);
  var statusMessage = new Date().toLocaleString() + ' - ';

  if (response.error) {
    statusMessage += '❌ ' + response.error;
  } else if (response.status === 200 || response.status === 201) {
    statusMessage += '✅ Synced';
    if (response.data && response.data.message) {
      statusMessage += ': ' + response.data.message;
    }
  } else {
    statusMessage += '❌ Error ' + response.status;
    if (response.content) {
      try {
        var errorData = JSON.parse(response.content);
        statusMessage += ': ' + (errorData.message || errorData.error || 'Unknown error');
      } catch (e) {
        statusMessage += ': ' + response.content.substring(0, 100);
      }
    }
  }

  statusCell.setValue(statusMessage);
}


// =============================
// DATE FORMATTER
// =============================
function formatDateForAPI(dateValue) {
  if (!dateValue) return '';
  try {
    if (typeof dateValue === 'string') {
      var parsedDate = new Date(dateValue.trim());
      if (!isNaN(parsedDate.getTime())) {
        dateValue = parsedDate;
      }
    }
    if (dateValue instanceof Date) {
      if (isNaN(dateValue.getTime())) return '';
      return formatDateObject(dateValue);
    }
    if (typeof dateValue === 'number') {
      var date = new Date(Math.round((dateValue - 25569) * 86400 * 1000));
      return formatDateObject(date);
    }
    return '';
  } catch (e) {
    return '';
  }
}

function formatDateObject(date) {
  return date.getFullYear() + '-' +
    String(date.getMonth() + 1).padStart(2, '0') + '-' +
    String(date.getDate()).padStart(2, '0');
}


// =============================
// HELPER - NORMALIZE STATUS
// =============================
function normalizeStatus(status) {
  if (!status) return '';
  return status.charAt(0).toUpperCase() + status.slice(1).toLowerCase();
}


// =============================
// MENU FOR MANUAL SYNC
// =============================
function onOpen() {
  SpreadsheetApp.getUi()
    .createMenu('Certificate Sync')
    .addItem('Sync All Pending', 'syncAllRows')
    .addToUi();
}

function syncAllRows() {
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName(CONFIG.SHEET_NAME);
  if (!sheet) return;
  var lastRow = sheet.getLastRow();
  for (var row = CONFIG.HEADER_ROW + 1; row <= lastRow; row++) {
    var statusValue = String(sheet.getRange(row, CONFIG.COLUMNS.STATUS + 1).getValue()).trim();
    if (normalizeStatus(statusValue) === "Pending") {
      var fakeEvent = { source: SpreadsheetApp.getActiveSpreadsheet(), range: sheet.getRange(row, CONFIG.COLUMNS.STATUS + 1) };
      onEdit(fakeEvent);
      Utilities.sleep(500);
    }
  }
}



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
  var response = {
    success: false,
    message: "",
    debug: null
  };

  try {
    // Log the incoming request for debugging
    var debugInfo = {
      parameters: e.parameter || {},
      timestamp: new Date().toISOString(),
      source: 'web_app'
    };

    // Enable CORS
    var output = ContentService.createTextOutput();
    output.setMimeType(ContentService.MimeType.JSON);

    // Check if parameters exist
    if (!e.parameter) {
      response.message = "No parameters provided";
      response.debug = debugInfo;
      output.setContent(JSON.stringify(response));
      return output;
    }

    var certificateNo = String(e.parameter.certificate_no || "").trim();
    var newStatus = String(e.parameter.status || "").trim();
    var source = String(e.parameter.source || "unknown");

    // Add to debug info
    debugInfo.certificateNo = certificateNo;
    debugInfo.newStatus = newStatus;
    debugInfo.source = source;

    if (!certificateNo || !newStatus) {
      response.message = "Missing certificate_no or status";
      response.debug = debugInfo;
      output.setContent(JSON.stringify(response));
      return output;
    }

    // Normalize status (capitalize first letter, rest lowercase)
    newStatus = newStatus.charAt(0).toUpperCase() + newStatus.slice(1).toLowerCase();
    debugInfo.normalizedStatus = newStatus;

    // Get the active spreadsheet and sheet
    var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
    var sheet = spreadsheet.getSheetByName(CONFIG.SHEET_NAME);

    if (!sheet) {
      response.message = "Sheet '" + CONFIG.SHEET_NAME + "' not found";
      response.debug = debugInfo;
      output.setContent(JSON.stringify(response));
      return output;
    }

    debugInfo.sheetName = sheet.getName();
    debugInfo.lastRow = sheet.getLastRow();

    // Get the certificate number and status columns
    var certNoRange = sheet.getRange(CONFIG.HEADER_ROW + 1, CONFIG.COLUMNS.CERTIFICATE_NO + 1, sheet.getLastRow() - CONFIG.HEADER_ROW, 1);
    var certNos = certNoRange.getValues();

    var updated = false;
    var rowIndex = -1;

    // Search for the certificate number
    for (var i = 0; i < certNos.length; i++) {
      var certValue = String(certNos[i][0] || "").trim();
      if (certValue === certificateNo) {
        rowIndex = i + CONFIG.HEADER_ROW + 1; // Convert to 1-based row index
        break;
      }
    }

    debugInfo.foundRow = rowIndex > 0 ? rowIndex : "Not found";

    if (rowIndex > 0) {
      // Get the current status for logging
      var currentStatus = sheet.getRange(rowIndex, CONFIG.COLUMNS.STATUS + 1).getValue();
      debugInfo.currentStatus = currentStatus;

      // Only update if the status is different
      if (String(currentStatus).trim() !== newStatus) {
        // Update the status
        sheet.getRange(rowIndex, CONFIG.COLUMNS.STATUS + 1).setValue(newStatus);

        // Update the sync status column to indicate web app update
        var syncStatusCell = sheet.getRange(rowIndex, CONFIG.COLUMNS.SYNC_STATUS + 1);
        var currentSyncStatus = String(syncStatusCell.getValue() || "");
        var timestamp = new Date().toLocaleString();
        var newSyncStatus = timestamp + ' - ✅ Updated from ' + source + ' to ' + newStatus;

        // If there's already a sync status, append to it
        if (currentSyncStatus) {
          newSyncStatus = currentSyncStatus + '\n' + newSyncStatus;
        }

        syncStatusCell.setValue(newSyncStatus);

        updated = true;
        response.success = true;
        response.message = "Status updated from '" + String(currentStatus).trim() + "' to '" + newStatus + "' via " + source;
      } else {
        response.success = true;
        response.message = "Status was already '" + newStatus + "', no update needed";
      }
    } else {
      response.message = "Certificate not found: " + certificateNo;
    }

    debugInfo.updated = updated;

    response.debug = debugInfo;
    output.setContent(JSON.stringify(response));
    return output;

  } catch (err) {
    // Log the error
    console.error("Error in handleWebUpdate: " + err.toString());

    // Prepare error response
    response.message = "An error occurred: " + err.toString();
    if (typeof debugInfo !== 'undefined') {
      response.debug = debugInfo;
    }

    var errorOutput = ContentService.createTextOutput(JSON.stringify(response));
    errorOutput.setMimeType(ContentService.MimeType.JSON);
    return errorOutput;
  }
}