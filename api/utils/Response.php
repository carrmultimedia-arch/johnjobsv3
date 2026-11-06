<?php

class Response {
    public static function success($data, $isList = false) {
        if ($isList) {
            return [
                'success' => true,
                'data' => [
                    'items' => $data,
                    'total' => count($data)
                ],
                'error' => null
            ];
        }
        return [
            'success' => true,
            'data' => $data,
            'error' => null
        ];
    }
    
    public static function error($message) {
        return [
            'success' => false,
            'data' => null,
            'error' => [
                'message' => $message
            ]
        ];
    }
    
    public static function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
