<?php

class TargetCompanyService extends BaseService {
    protected $table = 'target_companies';
    
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
