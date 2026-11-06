<?php

class ProspectService extends BaseService {
    protected $table = 'prospects';
    
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function batchDiscard($ids) {
        if (empty($ids)) {
            return false;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($ids);
    }

    public function getByCompanyId($companyId) {
        $sql = "SELECT * FROM {$this->table} WHERE company_id = ? AND deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll();
    }

    public function review($id) {
        $sql = "UPDATE {$this->table} SET status = 'reviewed' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function batchUpdateSort($updates) {
        if (empty($updates) || !is_array($updates)) {
            return false;
        }
        $sql = "UPDATE {$this->table} SET sort_order = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        foreach ($updates as $item) {
            if (!isset($item['id']) || !isset($item['sort_order'])) {
                continue;
            }
            $stmt->execute([$item['sort_order'], $item['id']]);
        }
        return true;
    }
}