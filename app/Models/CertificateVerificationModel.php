<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificateVerificationModel extends Model
{
    protected $table            = 'certificate_verifications';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'certificate_id',
        'name',
        'designation',
        'company_name',
        'contact_no',
        'country',
        'ip_address',
        'user_agent',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
