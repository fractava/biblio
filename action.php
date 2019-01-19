<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
if(isset($_POST["action"])){
	switch($_POST["action"]){
		 case "login":
			require_once("./actions/login.php");
		break;
		case "logout":
			require_once("./actions/logout.php");
		break;
		case "lent_media_instance":
			if(isset($_POST["student_id"]) && isset($_POST["barcode"])){
				if(isset($_POST["until"]) && validateDate($_POST["until"],'Y-m-d')){
					$until = $_POST["until"];
				}else{
					http_response_code(400);
					echo "Until not correctly set";
					exit();
				}
				if(!media_instance_exists($_POST["barcode"])){
					http_response_code(400);
					echo "Media doesnt exist";
					exit();
				}
				if(!student_exists($_POST["student_id"])){
					http_response_code(400);
					echo "Student doesnt exist";
					exit();
				}
				if(media_instance_loaned($_POST["barcode"])){
					http_response_code(400);
					echo "Media already loaned";
					exit();
				}
				if(!(($_POST["holiday"] == "1") || ($_POST["holiday"] == "0"))){
					http_response_code(400);
					echo "Holiday not correctly set";
					exit();
				}
				lend_media_instance($_POST["barcode"] , $_POST["student_id"], $until , $_POST["holiday"]);
				echo "success";
			}
		break;
		case "return_media_instance":
			if(isset($_POST["barcode"])){
				if(!media_instance_exists($_POST["barcode"])){
					http_response_code(400);
					echo "Media doesnt exist";
					exit();
				}
				return_media_instance($_POST["barcode"]);
				echo "success";
			}
		break;
	}
}
?>
