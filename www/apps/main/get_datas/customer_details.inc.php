<?php
    $user = check_user();
    if(isset($_GET["customer_id"])){
    	$details = new SimpleXMLElement("<details></details>");
    
    	$statement = $pdo->prepare("SELECT * FROM customers WHERE id = :customer_id;");
    	$statement->execute(array("customer_id" => $_GET['customer_id']));
    	$row = $statement->fetch();
    
    	$details->addChild('name')->addAttribute('value',$row['name']);
    	$details->addChild('class_id')->addAttribute('value',$row['class_id']);
    	$details->addChild('miscellaneous')->addAttribute('value',$row['miscellaneous']);
    
    	$statement2 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
    	$statement2->execute(array("class_id" => $row['class_id']));
    	$class_row = $statement2->fetch();
    
    	$details->addChild('class')->addAttribute('value',$class_row['name']);
    
    	header('Content-Type: text/xml');
    	echo $details->asXML();
    }
?>