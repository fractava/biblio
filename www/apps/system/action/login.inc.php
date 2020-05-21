<?php
    class login extends action{
        public function init(){
            $return_type = "json";
            
            // Check for params
            if(!(isset($params['email']) && isset($params['password']))){
                $requirements[] = 0;
            }
            
            //check email
            $statement = $pdo->prepare("SELECT * FROM members WHERE email = :email");
            $result = $statement->execute(array('email' => $params['email']));
            $user = $statement->fetch();
            if ($user == false){
                $requirements[] = 10;
            }
            
            //check password
            if(!password_verify($params['password'], $user['password']){
                $requirements[] = 10;
            }
        }
        public function run(){
        	$_SESSION['userid'] = $user['id'];
        
        	$identifier = random_string();
        	$securitytoken = random_string();
        	$insert = $pdo->prepare("INSERT INTO securitytokens (user_id, identifier, securitytoken) VALUES (:user_id, :identifier, :securitytoken)");
        	$insert->execute(array('user_id' => $user['id'], 'identifier' => $identifier, 'securitytoken' => sha1($securitytoken)));
        	setcookie("identifier",$identifier,time()+(3600*24*365)); //Valid for 1 year
        	setcookie("securitytoken",$securitytoken,time()+(3600*24*365)); //Valid for 1 year
        }
    }
    return new login()->get_this();
?>
