<?php

namespace \apps\system\getData\user;

use \core\session\sessionManager;

class firstName extends \core\network\getData{
    
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
        $firstName = $this->sessionManager->getLoggedInUser()->getAttribute("firstName");
        return array("firstName" => $firstName);
    }
}
?>
