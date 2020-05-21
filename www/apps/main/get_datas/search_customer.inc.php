<?php
    $user = check_user();
    if(isset($_GET["search"]) && isset($_GET["class_id"])){
    	if(isset($_GET["order_by"])){
    		switch($_GET["order_by"]){
    			case "id":
    				$statement = $pdo->prepare("SELECT * FROM customers WHERE name LIKE :search ORDER BY id;");
    			break;
    			case "name":
    				$statement = $pdo->prepare("SELECT * FROM customers WHERE name LIKE :search ORDER BY name;");
    			break;
    			default:
    				$statement = $pdo->prepare("SELECT * FROM customers WHERE name LIKE :search;");
    			break;
    		}
    	}else{
    		$statement = $pdo->prepare("SELECT * FROM customers WHERE name LIKE :search;");
    	}
    
    	$statement->execute(array("search" => "%" . $_GET["search"] . "%"));
    	$customers = new SimpleXMLElement("<customers></customers>");
    
    	while($row = $statement->fetch()){
    		if(($row['class_id'] == $_GET["class_id"]) || $_GET["class_id"] == -1){
    			$xml_row = $customers->addChild('customer');
    			$xml_row->addAttribute('id',$row['id']);
    			$xml_row->addAttribute('name',$row['name']);
    			$xml_row->addAttribute('class_id',$row['class_id']);
    
    			$statement2 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
    			$statement2->execute(array("class_id" => $row['class_id']));
    			$class_row = $statement2->fetch();
    
    			$xml_row->addAttribute('class_name',$class_row['name']);
    			$xml_row->addAttribute('school_year_id',$class_row['school_year_id']);
    
    			$statement3 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
    			$result = $statement3->execute(array("school_year_id" => $class_row['school_year_id']));
    			$school_year_row = $statement3->fetch();
    
    			$xml_row->addAttribute('school_year',$school_year_row['name']);
    		}
    	}
    	header('Content-Type: text/xml');
    	echo $customers->asXML();
    }
?>