<?php
require_once("/web/inc/config.inc.php");
require_once("/web/inc/functions.inc.php");
session_start();
//record_log(0,"acces to getData.php");
if(isset($_GET["requested_data"])){
	switch($_GET["requested_data"]){
		case "logged_in":
			$user = check_user();
			echo "true";
		break;
		case "permission_list":
			$user = check_user();
			$permissions = new SimpleXMLElement("<permission_list></permission_list>");

			foreach(permission_list() as $name => $permission){
				if($name != "name" && $name != "id"){
					$xml_row = $permissions->addChild($name);
					$xml_row->addAttribute("value",$permission);
				}
			}

			header('Content-Type: text/xml');
			echo $permissions->asXML();
		break;
		case "language":
			if(isset($_GET["language_id"])){
				$language = new SimpleXMLElement("<language></language>");

				foreach(language($_GET["language_id"]) as $name => $value){
					$xml_row = $language->addChild($name);
					$xml_row->addAttribute("value",$value);
				}
				header('Content-Type: text/xml');
				echo $language->asXML();
			}
		break;
		case "languages_list":
			$languages = new SimpleXMLElement("<languages></languages>");

			$statement = $pdo->prepare("SELECT id , lang_name FROM languages;");
			$statement->execute();

			while($row = $statement->fetch()){
				$xml_row = $languages->addChild('language');
				$xml_row->addAttribute('id',$row['id']);
				$xml_row->addAttribute('name',$row['lang_name']);
			 }
			header('Content-Type: text/xml');
			echo $languages->asXML();
		break;
		case "active_design_id":
		    $design = new SimpleXMLElement("<design></design>");
		    $design->addChild("id")->addAttribute("value",config("active_design"));
		    header('Content-Type: text/xml');
			echo $design->asXML();
		break;
		case "active_design":
		    $design_id = config("active_design");
			if(design_exists($design_id)){
				$design = new SimpleXMLElement("<design></design>");
				
				foreach(design($design_id) as $name => $value){
					$xml_row = $design->addChild($name);
					$xml_row->addAttribute("value",$value);
				}
				header('Content-Type: text/xml');
				echo $design->asXML();
			}else{
				http_response_code(400);
				echo "Design not found";
			}
		break;
	    case "design":
		    $user = check_user();
		    
		    if(isset($_GET["id"])){
    			if(design_exists($_GET["id"])){
    				$design = new SimpleXMLElement("<design></design>");
    				
    				foreach(design($_GET["id"]) as $name => $value){
    					$xml_row = $design->addChild($name);
    					$xml_row->addAttribute("value",$value);
    				}
    				header('Content-Type: text/xml');
    				echo $design->asXML();
    			}else{
    				http_response_code(400);
    				echo "Design not found";
    			}
		    }
		break;
		case "classes_list":
			$user = check_user();
			$classes = new SimpleXMLElement("<classeslist></classeslist>");

			$statement = $pdo->prepare("SELECT * FROM classes ORDER BY school_year_id;");
			$statement->execute();

			while($row = $statement->fetch()){
				$xml_row = $classes->addChild('class');
				$xml_row->addAttribute('id',$row['id']);
				$xml_row->addAttribute('name',$row['name']);
				$xml_row->addAttribute('school_year_id',$row['school_year_id']);

				$statement2 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
				$result = $statement2->execute(array("school_year_id" => $row['school_year_id']));
				$school_year_row = $statement2->fetch();

				$xml_row->addAttribute('school_year',$school_year_row['name']);
			}

			header('Content-Type: text/xml');
			echo $classes->asXML();
		break;
		case "customers_list":
			$user = check_user();
			$customers = new SimpleXMLElement("<customerslist></customerslist>");

			$statement = $pdo->prepare("SELECT id , name , class_id FROM customers;");
			$statement->execute();

			while($row = $statement->fetch()){
				$xml_row = $customers->addChild('customer');
				$xml_row->addAttribute('id',$row['id']);
				$xml_row->addAttribute('name',$row['name']);

				$statement2 = $pdo->prepare("SELECT id , name FROM classes WHERE id = :class_id;");
				$statement2->execute(array("class_id" => $row['class_id']));

				$xml_row->addAttribute('class', $statement2->fetch()["name"]);
			}
			header('Content-Type: text/xml');
			echo $customers->asXML();
		break;
		case "subjects_list":
			$user = check_user();
			$subjects = new SimpleXMLElement("<subjectslist></subjectslist>");
			$statement = $pdo->prepare("SELECT id , name FROM subjects;");
			$statement->execute();

			while($row = $statement->fetch()){
				$xml_row = $subjects->addChild('subject');
				$xml_row->addAttribute('id',$row['id']);
				$xml_row->addAttribute('name',$row['name']);
			}
			header('Content-Type: text/xml');
			echo $subjects->asXML();
		break;
		case "school_years_list":
			$user = check_user();
			$school_years = new SimpleXMLElement("<schoolyearslist></schoolyearslist>");
			$statement = $pdo->prepare("SELECT id , name FROM school_years;");
			$statement->execute();

			while($row = $statement->fetch()){
				$xml_row = $school_years->addChild('school_year');
				$xml_row->addAttribute('id',$row['id']);
				$xml_row->addAttribute('name',$row['name']);
			}
			header('Content-Type: text/xml');
			echo $school_years->asXML();
		break;
		case "dates_list":
			$user = check_user();
			if(isset($_GET["holiday"])){
				if($_GET["holiday"] == 1 || $_GET["holiday"] == 0){
					$dates = new SimpleXMLElement("<dateslist></dateslist>");
					$statement = $pdo->prepare("SELECT id , date , name FROM loaned_until_dates WHERE holiday = :holiday;");
					$statement->execute(array("holiday" => $_GET["holiday"]));

					while($row = $statement->fetch()){
						$xml_row = $dates->addChild('date');
						$xml_row->addAttribute('id',$row['id']);
						$xml_row->addAttribute('date',$row['date']);
						$xml_row->addAttribute('name',$row['name']);
					}
					header('Content-Type: text/xml');
					echo $dates->asXML();
				}
			}
		break;
		case "medias_of_customer":
			$user = check_user();
			if(isset($_GET["customer_id"])){
				$statement = $pdo->prepare("SELECT media_id , loaned_until , holiday , barcode FROM media_instances WHERE loaned_to = :customer_id;");
                        	$statement->execute(array("customer_id" => $_GET["customer_id"]));
				$medias = new SimpleXMLElement("<medias></medias>");
				while($row = $statement->fetch()){
					$xml_row = $medias->addChild('media');
					$xml_row->addAttribute('media_id',$row['media_id']);

					$statement2 = $pdo->prepare("SELECT * FROM medias WHERE id = :media_id;");
					$statement2->execute(array("media_id" => $row['media_id']));
					$media_row = $statement2->fetch();

					$xml_row->addAttribute('title',$media_row['title']);
					$xml_row->addAttribute('author',$media_row['author']);
					$xml_row->addAttribute('publisher',$media_row['publisher']);
					$xml_row->addAttribute('price',$media_row['price']);
					$xml_row->addAttribute('school_year_id',$media_row['school_year_id']);

					$statement3 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
					$result = $statement3->execute(array("school_year_id" => $media_row['school_year_id']));
					$school_year_row = $statement3->fetch();

					$xml_row->addAttribute('school_year',$school_year_row['name']);

					$xml_row->addAttribute('loaned_until',$row['loaned_until']);
					$xml_row->addAttribute('holiday',$row['holiday']);
					$xml_row->addAttribute('barcode',$row['barcode']);
				}
			header('Content-Type: text/xml');
      echo $medias->asXML();
			}
		break;
		case "media_instances":
			$user = check_user();
			if(isset($_GET["media_id"])){
				$statement = $pdo->prepare("SELECT * FROM media_instances WHERE media_id = :media_id ORDER BY barcode;");
				$statement->execute(array("media_id" => $_GET["media_id"]));

				$instances = new SimpleXMLElement("<media_instances></media_instances>");

				while($row = $statement->fetch()){
					$xml_row = $instances->addChild('media_instance');

					$xml_row->addAttribute('barcode',$row['barcode']);

					$xml_row->addAttribute('loaned_to',$row['loaned_to']);
					$xml_row->addAttribute('loaned_until',$row['loaned_until']);
					$xml_row->addAttribute('holiday',$row['holiday']);

					$statement2 = $pdo->prepare("SELECT * FROM customers WHERE id = :customer_id;");
					$statement2->execute(array("customer_id" => $row['loaned_to']));
					$customer_row = $statement2->fetch();

					$xml_row->addAttribute('loaned_to_name',$customer_row['name']);
					$xml_row->addAttribute('class_id',$customer_row['class_id']);

					$statement3 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
					$statement3->execute(array("class_id" => $customer_row['class_id']));
					$class_row = $statement3->fetch();

					$xml_row->addAttribute('class_name',$class_row['name']);
				}
				header('Content-Type: text/xml');
				echo $instances->asXML();
			}
		break;
		case "media_details":
			$user = check_user();
			if(isset($_GET["media_id"])){
				$details = new SimpleXMLElement("<details></details>");

				$statement = $pdo->prepare("SELECT * FROM medias WHERE id = :media_id;");
				$statement->execute(array("media_id" => $_GET['media_id']));
				$row = $statement->fetch();

				/*$xml_row = $details->addChild('media');
				$xml_row->addAttribute('title',$row['title']);
				$xml_row->addAttribute('author',$row['author']);
				$xml_row->addAttribute('publisher',$row['publisher']);
				$xml_row->addAttribute('price',$row['price']);
				$xml_row->addAttribute('school_year_id',$row['school_year_id']);
				$xml_row->addAttribute('subject_id',$row['subject_id']);
				$xml_row->addAttribute('miscellaneous',$row['miscellaneous']);*/

				$details->addChild('title')->addAttribute('value',$row['title']);
				$details->addChild('author')->addAttribute('value',$row['author']);
				$details->addChild('publisher')->addAttribute('value',$row['publisher']);
				$details->addChild('price')->addAttribute('value',$row['price']);
				$details->addChild('school_year_id')->addAttribute('value',$row['school_year_id']);
				$details->addChild('subject_id')->addAttribute('value',$row['subject_id']);
				$details->addChild('miscellaneous')->addAttribute('value',$row['miscellaneous']);

				$statement2 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
				$result = $statement2->execute(array("school_year_id" => $row['school_year_id']));
				$school_year_row = $statement2->fetch();

				//$xml_row->addAttribute('school_year',$school_year_row['name']);
				$details->addChild('school_year')->addAttribute('value',$school_year_row['name']);

				$statement3 = $pdo->prepare("SELECT * FROM types WHERE id = :type_id;");
				$statement3->execute(array("type_id" => $row['type_id']));
				$type_row = $statement3->fetch();

				/*$xml_row->addAttribute('type_id',$type_row['id']);
				$xml_row->addAttribute('type_name',$type_row['name']);*/
				$details->addChild('type_id')->addAttribute('value',$type_row['id']);
				$details->addChild('type_name')->addAttribute('value',$type_row['name']);

				header('Content-Type: text/xml');
				echo $details->asXML();
			}
		break;
		case "customer_details":
			$user = check_user();
			if(isset($_GET["customer_id"])){
				$details = new SimpleXMLElement("<details></details>");

				$statement = $pdo->prepare("SELECT * FROM customers WHERE id = :customer_id;");
				$statement->execute(array("customer_id" => $_GET['customer_id']));
				$row = $statement->fetch();

				/*$xml_row = $details->addChild('customer');
				$xml_row->addAttribute('name',$row['name']);
				$xml_row->addAttribute('class_id',$row['class_id']);
				$xml_row->addAttribute('miscellaneous',$row['miscellaneous']);*/
				$details->addChild('name')->addAttribute('value',$row['name']);
				$details->addChild('class_id')->addAttribute('value',$row['class_id']);
				$details->addChild('miscellaneous')->addAttribute('value',$row['miscellaneous']);

				$statement2 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
				$statement2->execute(array("class_id" => $row['class_id']));
				$class_row = $statement2->fetch();

				//$xml_row->addAttribute('class',$class_row['name']);
				$details->addChild('class')->addAttribute('value',$class_row['name']);

				header('Content-Type: text/xml');
				echo $details->asXML();
			}
		break;
		case "types_list":
			$user = check_user();
			$types = new SimpleXMLElement("<types></types>");

			$statement = $pdo->prepare("SELECT * FROM types;");
			$statement->execute();

			while($row = $statement->fetch()){
				$xml_row = $types->addChild('type');

				$xml_row->addAttribute('id',$row['id']);
				$xml_row->addAttribute('name',$row['name']);
			}

			header('Content-Type: text/xml');
			echo $types->asXML();
		break;
		case "designs_list":
			$user = check_user();
			$designs = new SimpleXMLElement("<designs></designs>");

			$statement = $pdo->prepare("SELECT * FROM designs;");
			$statement->execute();

			while($row = $statement->fetch(PDO::FETCH_ASSOC)){
				$xml_row = $designs->addChild('design');
				
				foreach($row as $name => $value){
				   $xml_row->addAttribute($name,$value); 
				}
			}

			header('Content-Type: text/xml');
			echo $designs->asXML();
		break;
		case "media_instance_infos":
			$user = check_user();
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
				$xml_row->addAttribute('school_year_id',$media_row['school_year_id']);

				$statement3 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
				$result = $statement3->execute(array("school_year_id" => $media_row['school_year_id']));
				$school_year_row = $statement3->fetch();

				$xml_row->addAttribute('school_year',$school_year_row['name']);

				$statement4 = $pdo->prepare("SELECT * FROM customers WHERE id = :customer_id;");
				$statement4->execute(array("customer_id" => $row['loaned_to']));
				$customer_row = $statement4->fetch();

				$xml_row->addAttribute('loaned_to_name',$customer_row['name']);
				$xml_row->addAttribute('class_id',$customer_row['class_id']);

				header('Content-Type: text/xml');
				echo $infos->asXML();
			}
		break;
		case "overdue_medias":
			$user = check_user();
			if(isset($_GET["holiday"]) && ($_GET["holiday"] == 0 || $_GET["holiday"] == 1)){
				$statement = $pdo->prepare("SELECT * FROM media_instances WHERE DATEDIFF(loaned_until,NOW()) < 0 AND holiday = :holiday;");
				$statement->execute(array("holiday" => $_GET["holiday"]));
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
					$xml_row->addAttribute('school_year_id',$media_row['school_year_id']);

					$statement3 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
					$result = $statement3->execute(array("school_year_id" => $media_row['school_year_id']));
					$school_year_row = $statement3->fetch();

					$xml_row->addAttribute('school_year',$school_year_row['name']);

					$statement4 = $pdo->prepare("SELECT * FROM customers WHERE id = :customer_id;");
					$statement4->execute(array("customer_id" => $row['loaned_to']));
					$customer_row = $statement4->fetch();

					$xml_row->addAttribute('loaned_to_name',$customer_row['name']);
					$xml_row->addAttribute('class_id',$customer_row['class_id']);

					$statement5 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
					$statement5->execute(array("class_id" => $customer_row['class_id']));
					$class_row = $statement5->fetch();

					$xml_row->addAttribute('class_name',$class_row['name']);
				}
				header('Content-Type: text/xml');
				echo $books->asXML();
			}
		break;
		case "search_customer":
			$user = check_user();
			if(isset($_GET["search"]) && isset($_GET["class_id"])){
				if(isset($_GET["order_by"])){
					switch($_GET["order_by"]){
						case "id":
							$statement = $pdo->prepare("SELECT * FROM customers WHERE name LIKE :search ORDER BY id;");
						break;
						case "name":
							$statement = $pdo->prepare("SELECT * FROM customers WHERE name LIKE :search ORDER BY name;");
						break;
						default:
							$statement = $pdo->prepare("SELECT * FROM customers WHERE name LIKE :search;");
						break;
					}
				}else{
					$statement = $pdo->prepare("SELECT * FROM customers WHERE name LIKE :search;");
				}

				$statement->execute(array("search" => "%" . $_GET["search"] . "%"));
				$customers = new SimpleXMLElement("<customers></customers>");

				while($row = $statement->fetch()){
					if(($row['class_id'] == $_GET["class_id"]) || $_GET["class_id"] == -1){
						$xml_row = $customers->addChild('customer');
						$xml_row->addAttribute('id',$row['id']);
						$xml_row->addAttribute('name',$row['name']);
						$xml_row->addAttribute('class_id',$row['class_id']);
						//$xml_row->addAttribute('birthday',$row['birthday']);

						$statement2 = $pdo->prepare("SELECT * FROM classes WHERE id = :class_id;");
						$statement2->execute(array("class_id" => $row['class_id']));
						$class_row = $statement2->fetch();

						$xml_row->addAttribute('class_name',$class_row['name']);
						$xml_row->addAttribute('school_year_id',$class_row['school_year_id']);

						$statement3 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
						$result = $statement3->execute(array("school_year_id" => $class_row['school_year_id']));
						$school_year_row = $statement3->fetch();

						$xml_row->addAttribute('school_year',$school_year_row['name']);
					}
				}
				header('Content-Type: text/xml');
				echo $customers->asXML();
			}
		break;
		case "search_media":
			$user = check_user();
			if(isset($_GET["search"]) && isset($_GET["subject_id"]) && isset($_GET["school_year_id"])){
				if(isset($_GET["order_by"])){
					switch($_GET["order_by"]){
						case "id":
							$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search ORDER BY id;");
						break;
						case "title":
							$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search ORDER BY title;");
						break;
						case "author":
							$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search ORDER BY author;");
						break;
						case "publisher":
							$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search ORDER BY publisher;");
						break;
						case "price":
							$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search ORDER BY price;");
						break;
						default:
							$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search;");
						break;
					}
				}else{
					$statement = $pdo->prepare("SELECT * FROM medias WHERE title LIKE :search;");
				}

				$statement->execute(array("search" => "%" . $_GET["search"] . "%"));
				$medias = new SimpleXMLElement("<medias></medias>");

				while($row = $statement->fetch()){
					if(($row['subject_id'] == $_GET["subject_id"]) || $_GET["subject_id"] == -1){
						if(($row['school_year_id'] == $_GET["school_year_id"]) || $_GET["school_year_id"] == -1){
							$xml_row = $medias->addChild('media');
							$xml_row->addAttribute('id',$row['id']);
							$xml_row->addAttribute('title',$row['title']);
							$xml_row->addAttribute('author',$row['author']);
							$xml_row->addAttribute('publisher',$row['publisher']);
							$xml_row->addAttribute('price',$row['price']);
							$xml_row->addAttribute('subject_id',$row['subject_id']);
							$xml_row->addAttribute('type_id',$row['type_id']);
							$xml_row->addAttribute('school_year_id',$row['school_year_id']);

							$statement2 = $pdo->prepare("SELECT * FROM types WHERE id = :type_id;");
							$statement2->execute(array("type_id" => $row['type_id']));
							$types_row = $statement2->fetch();

							$xml_row->addAttribute('type',$types_row['name']);

							$statement3 = $pdo->prepare("SELECT name FROM school_years WHERE id = :school_year_id");
							$result = $statement3->execute(array("school_year_id" => $row['school_year_id']));
							$school_year_row = $statement3->fetch();

							$xml_row->addAttribute('school_year',$school_year_row['name']);
						}
					}
				}
				header('Content-Type: text/xml');
				echo $medias->asXML();
			}
		break;
	}
}
?>
