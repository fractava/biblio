<?php
    if(permission_granted("delete_media")){
        if(!isset($_POST["media_id"])){
    	    $success = false;
    	    $error = $request->addChild("error");
    	    $error->addAttribute("id","0");
        }
    	if($success){
    		remove_media($_POST["media_id"]);
    	}
    }else{
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","4");
    }
?>