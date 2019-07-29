<?php
    if(permission_granted("lend_media_instance")){
    	if(!(isset($_POST["customer_id"]) && isset($_POST["barcode"]) && isset($_POST["until"]))){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","0");
    	}
    	if(!validateDate($_POST["until"],'Y-m-d')){
        	$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","5");
    	}
    	if(!media_instance_exists($_POST["barcode"])){
        	$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","2");
    	}
    	if(!customer_exists($_POST["customer_id"])){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","6");
    	}
    	if(media_instance_loaned($_POST["barcode"])){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","7");
    		$error->addAttribute("extra_detail",customer_name(media_instance_loaned_to($_POST["barcode"])));
        }
    	if(!(($_POST["holiday"] == "1") || ($_POST["holiday"] == "0"))){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","8");
    	}
    	
    	if($success){
    		lend_media_instance($_POST["barcode"] , $_POST["customer_id"], $_POST["until"] , $_POST["holiday"]);
    	}
    }else{
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","4");
    }
?>