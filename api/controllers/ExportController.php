<?php

class ExportController extends BaseController {
    public function __construct() {
        $service = new ExportService();
        parent::__construct($service);
    }
    
    public function list() {
        $data = $this->service->exportAll();
        
        $filename = 'jobhunt-export-' . date('Y-m-d-His') . '.json';
        $filepath = ROOT_PATH . '/documents/exports/' . $filename;
        
        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
        
        $exportData = [
            'type' => 'full_export',
            'format' => 'json',
            'file_path' => '/documents/exports/' . $filename,
            'status' => 'completed',
            'metadata' => json_encode(['tables' => array_keys($data)])
        ];
        
        $db = DB::getInstance();
        $sql = "INSERT INTO exports (user_id, type, format, file_path, status, metadata) VALUES (1, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $exportData['type'],
            $exportData['format'],
            $exportData['file_path'],
            $exportData['status'],
            $exportData['metadata']
        ]);
        
        Response::json(Response::success([
            'export' => $exportData,
            'data' => $data
        ]));
    }
    
    public function create() {
        $this->list();
    }
}
