<?php

namespace \apps\system\getData\user;

use \core\session\sessionManager;

class loggedIn extends \core\network\getData{
    
    public $userInfo;
    
    public function init(){
        return array();
    }
    public function run(){
        $sessionManager = new sessionManager();
        if($sessionManager->isLoggedIn()){
            $user = $sessionManager->getLoggedInUser();
            return array("isLoggedIn" => "true", "email" => $user->getAttribute("email"), "username" => $user->getAttribute("username"));
        }else {
            return array("isLoggedIn" => "false");
        }
    }
}
?>
