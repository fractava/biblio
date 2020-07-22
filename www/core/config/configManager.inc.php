<?php
namespace \core\config;

class configManager {
    function getConfig() {
        return require($_SERVER['DOCUMENT_ROOT'] . "/config/config.inc.php");
    }
}
