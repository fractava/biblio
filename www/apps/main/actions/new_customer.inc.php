<?php
    if(permission_granted("create_member")){
    	if(!(isset($_POST["name"]) && isset($_POST["class_id"]))){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","0");
    	}
    	if($success){
    		new_customer($_POST["name"],$_POST["class_id"]);
    	}
    }else{
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","4");
    }
?>