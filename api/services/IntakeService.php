<?php

require_once ROOT_PATH . '/api/services/BaseService.php';

class IntakeService extends BaseService
{
    protected $table = 'intake_queue';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll($status = 'new')
    {
        if ($status === 'all') {
            $sql = "SELECT * FROM {$this->table} ORDER BY date_added DESC";
            return $this->db->query($sql)->fetchAll();
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE status = ? ORDER BY date_added DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (company_name, job_title, location, source, score, summary, salary, employment_type, apply_link, required_skills, why_match, notes, status, date_added) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['company_name'] ?? '',
            $data['job_title'] ?? '',
            $data['location'] ?? null,
            $data['source'] ?? null,
            $data['score'] ?? null,
            $data['summary'] ?? null,
            $data['salary'] ?? null,
            $data['employment_type'] ?? null,
            $data['apply_link'] ?? null,
            $data['required_skills'] ?? null,
            $data['why_match'] ?? null,
            $data['notes'] ?? null
        ]);
        return $this->getById($this->db->lastInsertId());
    }

    public function bulkCreate($items)
    {
        if (empty($items) || !is_array($items)) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (company_name, job_title, location, source, score, summary, salary, employment_type, apply_link, required_skills, why_match, notes, status, date_added) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new', NOW())";
        $stmt = $this->db->prepare($sql);

        foreach ($items as $item) {
            $stmt->execute([
                $item['company_name'] ?? '',
                $item['job_title'] ?? '',
                $item['location'] ?? null,
                $item['source'] ?? null,
                $item['score'] ?? null,
                $item['summary'] ?? null,
                $item['salary'] ?? null,
                $item['employment_type'] ?? null,
                $item['apply_link'] ?? null,
                $item['required_skills'] ?? null,
                $item['why_match'] ?? null,
                $item['notes'] ?? null
            ]);
        }

        return true;
    }

    public function dismiss($id)
    {
        $sql = "UPDATE {$this->table} SET status = 'dismissed', date_actioned = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function import($id)
    {
        $intake = $this->getById($id);
        if (!$intake) {
            return false;
        }

        $companyId = $this->findOrCreateCompany($intake['company_name']);
        if (!$companyId) {
            return false;
        }

        $jobId = $this->findOrCreateJob($companyId, $intake['job_title'], $intake['location'], $intake);
        if (!$jobId) {
            return false;
        }

        $applicationId = $this->createApplication($companyId, $jobId, $intake);
        if (!$applicationId) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET status = 'imported', date_actioned = NOW(), imported_application_id = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$applicationId, $id]);

        return $applicationId;
    }

    private function findOrCreateCompany($companyName)
    {
        $sql = "SELECT id FROM companies WHERE LOWER(name) = LOWER(?) AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyName]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            return $existing['id'];
        }

        $sql = "INSERT INTO companies (name, status, created_at, updated_at) VALUES (?, 'active', NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyName]);
        return $this->db->lastInsertId();
    }

    private function findOrCreateJob($companyId, $jobTitle, $location, $intake)
    {
        $sql = "SELECT id FROM jobs WHERE company_id = ? AND title = ? AND location = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyId, $jobTitle, $location]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            return $existing['id'];
        }

        $sql = "INSERT INTO jobs (company_id, title, location, description, employment_type, salary_range, source_url, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'open', NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $companyId,
            $jobTitle,
            $location,
            $intake['summary'] ?? null,
            $intake['employment_type'] ?? null,
            $intake['salary'] ?? null,
            $intake['apply_link'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    private function createApplication($companyId, $jobId, $intake)
    {
        $columns = ['company_id', 'job_id', 'status', 'created_at', 'updated_at'];
        $values = [$companyId, $jobId, 'draft', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')];

        $optionalFields = [
            'company' => $intake['company_name'] ?? null,
            'salary' => $intake['salary'] ?? null,
            'url' => $intake['apply_link'] ?? null,
            'location' => $intake['location'] ?? null,
            'notes' => $intake['notes'] ?? null
        ];

        $checkSql = "SHOW COLUMNS FROM applications";
        $existingColumns = $this->db->query($checkSql)->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($optionalFields as $field => $value) {
            if (in_array($field, $existingColumns) && $value !== null) {
                $columns[] = $field;
                $values[] = $value;
            }
        }

        $sql = "INSERT INTO applications (" . implode(', ', $columns) . ") VALUES (" . implode(', ', array_fill(0, count($values), '?')) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        
        return $this->db->lastInsertId();
    }
}