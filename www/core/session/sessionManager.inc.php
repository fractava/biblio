<?php
namespace \core\session;

use \core\user\user;
use \core\cookies\cookieManager;
use \core\database\selectQuery;

class sessionManager {
    function sessionStart() {
        $cookieManager = new cookieManager();
        if($cookieManager->cookiePermissionGranted()) {
            session_start();
            return true;
        }else {
            return false;
        }
    }
    function sessionClose() {
        session_destroy();
        $cookieManager = new cookieManager();
        $cookieManager->removeCookie("PHPSESSID");
    }
    function isLoggedIn() {
        $this->sessionStart();
        $cookieManager = new cookieManager();
        
        if(isset($_SESSION['userid'])){
            $query = new selectQuery();
            $query
            ->from("securityTokens")
            ->getCountOf("id")
            ->where("identifier", $_COOKIE["identifier"])
            ->and('securitytoken', sha1($_COOKIE["securitytoken"]))
            ->and("user_id", $_SESSION['userid']);
            
            return $query->run()[0][0] == "1";
        }else {
            $this->sessionClose();
            return false;
        }
    }
    function getLoggedInUser() {
        $this->sessionStart();
        if($this->isLoggedIn()){
            return new user($_SESSION['userid']);
        }else {
            return false;
        }
    }
}
