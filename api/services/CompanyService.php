<?php

require_once ROOT_PATH . '/api/services/BaseService.php';
require_once ROOT_PATH . '/api/models/CompanyModel.php';

class CompanyService extends BaseService
{
    private CompanyModel $model;

    public function __construct()
    {
        $this->model = new CompanyModel();
        parent::__construct($this->model->getTable());
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (name, website, industry, size, location, description, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'] ?? '',
            $data['website'] ?? null,
            $data['industry'] ?? null,
            $data['size'] ?? null,
            $data['location'] ?? null,
            $data['description'] ?? null,
            $data['status'] ?? 'active'
        ]);
        return $this->getById($this->db->lastInsertId());
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [];
        
        foreach (['name', 'website', 'industry', 'size', 'location', 'description', 'status'] as $field) {
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

    public function getStats()
    {
        $counts = $this->db->query("SELECT COUNT(*) AS total FROM {$this->table} WHERE deleted_at IS NULL")->fetch();
        $byIndustry = $this->db->query("SELECT industry, COUNT(*) AS count FROM {$this->table} WHERE deleted_at IS NULL GROUP BY industry ORDER BY count DESC")->fetchAll();
        $byStatus = $this->db->query("SELECT status, COUNT(*) AS count FROM {$this->table} WHERE deleted_at IS NULL GROUP BY status ORDER BY count DESC")->fetchAll();

        return [
            'total' => (int)$counts['total'],
            'by_industry' => $byIndustry,
            'by_status' => $byStatus
        ];
    }

    public function bulkUpdateStatus($ids, $status)
    {
        if (empty($ids) || !is_array($ids)) {
            return false;
        }

        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $params = array_merge([$status], $ids);
        $sql = "UPDATE {$this->table} SET status = ? WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getJobs($companyId)
    {
        $sql = "SELECT * FROM jobs WHERE company_id = ? AND deleted_at IS NULL ORDER BY posted_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll();
    }

    public function delete($id)
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
