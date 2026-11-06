<?php

require_once ROOT_PATH . '/api/services/BaseService.php';
require_once ROOT_PATH . '/api/models/RecruiterModel.php';

class RecruiterService extends BaseService
{
    private RecruiterModel $model;

    public function __construct()
    {
        $this->model = new RecruiterModel();
        parent::__construct($this->model->getTable());
    }

    public function getAll()
    {
        $sql = "SELECT r.*, c.name as company_name FROM {$this->table} r 
                LEFT JOIN companies c ON r.company_id = c.id 
                WHERE r.deleted_at IS NULL ORDER BY r.name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT r.*, c.name as company_name FROM {$this->table} r 
                LEFT JOIN companies c ON r.company_id = c.id 
                WHERE r.id = ? AND r.deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (company_id, name, email, phone, linkedin, role, notes, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['company_id'] ?? null,
            $data['name'] ?? '',
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['linkedin'] ?? null,
            $data['role'] ?? null,
            $data['notes'] ?? null
        ]);
        return $this->getById($this->db->lastInsertId());
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [];
        
        foreach (['company_id', 'name', 'email', 'phone', 'linkedin', 'role', 'notes'] as $field) {
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

    public function delete($id)
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getByCompanyId($companyId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE company_id = ? AND deleted_at IS NULL ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll();
    }
}
