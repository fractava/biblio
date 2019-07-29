<?php
    if(!isset($_POST["barcode"])){
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","0");
    }
    if(!media_instance_exists($_POST["barcode"])){
        $success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","2");
    }
    $did_something = false;
    if(isset($_POST["new_loaned_until"])){
    	$statement = $pdo->prepare("UPDATE media_instances SET loaned_until = :loaned_until WHERE barcode = :barcode LIMIT 1");
    	$statement->execute(array("barcode" => $_POST["barcode"], "loaned_until" => $_POST["new_loaned_until"]));
    	$did_something = true;
    }
    if(isset($_POST["new_holiday"])){
    	if($_POST["new_holiday"] == "0" || $_POST["new_holiday"] == "1"){
    		$statement = $pdo->prepare("UPDATE media_instances SET holiday = :holiday WHERE barcode = :barcode LIMIT 1");
    		$statement->execute(array("barcode" => $_POST["barcode"], "holiday" => $_POST["new_holiday"]));
    		$did_something = true;
        }
    }
    if($did_something == false){
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","0");
    }
?>