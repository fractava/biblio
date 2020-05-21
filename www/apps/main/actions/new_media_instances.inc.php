<?php
    if(permission_granted("create_media_instance")){
    	if(isset($_POST["media_id"]) && isset($_POST["barcodes"])){
    		$json = json_decode($_POST["barcodes"]);
    
    		foreach($json as $row){
    			if(!media_instance_exists($row)){
    				new_media_instance($_POST["media_id"],$row);
    			}
    		}
    	}else{
    		http_response_code(400);
    		echo "Not enough information provided";
    	}
    }
?>