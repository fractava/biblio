<?php
//$db_host = 'cloud.fractava.com';
$db_host = '192.168.1.103';
$db_name = 'biblio';
$db_user = 'biblio';
$db_password = '#WeHateOpenBiblio';
$pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
