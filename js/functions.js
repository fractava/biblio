// ================================================
//Events
function onload(){
	last_href = "";
	md = new MobileDetect(window.navigator.userAgent);
	configure_particles_js();
	configureSelect2();
	check_if_already_logged_in();
}
function onlogin(){
	if(findGetParameter("side") && findGetParameter("side") != 0){
		switchToSide(parseInt(findGetParameter("side")));
	}else{
		switchToSide(1);
	}

	auto_refresh(true);

	auto_refresh_interval = setInterval(function(){auto_refresh();}, 750);
	still_logged_in_interval = setInterval(function(){check_if_still_logged_in();}, 3000);

	if(isChristmas()){
		snowStorm.toggleSnow();
	}
}
function onlogout(){
	clearTimeout(still_logged_in_interval);
	if(isChristmas()){
		snowStorm.toggleSnow();
	}
}

// ================================================
//Configure Functions
function configureSelect2(){
	$('#lend_student_select').select2({width: '100%'});
}
function configure_particles_js(){
	if(isChristmas()){
		particlesJS("particles-js", {"particles":{"number":{"value":120,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.55,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":4,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":false,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":6,"direction":"bottom","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"repulse"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":0}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});var count_particles, stats, update; stats = new Stats; stats.setMode(-1); stats.domElement.style.position = 'absolute'; stats.domElement.style.left = '0px'; stats.domElement.style.top = '0px'; document.body.appendChild(stats.domElement); count_particles = document.querySelector('.js-count-particles'); update = function() { stats.begin(); stats.end(); if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) { count_particles.innerText = window.pJSDom[0].pJS.particles.array.length; } requestAnimationFrame(update); }; requestAnimationFrame(update);;
	}else{
		particlesJS("particles-js", {"particles":{"number":{"value":80,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.55,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":3,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":true,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":6,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"repulse"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});var count_particles, stats, update; stats = new Stats; stats.setMode(-1); stats.domElement.style.position = 'absolute'; stats.domElement.style.left = '0px'; stats.domElement.style.top = '0px'; document.body.appendChild(stats.domElement); count_particles = document.querySelector('.js-count-particles'); update = function() { stats.begin(); stats.end(); if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) { count_particles.innerText = window.pJSDom[0].pJS.particles.array.length; } requestAnimationFrame(update); }; requestAnimationFrame(update);;
	}
}

// ================================================
//Login Management
function login(){
	$("#login_error_message").text("");
	$.post('action.php',{action : "login" ,email : document.getElementById('login_email').value, 'passwort' : document.getElementById('login_passwort').value}, function(data, status){
		//alert('Data: ' + data + '\nStatus: ' + status);
		if(data == "sucess"){
			onlogin();
			login_animation();
		}else{
			$("#login_error_message").text(data);
		}
	});
}
function logout(){
	$.post('action.php',{action: "logout"}, function(data, status){
		if(data == "sucess"){
			switchToSide(0);
			//$("#main").animate({height: "0"});
			//$("#main").animate({width: "0"});
			logout_animation();
			onlogout();
		}
	});
}
function check_if_already_logged_in(){
	get_data({requested_data : "logged_in"},
		function(data , status){
			if(data=="true"){
				onlogin();
				login_animation(true);
			}
			$( "html" ).removeClass( "loading" );
		},function(){
			//$(".loading").css("background","#333 url(/assets/error.gif) no-repeat 50% 50%;");
			$(".loading").addClass("loading_error");
			//$(".loading").removeClass("loading");
			console.log("Verbindung zum Server fehlgeschlagen");
			setTimeout(function(){check_if_already_logged_in();},1000);
		}
	);
}
function check_if_still_logged_in(){
	get_data({requested_data : "logged_in"},
		function(data , status){
			if(data !="true"){
				switchToSide(0);
				logout_animation();
				onlogout();
			}
		},function(){}
	);
}
// ================================================
//Animations
function login_animation(fast){
	if(fast){
		speed = 0;
	}else{
		speed = "normal";
	}

	$("#main").animate({height: "100%",width: "100%"},speed,function(){$("#particles-js").hide();});
	$("#nav_div").show(speed);
	//switchToSide(1);
}
function logout_animation(fast){
	if(fast){
		speed = 0;
	}else{
		speed = "normal";
	}
	$("#particles-js").show();
	$("#main").animate({height: "0",width: "0"},speed);
}
function switch_color_scheme(color_scheme){
	switch(color_scheme){
		case 0:
			background_color = "white";
			text_color = "black";
		break;
		case 1:
			background_color = "rgb(35,35,35)";
			text_color = "white";
		break;

	}
	$("div , h1 , h2 , h3 , select , .select2-selection__rendered").css("color",text_color);
	$("div , select , .select2-selection").css("background-color",background_color);
}
function switchToSide(show_side){
	//0 Login
	//1 Bücher E/A
	//2 Katalogisierung
	//3 Schüler
	//4 Listen
	//5 Benutzer
	//6 Admin

	side = show_side;
	var side_names = ["Login","Bücher E/A","Katalogisierung","Schüler","Listen","Benutzer","Admin"];
	var sides = ["login_div","books_div","katalogisierung_div","schüler_div","listen_div"];

	for(i = 0; i <= sides.length;i++){
		if(show_side == i){
			$("#nav_item_side"+i).addClass("nav_a_selected");
			$("#"+sides[i]).show();
		}else{
			$("#nav_item_side"+i).removeClass("nav_a_selected");
			$("#"+sides[i]).hide();
		}
	}
	window.history.pushState(null, side_names[show_side], '?side='+show_side);
	document.title = side_names[show_side];
}
function check_for_href_change(){
	if(window.location.href != last_href){
		switchToSide(parseInt(findGetParameter("side")));
	}
	last_href = window.location.href;
}
function switch_media_search_side(show_side){
	//0 Search Input and List
	//1 Media Details and Media Instances

	var sides = ["media_search_side1","media_search_side2"];
	for(i = 0; i <= sides.length;i++){
		if(show_side == i){
			$("#"+sides[i]).show();
		}else{
			$("#"+sides[i]).hide();
		}
	}
}
function switch_books_side_mobile(show_side){
	switch (show_side){
		case 0:
			$(".book_div_right").hide();
			$(".book_div_left").show();
		break;
		case 1:
			$(".book_div_left").hide();
			$(".book_div_right").show();
		break;
	}
}
function search_button_color_change(state,object){
	if(state == 1){
		$(object).children().first().attr('xlink:href','#search_icon_white');
	}else if(state == 0){
		$(object).children().first().attr('xlink:href','#search_icon');
	}
}
// ================================================
// Date Functions
function getMonth(){
	var jetzt = new Date();
	return jetzt.getMonth()+1;
}
function getHour(){
	var jetzt = new Date();
	return jetzt.getHours();
}
function getMinute(){
	var jetzt = new Date();
	return jetzt.getMinutes();
}
function isChristmas(){
	return (getMonth() == 12);
}
// ================================================
//Networking
function get_data(parameters,callback_sucess,callback_fail){
	get_request("/getData.php",parameters,"xml",callback_sucess,callback_fail);
}
function action(parameters,callback_sucess,callback_fail){
	post_request("/action.php",parameters,"text",callback_sucess,callback_fail);
}
function get_request(url,parameters,type,callback_sucess,callback_fail){
	let jqxhr = $.get(url, parameters, function(data,textStatus,jqXHR){callback_sucess(data,textStatus,jqXHR);});
	jqxhr.fail(function(jqXHR, exception){callback_fail(jqXHR, exception);});
}
function post_request(url,parameters,type,callback_sucess,callback_fail){
	let jqxhr = $.post(url, parameters, function(data,textStatus,jqXHR){callback_sucess(data,textStatus,jqXHR);});
	jqxhr.fail(function(jqXHR, exception){callback_fail(jqXHR, exception);});
}
// ================================================
// Refresh Functions
function auto_refresh(everything){
		if(everything){
			refresh_students_list();
			refreshMediaSearch();
			refreshStudentSearch();
			refresh_lists_overdue_tables();
			refresh_date_inputs();
		}else{
			switch(side){
				case 2:
					refreshMediaSearch();
					break;
				case 3:
					refreshStudentSearch();
					break;
				case 4:
					refresh_lists_overdue_tables();
					break;
			}
		}
}
function refresh_students_list(){
	clear_text("#lend_media_error_message");
	get_data({requested_data : "students_list"},
	function(data , status){
		$xml = $(data);
		$students = $xml.find("student");

		$('#lend_student_select').empty().trigger("change");
		$('#lend_student_select').append(new Option("Schüler auswählen",-1,false,false));

		for(i = 0; i < $students.length; i++){
			$('#lend_student_select').append(new Option($students[i].getAttribute('name')+" "+$students[i].getAttribute('class'),$students[i].getAttribute('id'),false,false));
		}
        },
	function(){
		$("#lend_media_error_message").text("Fehler beim Abrufen der Schülerliste vom Server. Nächster Versuch in 3 Sekunden.");
		setTimeout(function(){refresh_students_list();}, 3000);
	});
}
function refresh_date_input(holiday,date_select_id,error_message_id,auto_retry){
	get_data({"requested_data": "dates_list","holiday": holiday},
	function(data , status){
		$xml = $(data);
		$dates = $xml.find("date");

		$(date_select_id).empty().trigger("change");
		//$(date_select_id).append(new Option("Datum auswählen","",false,false));

		for(i = 0; i < $dates.length; i++){
			$(date_select_id).append(new Option($dates[i].getAttribute('name'),$dates[i].getAttribute('date'),false,false));
		}
	},
	function(){
		if(error_message_id){
			$(error_message_id).text("Fehler beim Abrufen der Datumsliste vom Server. Nächster Versuch in 3 Sekunden.");
		}
		if(auto_retry){
			setTimeout(function(hoilday,date_select_id,error_message_id,auto_retry){refresh_date_input(hoilday,date_select_id,error_message_id,auto_retry);}, 3000);
		}
	});
}
function refresh_date_inputs(){
	refresh_date_input(0,'#date_select','#lend_media_error_message',true);
	refresh_date_input(1,'#date_select_holiday','#lend_media_error_message',true);
}
function refreshBooksTable(){
	clear_text("#lend_media_error_message");

	get_data({"requested_data":"books_of_student","student_id": $("#lend_student_select")[0].value},
	function(data, status) {
		$xml = $(data);
		$books = $xml.find( "book" );

		$("#ausleihen_tabelle").empty();
		add_row_to_table("ausleihen_tabelle",["Name","Barcode","überfällig"],true);
		for(i = 0; i < $books.length; i++){
			let overdue_text = "";
			if($books[i].getAttribute("holiday") == "1"){
				overdue_text = "Ferienausleihe";
			}else{
				if(Date.parse($books[i].getAttribute("loaned_until")) > Date.now()){
					overdue_text = "nein";
				}else{
					overdue_text = "ja";
				}
			}
			add_row_to_table("ausleihen_tabelle",[$books[i].getAttribute("title"),$books[i].getAttribute("barcode"),overdue_text],false);
		}
	},
	function(){
		$("#lend_media_error_message").text("Fehler beim Abrufen der Daten vom Server. Nächster Versuch in 3 Sekunden.");
		setTimeout(function(){refreshBooksTable();}, 3000);
	});
}
function refresh_overdue_table(holiday,table_id){
	get_data({"requested_data" : "overdue_medias","holiday" : holiday},
	function(data, status){
		$xml = $(data);
		$books = $xml.find("book");

		$('#'+table_id).empty();
		add_row_to_table(table_id,["Medium Name","Barcode","Schüler Name","Klasse"],true);

		for(let i = 0; i < $books.length; i++){
			add_row_to_table(table_id,[$books[i].getAttribute("title"),$books[i].getAttribute("barcode"),$books[i].getAttribute("loaned_to_name"),$books[i].getAttribute("class_name")],false);
		}
	},
	function(){
	});
}
function refresh_lists_overdue_tables(){
	refresh_overdue_table(0,"overdue_table");
	refresh_overdue_table(1,"overdue_holiday_table");
}
function refreshStudentSearch(){
	get_data({"requested_data" : "search_student", "search" : $("#student_search_input")[0].value},
	function(data, status){
		$xml = $(data);
		$students = $xml.find( "student" );

		$("#student_search_table").empty();
		add_row_to_table("student_search_table",["id","Name","Geburtstag","Klasse"],true);

		for(let i = 0; i < $students.length; i++){
			add_row_to_table("student_search_table",[$students[i].getAttribute("id"),$students[i].getAttribute("name"),$students[i].getAttribute("birthday"),$students[i].getAttribute("class_name")],false);
		}
	},
	function(){
	});
}
function refreshMediaSearch(){
	get_data({"requested_data" : "search_media", "search" : $("#media_search_input")[0].value},
	function(data, status){
		$xml = $(data);
		$medias = $xml.find( "media" );

		$("#media_search_table").empty();
		add_row_to_table("media_search_table",["id","Titel","Autor","Verlag","Preis","Schuljahr","Typ"],true);

		for(let i = 0; i < $medias.length; i++){
			add_row_to_table("media_search_table",[$medias[i].getAttribute("id"), $medias[i].getAttribute("title"), $medias[i].getAttribute("author"),$medias[i].getAttribute("publisher"),$medias[i].getAttribute("price") + " €",$medias[i].getAttribute("school_year"),$medias[i].getAttribute("type")],false,"switch_media_search_side(1);");
		}
	},
	function(){
	});
}

// ================================================
// Actions
function lend_media_instance(student_id, barcode, until, holiday, callback){
	$("#return_media_error_message").text("");
	var jqxhr = $.post("action.php",{"action" : "lent_media_instance", "student_id" : student_id, "barcode" : barcode,"until" : until, "holiday": holiday},function (data){callback(data)});
	jqxhr.fail(function(response){
		if(response.status == 400){
			$("#lend_media_error_message").text(response.responseText);
			setTimeout(function(){$("#lend_media_error_message").text("")},3000);
		}else{
			$("#lend_media_error_message").text("Fehler beim Senden der Daten zum Server. Nächster Versuch in 3 Sekunden");
			setTimeout(function(){$("#lend_media_error_message").text("")},3000);
		}
	});
}
function return_media_instance(barcode,callback){
	$.get("getData.php",{"requested_data" : "media_instance_infos", "barcode" : $("#media_return_input")[0].value},function(data1){
		xmlDoc = $.parseXML( data1 );
		$xml = $( xmlDoc );
		$media = $xml.find( "media" );
		infos = $media;

		//$.post("action.php",{"action" : "return_media_instance", "barcode" : barcode},function (data,infos){callback(data,infos)});
		action({"action" : "return_media_instance", "barcode" : barcode},
		function(data, status){
			callback(data,status)
		},
		function(){
			$('#return_media_error_message').text("Fehler beim Senden der Daten zum Server. Nächster Versuch in 3 Sekunden");
			setTimeout(function(){return_button_clicked();},3000);
		});
	},"text");
}

// ================================================
// Action Button Handler
function lend_button_clicked(input_box_id, date_select_id, holiday){
	lend_media_instance($('#lend_student_select')[0].value,$('#'+input_box_id)[0].value,$('#'+date_select_id)[0].value,holiday, function(data){
		$('#'+input_box_id)[0].value = '';
		refreshBooksTable();
	});
}
function return_button_clicked(){
		clear_text('#return_media_error_message');
		return_media_instance($('#media_return_input')[0].value,function (data,media0){
			add_row_to_table("return_history_table",[infos[0].getAttribute("title"),$('#media_return_input')[0].value,infos[0].getAttribute("loaned_to_name"),getHour()+":"+getMinute(),"rückgängig"],false,[false,false,false,false,"alert('Feature noch nicht verfügbar')"]);
			$('#media_return_input')[0].value='';
			refreshBooksTable();
		});
}

// ================================================
// Table Functions
function add_row_to_table(table_id,column_array,headline,onclick_array){
	let tr = document.createElement('tr');
	for(let i=0; i < column_array.length; i++){
		if(typeof headline === "boolean"){
			if(headline == true){
				var td = document.createElement('th');
			}else{
				var td = document.createElement('td');
			}
		}else if(Array.isArray(headline)){
			if(headline[i] == true){
				var td = document.createElement('th');
			}else{
				var td = document.createElement('td');
			}
		}
		td.classList.add("td");
		if(onclick_array){
			if(Array.isArray(onclick_array)){
				if(onclick_array[i]){
					$(td).attr("onclick",onclick_array[i]);
					td.classList.add("mouse_change_onhover");
				}
			}
			if(typeof onclick_array === "string"){
				$(td).attr("onclick",onclick_array);
				td.classList.add("mouse_change_onhover");
			}
		}
		td.appendChild(document.createTextNode(column_array[i]));
		tr.appendChild(td);
	}
	$("#"+table_id)[0].appendChild(tr);
}
// ================================================
// Miscellaneous
function clear_input_box(input){
	$(input).val("");
}
function clear_text(object){
	$(object).text("");
}
function escapeHtml(text) {
	var map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;'
	};
	return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
function findGetParameter(parameterName) {
	var result = null,
	tmp = [];
	location.search
		.substr(1)
		.split("&")
		.forEach(function (item) {
			tmp = item.split("=");
			if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
		});
	return result;
}
