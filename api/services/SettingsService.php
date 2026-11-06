<?php

class SettingsService extends BaseService {
    protected $table = 'settings';
    private const PROFILE_FIELDS = [
        'name', 'email', 'phone', 'linkedin', 'portfolio', 'salary',
        'arrangement', 'target_location', 'bio', 'master_resume', 'api_keys'
    ];
    private const KEY_VALUE_FIELDS = ['key', 'value', 'category', 'user_id'];
    
    public function getAll() {
        // The UI manages one profile record. Key/value records (for example
        // active_resume_id) are internal settings and must not replace it.
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND (`key` IS NULL OR `key` = '') ORDER BY id DESC LIMIT 1";
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
    
    private function normalizeData($data) {
        $mappedData = [];
        foreach ($data as $key => $value) {
            $cleanKey = preg_replace('/^set-/', '', $key);
            if (in_array($cleanKey, array_merge(self::PROFILE_FIELDS, self::KEY_VALUE_FIELDS), true)) {
                $mappedData[$cleanKey] = $value;
            }
        }
        return $mappedData;
    }

    private function quoteIdentifier($column) {
        return '`' . str_replace('`', '``', $column) . '`';
    }

    public function create($data) {
        $mappedData = $this->normalizeData($data);
        if (empty($mappedData)) {
            return false;
        }

        // Preserve support for internal key/value settings and callers that
        // create a standalone setting record.
        if (isset($mappedData['key'])) {
            $mappedData['created_at'] = date('Y-m-d H:i:s');
            $mappedData['updated_at'] = date('Y-m-d H:i:s');
            $columns = array_keys($mappedData);
            $quotedColumns = array_map([$this, 'quoteIdentifier'], $columns);
            $placeholders = array_fill(0, count($columns), '?');
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $quotedColumns) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($mappedData));
            return $this->getById($this->db->lastInsertId());
        }

        $existing = $this->getAll()[0] ?? null;
        if ($existing) {
            return $this->update($existing['id'], $mappedData);
        }

        $mappedData['created_at'] = date('Y-m-d H:i:s');
        $mappedData['updated_at'] = date('Y-m-d H:i:s');

        $columns = array_keys($mappedData);
        $quotedColumns = array_map([$this, 'quoteIdentifier'], $columns);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $quotedColumns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($mappedData));
        return $this->getById($this->db->lastInsertId());
    }
    
    public function update($id, $data) {
        $mappedData = $this->normalizeData($data);
        if (empty($mappedData)) {
            return false;
        }
        $mappedData['updated_at'] = date('Y-m-d H:i:s');
        
        $columns = array_keys($mappedData);
        $setClauses = array_map(function($col) { return $this->quoteIdentifier($col) . ' = ?'; }, $columns);
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_merge(array_values($mappedData), [$id]));
        return $this->getById($id);
    }
    
    public function delete($id) {
        return false;
    }
}
