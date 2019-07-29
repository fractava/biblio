<?php
    $user = check_user();
    if(isset($_GET["media_id"])){
    	$statement = $pdo->prepare("SELECT * FROM media_instances WHERE media_id = :media_id ORDER BY barcode;");
    	$statement->execute(array("media_id" => $_GET["media_id"]));
        
    	$instances = new SimpleXMLElement("<media_instances></media_instances>");
        
    	while($row = $statement->fetch()){
            $xml_row = $instances->addChild('media_instance');
            
    		$xml_row->addAttribute('barcode',$row['barcode']);
            
    		$xml_row->addAttribute('loaned_to',$row['loaned_to']);
    		$xml_row->addAttribute('loaned_until',$row['loaned_until']);
    		$xml_row->addAttribute('holiday',$row['holiday']);
    
    		$statement2 = $pdo->prepare("SELECT * FROM customers WHERE id = :customer_id;");
    		$statement2->execute(array("customer_id" => $row['loaned_to']));
    		$customer_row = $statement2->fetch();
    
    		$xml_row->addAttribute('loaned_to_name',$customer_row['name']);
    		$xml_row->addAttribute('class_id',$customer_row['class_id']);
    
    		$statement3 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
    		$statement3->execute(array("class_id" => $customer_row['class_id']));
    		$class_row = $statement3->fetch();
    
    		$xml_row->addAttribute('class_name',$class_row['name']);
    	}
    	header('Content-Type: text/xml');
    	echo $instances->asXML();
    }
?>