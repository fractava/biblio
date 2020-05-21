<?php

namespace \core\database;

class selectQuery extends databaseQuery {
    function init() {
        $this->table = "";
        $this->get = "*";
        $this->conditions = array();
        $this->sortBy = false;
        $this->reverseSort = false;
        $this->limit = false;
        $this->offset = 0;
        $this->sql = "";
        $this->vars = array();
    }
    function from($table) {
        if($this->databaseController->validTable($table)) {
            $this->table = $table;
        }
        
        return $this;
    }
    function get($attributes = array()) {
        if(is_array($attributes)) {
            $newGet = "";
            
            for($i = 0; $i < sizeof($attributes); $i++) {
                if($this->databaseController->validColumn($this->table, $attributes[$i])) {
                    $newGet .= (string) $attributes[$i];
                    if($i !== sizeof($attributes)-1) {
                        $newGet .= ", ";
                    }
                }
            }
        }
        
        $this->get = $newGet;
        
        return $this;
    }
    function getAll() {
        $this->get = "*";
        
        return $this;
    }
    function getCountOf($attribute) {
        if($this->databaseController->validColumn($this->table, $attribute)) {
            $this->get = "COUNT($attribute)";
        }
        
        return $this;
    }
    function where($attribute, $value) {
        if($this->databaseController->validColumn($this->table, $attribute)) {
            $this->conditions[] = array("attribute" => $attribute, "value" => $value);
        }
        
        return $this;
    }
    function and($attribute, $value) {
        if($this->databaseController->validColumn($this->table, $attribute)) {
            $this->conditions[] = array("type" => "AND", "attribute" => $attribute, "value" => $value);
        }
        
        return $this;
    }
    function or($attribute, $value) {
        if($this->databaseController->validColumn($this->table, $attribute)) {
            $this->conditions[] = array("type" => "OR", "attribute" => $attribute, "value" => $value);
        }
        
        return $this;
    }
    function limit($limit) {
        if(is_int($limit)) {
            $this->limit = $limit;
        }
        
        return $this;
    }
    function offset($offset) {
        if(is_int($offset)) {
            $this->offset = (int) $offset;
        }
        
        return $this;
    }
    function sortBy($sortBy) {
        if(!$this->databaseController->validColumn($this->table, $sortBy)) {
            $this->sortBy = $sortBy;
        }
        
        return $this;
    }
    function reverseSort($reverseSort) {
        if(is_bool($reverseSort)){
            $this->reverseSort = $reverseSort;
        }
        
        return $this;
    }
    private function buildSql() {
        $sql = "";
        
        $sql .= "SELECT ";
        $sql .= $this->get;
        $sql .= " FROM ";
        $sql .= $this->table;
        $sql .= " ";
        
        if(!empty($this->conditions)){
            $sql .= "WHERE ";
            $sql .= $this->conditions[0]["attribute"];
            $sql .= " = :";
            $sql .= $this->conditions[0]["attribute"];
            $sql .= " ";
            $this->vars[$this->conditions[0]["attribute"]] = $this->conditions[0]["value"];
            
            for($i = 1; $i < sizeof($this->conditions); $i++) {
                $sql .= $this->conditions[$i]["type"];
                $sql .= " ";
                $sql .= $this->conditions[$i]["attribute"];
                $sql .= " = :";
                $sql .= $this->conditions[$i]["attribute"];
                $sql .= " ";
                $this->vars[$this->conditions[$i]["attribute"]] = $this->conditions[$i]["value"];
            }
        }
        
        if($this->sortBy !== false) {
            $sql .= "SORT BY ";
            $sql .= $this->sortBy;
            
            if($this->reverseSort == true){
                $sql .= " DESC ";
            }else {
                $sql .= " ASC ";
            }
        }
        
        if($this->limit !== false) {
            $sql .= "LIMIT :limit ";
        }
        
        if($this->offset !== 0) {
            $sql .= "OFFSET :offset";
        }
        
        $sql .= ";";
        
        $this->sql = $sql;
    }
    private function bindVars() {
        if($this->limit !== false) {
            $this->statement->bindValue(':limit', $this->limit, \PDO::PARAM_INT);
        }
        
        if($this->offset !== 0) {
            $this->statement->bindValue(':offset', $this->offset, \PDO::PARAM_INT);
        }
        
        foreach ($this->vars as $key => $value) {
            $this->statement->bindValue($key, $value);
        }
    }
    function run() {
        $this->buildSql();
        
        $this->statement = $this->databaseController->prepare($this->sql);
        
        $this->bindVars();
        
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
}

?>
