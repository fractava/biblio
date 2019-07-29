<?php
    if(permission_granted("delete_customer")){
    	if(!isset($_POST["customer_id"])){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","0");
    	}
        if($success){
    		remove_customer($_POST[customer_id]);
    	}
    }else{
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","4");
    }
?>