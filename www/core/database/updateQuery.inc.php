<?php

namespace \core\database;

class updateQuery extends databaseQuery {
    function init() {
        $this->table = "";
        $this->set = array();
        $this->conditions = array();
        $this->sortBy = false;
        $this->reverseSort = false;
        $this->limit = false;
        $this->sql = "";
        $this->vars = array();
    }
    function update($table) {
        if($this->databaseController->validTable($table)) {
            $this->table = $table;
        }
        
        return $this;
    }
    function set($attribute, $value) {
        if($this->databaseController->validColumn($this->table, $attribute)) {
            $this->set[] = array("attribute" => $attribute, "value" => $value);
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
        
        $sql .= "UPDATE ";
        $sql .= $this->table;
        $sql .= " SET ";

        for($i = 0; $i < sizeof($this->set); $i++) {
            $sql .= $this->set[$i]["attribute"];
            $sql .= " = :";
            $sql .= $this->set[$i]["attribute"];
            $sql .= " ";
            if($i != sizeof($this->set)-1) {
                $sql .= ", ";
            }
            $this->vars[$this->set[$i]["attribute"]] = $this->set[$i]["value"];
        }
        
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
        
        $sql .= ";";
        
        $this->sql = $sql;
    }
    private function bindVars() {
        if($this->limit !== false) {
            $this->statement->bindValue(':limit', $this->limit, \PDO::PARAM_INT);
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
