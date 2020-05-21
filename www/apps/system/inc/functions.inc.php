<?php
include_once("password.inc.php");

function validateDate($date, $format = 'Y-m-d H:i:s'){
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

function sonderzeichen($string){
	$string = str_replace("ä", "ae", $string);
	$string = str_replace("ü", "ue", $string);
	$string = str_replace("ö", "oe", $string);
	$string = str_replace("Ä", "Ae", $string);
	$string = str_replace("Ü", "Ue", $string);
	$string = str_replace("Ö", "Oe", $string);
	$string = str_replace("ß", "ss", $string);

	return $string;
}

function getSiteURL() {
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	return $protocol.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';
}

function record_log($type,$log){
	global $pdo;
	global $user;

	$statement = $pdo->prepare("INSERT INTO logs (type,member_id,log,post_array,get_array,remote_addr,proxy_forwared_for_addr) VALUES (:type,:member_id,:log,:post,:get,:remote_addr,:proxy_forwared_for_addr);");

	$get_array = "";
	$post_array = "";
	if(config("log_get_and_post_values") == 1){
		$get_array = json_encode($_GET);
		$post_array = json_encode($_POST);
	}

	$statement->execute(array("type" => $type, "member_id" => $user["id"], "log" => $log,"get" => $get_array, "post" => $post_array, "remote_addr" => $_SERVER['REMOTE_ADDR'], "proxy_forwared_for_addr" => $_SERVER['HTTP_X_FORWARDED_FOR']));
}

function error($error_msg) {
	include("templates/error.inc.php");
	exit();
}
function language($id){
	global $pdo;

	$statement = $pdo->prepare("SELECT * FROM languages WHERE id = :id LIMIT 1;");
	$statement->execute(array("id" => $id));
	$language = $statement->fetchAll(PDO::FETCH_ASSOC);

	return $language[0];
}
function media_exists($title){
	global $pdo;

	$statement = $pdo->prepare("SELECT COUNT(id) FROM medias WHERE title = :title;");
	$statement->execute(array("title" => $title));
	$exists = $statement->fetch();

	return ($exists[0] == 1);
}
function media_id_exists($id){
	global $pdo;

	$statement = $pdo->prepare("SELECT COUNT(id) FROM medias WHERE id = :id;");
	$statement->execute(array("id" => $id));
	$exists = $statement->fetch();

	return ($exists[0] == 1);
}
function media_instance_exists($barcode){
	global $pdo;

	$statement = $pdo->prepare("SELECT COUNT(barcode) FROM media_instances WHERE barcode = :barcode;");
	$statement->execute(array("barcode" => $barcode));
	$exists = $statement->fetch();

	return ($exists[0] == 1);
}
function design_exists($id){
	global $pdo;

	$statement = $pdo->prepare("SELECT COUNT(id) FROM designs WHERE id = :id;");
	$statement->execute(array("id" => $id));
	$exists = $statement->fetch();

	return ($exists[0] == 1);
}
function customer_exists($customer_id){
	global $pdo;

	$statement = $pdo->prepare("SELECT COUNT(id) FROM customers WHERE id = :customer_id LIMIT 1;");
	$statement->execute(array("customer_id" => $customer_id));
	$exists = $statement->fetch();

	return ($exists[0] == 1);
}
function customer_class_exists($class_id){
    //customer_exists() is sadly a php function :(
	global $pdo;

	$statement = $pdo->prepare("SELECT COUNT(id) FROM classes WHERE id = :class_id LIMIT 1;");
	$statement->execute(array("class_id" => $class_id));
	$exists = $statement->fetch();

	return ($exists[0] == 1);
}
function school_year_exists($school_year_id){
	global $pdo;

	$statement = $pdo->prepare("SELECT COUNT(id) FROM school_years WHERE id = :school_year_id LIMIT 1;");
	$statement->execute(array("school_year_id" => $school_year_id));
	$exists = $statement->fetch();

	return ($exists[0] == 1);
}
function type_exists($type_id){
	global $pdo;

	$statement = $pdo->prepare("SELECT COUNT(id) FROM types WHERE id = :type_id LIMIT 1;");
	$statement->execute(array("type_id" => $type_id));
	$exists = $statement->fetch();

	return ($exists[0] == 1);
}
function customer_name($customer_id){
	global $pdo;

	$statement = $pdo->prepare("SELECT name FROM customers WHERE id = :customer_id LIMIT 1;");
	$statement->execute(array("customer_id" => $customer_id));
	$name = $statement->fetch();

	return $name[0];
}
function media_instance_loaned($barcode){
	global $pdo;

	$statement = $pdo->prepare("SELECT COUNT(barcode) FROM media_instances WHERE barcode = :barcode AND loaned_to is not null;");
	$statement->execute(array("barcode" => $barcode));
	$exists = $statement->fetch();

	return ($exists[0] == 1);
}
function media_instance_loaned_to($barcode){
	global $pdo;
	if(media_instance_loaned($barcode)){
		$statement = $pdo->prepare("SELECT loaned_to FROM media_instances WHERE barcode = :barcode LIMIT 1;");
		$statement->execute(array("barcode" => $barcode));
		$loaned_to = $statement->fetch();

		return $loaned_to[0][0];
	}else{
		return false;
	}
}
function lend_media_instance($barcode, $customer_id, $until, $holiday){
	global $pdo;

	$statement = $pdo->prepare("UPDATE media_instances SET loaned_to = :customer_id, loaned_until = :loaned_until, holiday = :holiday WHERE barcode = :barcode LIMIT 1;");
	$statement->execute(array("customer_id" => $customer_id, "loaned_until" => $until,"barcode" => $barcode,"holiday" => $holiday));
}
function return_media_instance($barcode){
	global $pdo;

	$statement = $pdo->prepare("UPDATE media_instances SET loaned_to = null, loaned_until = null, holiday = null WHERE barcode = :barcode LIMIT 1;");
	$statement->execute(array("barcode" => $barcode));
}
function new_media_instance($media_id,$barcode){
	global $pdo;

	$statement = $pdo->prepare("INSERT INTO media_instances (media_id,barcode) VALUES (:media_id,:barcode);");
	$statement->execute(array("media_id" => $media_id, "barcode" => $barcode));
}
function new_media($title,$author,$publisher,$price,$school_year_id,$subject_id,$type_id){
	global $pdo;
	if($price == ""){
		$price = NULL;
	}

	$statement = $pdo->prepare("INSERT INTO medias (title,author,publisher,price,school_year_id,subject_id,type_id) VALUES (:title,:author,:publisher,:price,:school_year_id,:subject_id,:type_id);");
	$statement->execute(array("title" => $title, "author" => $author, "publisher" => $publisher, "price" => $price, "school_year_id" => $school_year_id, "subject_id" => $subject_id, "type_id" => $type_id));
}
function new_customer($name,$class_id){
	global $pdo;

	$statement = $pdo->prepare("INSERT INTO customers (name,class_id) VALUES (:name,:class_id);");
	$statement->execute(array("name" => $name, "class_id" => $class_id));
}
function remove_media_instance($barcode){
	global $pdo;

	$statement = $pdo->prepare("DELETE FROM media_instances WHERE barcode = :barcode LIMIT 1");
	$statement->execute(array("barcode" => $barcode));
}
function remove_media($media_id){
	global $pdo;

	$statement = $pdo->prepare("DELETE FROM medias WHERE id = :id LIMIT 1");
	$statement->execute(array("id" => $media_id));

	$statement2 = $pdo->prepare("DELETE FROM media_instances WHERE media_id = :media_id;");
	$statement2->execute(array("media_id" => $media_id));
}
function remove_customer($customer_id){
	global $pdo;

	$statement = $pdo->prepare("DELETE FROM customers WHERE id = :id LIMIT 1");
	$statement->execute(array("id" => $customer_id));

	$statement2 = $pdo->prepare("UPDATE media_instances SET loaned_to = null, loaned_until = null, holiday = null WHERE loaned_to = :customer_id;");
	$statement2->execute(array("customer_id" => $customer_id));
}
