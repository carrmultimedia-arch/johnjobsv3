<?php

require_once ROOT_PATH . '/api/services/BaseService.php';
require_once ROOT_PATH . '/api/models/AnalyticsModel.php';

class AnalyticsService extends BaseService
{
    private AnalyticsModel $model;

    public function __construct()
    {
        $this->model = new AnalyticsModel();
        parent::__construct($this->model->getTable());
    }

    public function getOverview(): array
    {
        $stats = [];
        
        // Application stats
        $stats['applications'] = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'applied' THEN 1 ELSE 0 END) as applied,
            SUM(CASE WHEN status = 'interview' THEN 1 ELSE 0 END) as interviewing,
            SUM(CASE WHEN status = 'offer' THEN 1 ELSE 0 END) as offers,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM applications WHERE deleted_at IS NULL")->fetch();
        
        // Prospect stats
        $stats['prospects'] = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as high_rating,
            AVG(rating) as avg_rating
            FROM prospects WHERE deleted_at IS NULL")->fetch();
        
        // Conversation stats
        $stats['conversations'] = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN follow_up_needed = 1 THEN 1 ELSE 0 END) as follow_ups_needed,
            channel,
            COUNT(*) as count
            FROM conversations WHERE deleted_at IS NULL GROUP BY channel")->fetchAll();
        
        // Document stats
        $stats['documents'] = $this->db->query("SELECT 
            document_type,
            COUNT(*) as count
            FROM documents WHERE deleted_at IS NULL GROUP BY document_type")->fetchAll();
        
        // Weekly activity (last 7 days)
        $stats['weekly_activity'] = $this->db->query("SELECT 
            DATE(created_at) as date,
            COUNT(*) as count
            FROM applications WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at) ORDER BY date DESC")->fetchAll();
        
        return $stats;
    }

    public function getApplicationFunnel(): array
    {
        return $this->db->query("SELECT 
            status,
            COUNT(*) as count
            FROM applications WHERE deleted_at IS NULL GROUP BY status ORDER BY 
            FIELD(status, 'draft', 'new', 'applied', 'interview', 'offer', 'rejected', 'ghosted')")->fetchAll();
    }

    public function getSourceBreakdown(): array
    {
        return $this->db->query("SELECT 
            method as source,
            COUNT(*) as count
            FROM applications WHERE deleted_at IS NULL AND method IS NOT NULL 
            GROUP BY method ORDER BY count DESC")->fetchAll();
    }

    public function getTimeToResponse(): array
    {
        return $this->db->query("SELECT 
            a1.company,
            a1.title,
            DATEDIFF(a1.applied_at, a1.created_at) as days_to_apply,
            DATEDIFF(COALESCE(a1.follow_up_at, NOW()), a1.applied_at) as days_since_apply
            FROM applications a1 WHERE a1.deleted_at IS NULL AND a1.applied_at IS NOT NULL
            ORDER BY a1.applied_at DESC LIMIT 20")->fetchAll();
    }

    public function getConversionRates(): array
    {
        $total = $this->db->query("SELECT COUNT(*) as count FROM applications WHERE deleted_at IS NULL")->fetch()['count'];
        
        if ($total == 0) {
            return ['applied_to_interview' => 0, 'interview_to_offer' => 0, 'overall' => 0];
        }
        
        $interviews = $this->db->query("SELECT COUNT(*) as count FROM applications WHERE deleted_at IS NULL AND status = 'interview'")->fetch()['count'];
        $offers = $this->db->query("SELECT COUNT(*) as count FROM applications WHERE deleted_at IS NULL AND status = 'offer'")->fetch()['count'];
        
        return [
            'applied_to_interview' => $total > 0 ? round(($interviews / $total) * 100, 1) : 0,
            'interview_to_offer' => $interviews > 0 ? round(($offers / $interviews) * 100, 1) : 0,
            'overall' => $total > 0 ? round(($offers / $total) * 100, 1) : 0
        ];
    }

    public function getEntityCounts(): array
    {
        $tables = ['companies', 'jobs', 'applications', 'interviews', 'prospects', 'recruiters'];
        $counts = [];
        foreach ($tables as $table) {
            $counts[$table] = (int)$this->db->query("SELECT COUNT(*) as count FROM {$table} WHERE deleted_at IS NULL")->fetch()['count'];
        }
        return $counts;
    }

    public function getRecentActivity($start = null, $end = null): array
    {
        $sql = "SELECT id, company, title, status, created_at FROM applications WHERE deleted_at IS NULL";
        $params = [];

        if ($start !== null) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $start;
        }
        if ($end !== null) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $end;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
