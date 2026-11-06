<?php

class DocumentService extends BaseService {
    protected $table = 'documents';
    
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $items = $stmt->fetchAll();
        
        foreach ($items as &$item) {
            if (isset($item['file_path'])) {
                $item['content'] = FileStorage::read($item['file_path']);
            }
        }
        
        return $items;
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        
        if ($item && isset($item['file_path'])) {
            $item['content'] = FileStorage::read($item['file_path']);
        }
        
        return $item;
    }

    public function getByType($type) {
        $sql = "SELECT * FROM {$this->table} WHERE document_type = ? AND deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type]);
        $items = $stmt->fetchAll();

        foreach ($items as &$item) {
            if (isset($item['file_path'])) {
                $item['content'] = FileStorage::read($item['file_path']);
            }
        }

        return $items;
    }

    public function getActiveDocuments($limit = 10) {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        foreach ($items as &$item) {
            if (isset($item['file_path'])) {
                $item['content'] = FileStorage::read($item['file_path']);
            }
        }

        return $items;
    }
    
    public function create($data) {
        $content = $data['content'] ?? '';
        unset($data['content']);
        // Standalone documents created from the UI are not attached to an
        // application or company, but the deployed schema requires owners.
        $data['owner_type'] = $data['owner_type'] ?? 'general';
        $data['owner_id'] = $data['owner_id'] ?? 0;
        // The file is written after the row has an id; the production schema
        // requires a non-null path during that initial insert.
        $data['file_path'] = $data['file_path'] ?? '';
        
        $metadata = [];
        if (isset($data['company'])) {
            $metadata['company'] = $data['company'];
            unset($data['company']);
        }
        if (isset($data['job_title'])) {
            $metadata['job_title'] = $data['job_title'];
            unset($data['job_title']);
        }
        
        if (isset($data['type'])) {
            $data['document_type'] = $data['type'];
            unset($data['type']);
        }
        
        if (isset($data['name'])) {
            $data['title'] = $data['name'];
            unset($data['name']);
        }
        
        $data['metadata'] = json_encode($metadata);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        
        $id = $this->db->lastInsertId();
        $documentType = $data['document_type'];
        $title = $data['title'];
        $filepath = FileStorage::write($documentType, $title, $id, $content);
        
        $updateSql = "UPDATE {$this->table} SET file_path = ? WHERE id = ?";
        $updateStmt = $this->db->prepare($updateSql);
        $updateStmt->execute([$filepath, $id]);
        
        return $this->getById($id);
    }
    
    public function update($id, $data) {
        $content = $data['content'] ?? null;
        unset($data['content']);
        
        $metadata = [];
        if (isset($data['company'])) {
            $metadata['company'] = $data['company'];
            unset($data['company']);
        }
        if (isset($data['job_title'])) {
            $metadata['job_title'] = $data['job_title'];
            unset($data['job_title']);
        }
        
        if (isset($data['type'])) {
            $data['document_type'] = $data['type'];
            unset($data['type']);
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
        
        if ($content !== null) {
            $existing = $this->getById($id);
            if (isset($existing['file_path'])) {
                FileStorage::delete($existing['file_path']);
            }
            $documentType = $data['document_type'] ?? $existing['document_type'];
            $title = $data['title'] ?? $existing['title'];
            $filepath = FileStorage::write($documentType, $title, $id, $content);
            $data['file_path'] = $filepath;
        }
        
        $columns = array_keys($data);
        $setClauses = array_map(function($col) { return "$col = ?"; }, $columns);
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $values = array_merge(array_values($data), [$id]);
        $stmt->execute($values);
        
        return $this->getById($id);
    }
    
    public function delete($id) {
        $existing = $this->getById($id);
        if ($existing && isset($existing['file_path'])) {
            FileStorage::delete($existing['file_path']);
        }
        
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
