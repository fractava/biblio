<?php
    if(!isset($_POST["barcode"])){
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","0");
    }
    if(!media_instance_exists($_POST["barcode"])){
    	$success = false;
    	$error = $request->addChild("error");
        $error->addAttribute("id","2");
    }
    if(!media_instance_loaned($_POST["barcode"])) {
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","11");
    }
    if($success){
    	return_media_instance($_POST["barcode"]);
    }
?>