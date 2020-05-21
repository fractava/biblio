<?php
namespace database;

abstract class databaseQuery {
    function __construct() {
        $this->databaseController = new databaseController();
        $this->init();
    }
}

?>
