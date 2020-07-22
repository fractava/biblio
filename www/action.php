<?php
require($_SERVER['DOCUMENT_ROOT'] . "/core/autoload.inc.php");

use xml\xml;

$actionName = $_POST["action"];
$actionApp = $_POST["app"];

if(isset($actionName) && isset($actionApp)){
	if(ctype_alnum($actionApp)) {

		$className = "apps\\" . actionApp . "\\actions\\" . str_replace(":", "\\", $actionName);
		
		if(class_exists($className)) {
			$action = new $className;
			
			if($action->clearOutput) {
				ob_start();
			}
			
			$errors = $action->init();
			if(empty($errors)) {
				$results = $action->run();
			}else {
				http_response_code(400);
			}
			
			if($action->clearOutput) {
				ob_get_clean();
			}
			
			if($action->returnType == "xml") {
				$xml = new \SimpleXMLElement('<?xml version="1.0"?><request></request>');
				$xml_errors = $xml->addChild("errors");
				$xml_results = $xml->addChild("results");
				
				if(is_array($errors)) {
					foreach($errors as $error) {
						$xml_error = $xml_errors->addChild("error");
						$xml_error->addAttribute("id", $error);
					}
				}
				
				if(is_array($results)) {
					foreach($results as $key => $value) {
						$xml_results->addChild($key, $value);
					}
				}
				
				header('Content-Type: application/xml; charset=utf-8');
				echo $xml->asXML();
			}
		}else {
			http_response_code(404);
		}
	}
}else {
    http_response_code(404);
}
