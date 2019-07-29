<?php
    $user = check_user();
    $subjects = new SimpleXMLElement("<subjectslist></subjectslist>");
    $statement = $pdo->prepare("SELECT id , name FROM subjects;");
    $statement->execute();
    
    while($row = $statement->fetch()){
    	$xml_row = $subjects->addChild('subject');
    	$xml_row->addAttribute('id',$row['id']);
    	$xml_row->addAttribute('name',$row['name']);
    }
    
    header('Content-Type: text/xml');
    echo $subjects->asXML();
?>