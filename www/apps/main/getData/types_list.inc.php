<?php
    $user = check_user();
    $types = new SimpleXMLElement("<types></types>");
    
    $statement = $pdo->prepare("SELECT * FROM types;");
    $statement->execute();
    
    while($row = $statement->fetch()){
    	$xml_row = $types->addChild('type');
    
        $xml_row->addAttribute('id',$row['id']);
    	$xml_row->addAttribute('name',$row['name']);
    }
    
    header('Content-Type: text/xml');
    echo $types->asXML();
?>