<?php

class BaseService {
    protected $db;
    protected $table;
    
    public function __construct($table = null) {
        $this->db = DB::getInstance();
        if ($table !== null) {
            $this->table = $table;
        }
    }
    
    
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        // The deployed MySQL 5.7 schema has NOT NULL datetime columns without
        // defaults on several tables. Supplying timestamps here protects every
        // resource that uses the generic CRUD service.
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        // These two lightweight tables have no updated_at column in the
        // deployed schema; all other generic CRUD resources do.
        if (!in_array($this->table, ['target_companies', 'linkedin_stats'], true)) {
            $data['updated_at'] = $data['updated_at'] ?? date('Y-m-d H:i:s');
        }
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->getById($this->db->lastInsertId());
    }
    
    public function update($id, $data) {
        $columns = array_keys($data);
        $setClauses = array_map(function($col) { return "$col = ?"; }, $columns);
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $values = array_merge(array_values($data), [$id]);
        $stmt->execute($values);
        return $this->getById($id);
    }
    
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
