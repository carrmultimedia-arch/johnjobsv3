<?php

class ExportService extends BaseService {
    protected $table = 'exports';

    private $tables = [
        'applications',
        'companies',
        'conversations',
        'cover_letters',
        'documents',
        'exports',
        'interviews',
        'jobs',
        'prospects',
        'recruiters',
        'resumes',
        'saved_searches',
        'settings',
        'target_companies',
        'linkedin_stats'
    ];
    
    public function exportAll() {
        $data = [];
        $tablesWithoutDeletedAt = ['linkedin_stats', 'exports'];

        foreach ($this->tables as $table) {
            $sql = "SELECT * FROM {$table}";
            if (!in_array($table, $tablesWithoutDeletedAt, true)) {
                $sql .= " WHERE deleted_at IS NULL";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $data[$table] = $stmt->fetchAll();
        }

        return $data;
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
