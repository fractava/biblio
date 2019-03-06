<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
if(isset($_POST["action"])){
	$request = new SimpleXMLElement("<request></request>");
	$success = true;
	switch($_POST["action"]){
		 case "login":
			//require_once("./actions/login.php");
			$error_msg = "";
			if(!(isset($_POST['email']) && isset($_POST['passwort']))){
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","0");
			}else{
				$email = $_POST['email'];
				$passwort = $_POST['passwort'];

				$statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
				$result = $statement->execute(array('email' => $email));
				$user = $statement->fetch();

				//Überprüfung des Passworts
				if (!($user !== false && password_verify($passwort, $user['passwort']))){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","10");
				}
			}
			if($success){
				$_SESSION['userid'] = $user['id'];

					//Möchte der Nutzer angemeldet beleiben?
					if(isset($_POST['angemeldet_bleiben'])) {
						$identifier = random_string();
						$securitytoken = random_string();

						$insert = $pdo->prepare("INSERT INTO securitytokens (user_id, identifier, securitytoken) VALUES (:user_id, :identifier, :securitytoken)");
						$insert->execute(array('user_id' => $user['id'], 'identifier' => $identifier, 'securitytoken' => sha1($securitytoken)));
						setcookie("identifier",$identifier,time()+(3600*24*365)); //Valid for 1 year
						setcookie("securitytoken",$securitytoken,time()+(3600*24*365)); //Valid for 1 year
					}
				}
				break;
		case "logout":
			//require_once("./actions/logout.php");
			session_start();
			session_destroy();
			unset($_SESSION['userid']);

			//Remove Cookies
			setcookie("identifier","",time()-(3600*24*365));
			setcookie("securitytoken","",time()-(3600*24*365));

			require_once("/web/inc/config.inc.php");
			require_once("/web/inc/functions.inc.php");
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
					lend_media_instance($_POST["barcode"] , $_POST["student_id"], $_POST["until"] , $_POST["holiday"]);
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
			}
			break;
		case "new_media":
			if(permission_granted("create_media")){
				if(!(isset($_POST["title"]) && isset($_POST["school_year_id"]) && isset($_POST["subject_id"]) && isset($_POST["type_id"]))){
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
					new_media($_POST["title"],$_POST["author"],$_POST["publisher"],$_POST["price"],$_POST["school_year_id"],$_POST["subject_id"],$_POST["type_id"]);
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
			}
		break;
		case "new_student":
			if(permission_granted("create_member")){
				if(!(isset($_POST["name"]) && isset($_POST["class_id"]))){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","0");
				}
				if($success){
					new_student($_POST["name"],$_POST["class_id"]);
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
			}
		break;
		case "modify_student":
			if(permission_granted("edit_student")){
				if(!(isset($_POST["student_id"]) && (isset($_POST["new_name"]) || isset($_POST["new_class_id"])))){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","0");
				}
				if(!student_exists($_POST["student_id"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","6");
				}
				if($success && isset($_POST["new_name"])){
					$statement = $pdo->prepare("UPDATE students SET name = :name WHERE id = :student_id LIMIT 1");
					$statement->execute(array("student_id" => $_POST["student_id"], "name" => $_POST["new_name"]));
				}
				if($success && isset($_POST["new_class_id"])){
					$statement = $pdo->prepare("UPDATE students SET class_id = :class_id WHERE id = :student_id LIMIT 1");
					$statement->execute(array("student_id" => $_POST["student_id"], "class_id" => $_POST["new_class_id"]));
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
			}
		break;
		case "modify_media":
			if(permission_granted("edit_media")){
				if(!(isset($_POST["media_id"]) && (isset($_POST["new_title"]) || isset($_POST["new_subject"]) || isset($_POST["new_school_year_id"])))){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","0");
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
			}
		case "new_media_instances":
			if(permission_granted("create_media_instance")){
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
				if(!isset($_POST["media_id"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","0");
				}
				if($success){
					remove_media($_POST["media_id"]);
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
			}
		break;
		case "remove_student":
			if(permission_granted("delete_member")){
				if(!isset($_POST["student_id"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","0");
				}
				if($success){
					remove_student($_POST[student_id]);
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
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
