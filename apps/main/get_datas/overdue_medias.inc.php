<?php
    $user = check_user();
    if(isset($_GET["holiday"]) && ($_GET["holiday"] == 0 || $_GET["holiday"] == 1)){
    	$statement = $pdo->prepare("SELECT * FROM media_instances WHERE DATEDIFF(loaned_until,NOW()) < 0 AND holiday = :holiday;");
    	$statement->execute(array("holiday" => $_GET["holiday"]));
    	$books = new SimpleXMLElement("<books></books>");
    	while($row = $statement->fetch()){
    		$xml_row = $books->addChild('book');
    		$xml_row->addAttribute('id',$row['id']);
    		$xml_row->addAttribute('media_id',$row['media_id']);
    		$xml_row->addAttribute('loaned_to',$row['loaned_to']);
    		$xml_row->addAttribute('barcode',$row['barcode']);
    
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
    
    		$statement5 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
    		$statement5->execute(array("class_id" => $customer_row['class_id']));
    		$class_row = $statement5->fetch();
    
    		$xml_row->addAttribute('class_name',$class_row['name']);
    	}
    	header('Content-Type: text/xml');
    	echo $books->asXML();
    }
?>