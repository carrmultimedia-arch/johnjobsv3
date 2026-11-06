<?php

class Model {
    protected $db;
    
    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function getTable() {
        return $this->table;
    }
}
