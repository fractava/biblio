<?php

namespace \core\password;

class encryption {
    function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    function checkPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    function randomString() {
    	if(function_exists('openssl_random_pseudo_bytes')) {
    		$bytes = openssl_random_pseudo_bytes(16);
    		$str = bin2hex($bytes); 
    	} else if(function_exists('mcrypt_create_iv')) {
    		$bytes = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
    		$str = bin2hex($bytes); 
    	} else {
    		//Replace your_secret_string with a string of your choice (>12 characters)
    		$str = md5(uniqid('857571123444705', true));
    	}	
    	return $str;
    }
}
