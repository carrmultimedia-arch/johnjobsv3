<?php

class SavedSearchService extends BaseService {
    protected $table = 'saved_searches';
    
    public function getAll() {
        $sql = "SELECT *, query as url FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT *, query as url FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        if (isset($data['url'])) {
            $data['query'] = $data['url'];
            unset($data['url']);
        }
        
        if (isset($data['filters']) && is_array($data['filters'])) {
            $data['filters'] = json_encode($data['filters']);
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
        if (isset($data['url'])) {
            $data['query'] = $data['url'];
            unset($data['url']);
        }
        
        if (isset($data['filters']) && is_array($data['filters'])) {
            $data['filters'] = json_encode($data['filters']);
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
