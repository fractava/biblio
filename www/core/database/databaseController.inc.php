<?php

namespace \core\database;

use \core\config\configManager;

class databaseController {
    public $pdo;
    public $connected = false;
    
    function __construct() {
        $this->autoConnect();
    }
    
    function connect($host, $name, $user, $password) {
        $this->pdo = new \PDO("mysql:host=$host;dbname=$name", $user, $password);
        $this->connected = true;
    }
    function autoConnect() {
        if(!$this->connected) {
            $this->config = configManager::getConfig();
            $this->connect($this->config["db_host"], $this->config["db_name"], $this->config["db_user"], $this->config["db_password"]);
        }
    }
    
    function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
    function getColumnsOfTable($table) {
        if(!$this->validTable($table)) {
            return false;
        }
        
        $database = $this->config["db_name"];
        
        $query = $this->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :database AND TABLE_NAME = :table");
        $query->execute(array("database" => $database, "table" => $table));
        $columnNames = $query->fetchAll();
        
        $columnNamesResorted = array();
        
        foreach($columnNames as $name){
            $columnNamesResorted[] = $name["COLUMN_NAME"];
        }
        
        return $columnNamesResorted;
    }
    function validTable($table) {
        $query = $this->prepare("SELECT COUNT(TABLE_NAME) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = :database AND TABLE_NAME = :table;");
        $query->execute(array("database" => $this->config["db_name"], "table" => $table));
        $count = $query->fetch()[0];
        
        return $count == 1;
    }
    function validColumn($table, $column) {
        if(!$this->validTable($table)) {
            return false;
        }
        
        if(preg_match("/^[\w.-]*$/", $column) == 0) {
            return false;
        }
        
        $databaseController = new databaseController();
        $columns = $databaseController->getColumnsOfTable($table);
        if(!in_array($column, $columns)){
            return false;
        }
        
        return true;
    }
}
