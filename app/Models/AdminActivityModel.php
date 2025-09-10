<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminActivityModel extends Model
{
    protected $table = 'admin_activities';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['admin_id', 'activity_type', 'description', 'ip_address'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    /**
     * Log an admin activity
     *
     * @param int $adminId
     * @param string $activityType
     * @param string $description
     * @return bool
     */
    public function logActivity($adminId, $activityType, $description)
    {
        return $this->insert([
            'admin_id' => $adminId,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => service('request')->getIPAddress()
        ]);
    }

    /**
     * Get recent activities for an admin
     *
     * @param int $adminId
     * @param int $limit
     * @return array
     */
    public function getRecentActivities($adminId, $limit = 5)
    {
        return $this->where('admin_id', $adminId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->find();
    }
}