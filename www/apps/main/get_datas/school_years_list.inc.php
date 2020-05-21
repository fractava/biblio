<?php
    $user = check_user();
    $school_years = new SimpleXMLElement("<schoolyearslist></schoolyearslist>");
    $statement = $pdo->prepare("SELECT id , name FROM school_years;");
    $statement->execute();
    
    while($row = $statement->fetch()){
    	$xml_row = $school_years->addChild('school_year');
    	$xml_row->addAttribute('id',$row['id']);
        $xml_row->addAttribute('name',$row['name']);
    }
    
    header('Content-Type: text/xml');
    echo $school_years->asXML();
?>