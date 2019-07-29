<?php
    $user = check_user();
    
    if(isset($_GET["holiday"])){
    	if($_GET["holiday"] == 1 || $_GET["holiday"] == 0){
    		$dates = new SimpleXMLElement("<dateslist></dateslist>");
    		$statement = $pdo->prepare("SELECT id , date , name FROM loaned_until_dates WHERE holiday = :holiday;");
    		$statement->execute(array("holiday" => $_GET["holiday"]));
    
    		while($row = $statement->fetch()){
    			$xml_row = $dates->addChild('date');
    		    $xml_row->addAttribute('id',$row['id']);
    			$xml_row->addAttribute('date',$row['date']);
    			$xml_row->addAttribute('name',$row['name']);
    		}
    		header('Content-Type: text/xml');
    		echo $dates->asXML();
    	}
    }
?>