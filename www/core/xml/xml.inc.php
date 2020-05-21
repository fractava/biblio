<?php

namespace \core\xml;

class xml {
    function arrayToXml($data) {
        $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
        
        foreach($data as $key => $value) {
            if(is_array($value)) {
                if(is_numeric($key)){
                    $key = 'item'.$key;
                }
                $subnode = $xml_data->addChild($key);
                xml::arrayToXml($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
        
        return $xml_data;
    }
}
