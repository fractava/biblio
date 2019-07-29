<?php
    $error_msg = "";
    if(!(isset($_POST['email']) && isset($_POST['passwort']))){
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","0");
    }else{
    	$email = $_POST['email'];
    	$passwort = $_POST['passwort'];
        
    	$statement = $pdo->prepare("SELECT * FROM members WHERE email = :email");
    	$result = $statement->execute(array('email' => $email));
    	$user = $statement->fetch();
    
    	//Überprüfung des Passworts
    	if (!($user !== false && password_verify($passwort, $user['passwort']))){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","10");
    	}
    }
    if($success){
    	$_SESSION['userid'] = $user['id'];
    
    	$identifier = random_string();
    	$securitytoken = random_string();
    	$insert = $pdo->prepare("INSERT INTO securitytokens (user_id, identifier, securitytoken) VALUES (:user_id, :identifier, :securitytoken)");
    	$insert->execute(array('user_id' => $user['id'], 'identifier' => $identifier, 'securitytoken' => sha1($securitytoken)));
    	setcookie("identifier",$identifier,time()+(3600*24*365)); //Valid for 1 year
    	setcookie("securitytoken",$securitytoken,time()+(3600*24*365)); //Valid for 1 year
    }
?>