<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
if(isset($_POST["action"])){
	$request = new SimpleXMLElement("<request></request>");
	$success = true;
	switch($_POST["action"]){
		 case "login":
			require_once("./actions/login.php");
		break;
		case "logout":
			require_once("./actions/logout.php");
		break;
		case "lend_media_instance":
			if(permission_granted("lend_media_instance")){
				if(!(isset($_POST["student_id"]) && isset($_POST["barcode"]) && isset($_POST["until"]))){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","0");
				}
				if(!validateDate($_POST["until"],'Y-m-d')){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","5");
				}
				if(!media_instance_exists($_POST["barcode"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","2");
				}
				if(!student_exists($_POST["student_id"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","6");
				}
				if(media_instance_loaned($_POST["barcode"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","7");
					$error->addAttribute("extra_detail",student_name(media_instance_loaned_to($_POST["barcode"])));
				}
				if(!(($_POST["holiday"] == "1") || ($_POST["holiday"] == "0"))){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","8");
				}
				if($success){
					lend_media_instance($_POST["barcode"] , $_POST["student_id"], $until , $_POST["holiday"]);
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
			}
			break;
		case "new_media":
			if(permission_granted("create_media")){
				if(!(isset($_POST["title"]) && isset($_POST["school_year"]) && isset($_POST["subject_id"]) && isset($_POST["type_id"]))){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","0");
				}
				if(media_exists($_POST["title"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","9");
				}
				if($success == true){
					new_media($_POST["title"],$_POST["author"],$_POST["publisher"],$_POST["price"],$_POST["school_year"],$_POST["subject_id"],$_POST["type_id"]);
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
			}
		break;
		case "new_media_instances":
			if(permission_granted("create_media_instance"){
				if(isset($_POST["media_id"]) && isset($_POST["barcodes"])){
					$json = json_decode($_POST["barcodes"]);

					foreach($json as $row){
						if(!media_instance_exists($row)){
							new_media_instance($_POST["media_id"],$row);
						}
					}
				}else{
					http_response_code(400);
					echo "Not enough information provided";
				}
			}
		break;
		case "remove_media":
			if(permission_granted("delete_media")){
				if(isset($_POST["media_id"])){
					remove_media($_POST["media_id"]);
				}else{
					http_response_code(400);
					echo "Not enough information provided";
				}
			}
		break;
		case "remove_media_instances":
			if(!isset($_POST["barcodes"])){
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","0");
			}

			$json = json_decode($_POST["barcodes"]);
			if($json == NULL){
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","3");
			}

			foreach($json as $row){
				if(media_instance_exists($row)){
					remove_media_instance($row);
				}else{
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","2");
					$error->addAttribute("extra_detail",$row);
				}
			}
		break;
		case "return_media_instance":
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
			if($success){
				return_media_instance($_POST["barcode"]);
			}
		break;
		case "return_media_instances":
			if(!isset($_POST["barcodes"])){
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","0");
			}

			$json = json_decode($_POST["barcodes"]);
			if($json == NULL){
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","3");
			}
			if($success){
				foreach($json as $row){
					if(media_instance_exists($row)){
						return_media_instance($row);
					}else{
						$success = false;
						$error = $request->addChild("error");
						$error->addAttribute("id","2");
						$error->addAttribute("extra_detail",$row);
					}
				}
			}
			break;
		default:
			$success = false;
			$error = $request->addChild("error");
			$error->addAttribute("id","0");
		break;
	}
	if(!$success){
		http_response_code(400);
		$request->addAttribute("success","false");
	}else{
		$request->addAttribute("success","true");
	}
	header('Content-Type: text/xml');
	echo $request->asXML();
}
?>
