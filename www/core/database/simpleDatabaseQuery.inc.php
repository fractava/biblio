<?php
namespace \core\database;

class simpleDatabaseQuery {
    public $statement;
    private $databaseController;

    function __construct($sql,$parameters) {
        $this->databaseController = new databaseController();
        $this->statement = $this->databaseController->prepare($sql);
        $this->statement->execute($parameters);
    }
    function fetchAll() {
        return $this->statement->fetchAll();
    }
    function fetch() {
        return $this->statement->fetch();
    }
}
