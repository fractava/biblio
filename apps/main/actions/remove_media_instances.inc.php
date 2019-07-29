<?php
    if(!isset($_POST["barcodes"])){
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","0");
    }
    
    $json = json_decode($_POST["barcodes"]);
    if($json == NULL){
        $success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","3");
    }
    
    foreach($json as $row){
    	if(media_instance_exists($row)){
    		remove_media_instance($row);
    	}else{
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","2");
    	    $error->addAttribute("extra_detail",$row);
        }
    }
?>