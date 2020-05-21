<?php
    $user = check_user();
    $designs = new SimpleXMLElement("<designs></designs>");
    
    $statement = $pdo->prepare("SELECT * FROM designs;");
    $statement->execute();
    
    while($row = $statement->fetch(PDO::FETCH_ASSOC)){
        $xml_row = $designs->addChild('design');
    				
    	foreach($row as $name => $value){
    	    $xml_row->addAttribute($name,$value); 
    	}
    }
    
    header('Content-Type: text/xml');
    echo $designs->asXML();
?>