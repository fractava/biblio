<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
if(isset($_POST["action"])){
	$request = new SimpleXMLElement("<request></request>");
	$success = true;
	switch($_POST["action"]){
		case "set_active_design":

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
