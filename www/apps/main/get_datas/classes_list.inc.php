<?php
    $user = check_user();
    $classes = new SimpleXMLElement("<classeslist></classeslist>");
    
    $statement = $pdo->prepare("SELECT * FROM classes ORDER BY school_year_id;");
    $statement->execute();
    
    while($row = $statement->fetch()){
    	$xml_row = $classes->addChild('class');
    	$xml_row->addAttribute('id',$row['id']);
    	$xml_row->addAttribute('name',$row['name']);
    	$xml_row->addAttribute('school_year_id',$row['school_year_id']);
    
    	$statement2 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
    	$result = $statement2->execute(array("school_year_id" => $row['school_year_id']));
    	$school_year_row = $statement2->fetch();
    
    	$xml_row->addAttribute('school_year',$school_year_row['name']);
    }
    
    header('Content-Type: text/xml');
    echo $classes->asXML();
?>