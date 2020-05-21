<?php

namespace \apps\system\action\user;

use \core\session\sessionManager;

class changeFirstName extends \core\network\action{
    public function init(){
        $this->sessionManager = new sessionManager();
        $errors = array();
        
        if(!isset($this->params["newValue"])) {
            $errors[] = 0;
        }
        
        if(!$this->sessionManager->isLoggedIn()) {
            $errors[] = 7;
        }
        
        return $errors;
    }
    public function run(){
        $this->sessionManager->getLoggedInUser()->setAttribute("firstName", $this->params["newValue"]);
        return array();
    }
}
?>
