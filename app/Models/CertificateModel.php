<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificateModel extends Model
{
    protected $table            = 'certificates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'certificate_no',
        'admission_no',
        'course',
        'student_name',
        'start_date',
        'end_date',
        'date_of_issue',
        'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'certificate_no' => 'required|max_length[50]|is_unique[certificates.certificate_no,id,{id}]',
        'student_name'  => 'required|max_length[100]',
        'course'        => 'permit_empty|max_length[100]',
        'admission_no'  => 'permit_empty|max_length[50]',
        'start_date'    => 'permit_empty|valid_date[Y-m-d]',
        'end_date'      => 'permit_empty|valid_date[Y-m-d]',
        'date_of_issue' => 'required|valid_date[Y-m-d]',
        'status'        => 'permit_empty|in_list[Pending,Verified,Rejected]'
    ];

    protected $validationMessages = [
        'certificate_no' => [
            'required' => 'Certificate number is required',
            'max_length' => 'Certificate number cannot exceed 50 characters',
            'is_unique' => 'This certificate number already exists'
        ],
        'student_name' => [
            'required' => 'Student name is required',
            'max_length' => 'Student name cannot exceed 100 characters'
        ],
        'course' => [
            'max_length' => 'Course name cannot exceed 100 characters'
        ],
        'admission_no' => [
            'max_length' => 'Admission number cannot exceed 50 characters'
        ],
        'date_of_issue' => [
            'required' => 'Date of issue is required',
            'valid_date' => 'Please enter a valid date'
        ],
        'status' => [
            'in_list' => 'Invalid status value'
        ]
    ];

    public function validateUnique($data, $id = null)
    {
        // Check certificate number uniqueness
        if (isset($data['certificate_no'])) {
            $exists = $this->where('certificate_no', $data['certificate_no'])
                         ->where('id !=', $id)
                         ->first();
            if ($exists) {
                $this->validation->setError('certificate_no', 'This certificate number already exists');
                return false;
            }
        }

        return true;
    }
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
