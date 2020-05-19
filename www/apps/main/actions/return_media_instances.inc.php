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
    if($success){
    	foreach($json as $row){
    		if(!media_instance_exists($row)){
    			$success = false;
    			$error = $request->addChild("error");
    			$error->addAttribute("id","2");
    			$error->addAttribute("extra_detail",$row);
    		}else{
    			return_media_instance($row);
    		}
    	}
    }
?>