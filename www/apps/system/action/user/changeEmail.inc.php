<?php

namespace \apps\system\action\user;

use \core\session\sessionManager;
use \core\user\userManagement;

class changeEmail extends \core\network\action{
    public function init(){
        $this->sessionManager = new sessionManager();
        $this->userManagement = new userManagement();
        
        $errors = array();
        
        if(!isset($this->params["newValue"])) {
            $errors[] = 0;
        }
        
        if(!filter_var($this->params["newValue"], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 1;
        }
        
        if($this->userManagement->emailRegistered($this->params["newValue"])) {
            $registeredUserId = $this->userManagement->findByEmail($this->params["newValue"])->getAttribute("id");
            $loggedInUserId = $this->sessionManager->getLoggedInUser()->getAttribute("id");
            
            if($registeredUserId != $loggedInUserId) {
        	    $errors[] = 5;
            }
        }
        
        if(!$this->sessionManager->isLoggedIn()) {
            $errors[] = 7;
        }
        
        return $errors;
    }
    public function run(){
        $this->sessionManager->getLoggedInUser()->setAttribute("email", $this->params["newValue"]);
        return array();
    }
}
?>
