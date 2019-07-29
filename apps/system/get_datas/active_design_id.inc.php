<?php
    $design = new SimpleXMLElement("<design></design>");
    $design->addChild("id")->addAttribute("value",config("active_design"));
    header('Content-Type: text/xml');
    echo $design->asXML();
?>