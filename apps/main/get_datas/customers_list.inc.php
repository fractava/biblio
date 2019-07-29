<?php
    $user = check_user();
    $customers = new SimpleXMLElement("<customerslist></customerslist>");
    
    $statement = $pdo->prepare("SELECT id , name , class_id FROM customers;");
    $statement->execute();
    
    while($row = $statement->fetch()){
    	$xml_row = $customers->addChild('customer');
    	$xml_row->addAttribute('id',$row['id']);
    	$xml_row->addAttribute('name',$row['name']);
    
    	$statement2 = $pdo->prepare("SELECT id , name FROM classes WHERE id = :class_id;");
    	$statement2->execute(array("class_id" => $row['class_id']));
    
    	$xml_row->addAttribute('class', $statement2->fetch()["name"]);
    }
    header('Content-Type: text/xml');
    echo $customers->asXML();
?>