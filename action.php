<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
if(isset($_POST["action"])){
	$request = new SimpleXMLElement("<request></request>");
	$success = true;
	switch($_POST["action"]){
		 case "login":
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

				$identifier = random_string();
				$securitytoken = random_string();
				$insert = $pdo->prepare("INSERT INTO securitytokens (user_id, identifier, securitytoken) VALUES (:user_id, :identifier, :securitytoken)");
				$insert->execute(array('user_id' => $user['id'], 'identifier' => $identifier, 'securitytoken' => sha1($securitytoken)));
				setcookie("identifier",$identifier,time()+(3600*24*365)); //Valid for 1 year
				setcookie("securitytoken",$securitytoken,time()+(3600*24*365)); //Valid for 1 year
			}
			break;
		case "logout":
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
				if(!(isset($_POST["customer_id"]) && isset($_POST["barcode"]) && isset($_POST["until"]))){
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
				if(!customer_exists($_POST["customer_id"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","6");
				}
				if(media_instance_loaned($_POST["barcode"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","7");
					$error->addAttribute("extra_detail",customer_name(media_instance_loaned_to($_POST["barcode"])));
				}
				if(!(($_POST["holiday"] == "1") || ($_POST["holiday"] == "0"))){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","8");
				}
				if($success){
					lend_media_instance($_POST["barcode"] , $_POST["customer_id"], $_POST["until"] , $_POST["holiday"]);
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
		case "new_customer":
			if(permission_granted("create_member")){
				if(!(isset($_POST["name"]) && isset($_POST["class_id"]))){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","0");
				}
				if($success){
					new_customer($_POST["name"],$_POST["class_id"]);
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
			}
		break;
		case "modify_customer":
			if(permission_granted("edit_customer")){
				if(!(isset($_POST["customer_id"]) && (isset($_POST["new_name"]) || isset($_POST["new_class_id"])))){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","0");
				}
				if(!customer_exists($_POST["customer_id"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","6");
				}
				if($success && isset($_POST["new_name"])){
					$statement = $pdo->prepare("UPDATE customers SET name = :name WHERE id = :customer_id LIMIT 1");
					$statement->execute(array("customer_id" => $_POST["customer_id"], "name" => $_POST["new_name"]));
				}
				if($success && isset($_POST["new_class_id"])){
					$statement = $pdo->prepare("UPDATE customers SET class_id = :class_id WHERE id = :customer_id LIMIT 1");
					$statement->execute(array("customer_id" => $_POST["customer_id"], "class_id" => $_POST["new_class_id"]));
				}
			}else{
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","4");
			}
		break;
		case "modify_media":
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
					$statement->execute(array("media_id" => $_POST["media_id"], "price" => $_POST["new_price"]));
					$did_something = true;
				}
				if(isset($_POST["new_type_id"])){
					$statement = $pdo->prepare("UPDATE medias SET type_id = :type WHERE id = :media_id LIMIT 1");
					$statement->execute(array("media_id" => $_POST["media_id"], "type" => $_POST["new_type_id"]));
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
		break;
		case "modify_media_instance":
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
		break;
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
		case "remove_customer":
			if(permission_granted("delete_customer")){
				if(!isset($_POST["customer_id"])){
					$success = false;
					$error = $request->addChild("error");
					$error->addAttribute("id","0");
				}
				if($success){
					remove_customer($_POST[customer_id]);
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
			if(!media_instance_loaned($_POST["barcode"])) {
				$success = false;
				$error = $request->addChild("error");
				$error->addAttribute("id","11");
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
					if(!media_instance_exists($row)){
						$success = false;
						$error = $request->addChild("error");
						$error->addAttribute("id","2");
						$error->addAttribute("extra_detail",$row);
					}else{
						return_media_instance($row);
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
