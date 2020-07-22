<?php

namespace \apps\system\getData\user;

use \core\session\sessionManager;

class lastName extends \core\network\getData{
    
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
        $lastName = $this->sessionManager->getLoggedInUser()->getAttribute("lastName");
        return array("lastName" => $lastName);
    }
}
?>
