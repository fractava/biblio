<?php
require_once("/web/inc/config.inc.php");
require_once("/web/inc/functions.inc.php");
session_start();
$user = check_user();
if(isset($_GET["requested_data"])){
	switch($_GET["requested_data"]){
		case "logged_in":
			echo "true";
		break;
		case "students_list":
			$schueler = new SimpleXMLElement("<studentslist></studentslist>");

			$statement = $pdo->prepare("SELECT id , name , class_id FROM students;");
			$statement->execute();

			$statement2 = $pdo->prepare("SELECT id , name FROM classes;");
			$statement2->execute();
			$classes = $statement2->fetch();

			while($row = $statement->fetch()){
				//var_dump($classes);
				$xml_row = $schueler->addChild('student');
				$xml_row->addAttribute('id',$row['id']);
				$xml_row->addAttribute('name',$row['name']);
				$statement2 = $pdo->prepare("SELECT id , name FROM classes WHERE id = :class_id;");
				$statement2->execute(array("class_id" => $row['class_id']));
				$xml_row->addAttribute('class', $statement2->fetch()["name"]);
			}
			header('Content-Type: text/xml');
			echo $schueler->asXML();
		break;
		case "books_of_student":
			if(isset($_GET["student_id"])){ 
				$statement = $pdo->prepare("SELECT media_id , loaned_until , holiday , barcode FROM media_instances WHERE loaned_to = :student_id;"); 
                        	$statement->execute(array("student_id" => $_GET["student_id"]));
				$books = new SimpleXMLElement("<books></books>"); 
				while($row = $statement->fetch()){
					$xml_row = $books->addChild('book');
					$xml_row->addAttribute('media_id',$row['media_id']);

					$statement2 = $pdo->prepare("SELECT * FROM medias WHERE id = :media_id;");
					$statement2->execute(array("media_id" => $row['media_id']));
					$media_row = $statement2->fetch();

					$xml_row->addAttribute('title',$media_row['title']);
					$xml_row->addAttribute('author',$media_row['author']);
					$xml_row->addAttribute('publisher',$media_row['publisher']);
					$xml_row->addAttribute('price',$media_row['price']);
					$xml_row->addAttribute('school_year',$media_row['school_year']);

					$xml_row->addAttribute('loaned_until',$row['loaned_until']);
					$xml_row->addAttribute('holiday',$row['holiday']);
					$xml_row->addAttribute('barcode',$row['barcode']);
				}
			header('Content-Type: text/xml');
                        echo $books->asXML();
			}
		break;
		case "media_instance_infos":
			if(isset($_GET["barcode"])){
				$statement = $pdo->prepare("SELECT media_id , loaned_until , loaned_to , holiday , barcode FROM media_instances WHERE barcode = :barcode;");
				$statement->execute(array("barcode" => $_GET["barcode"]));
				$row = $statement->fetch();
				$infos = new SimpleXMLElement("<infos></infos>");

				$xml_row = $infos->addChild('media');
				$xml_row->addAttribute('media_id',$row['media_id']);
				$xml_row->addAttribute('loaned_to',$row['loaned_to']);
				$xml_row->addAttribute('loaned_until',$row['loaned_until']);
				$xml_row->addAttribute('holiday',$row['holiday']);

				$statement2 = $pdo->prepare("SELECT * FROM medias WHERE id = :media_id;");
				$statement2->execute(array("media_id" => $row['media_id']));
				$media_row = $statement2->fetch();

				$xml_row->addAttribute('title',$media_row['title']);
				$xml_row->addAttribute('author',$media_row['author']);
				$xml_row->addAttribute('publisher',$media_row['publisher']);
				$xml_row->addAttribute('price',$media_row['price']);
				$xml_row->addAttribute('school_year',$media_row['school_year']);

				$statement3 = $pdo->prepare("SELECT * FROM students WHERE id = :student_id;");
				$statement3->execute(array("student_id" => $row['loaned_to']));
				$student_row = $statement3->fetch();

				$xml_row->addAttribute('loaned_to_name',$student_row['name']);
				$xml_row->addAttribute('class_id',$student_row['class_id']);

				header('Content-Type: text/xml');
				echo $infos->asXML();
			}
		break;
		case "overdue_medias":
			$statement = $pdo->prepare("SELECT * FROM media_instances WHERE DATEDIFF(loaned_until,NOW()) < 0 AND holiday = 0;");
			$statement->execute();
			$books = new SimpleXMLElement("<books></books>");
			while($row = $statement->fetch()){
				$xml_row = $books->addChild('book');
				$xml_row->addAttribute('id',$row['id']);
				$xml_row->addAttribute('media_id',$row['media_id']);
				$xml_row->addAttribute('loaned_to',$row['loaned_to']);
				$xml_row->addAttribute('barcode',$row['barcode']);

				$statement2 = $pdo->prepare("SELECT * FROM medias WHERE id = :media_id;");
				$statement2->execute(array("media_id" => $row['media_id']));
				$media_row = $statement2->fetch();

				$xml_row->addAttribute('title',$media_row['title']);
				$xml_row->addAttribute('author',$media_row['author']);
				$xml_row->addAttribute('publisher',$media_row['publisher']);
				$xml_row->addAttribute('price',$media_row['price']);
				$xml_row->addAttribute('school_year',$media_row['school_year']);

				$statement3 = $pdo->prepare("SELECT * FROM students WHERE id = :student_id;");
				$statement3->execute(array("student_id" => $row['loaned_to']));
				$student_row = $statement3->fetch();

				$xml_row->addAttribute('loaned_to_name',$student_row['name']);
				$xml_row->addAttribute('class_id',$student_row['class_id']);

				$statement4 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
				$statement4->execute(array("class_id" => $student_row['class_id']));
				$class_row = $statement4->fetch();

				$xml_row->addAttribute('class_name',$class_row['name']);
			}
			header('Content-Type: text/xml');
			echo $books->asXML();
		break;
		case "overdue_holiday_medias":
			$statement = $pdo->prepare("SELECT * FROM media_instances WHERE DATEDIFF(loaned_until,NOW()) < 0 AND holiday = 1;");
			$statement->execute();
			$books = new SimpleXMLElement("<medias></medias>");
			while($row = $statement->fetch()){
				$xml_row = $books->addChild('book');
				$xml_row->addAttribute('id',$row['id']);
				$xml_row->addAttribute('media_id',$row['media_id']);
				$xml_row->addAttribute('loaned_to',$row['loaned_to']);
				$xml_row->addAttribute('barcode',$row['barcode']);

				$statement2 = $pdo->prepare("SELECT * FROM medias WHERE id = :media_id;");
				$statement2->execute(array("media_id" => $row['media_id']));
				$media_row = $statement2->fetch();

				$xml_row->addAttribute('title',$media_row['title']);
				$xml_row->addAttribute('author',$media_row['author']);
				$xml_row->addAttribute('publisher',$media_row['publisher']);
				$xml_row->addAttribute('price',$media_row['price']);
				$xml_row->addAttribute('school_year',$media_row['school_year']);

				$statement3 = $pdo->prepare("SELECT * FROM students WHERE id = :student_id;");
				$statement3->execute(array("student_id" => $row['loaned_to']));
				$student_row = $statement3->fetch();

				$xml_row->addAttribute('loaned_to_name',$student_row['name']);
				$xml_row->addAttribute('class_id',$student_row['class_id']);

				$statement4 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
				$statement4->execute(array("class_id" => $student_row['class_id']));
				$class_row = $statement4->fetch();

				$xml_row->addAttribute('class_name',$class_row['name']);
			}
			header('Content-Type: text/xml');
			echo $books->asXML();
		break;
		case "search_student":
			if(isset($_GET["search"])){
				$statement = $pdo->prepare("SELECT * FROM students WHERE name LIKE :search;");
				$statement->execute(array("search" => "%" . $_GET["search"] . "%"));
				$students = new SimpleXMLElement("<students></students>");

				while($row = $statement->fetch()){
					$xml_row = $students->addChild('student');
					$xml_row->addAttribute('id',$row['id']);
					$xml_row->addAttribute('name',$row['name']);
					$xml_row->addAttribute('class_id',$row['class_id']);
					$xml_row->addAttribute('birthday',$row['birthday']);

					$statement2 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
					$statement2->execute(array("class_id" => $row['class_id']));
					$class_row = $statement2->fetch();

					$xml_row->addAttribute('class_name',$class_row['name']);
					$xml_row->addAttribute('school_year',$class_row['school_year']);
				}
				header('Content-Type: text/xml');
				echo $students->asXML();
			}
		break;
		case "search_media":
			if(isset($_GET["search"])){
				$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search;");
				$statement->execute(array("search" => "%" . $_GET["search"] . "%"));
				$medias = new SimpleXMLElement("<medias></medias>");

				while($row = $statement->fetch()){
					$xml_row = $medias->addChild('media');
					$xml_row->addAttribute('id',$row['id']);
					$xml_row->addAttribute('title',$row['title']);
					$xml_row->addAttribute('author',$row['author']);
					$xml_row->addAttribute('publisher',$row['publisher']);
					$xml_row->addAttribute('price',$row['price']);
					$xml_row->addAttribute('school_year',$row['school_year']);
					$xml_row->addAttribute('type_id',$row['type_id']);

					$statement2 = $pdo->prepare("SELECT * FROM types WHERE id = :type_id;");
					$statement2->execute(array("type_id" => $row['type_id']));
					$types_row = $statement2->fetch();

					$xml_row->addAttribute('type',$types_row['name']);
				}
				header('Content-Type: text/xml');
				echo $medias->asXML();
			}
		break;
	}
}
?>
