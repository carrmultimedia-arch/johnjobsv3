<?php

class CalendarController {
    private $db;
    
    public function __construct() {
        $this->db = DB::getInstance();
    }
    
    public function events() {
        $month = $_GET['month'] ?? date('Y-m');
        
        // Validate month format (YYYY-MM)
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            Response::json(Response::error('Invalid month format. Use YYYY-MM'));
            return;
        }
        
        // Calculate date range for the month
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $events = [];
        
        // 1. Get interviews for the month
        $interviewSql = "SELECT 
            DATE(i.scheduled_at) as date,
            TIME(i.scheduled_at) as time,
            'interview' as type,
            CONCAT('Interview — ', a.company, ' — ', i.interview_type) as label,
            i.application_id
        FROM interviews i
        JOIN applications a ON i.application_id = a.id
        WHERE i.deleted_at IS NULL 
        AND i.scheduled_at IS NOT NULL
        AND DATE(i.scheduled_at) BETWEEN ? AND ?
        ORDER BY i.scheduled_at ASC";
        
        $stmt = $this->db->prepare($interviewSql);
        $stmt->execute([$startDate, $endDate]);
        $interviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $events = array_merge($events, $interviews);
        
        // 2. Get conversation follow-ups for the month
        $conversationSql = "SELECT 
            follow_up_date as date,
            NULL as time,
            'followup' as type,
            CONCAT('Follow up — ', COALESCE(contact_name, subject, 'Conversation')) as label,
            CASE WHEN owner_type = 'application' THEN owner_id ELSE NULL END as application_id
        FROM conversations
        WHERE deleted_at IS NULL 
        AND follow_up_needed = 1
        AND follow_up_date IS NOT NULL
        AND follow_up_date BETWEEN ? AND ?
        ORDER BY follow_up_date ASC";
        
        $stmt = $this->db->prepare($conversationSql);
        $stmt->execute([$startDate, $endDate]);
        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $events = array_merge($events, $conversations);
        
        // 3. Get manual calendar events for the month
        $calendarSql = "SELECT 
            event_date as date,
            event_time as time,
            'manual' as type,
            title as label,
            application_id,
            id as event_id
        FROM calendar_events
        WHERE deleted_at IS NULL 
        AND event_date BETWEEN ? AND ?
        ORDER BY event_date ASC, event_time ASC";
        
        $stmt = $this->db->prepare($calendarSql);
        $stmt->execute([$startDate, $endDate]);
        $calendarEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $events = array_merge($events, $calendarEvents);
        
        // Sort all events by date and time
        usort($events, function($a, $b) {
            $dateCompare = strcmp($a['date'], $b['date']);
            if ($dateCompare !== 0) return $dateCompare;
            return strcmp($a['time'] ?? '', $b['time'] ?? '');
        });
        
        Response::json(Response::success($events));
    }
}
