<?php

class ConversationService extends BaseService {
    protected $table = 'conversations';
    
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByType($type) {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND (channel = ? OR direction = ?) ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type, $type]);
        return $stmt->fetchAll();
    }

    public function getFollowUps() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND follow_up_needed = 1 ORDER BY follow_up_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getThread($threadId) {
        if (!$threadId) {
            return [];
        }

        if (is_numeric($threadId)) {
            $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND (owner_id = ? OR id = ?) ORDER BY created_at ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$threadId, $threadId]);
            return $stmt->fetchAll();
        }

        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.thread_id')) = ? ORDER BY created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$threadId]);
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        if (!isset($data['owner_type'])) {
            $data['owner_type'] = 'general';
        }
        if (!isset($data['owner_id'])) {
            $data['owner_id'] = 0;
        }
        
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $data['metadata'] = json_encode($data['metadata']);
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
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $data['metadata'] = json_encode($data['metadata']);
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
