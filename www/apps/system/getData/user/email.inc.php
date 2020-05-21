<?php

namespace \apps\system\getData\user;

use \core\session\sessionManager;

class email extends \core\network\getData{
    
    public $userInfo;
    
    public function init(){
        $this->sessionManager = new sessionManager();
        $errors = array();
        
        if(!$this->sessionManager->isLoggedIn()) {
            $errors[] = 7;
        }
        
        return $errors;
    }
    public function run(){
        $email = $this->sessionManager->getLoggedInUser()->getAttribute("email");
        return array("email" => $email);
    }
}
?>
