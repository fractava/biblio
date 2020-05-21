<?php
    if(permission_granted("edit_customer")){
    	if(!isset($_POST["customer_id"])){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","0");
    	}
    	if(!customer_exists($_POST["customer_id"])){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","6");
    	}
    	$did_something = false;
        if($success && isset($_POST["new_name"])){
    	    $statement = $pdo->prepare("UPDATE customers SET name = :name WHERE id = :customer_id LIMIT 1");
    		$statement->execute(array("customer_id" => $_POST["customer_id"], "name" => $_POST["new_name"]));
    	    $did_something = true;
    	}
    	if($success && isset($_POST["new_class_id"])){
    		$statement = $pdo->prepare("UPDATE customers SET class_id = :class_id WHERE id = :customer_id LIMIT 1");
    		$statement->execute(array("customer_id" => $_POST["customer_id"], "class_id" => $_POST["new_class_id"]));
    		$did_something = true;
    	}
    	if($success && isset($_POST["new_miscellaneous"])){
    		$statement = $pdo->prepare("UPDATE customers SET miscellaneous = :miscellaneous WHERE id = :customer_id LIMIT 1");
    		$statement->execute(array("customer_id" => $_POST["customer_id"], "miscellaneous" => $_POST["new_miscellaneous"]));
    		$did_something = true;
    	}
    	if($did_something == false){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","0");
    	}
    }else{
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","4");
    }
?>