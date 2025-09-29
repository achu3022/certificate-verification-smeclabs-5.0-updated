<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class GoogleSheets extends BaseConfig
{
    /**
     * The ID of your Google Spreadsheet
     * You can find this in the URL of your spreadsheet:
     * https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/edit#gid=0
     * 
     * @var string
     */
    public $spreadsheetId = '';
    
    /**
     * The name of the sheet (tab) in your spreadsheet
     *
     * @var string
     */
   public $sheetName = 'Sheet1';

   /**
    * The URL of the Google Apps Script web app
    * This should be the URL of your deployed Apps Script web app
    * that handles status updates from the web application
    *
    * @var string
    */
   public $appsScriptWebAppUrl = 'https://script.google.com/macros/s/AKfycbzcSq0H0b6HPIWwBlbKiTjkiQ9jV_Cz8Q0AtMu8a-ULlZQiy_FjgYl6dAYzrGW4HeXt/exec';
    
    /**
     * Service account credentials for Google API
     * Follow these steps to create service account credentials:
     * 1. Go to Google Cloud Console: https://console.cloud.google.com/
     * 2. Create a new project or select an existing one
     * 3. Enable Google Sheets API
     * 4. Go to "APIs & Services" > "Credentials"
     * 5. Click "Create Credentials" > "Service account"
     * 6. Create a service account and download the JSON key file
     * 7. Save the JSON file in your project
     * 8. Share the Google Sheet with the service account email
     * 
     * @var string
     */
    public $serviceAccountKeyPath = WRITEPATH . 'credentials/service-account-credentials.json';
    
    /**
     * Enable or disable Google Sheets synchronization
     * 
     * @var bool
     */
    public $enabled = true;
    
    /**
     * Maximum number of retry attempts if the Google Sheets update fails
     * 
     * @var int
     */
    public $maxRetries = 3;
    
    /**
     * Timeout in seconds for the Google Sheets API request
     * 
     * @var int
     */
    public $timeout = 30;
    
    /**
     * Enable debug logging
     * 
     * @var bool
     */
    public $debug = true;
}
