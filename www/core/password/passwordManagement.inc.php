<?php

namespace \core\password;

use \core\user\user;
use \core\password\encryption;

class passwordManagement {
    function checkPassword($userId,$password) {
        $user = new user($userId);
        $hash = $user->getAttribute("password");

        return encryption::checkPassword($password, $hash);
    }
}
