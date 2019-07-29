<?php
    $design_id = config("active_design");
    if(design_exists($design_id)){
        $design = new SimpleXMLElement("<design></design>");
    				
    	foreach(design($design_id) as $name => $value){
    		$xml_row = $design->addChild($name);
    		$xml_row->addAttribute("value",$value);
    	}
    	
    	header('Content-Type: text/xml');
    	echo $design->asXML();
    }else{
    	http_response_code(400);
    	echo "Design not found";
    }
?>