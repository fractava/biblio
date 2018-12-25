<?php
/**
 * A complete login script with registration and members area.
 *
 * @author: Nils Reimers / http://www.php-einfach.de/experte/php-codebeispiele/loginscript/
 * @license: GNU GPLv3
 */
 
//Tragt hier eure Verbindungsdaten zur Datenbank ein
$db_host = 'cloud.fractava.com';
//echo "dbhost set";
$db_name = 'biblio';
//echo "dbname set";
$db_user = 'root';
//echo "dbuser set";
$db_password = 'nextcloud@tessy14';
//echo "dbpassword set";
$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
//echo "pdo set";
