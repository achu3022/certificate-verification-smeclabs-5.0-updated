<?php

namespace App\Libraries;

use CodeIgniter\View\Cell;

class CertificateCell extends Cell
{
    public function generateTableBody(array $params = []): string
    {
        if (empty($params['certificates'])) {
            return '<tr><td colspan="8" class="text-center">No certificates found</td></tr>';
        }

        $html = '';
        $counter = ($this->request->getGet('page') ?? 1) * 10 - 9;
        
        foreach ($params['certificates'] as $certificate) {
            $statusClass = strtolower($certificate['status']) === 'active' ? 'success' : 
                          (strtolower($certificate['status']) === 'pending' ? 'warning' : 'danger');
            
            $html .= '<tr>';
            $html .= '<td class="px-3">' . $counter++ . '</td>';
            $html .= '<td class="px-4">' . esc($certificate['certificate_no']) . '</td>';
            $html .= '<td>' . esc($certificate['admission_no'] ?? 'N/A') . '</td>';
            $html .= '<td>' . esc($certificate['student_name']) . '</td>';
            $html .= '<td>' . esc($certificate['course']) . '</td>';
            $html .= '<td>' . date('d M Y', strtotime($certificate['date_of_issue'])) . '</td>';
            $html .= '<td><span class="badge bg-' . $statusClass . '">' . $certificate['status'] . '</span></td>';
            $html .= '<td class="text-end">';
            $html .= '<div class="btn-group">';
            $html .= '<button class="btn btn-sm btn-outline-primary" onclick="viewCertificate(' . $certificate['id'] . ')"><i class="fas fa-eye"></i></button>';
            $html .= '<button class="btn btn-sm btn-outline-secondary" onclick="editCertificate(' . $certificate['id'] . ')"><i class="fas fa-edit"></i></button>';
            $html .= '<button class="btn btn-sm btn-outline-danger" onclick="deleteCertificate(' . $certificate['id'] . ')"><i class="fas fa-trash"></i></button>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        return $html;
    }
}
