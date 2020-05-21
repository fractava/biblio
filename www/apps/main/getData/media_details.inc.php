<?php
    $user = check_user();
    if(isset($_GET["media_id"])){
    	$details = new SimpleXMLElement("<details></details>");
    
    	$statement = $pdo->prepare("SELECT * FROM medias WHERE id = :media_id;");
    	$statement->execute(array("media_id" => $_GET['media_id']));
    	$row = $statement->fetch();
    
    	$details->addChild('title')->addAttribute('value',$row['title']);
    	$details->addChild('author')->addAttribute('value',$row['author']);
    	$details->addChild('publisher')->addAttribute('value',$row['publisher']);
    	$details->addChild('price')->addAttribute('value',$row['price']);
    	$details->addChild('school_year_id')->addAttribute('value',$row['school_year_id']);
    	$details->addChild('subject_id')->addAttribute('value',$row['subject_id']);
    	$details->addChild('miscellaneous')->addAttribute('value',$row['miscellaneous']);
    
    	$statement2 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
    	$result = $statement2->execute(array("school_year_id" => $row['school_year_id']));
    	$school_year_row = $statement2->fetch();
    
    	$details->addChild('school_year')->addAttribute('value',$school_year_row['name']);
    
    	$statement3 = $pdo->prepare("SELECT * FROM types WHERE id = :type_id;");
    	$statement3->execute(array("type_id" => $row['type_id']));
    	$type_row = $statement3->fetch();
    
    	$details->addChild('type_id')->addAttribute('value',$type_row['id']);
    	$details->addChild('type_name')->addAttribute('value',$type_row['name']);
        
        header('Content-Type: text/xml');
        echo $details->asXML();
    }
?>