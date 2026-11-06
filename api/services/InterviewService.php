<?php

require_once ROOT_PATH . '/api/services/BaseService.php';
require_once ROOT_PATH . '/api/models/InterviewModel.php';

class InterviewService extends BaseService
{
    private InterviewModel $model;

    public function __construct()
    {
        $this->model = new InterviewModel();
        parent::__construct($this->model->getTable());
    }

    public function getAll()
    {
        $sql = "SELECT i.*, a.company, a.title as job_title FROM {$this->table} i 
                LEFT JOIN applications a ON i.application_id = a.id 
                WHERE i.deleted_at IS NULL ORDER BY i.scheduled_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT i.*, a.company, a.title as job_title FROM {$this->table} i 
                LEFT JOIN applications a ON i.application_id = a.id 
                WHERE i.id = ? AND i.deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (application_id, recruiter_id, interviewer_name, interviewer_title, location_type, location, interview_type, scheduled_at, completed_at, feedback, outcome, notes, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['application_id'] ?? null,
            $data['recruiter_id'] ?? null,
            $data['interviewer_name'] ?? null,
            $data['interviewer_title'] ?? null,
            $data['location_type'] ?? null,
            $data['location'] ?? null,
            $data['interview_type'] ?? null,
            $data['scheduled_at'] ?? null,
            $data['completed_at'] ?? null,
            $data['feedback'] ?? null,
            $data['outcome'] ?? null,
            $data['notes'] ?? null
        ]);
        return $this->getById($this->db->lastInsertId());
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [];
        
        foreach (['application_id', 'recruiter_id', 'interviewer_name', 'interviewer_title', 'location_type', 'location', 'interview_type', 'scheduled_at', 'completed_at', 'feedback', 'outcome', 'notes'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->getById($id);
    }

    public function delete($id)
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getByApplicationId($applicationId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE application_id = ? AND deleted_at IS NULL ORDER BY scheduled_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$applicationId]);
        return $stmt->fetchAll();
    }

    public function getUpcoming($limit = 20)
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND scheduled_at >= NOW() ORDER BY scheduled_at ASC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByScheduledRange($start = null, $end = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL";
        $params = [];

        if ($start !== null) {
            $sql .= " AND scheduled_at >= ?";
            $params[] = $start;
        }
        if ($end !== null) {
            $sql .= " AND scheduled_at <= ?";
            $params[] = $end;
        }

        $sql .= " ORDER BY scheduled_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
