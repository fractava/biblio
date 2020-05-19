<?php
    if(!isset($_POST["class_id"])){
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","0");
    }
    if(!customer_class_exists($_POST["class_id"])){
        $success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","12");
    }
    $did_something = false;
    if(isset($_POST["new_name"])){
    	$statement = $pdo->prepare("UPDATE classes SET name = :name WHERE id = :class_id LIMIT 1");
    	$statement->execute(array("class_id" => $_POST["class_id"], "name" => $_POST["new_name"]));
    	$did_something = true;
    }
    if(isset($_POST["new_school_year_id"])){
    	$statement = $pdo->prepare("UPDATE classes SET school_year_id = :school_year_id WHERE id = :class_id LIMIT 1");
    	$statement->execute(array("class_id" => $_POST["class_id"], "school_year_id" => $_POST["new_school_year_id"]));
        $did_something = true;
    }
    if($did_something == false){
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","0");
    }
?>