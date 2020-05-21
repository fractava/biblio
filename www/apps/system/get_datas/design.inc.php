<?php
    $user = check_user();
    		    
    if(isset($_GET["id"])){
        if(design_exists($_GET["id"])){
        	$design = new SimpleXMLElement("<design></design>");
        				
        	foreach(design($_GET["id"]) as $name => $value){
        		$xml_row = $design->addChild($name);
        		$xml_row->addAttribute("value",$value);
        	}
        	header('Content-Type: text/xml');
        	echo $design->asXML();
        }else{
        	http_response_code(400);
        	echo "Design not found";
        }
    }
?>