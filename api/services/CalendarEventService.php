<?php

class CalendarEventService extends BaseService {
    protected $table = 'calendar_events';
    
    // Fillable whitelist to prevent "Unknown column" errors
    protected $fillable = [
        'title',
        'event_date', 
        'event_time',
        'notes',
        'application_id'
    ];
    
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY event_date ASC, event_time ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getByDateRange($start, $end) {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND event_date BETWEEN ? AND ? ORDER BY event_date ASC, event_time ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$start, $end]);
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        // Filter data to only include fillable fields
        $data = array_intersect_key($data, array_flip($this->fillable));
        
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
        // Filter data to only include fillable fields
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $columns = array_keys($data);
        $setClauses = array_map(function($col) { return "$col = ?"; }, $columns);
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $values = array_merge(array_values($data), [$id]);
        $stmt->execute($values);
        return $this->getById($id);
    }
    
    public function delete($id) {
        // Soft delete
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
