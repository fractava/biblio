<?php
require($_SERVER['DOCUMENT_ROOT'] . "/inc/autoload.inc.php");

use xml\xml;

$getDataName = $_GET["getData"];
$actionApp = $_POST["app"];

if(isset($getDataName)){
	if(ctype_alnum($actionApp)) {
		$className = "apps\\" . actionApp . "\\getData\\" . str_replace(":", "\\", $actionName);

		if(class_exists($className)) {
			$getData = new $className;
			
			if($getData->clearOutput) {
				ob_start();
			}
		
			$errors = $getData->init();
			if(empty($errors)) {
				$results = $getData->run();
			}else {
				http_response_code(400);
			}
			
			if($getData->clearOutput) {
				ob_get_clean();
			}
			
			if($getData->returnType == "xml") {
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
