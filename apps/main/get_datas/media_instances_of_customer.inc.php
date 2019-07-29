<?php
    $user = check_user();
    if(isset($_GET["customer_id"])){
    	$statement = $pdo->prepare("SELECT media_id , loaned_until , holiday , barcode FROM media_instances WHERE loaned_to = :customer_id;");
        $statement->execute(array("customer_id" => $_GET["customer_id"]));
    	$medias = new SimpleXMLElement("<medias></medias>");
    	while($row = $statement->fetch()){
    		$xml_row = $medias->addChild('media');
    		$xml_row->addAttribute('media_id',$row['media_id']);
    
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
    		$xml_row->addAttribute('loaned_until',$row['loaned_until']);
    		$xml_row->addAttribute('holiday',$row['holiday']);
    		$xml_row->addAttribute('barcode',$row['barcode']);
    	}
        
        header('Content-Type: text/xml');
        echo $medias->asXML();
    }
?>