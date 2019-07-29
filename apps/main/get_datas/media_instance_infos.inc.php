<?php
    $user = check_user();
    if(isset($_GET["barcode"])){
    	$statement = $pdo->prepare("SELECT media_id , loaned_until , loaned_to , holiday , barcode FROM media_instances WHERE barcode = :barcode;");
    	$statement->execute(array("barcode" => $_GET["barcode"]));
    	$row = $statement->fetch();
    	$infos = new SimpleXMLElement("<infos></infos>");
    
    	$xml_row = $infos->addChild('media');
    	$xml_row->addAttribute('media_id',$row['media_id']);
    	$xml_row->addAttribute('loaned_to',$row['loaned_to']);
    	$xml_row->addAttribute('loaned_until',$row['loaned_until']);
    	$xml_row->addAttribute('holiday',$row['holiday']);
    
    	$statement2 = $pdo->prepare("SELECT * FROM medias WHERE id = :media_id;");
    	$statement2->execute(array("media_id" => $row['media_id']));
    	$media_row = $statement2->fetch();
    
    	$xml_row->addAttribute('title',$media_row['title']);
    	$xml_row->addAttribute('author',$media_row['author']);
    	$xml_row->addAttribute('publisher',$media_row['publisher']);
    	$xml_row->addAttribute('price',$media_row['price']);
    	$xml_row->addAttribute('school_year_id',$media_row['school_year_id']);
    
    	$statement3 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
    	$result = $statement3->execute(array("school_year_id" => $media_row['school_year_id']));
    	$school_year_row = $statement3->fetch();
    
    	$xml_row->addAttribute('school_year',$school_year_row['name']);
    
    	$statement4 = $pdo->prepare("SELECT * FROM customers WHERE id = :customer_id;");
    	$statement4->execute(array("customer_id" => $row['loaned_to']));
    	$customer_row = $statement4->fetch();
    
    	$xml_row->addAttribute('loaned_to_name',$customer_row['name']);
    	$xml_row->addAttribute('class_id',$customer_row['class_id']);
    
    	header('Content-Type: text/xml');
    	echo $infos->asXML();
    }
?>