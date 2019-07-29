<?php
    $user = check_user();
    if(isset($_GET["search"]) && isset($_GET["subject_id"]) && isset($_GET["school_year_id"])){
    	if(isset($_GET["order_by"])){
    		switch($_GET["order_by"]){
    			case "id":
    				$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search ORDER BY id;");
    			break;
    			case "title":
    				$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search ORDER BY title;");
    			break;
    			case "author":
    				$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search ORDER BY author;");
    			break;
    			case "publisher":
    				$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search ORDER BY publisher;");
    			break;
    			case "price":
    				$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search ORDER BY price;");
    			break;
    			default:
    				$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search;");
    			break;
    		}
    	}else{
    		$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search;");
    	}
    
    	$statement->execute(array("search" => "%" . $_GET["search"] . "%"));
    	$medias = new SimpleXMLElement("<medias></medias>");
    
    	while($row = $statement->fetch()){
    		if(($row['subject_id'] == $_GET["subject_id"]) || $_GET["subject_id"] == -1){
    			if(($row['school_year_id'] == $_GET["school_year_id"]) || $_GET["school_year_id"] == -1){
    				$xml_row = $medias->addChild('media');
    				$xml_row->addAttribute('id',$row['id']);
    				$xml_row->addAttribute('title',$row['title']);
    				$xml_row->addAttribute('author',$row['author']);
    				$xml_row->addAttribute('publisher',$row['publisher']);
    				$xml_row->addAttribute('price',$row['price']);
    				$xml_row->addAttribute('subject_id',$row['subject_id']);
    				$xml_row->addAttribute('type_id',$row['type_id']);
    				$xml_row->addAttribute('school_year_id',$row['school_year_id']);
    
    				$statement2 = $pdo->prepare("SELECT * FROM types WHERE id = :type_id;");
    				$statement2->execute(array("type_id" => $row['type_id']));
    				$types_row = $statement2->fetch();
    
    				$xml_row->addAttribute('type',$types_row['name']);
    
    				$statement3 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
    				$result = $statement3->execute(array("school_year_id" => $row['school_year_id']));
    				$school_year_row = $statement3->fetch();
    
    				$xml_row->addAttribute('school_year',$school_year_row['name']);
    			}
    		}
    	}
    	header('Content-Type: text/xml');
    	echo $medias->asXML();
    }
?>