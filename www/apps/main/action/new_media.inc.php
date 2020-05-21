<?php
    if(permission_granted("create_media")){
    	if(!(isset($_POST["title"]) && isset($_POST["school_year_id"]) && isset($_POST["subject_id"]) && isset($_POST["type_id"]))){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","0");
    	}
    	if(media_exists($_POST["title"])){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","9");
    	}
    	if(!type_exists($_POST["type_id"])){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","13");
    	}
    	if($success == true){
    		new_media($_POST["title"],$_POST["author"],$_POST["publisher"],$_POST["price"],$_POST["school_year_id"],$_POST["subject_id"],$_POST["type_id"]);
    	}
    }else{
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","4");
    }
?>