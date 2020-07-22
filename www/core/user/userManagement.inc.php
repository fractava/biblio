<?php
namespace \core\user;

use \core\database\simpleDatabaseQuery;
use \core\database\selectQuery;
use \core\user\user;
use \core\password\encryption;
use \core\cookies\cookieManager;
use \core\session\sessionManager;

class userManagement {
    // Login
    function checkLogin($email, $password) {
        $errors = array();
        
        if(!(isset($email) && isset($password))){
            $errors[] = 0;
        }
        
        if(empty($errors)) {
            $user = $this->findByEmail($email);
            
            //check email
            if ($user == false){
                $errors[] = 10;
            }
            
            //check password
            if(!encryption::checkPassword($password, $user->getAttribute("password"))){
                $errors[] = 10;
            }
        }
        return $errors;
    }
    function loginByEmail($email) {
        $userId = $this->findByEmail($email)->getAttribute("id");
        return $this->loginById($userId);
    }
    function loginByUsername($username) {
        $userId = $this->findByUsername($username)->getAttribute("id");
        return $this->loginById($userId);
    }
    function loginById($id) {
        $sessionManager = new sessionManager();
        $sessionManager->sessionStart();

    	$_SESSION['userid'] = $id;
    
    	$identifier = encryption::randomString();
    	$securitytoken = encryption::randomString();
    	
    	$insert = new simpleDatabaseQuery("INSERT INTO securityTokens (user_id, identifier, securitytoken) VALUES (:user_id, :identifier, :securitytoken);", array('user_id' => $id, 'identifier' => $identifier, 'securitytoken' => sha1($securitytoken)));
        
        $cookieManager = new cookieManager();
        $cookieManager->setCookie("identifier",$identifier,time()+(3600*24*365));
        $cookieManager->setCookie("securitytoken",$securitytoken,time()+(3600*24*365));
        
        return array("identifier" => $identifier, "securitytoken" => $securitytoken);
    }
    
    // registration
    function checkRegistration($firstName, $lastName, $username, $email, $password) {
        $errors = array();
        
        if(empty($firstName) || empty($lastName) || empty($email) || empty($username)) {
        	$errors[] = 0;
        }
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        	$errors[] = 1;
        } 	
        if(strlen($password) < 5) {
        	$errors[] = 2;
        }
        	
        if (stristr($username, ' ')){
        	$errors[] = 3;
        }
        if(!preg_match("/[a-zA-Z0-9_.]{3,16}$/", $username)) {
            $errors[] = 4;
        } 
        
        
        if(empty($errors)) {
        	if($this->emailRegistered($email)) {
        		$errors[] = 5;
        	}
        }
        
    	if(empty($errors)) {
        	if($this->usernameregistered($username)) {
        		$errors[] = 6;
        	}
    	}
    	
    	return $errors;
    }
    function register($firstName, $lastName, $username, $email, $password) {
        $passwordHash = encryption::hashPassword($password);
        
        $sql = "INSERT INTO users (email, password, firstName, lastName, username) VALUES (:email, :password, :firstName, :lastName, :username)";
        $sqlValues = array('email' => $email, 'password' => $passwordHash, 'firstName' => $firstName, 'lastName' => $lastName, 'username' => $username);
        $insertQuery = new simpleDatabaseQuery($sql, $sqlValues);
    }  
    
    // logout
    function logout() {
        $sessionManager = new sessionManager();
        $sessionManager->sessionStart();
        $sessionManager->sessionClose();
        
        unset($_SESSION['userid']);
        
        
        $sql = "DELETE FROM securityTokens WHERE identifier = :identifier AND securitytoken = :securitytoken LIMIT 1;";
        $sqlValues = array('identifier' => $_COOKIE["identifier"], 'securitytoken' => sha1($_COOKIE["securitytoken"]));
        new simpleDatabaseQuery($sql, $sqlValues);
        
        $cookieManager = new cookieManager();
        $cookieManager->removeCookie("identifier");
        $cookieManager->removeCookie("securitytoken");
    }
    
    // find
    function findById($id) {
        if($this->idExists($id)){
            return new user($id);
        }else {
            return false;
        }
    }
    function findByUsername($username) {
        if($this->usernameRegistered($username)){
            $getIdQuery = new selectQuery();
            $getIdQuery
            ->from("users")
            ->get(array("id"))
            ->where("username", $username);
            $id = $getIdQuery->run()[0][0];
            return new user($id);
        }else {
            return false;
        }
    }
    function findByEmail($email) {
        if($this->emailRegistered($email)){
            $getIdQuery = new selectQuery();
            $getIdQuery
            ->from("users")
            ->get(array("id"))
            ->where("email", $email);
            $id = $getIdQuery->run()[0][0];
            return new user($id);
        }else {
            return false;
        }
    }
    
    // already registered
    function idExists($id) {
        $query = new selectQuery();
        $query
        ->from("users")
        ->getCountOf("id")
        ->where("id", $id);
        
        return $query->run()[0][0] == "1";
    }
    function usernameRegistered($username) {
        $query = new selectQuery();
        $query
        ->from("users")
        ->getCountOf("id")
        ->where("username", $username);
        
        return $query->run()[0][0] == "1";
    }
    function emailRegistered($email) {
        $query = new selectQuery();
        $query
        ->from("users")
        ->getCountOf("id")
        ->where("email", $email);
        
        return $query->run()[0][0] == "1";
    }
}
