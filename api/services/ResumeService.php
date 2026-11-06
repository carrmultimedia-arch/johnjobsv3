<?php

class ResumeService extends BaseService {
    protected $table = 'resumes';
    
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $metadata = [];
        if (isset($data['target_role'])) {
            $metadata['target_role'] = $data['target_role'];
            unset($data['target_role']);
        }
        if (isset($data['company'])) {
            $metadata['company'] = $data['company'];
            unset($data['company']);
        }
        if (isset($data['keywords'])) {
            $metadata['keywords'] = $data['keywords'];
            unset($data['keywords']);
        }
        
        if (isset($data['name'])) {
            $data['title'] = $data['name'];
            unset($data['name']);
        }
        
        $data['metadata'] = json_encode($metadata);
        // Some deployed schemas predate the CURRENT_TIMESTAMP defaults in
        // database/schema.sql. Set these explicitly so new resumes never get
        // zero dates and still sort correctly on those installations.
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
        $metadata = [];
        if (isset($data['target_role'])) {
            $metadata['target_role'] = $data['target_role'];
            unset($data['target_role']);
        }
        if (isset($data['company'])) {
            $metadata['company'] = $data['company'];
            unset($data['company']);
        }
        if (isset($data['keywords'])) {
            $metadata['keywords'] = $data['keywords'];
            unset($data['keywords']);
        }
        
        if (isset($data['name'])) {
            $data['title'] = $data['name'];
            unset($data['name']);
        }
        
        if (!empty($metadata)) {
            $existing = $this->getById($id);
            $existingMetadata = json_decode($existing['metadata'] ?? '{}', true);
            $metadata = array_merge($existingMetadata, $metadata);
            $data['metadata'] = json_encode($metadata);
        }
        
        $columns = array_keys($data);
        $setClauses = array_map(function($col) { return "$col = ?"; }, $columns);
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $values = array_merge(array_values($data), [$id]);
        $stmt->execute($values);
        return $this->getById($id);
    }

    public function getActiveResume()
    {
        $stmt = $this->db->prepare("SELECT value FROM settings WHERE `key` = 'active_resume_id' AND deleted_at IS NULL ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $activeSetting = $stmt->fetch();

        if ($activeSetting && is_numeric($activeSetting['value'])) {
            $resume = $this->getById((int)$activeSetting['value']);
            if ($resume) {
                return $resume;
            }
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }

    public function setActiveResume($id)
    {
        $resume = $this->getById($id);
        if (!$resume) {
            return false;
        }

        $stmt = $this->db->prepare("SELECT id FROM settings WHERE `key` = 'active_resume_id' AND deleted_at IS NULL LIMIT 1");
        $stmt->execute();
        $existing = $stmt->fetch();

        if ($existing) {
            $update = $this->db->prepare("UPDATE settings SET value = ? WHERE id = ?");
            $update->execute([$id, $existing['id']]);
        } else {
            $insert = $this->db->prepare("INSERT INTO settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $insert->execute(['active_resume_id', (string)$id]);
        }

        return $resume;
    }
}
