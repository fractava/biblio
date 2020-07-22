<?php
    if(permission_granted("edit_media")){
    	if(!(isset($_POST["media_id"]))){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","0");
    	}
    	if(!media_id_exists($_POST["media_id"])){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","1");
    	}
    	$did_something = false;
    	if(isset($_POST["new_title"])){
    		$statement = $pdo->prepare("UPDATE medias SET title = :title WHERE id = :media_id LIMIT 1");
    		$statement->execute(array("media_id" => $_POST["media_id"], "title" => $_POST["new_title"]));
    		$did_something = true;
    	}
    	if(isset($_POST["new_subject_id"])){
    		$statement = $pdo->prepare("UPDATE medias SET subject_id = :subject WHERE id = :media_id LIMIT 1");
    		$statement->execute(array("media_id" => $_POST["media_id"], "subject" => $_POST["new_subject_id"]));
    		$did_something = true;
    	}
    	if(isset($_POST["new_school_year_id"])){
    		$statement = $pdo->prepare("UPDATE medias SET school_year_id = :school_year WHERE id = :media_id LIMIT 1");
    		$statement->execute(array("media_id" => $_POST["media_id"], "school_year" => $_POST["new_school_year_id"]));
    		$did_something = true;
    	}
    	if(isset($_POST["new_author"])){
    		$statement = $pdo->prepare("UPDATE medias SET author = :author WHERE id = :media_id LIMIT 1");
    		$statement->execute(array("media_id" => $_POST["media_id"], "author" => $_POST["new_author"]));
    		$did_something = true;
    	}
    	if(isset($_POST["new_publisher"])){
    		$statement = $pdo->prepare("UPDATE medias SET publisher = :publisher WHERE id = :media_id LIMIT 1");
    		$statement->execute(array("media_id" => $_POST["media_id"], "publisher" => $_POST["new_publisher"]));
    		$did_something = true;
    	}
    	if(isset($_POST["new_price"])){
    		$statement = $pdo->prepare("UPDATE medias SET price = :price WHERE id = :media_id LIMIT 1");
    		$statement->execute(array("media_id" => $_POST["media_id"], "price" => (float) $_POST["new_price"]));
    		$did_something = true;
    	}
    	if(isset($_POST["new_type_id"])){
    		$statement = $pdo->prepare("UPDATE medias SET type_id = :type WHERE id = :media_id LIMIT 1");
    		$statement->execute(array("media_id" => $_POST["media_id"], "type" => $_POST["new_type_id"]));
    		$did_something = true;
    	}
    	if(isset($_POST["new_miscellaneous"])){
    		$statement = $pdo->prepare("UPDATE medias SET miscellaneous = :miscellaneous WHERE id = :media_id LIMIT 1");
    		$statement->execute(array("media_id" => $_POST["media_id"], "miscellaneous" => $_POST["new_miscellaneous"]));
    		$did_something = true;
    	}
    	if($did_something == false){
    		$success = false;
    		$error = $request->addChild("error");
    		$error->addAttribute("id","0");
    	}
    }else{
    	$success = false;
    	$error = $request->addChild("error");
    	$error->addAttribute("id","4");
    }
?>