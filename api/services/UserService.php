<?php

require_once ROOT_PATH . '/api/services/BaseService.php';
require_once ROOT_PATH . '/api/models/UserModel.php';

class UserService extends BaseService
{
    private UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
        parent::__construct($this->model->getTable());
    }

    public function getAll()
    {
        $sql = "SELECT id, name, email, role, created_at FROM {$this->table} WHERE deleted_at IS NULL ORDER BY name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT id, name, email, role, created_at FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (name, email, password_hash, role, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'] ?? '',
            $data['email'] ?? null,
            $data['password_hash'] ?? null,
            $data['role'] ?? 'user'
        ]);
        return $this->getById($this->db->lastInsertId());
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [];
        
        foreach (['name', 'email', 'password_hash', 'role'] as $field) {
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

    public function getByEmail($email)
    {
        $sql = "SELECT id, name, email, role FROM {$this->table} WHERE email = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
