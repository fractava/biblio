<?php
    if(!isset($_POST["school_year_id"])){
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","0");
    }
    if(!school_year_exists($_POST["school_year_id"])){
        $success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","12");
    }
    $did_something = false;
    if(isset($_POST["new_name"])){
        $statement = $pdo->prepare("UPDATE school_years SET name = :name WHERE id = :school_year_id LIMIT 1");
    	$statement->execute(array("school_year_id" => $_POST["new_school_year_id"], "name" => $_POST["new_name"]));
    	$did_something = true;
    }
    if($did_something == false){
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","0");
    }
?>