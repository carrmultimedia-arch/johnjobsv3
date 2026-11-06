<?php

class CoverLetterService extends BaseService {
    protected $table = 'cover_letters';
    
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByJobId($jobId) {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND JSON_EXTRACT(metadata, '$.job_id') = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$jobId]);
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $metadata = [];

        // company is presentation metadata, not a cover_letters column.
        // Keep it before composing the display title so list views can show it.
        if (isset($data['company'])) {
            $metadata['company'] = $data['company'];
            $company = $data['company'];
            unset($data['company']);
            if (!empty($data['title'])) {
                $data['title'] = $company . ' - ' . $data['title'];
            }
        }
        
        if (isset($data['hiring_manager'])) {
            $metadata['hiring_manager'] = $data['hiring_manager'];
            unset($data['hiring_manager']);
        }
        if (isset($data['tone'])) {
            $metadata['tone'] = $data['tone'];
            unset($data['tone']);
        }
        if (isset($data['jd_text'])) {
            $metadata['jd_text'] = $data['jd_text'];
            unset($data['jd_text']);
        }
        if (isset($data['company_detail'])) {
            $metadata['company_detail'] = $data['company_detail'];
            unset($data['company_detail']);
        }
        
        if (isset($data['generated_text'])) {
            $data['content'] = $data['generated_text'];
            unset($data['generated_text']);
        }
        
        $data['metadata'] = json_encode($metadata);
        // Explicit timestamps support older deployed schemas that have no
        // DEFAULT CURRENT_TIMESTAMP on these columns.
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

        if (isset($data['company'])) {
            $metadata['company'] = $data['company'];
            unset($data['company']);
        }
        
        if (isset($data['hiring_manager'])) {
            $metadata['hiring_manager'] = $data['hiring_manager'];
            unset($data['hiring_manager']);
        }
        if (isset($data['tone'])) {
            $metadata['tone'] = $data['tone'];
            unset($data['tone']);
        }
        if (isset($data['jd_text'])) {
            $metadata['jd_text'] = $data['jd_text'];
            unset($data['jd_text']);
        }
        if (isset($data['company_detail'])) {
            $metadata['company_detail'] = $data['company_detail'];
            unset($data['company_detail']);
        }
        
        if (isset($data['generated_text'])) {
            $data['content'] = $data['generated_text'];
            unset($data['generated_text']);
        }
        
        if (!empty($metadata)) {
            $existing = $this->getById($id);
            $existingMetadata = json_decode($existing['metadata'] ?? '{}', true);
            $metadata = array_merge($existingMetadata, $metadata);
            $data['metadata'] = json_encode($metadata);
        }
        
        $columns = array_keys($data);
        if (empty($columns)) {
            return $this->getById($id);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        $columns = array_keys($data);
        $setClauses = array_map(function($col) { return "$col = ?"; }, $columns);
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $values = array_merge(array_values($data), [$id]);
        $stmt->execute($values);
        return $this->getById($id);
    }
}
