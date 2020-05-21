<?php

namespace \apps\system\action;

use \core\user\userManagement;

class logout extends \core\network\action {
    function init(){
        return array();
    }
    function run() {
        $userManagement = new userManagement();
        $userManagement->logout();
    }
}
