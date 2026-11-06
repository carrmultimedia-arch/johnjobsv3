<?php

class ApplicationService extends BaseService {
    protected $table = 'applications';
    protected $fillable = ['sort_order']; // Only allow sort_order in batch updates
    
    public function getAll() {
        $sql = "SELECT *, applied_at as date_applied FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT *, applied_at as date_applied FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getPipeline() {
        return $this->db->query("SELECT status, COUNT(*) AS count FROM {$this->table} WHERE deleted_at IS NULL GROUP BY status ORDER BY FIELD(status, 'draft', 'new', 'applied', 'interview', 'offer', 'rejected', 'ghosted')")->fetchAll();
    }

    public function setStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
    
    public function batchUpdateSort($updates) {
    foreach ($updates as $update) {
        $id = $update['id'];
        $sortOrder = $update['sort_order'];
        
        $sql = "UPDATE {$this->table} SET sort_order = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sortOrder, $id]);
    }
    
    return true;
}
    
    public function create($data) {
        if (isset($data['date_applied'])) {
            $data['applied_at'] = $data['date_applied'];
            unset($data['date_applied']);
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->getById($this->db->lastInsertId());
    }
    
    public function update($id, $data) {
        if (isset($data['date_applied'])) {
            $data['applied_at'] = $data['date_applied'];
            unset($data['date_applied']);
        }
        
        $columns = array_keys($data);
        $setClauses = array_map(function($col) { return "$col = ?"; }, $columns);
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $values = array_merge(array_values($data), [$id]);
        $stmt->execute($values);
        return $this->getById($id);
    }
}
