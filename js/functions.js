// ================================================
//Events
function onload(){
	site = 0;
	md = new MobileDetect(window.navigator.userAgent);
	configure_particles_js();
	check_if_already_logged_in(function (){
		$( "html" ).removeClass( "loading" );
	});
}
function onlogin(){
	configureSelect2();
	refresh_date_inputs();
	auto_refresh(true);
	setInterval(function(){auto_refresh();}, 750);
}

// ================================================
//Configure Functions
function configureSelect2(){
	$('#lend_student_select').select2({ width: '100%' });
	refresh_students_list();
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
		}
	});
}
function check_if_already_logged_in(callback){
	get_data({requested_data : "logged_in"},
		function(data , status){
			if(data=="true"){
				onlogin();
				login_animation(true);
			}
			callback();
		},function(){
			$(".loading").css("background","#333 url(/assets/error.gif) no-repeat 50% 50%;");
			console.log("error");
		}
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
	switchToSide(1);
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
function switchToSide(show_side){
	//0 Login
	//1 Bücher E/A
	//2 Katalogisierung
	//3 Schüler
	//4 Listen
	//5 Benutzer
	//6 Admin

	last_site = site;
	site = show_side;
	var sides = ["login_div","books_div","katalogisierung_div","schüler_div","listen_div"];
	//$("#"+sides[show_side-1]).animate({ left: '-100%' }, 500,function (){$("#"+sides[show_side-1]).hide();$("#"+sides[show_side]).show();$("#"+sides[show_side]).animate({ left: '0px' }, 500);});
	for(i = 0; i <= sides.length;i++){
		//$("#"+sides[i]).css({ position: 'relative' });
		if(show_side == i){
			//$("#nav_item_side"+i).css("text-decoration","underline");
			//$("#nav_item_side"+i).css("text-decoration-color","orange");
			//$("#nav_item_side"+i).css("border-bottom-color","orange");
			//$("#nav_item_side"+i).css("border-bottom-width", "5px");
			$("#nav_item_side"+i).addClass("nav_a_selected");
			$("#"+sides[i]).show();
			//$("#"+sides[i]).show();
			//$("#"+sides[i]).animate({ left: '0px' });
			//$("#"+sides[i-1]).animate({ left: '-100%' }, 500,function (){$("#"+sides[i-1]).hide();$("#"+sides[i]).show();$("#"+sides[i]).animate({ left: '0px' }, 500);});
		}else{
			//$("#nav_item_side"+i).css("border-bottom-width", "0");
			$("#nav_item_side"+i).removeClass("nav_a_selected");
			//$("#nav_item_side"+i).css("border-bottom-color","transparent");
			//$("#"+sides[i]).hide();
			//$("#"+sides[i]).animate({ left: '-100%' },(function(elem_id){
			//	$(elem_id).hide();
			//})("#"+sides[i]));
			$("#"+sides[i]).hide();
		}
	}
	/*
	$("#"+sides[last_site]).animate({ left: '-100%' },(function(elem_id){
		$(elem_id).hide();
	})("#"+sides[last_site]));

	$("#"+sides[show_side]).show();
	$("#"+sides[show_side]).animate({ left: '0px'});*/
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
	get_request("getData.php",parameters,"xml",callback_sucess,callback_fail);
}
function action(parameters,callback_sucess,callback_fail){
	post_request("action.php",parameters,"text",callback_sucess,callback_fail);
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
			refreshMediaSearch();
			refreshStudentSearch();
			refresh_lists_overdue_tables();
		}else{
			switch(site){
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
	$("#lend_media_error_message").text("");
	var jqxhr = $.get( "getData.php",{"requested_data":"students_list"}, function( data ) {
		$xml = $(data);
		$students = $xml.find("student");

		$('#lend_student_select').empty().trigger("change");
		$('#lend_student_select').append(new Option("Schüler auswählen",-1,false,false));

		for(i = 0; i < $students.length; i++){
			$('#lend_student_select').append(new Option($students[i].getAttribute('name')+" "+$students[i].getAttribute('class'),$students[i].getAttribute('id'),false,false));
		}
        });
	jqxhr.fail(function(){
		$("#lend_media_error_message").text("Fehler beim Abrufen der Schülerliste vom Server. Nächster Versuch in 3 Sekunden.");
		setTimeout(function(){refresh_students_list();}, 3000);
	});
}
function refresh_date_inputs(){
	$.get( "getData.php",{"requested_data": "dates_list","holiday": "0"}, function( data ) {
		$xml = $(data);
		$dates = $xml.find("date");

		$('#date_select').empty().trigger("change");
		$('#date_select').append(new Option("Datum auswählen","",false,false));

		for(i = 0; i < $dates.length; i++){
			$('#date_select').append(new Option($dates[i].getAttribute('name'),$dates[i].getAttribute('date'),false,false));
		}
	});
	$.get( "getData.php",{"requested_data": "dates_list","holiday": "1"}, function( data ) {
		$xml = $(data);
		$dates = $xml.find("date");

		$('#date_select_holiday').empty().trigger("change");
		$('#date_select_holiday').append(new Option("Datum auswählen","",false,false));

		for(i = 0; i < $dates.length; i++){
			$('#date_select_holiday').append(new Option($dates[i].getAttribute('name'),$dates[i].getAttribute('date'),false,false));
		}
	});
}
function refreshBooksTable(){
	clear_text("#lend_media_error_message");
	var jqxhr = $.get( "getData.php",{"requested_data":"books_of_student","student_id": $("#lend_student_select")[0].value}, function(data) {
		xmlDoc = $.parseXML( data );
		$xml = $( xmlDoc );
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
	},"text");
	jqxhr.fail(function(){
		$("#lend_media_error_message").text("Fehler beim Abrufen der Daten vom Server. Nächster Versuch in 3 Sekunden.");
		setTimeout(function(){refreshBooksTable();$("#lend_media_error_message").text("");}, 3000);
	});
}
function refresh_lists_overdue_tables(){
	$.get("getData.php",{"requested_data" : "overdue_medias"},function(data){
		xmlDoc = $.parseXML( data );
		$xml = $( xmlDoc );
		$books = $xml.find( "book" );

		$("#overdue_table").empty();
		add_row_to_table("overdue_table",["Medium Name","Barcode","Schüler Name","Klasse"],true);

		for(let i = 0; i < $books.length; i++){
			add_row_to_table("overdue_table",[$books[i].getAttribute("title"),$books[i].getAttribute("barcode"),$books[i].getAttribute("loaned_to_name"),$books[i].getAttribute("class_name")],false);
		}
	},"text");
	$.get("getData.php",{"requested_data" : "overdue_holiday_medias"},function(data){
		xmlDoc = $.parseXML( data );
		$xml = $( xmlDoc );
		$books = $xml.find( "book" );

		$("#overdue_holiday_table").empty();
		add_row_to_table("overdue_holiday_table",["Medium Name","Barcode","Schüler Name","Klasse"],true);

		for(let i = 0; i < $books.length; i++){
			add_row_to_table("overdue_holiday_table",[$books[i].getAttribute("title"),$books[i].getAttribute("barcode"),$books[i].getAttribute("loaned_to_name"),$books[i].getAttribute("class_name")],false);
		}
	},"text");
}
function refreshStudentSearch(){
	$.get("getData.php",{"requested_data" : "search_student", "search" : $("#student_search_input")[0].value},function(data){
		xmlDoc = $.parseXML( data );
		$xml = $( xmlDoc );
		$students = $xml.find( "student" );

		$("#student_search_table").empty();
		add_row_to_table("student_search_table",["id","Name","Geburtstag","Klasse"],true);

		for(let i = 0; i < $students.length; i++){
			add_row_to_table("student_search_table",[$students[i].getAttribute("id"),$students[i].getAttribute("name"),$students[i].getAttribute("birthday"),$students[i].getAttribute("class_name")],false);
		}
	},"text");
}
function refreshMediaSearch(){
	$.get("getData.php",{"requested_data" : "search_media", "search" : $("#media_search_input")[0].value},function(data){
		xmlDoc = $.parseXML( data );
		$xml = $( xmlDoc );
		$medias = $xml.find( "media" );

		$("#media_search_table").empty();
		add_row_to_table("media_search_table",["id","Titel","Autor","Verlag","Preis","Schuljahr","Typ"],true);

		for(let i = 0; i < $medias.length; i++){
			add_row_to_table("media_search_table",[$medias[i].getAttribute("id"), $medias[i].getAttribute("title"), $medias[i].getAttribute("author"),$medias[i].getAttribute("publisher"),$medias[i].getAttribute("price") + " €",$medias[i].getAttribute("school_year"),$medias[i].getAttribute("type")],false,"switch_media_search_side(1);");
		}
	},"text");
}

// ================================================
// Actions
function lend_media_instance(student_id, barcode, holiday, callback){
	$("#return_media_error_message").text("");
	var jqxhr = $.post("action.php",{"action" : "lent_media_instance", "student_id" : student_id, "barcode" : barcode,"holiday": holiday},function (data){callback(data)});
	jqxhr.fail(function(response){
		if(response.status == 400){
			$("#lend_media_error_message").text(response.responseText);
			setTimeout(function(){$("#lend_media_error_message").text("")},3000);
		}else{
			$("#lend_media_error_message").text("Fehler beim Senden der Daten zum Server.");
			setTimeout(function(){$("#lend_media_error_message").text("")},3000);
		}
	});
}
function return_media_instance(barcode, callback){
	$.get("getData.php",{"requested_data" : "media_instance_infos", "barcode" : $("#media_return_input")[0].value},function(data1){
		xmlDoc = $.parseXML( data1 );
		$xml = $( xmlDoc );
		$media = $xml.find( "media" );
		infos = $media;

		$.post("action.php",{"action" : "return_media_instance", "barcode" : barcode},function (data,infos){callback(data,infos)});
	},"text");
}

// ================================================
// Action Button Handler
function lend_button_clicked(input_box_id, holiday){
	lend_media_instance($('#lend_student_select')[0].value,$('#'+input_box_id)[0].value,holiday, function(data){
		$('#'+input_box_id)[0].value = '';
		refreshBooksTable();
	});
}
function return_button_clicked(){
		return_media_instance($('#media_return_input')[0].value,function (data,media0){
			add_row_to_table("return_history_table",[infos[0].getAttribute("title"),$('#media_return_input')[0].value,infos[0].getAttribute("loaned_to_name"),getHour()+":"+getMinute(),"rückgängig"],false);
			$('#media_return_input')[0].value='';
			refreshBooksTable();
		});
}

// ================================================
// Table Functions
function add_row_to_table(table_id,column_array,headline,onclick_array){
	let tr = document.createElement('tr');
	for(let i=0; i < column_array.length; i++){
		if(headline){
			var td = document.createElement('th');
		}else{
			var td = document.createElement('td');
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
