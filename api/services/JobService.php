<?php

require_once ROOT_PATH . '/api/services/BaseService.php';
require_once ROOT_PATH . '/api/models/JobModel.php';

class JobService extends BaseService
{
    private JobModel $model;

    public function __construct()
    {
        $this->model = new JobModel();
        parent::__construct($this->model->getTable());
    }

    public function getAll()
    {
        $sql = "SELECT j.*, c.name as company_name FROM {$this->table} j 
                LEFT JOIN companies c ON j.company_id = c.id 
                WHERE j.deleted_at IS NULL ORDER BY j.posted_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT j.*, c.name as company_name FROM {$this->table} j 
                LEFT JOIN companies c ON j.company_id = c.id 
                WHERE j.id = ? AND j.deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (company_id, title, description, location, employment_type, salary_range, source_url, external_id, status, posted_at, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['company_id'] ?? null,
            $data['title'] ?? '',
            $data['description'] ?? null,
            $data['location'] ?? null,
            $data['employment_type'] ?? null,
            $data['salary_range'] ?? null,
            $data['source_url'] ?? null,
            $data['external_id'] ?? null,
            $data['status'] ?? 'open',
            $data['posted_at'] ?? null
        ]);
        return $this->getById($this->db->lastInsertId());
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [];
        
        foreach (['company_id', 'title', 'description', 'location', 'employment_type', 'salary_range', 'source_url', 'external_id', 'status', 'posted_at'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->getById($id);
    }

    public function batchUpdateSort($updates) {
        try {
            $this->db->beginTransaction();
            
            foreach ($updates as $update) {
                $id = $update['id'];
                $sortOrder = $update['sort_order'];
                
                $sql = "UPDATE {$this->table} SET sort_order = ? WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$sortOrder, $id]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getByCompanyId($companyId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE company_id = ? AND deleted_at IS NULL ORDER BY posted_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll();
    }

    public function getStats()
    {
        $total = $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL")->fetch()['count'];
        $byStatus = $this->db->query("SELECT status, COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL GROUP BY status ORDER BY count DESC")->fetchAll();
        $byType = $this->db->query("SELECT employment_type, COUNT(*) as count FROM {$this->table} WHERE deleted_at IS NULL GROUP BY employment_type ORDER BY count DESC")->fetchAll();

        return [
            'total' => (int)$total,
            'by_status' => $byStatus,
            'by_type' => $byType
        ];
    }
}
