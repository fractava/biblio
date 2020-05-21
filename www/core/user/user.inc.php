<?php

namespace \core\user;

use \core\database\selectQuery;
use \core\database\updateQuery;

class user {
    function __construct($initId) {
        $this->id = $initId;
    }
    function getAttribute($attribute) {
        $query = new selectQuery();
        $query->from("users")
        ->get(array($attribute))
        ->where("id", $this->id)
        ->limit(1);
        $result = $query->run()[0][$attribute];
        
        return $result;
    }
    function setAttribute($attribute, $value) {
        $query = new updateQuery();
        $query->update("users")
        ->set($attribute, $value)
        ->where("id", $this->id)
        ->limit(1);
        $query->run();
    }
}
