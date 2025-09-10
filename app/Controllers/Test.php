<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Test extends Controller
{
    public function checkDatabase()
    {
        $db = \Config\Database::connect();
        
        // Check if certificates table exists
        $tables = $db->listTables();
        $tableExists = in_array('certificates', $tables);
        
        if (!$tableExists) {
            die('Error: The certificates table does not exist in the database.');
        }
        
        // Test database connection
        try {
            $db->query('SELECT 1');
            echo 'Database connection successful!<br>';
            
            // Check table structure
            $fields = $db->getFieldData('certificates');
            echo '<pre>';
            print_r($fields);
            echo '</pre>';
            
            // Try to insert a test record
            $testData = [
                'certificate_no' => 'TEST' . time(),
                'admission_no' => 'ADM' . time(),
                'student_name' => 'Test Student',
                'course' => 'Test Course',
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 year')),
                'date_of_issue' => date('Y-m-d'),
                'status' => 'Pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('certificates')->insert($testData);
            $insertId = $db->insertID();
            
            if ($insertId) {
                echo 'Test record inserted successfully! ID: ' . $insertId . '<br>';
                
                // Clean up
                $db->table('certificates')->where('certificate_no', $testData['certificate_no'])->delete();
                echo 'Test record cleaned up.<br>';
            } else {
                echo 'Failed to insert test record. Error: ' . $db->error()['message'] . '<br>';
            }
            
        } catch (\Exception $e) {
            die('Database error: ' . $e->getMessage());
        }
    }
}
