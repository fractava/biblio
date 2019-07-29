<?php
    $user = check_user();
    $permissions = new SimpleXMLElement("<permission_list></permission_list>");
    
    foreach(permission_list() as $name => $permission){
    	if($name != "name" && $name != "id"){
    		$xml_row = $permissions->addChild($name);
    		$xml_row->addAttribute("value",$permission);
    	}
    }
    
    header('Content-Type: text/xml');
    echo $permissions->asXML();
?>