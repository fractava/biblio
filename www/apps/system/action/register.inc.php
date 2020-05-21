<?php
namespace \apps\system\action;

use \core\database\selectQuery;
use \core\user\userManagement;

class register extends \core\network\action {
    public function init() {
        $this->userManagement = new userManagement();
        
        $this->firstName = trim($_POST['firstName']);
        $this->lastName = trim($_POST['lastName']);
        $this->username = trim($_POST['username']);
        $this->email = trim($_POST['email']);
        $this->password = $_POST['password'];
        
        return $this->userManagement->checkRegistration($this->firstName, $this->lastName, $this->username, $this->email, $this->password);
    }
    public function run() {
        $this->userManagement->register($this->firstName, $this->lastName, $this->username, $this->email, $this->password);

        $this->userManagement->loginByEmail($this->email);
    }
}
