<?php

class FileStorage {
    private static $basePath;
    
    public static function init() {
        self::$basePath = ROOT_PATH . '/documents';
        $dirs = ['resumes', 'cover_letters', 'job_descriptions', 'notes', 'exports'];
        foreach ($dirs as $dir) {
            $fullPath = self::$basePath . '/' . $dir;
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
        }
    }
    
    public static function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return $text ?: 'untitled';
    }
    
    public static function write($documentType, $title, $id, $content) {
        self::init();
        $dir = self::$basePath . '/' . $documentType . 's';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $slug = self::slugify($title);
        $filename = $slug . '-' . $id . '.txt';
        $filepath = $dir . '/' . $filename;
        file_put_contents($filepath, $content);
        return '/documents/' . $documentType . 's/' . $filename;
    }
    
    public static function read($filepath) {
        $fullPath = ROOT_PATH . $filepath;
        if (file_exists($fullPath)) {
            return file_get_contents($fullPath);
        }
        return '';
    }
    
    public static function delete($filepath) {
        $fullPath = ROOT_PATH . $filepath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
