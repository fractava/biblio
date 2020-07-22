<?php
spl_autoload_register(function ($pClassName) {
    $path = $_SERVER['DOCUMENT_ROOT'] . str_replace("\\", "/", $pClassName) . ".inc.php";
    if(file_exists($path)) {
        require($path);
    }
  }
);
