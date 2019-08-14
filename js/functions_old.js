// ================================================
//Events
function init(){
	$("#nav_div").hide();

	switch_options_side(0);
	
	design_parameters = ["text_color","button_text_color","button_text_color_hover","navbar_text_color","navbar_color","background_color","icon_color","navbar_icon_color","gradient_color1","gradient_color2","browser_theme_color"];
    
    detect_mobile()
	.then(apply_default_design)
	.then(configure_design_pickers)
	.then(configure_lang)
	.then(configure_pause_on_idle)
	.then(configure_particles_js)
	.then(configureSelect2)
	.then(configure_button_handler)
	.then(check_if_already_logged_in)
	.then(function(){
	    return new Promise(function(resolve,reject){
	        $("html").removeClass("loading");
    	    $("body").animate({"opacity":"1"},400);
    	    resolve();
	    });
	})
	.catch(function(error){
	    console.log(error);
	    $("html").addClass("loading_error");
	});
}
function onlogin(){
	logged_in = true;
	$("html").addClass("logged_in");

	if(findGetParameter("side") && findGetParameter("side") != 0){
		switch_side_to(parseInt(findGetParameter("side")));
	}else{
		switch_side_to(1);
	}

	auto_refresh_interval = setInterval(function(){auto_refresh();}, 750);
	still_logged_in_interval = setInterval(function(){check_if_still_logged_in();}, 10000);

	check_permissions();
	auto_refresh(true);
}
function onlogout(){
	logged_in = false;
	$("html").removeClass("logged_in");

	clearTimeout(still_logged_in_interval);
}


// ================================================
//Design Functions
function configure_design_pickers(){
    return new Promise(function(resolve,reject){
        let pickers = $(".design_picker");
        
        pickers.bind("change",function(){design_edit_input_changed('#'+$(this).val(),this.id);});
        for(i = 0; i < pickers.length; i++){
            pickers[i].jscolor.onFineChange = function(){design_edit_input_changed(this.toHEXString(), this.valueElement.id);};
        }
        resolve();
    });
}
function set_design_pickers_to_selected_preset(){
    if($("#admin_design_select").val()){
        get_data_request({"requested_data": "design","id": $("#admin_design_select").val()},true,true,5)
    	.then(function(data,status){
    		$xml = $(data);
            for(let name in design_parameters){
                $("#"+design_parameters[name]+"_picker")[0].jscolor.fromString($xml.find(design_parameters[name])[0].getAttribute("value"));
                $("#"+design_parameters[name]+"_picker").trigger("change");
            }
    	});
    }
}

function refresh_admin_designs_list(){
    let active_design_id;
    get_data_request({"requested_data": "active_design_id"})
    .then(function(data, status){
        active_design_id = $(data).find("id")[0].getAttribute("value");
    })
    .then(function(){return get_data_request({"requested_data": "designs_list"},true,false,5)})
    .then(function(data, status){
       let designs = data.find("design");
        $("#admin_design_select").empty().trigger("change");
        
        for(let i = 0; i < designs.length; i++){
            $("#admin_design_select").append(new Option(designs[i].getAttribute("name"),designs[i].getAttribute("id"),false,designs[i].getAttribute("id") == active_design_id));
        }
        $("#admin_design_select").trigger("change");
    });
}
function design_edit_input_changed(value,input_id){
    let key = input_id.replace("_picker","");
    
    let design_dict = {};
    design_dict[key] = value;
    
    set_design(design_dict);
}

// ================================================
//Configure Functions
function configure_lang(){
    return new Promise(function(resolve,reject){
        let lang_id = 1;
        if(get_cookie("lang")){
		    lang_id = get_cookie("lang");
    	}
	    vm.switch_language(lang_id)
	    .then(function(){
	        resolve();
	    });
    });
}
function configure_button_handler(){
    return new Promise(function(resolve,reject){
       	$("#media_return_button")[0].addEventListener("click",return_button_clicked);
	    $("#media_return_input")[0].addEventListener("keydown", function(event){if (event.keyCode == 13){return_button_clicked();}});
	    resolve();
    });
}
function configureSelect2(){
    return new Promise(function(resolve,reject){
        $('#lend_customer_select').select2({width: '100%'});
        resolve();
    });
}
function configure_particles_js(){
    return new Promise(function(resolve,reject){
        let number, opcaity, color, size, size_random, move_speed, move_direction, out_mode, onpush_particles_nb, onclick_mode, line_linked, line_linked_distance, line_linked_opacity, line_linked_color;
        
    	if(isChristmas()){
            number = 120;
            opacity = 0.55;
            color = "#ffffff";
            size = 5;
            size_random = true;
            move_speed = 6;
            move_direction = "bottom";
            out_mode = "out";
            onpush_particles_nb = 7;
            onclick_mode = "push";
            line_linked = false;
            line_linked_distance = 0;
            line_linked_opacity = 0;
            line_linked_color = "#ffffff";
    	}else{
    	    number = 80;
            opacity = 0.55;
            color = "#ffffff";
            size = 3;
            size_random = true;
            move_speed = 6;
            move_direction = "none";
            out_mode = "out";
            onpush_particles_nb = 4;
            onclick_mode = "push";
            line_linked = true;
            line_linked_distance = 150;
            line_linked_opacity = 0.4;
            line_linked_color = "#ffffff";
    	}
    	
        particlesJS("particles-js", 
        {"particles":{
            "number":{"value": number,
                "density":{"enable":true,"value_area":800}
                
            },
            "color":{"value": color},
            "shape":{"type":"circle"},
            "opacity":{"value": opacity},
            "size":{"value": size,"random": size_random},
            "line_linked":{"enable": line_linked,"distance": line_linked_distance,"color": line_linked_color,"opacity": line_linked_opacity,"width":1},
            "move":{"enable":true,"speed": move_speed,"direction": move_direction,"out_mode": out_mode}
        },
        "interactivity":{
            "detect_on":"canvas",
            "events":{
                "onhover":{"enable":false},
                "onclick":{"enable":true,"mode": onclick_mode},
                "resize":true
            },
            "modes":{
                "push":{"particles_nb": onpush_particles_nb}
            }
        },
        "retina_detect":true});
        
        resolve();
    });
}

// ================================================
//Login Management
function logout(){
	action_request({action: "logout"},false,true)
	.then(function(data){
		switch_side_to(0);
		logout_animation();
		onlogout();
	})
	.catch(function(){});
}

function check_if_still_logged_in(){
	get_data_request({"requested_data" : "logged_in"},false,false)
	.then(function(data , status){
			if(data !="true"){
				switch_side_to(0);
				logout_animation();
				onlogout();
			}
		})
		.catch(function(){});
}
function check_permissions(){
	get_data_request({"requested_data" : "permission_list"},true,true,-1)
	.then(function(data){
		$xml = $(data);

    let admin = $xml.find("admin").attr("value");
		let lend_media_instance = $xml.find("lend_media_instance").attr("value");
		let return_media_instance = $xml.find("return_media_instance").attr("value");

		if(lend_media_instance == 0){
			$("#media_lend_button").prop('disabled', true);
			$("#media_lend_button_holiday").prop('disabled', true);
		}
		if(return_media_instance == 0){
			$("#media_return_button").prop('disabled', true);
		}
		if(admin ==  0){
			$("#nav_li_side5").hide();
			$("#mobile_sidenav_a_side5").hide();
		}
	})
	.catch(function(){});
}

// ================================================
//Animations
function login_animation(fast){
	if(fast){
		speed = 0;
	}else{
		speed = "normal";
	}
	
 	$("#main").animate({height: "100%",width: "100%"},speed,function(){
 	    $("#particles-js").hide();
 	});
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
function switch_side_to(show_side){
	//0 Login
	//1 Books I/O
	//2 Catalog
	//3 Customers
	//4 Lists
	//5 Admin

	vm.side = ""+show_side;
	var side_names = [vm.lang["login"],vm.lang["book_return_and_lend_side_title"],vm.lang["catalog_side_title"],vm.lang["customers_side_title"],vm.lang["lists_side_title"],vm.lang["admin_side_title"]];
	//var sides = ["login_side","media_side","catalog_side","customers_side","lists_side","admin_side"];

	/*for(i = 0; i <= sides.length;i++){
		if(show_side == i){
			$("#nav_a_side"+i).addClass("nav_a_selected");
			$("#mobile_sidenav_a_side"+i).addClass("selected");
			$("#"+sides[i]).show();
		}else{
			$("#nav_a_side"+i).removeClass("nav_a_selected");
			$("#mobile_sidenav_a_side"+i).removeClass("selected");
			$("#"+sides[i]).hide();
		}
	}*/
	close_mobile_sidenav();
	//window.history.pushState(null, side_names[show_side], '?side='+show_side);*/
	document.title = side_names[show_side];
}
function switch_media_search_side(show_side, media_id){
	//0 Search Input and List
	//1 Media Details and Media Instances

	vm.media_search_side = show_side;

	var sides = ["media_search_side1","media_search_side2"];
	if(show_side == 1){
		refresh_media_editing_details(media_id);
		current_media_id = media_id;
	}else if(show_side == 0){
		current_media_id = -1;
	}

	for(i = 0; i <= sides.length;i++){
		if(show_side == i){
			$("#"+sides[i]).show();
		}else{
			$("#"+sides[i]).hide();
		}
	}
}
function switch_customer_search_side(show_side, customer_id){
	//0 Search Input and List
	//1 customer Details

	vm.customer_search_side = show_side;

	var sides = ["customer_search_side1","customer_search_side2"];
	if(show_side == 1){
		refresh_customer_editing_details(customer_id);
		current_customer_id = customer_id;
	}else if(show_side == 0){
		current_customer_id = -1;
	}

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
			$("#switch_books_side_mobile_button0").css("font-weight","bold");
			$("#switch_books_side_mobile_button1").css("font-weight","normal");
		break;
		case 1:
			$(".book_div_left").hide();
			$(".book_div_right").show();
			$("#switch_books_side_mobile_button1").css("font-weight","bold");
			$("#switch_books_side_mobile_button0").css("font-weight","normal");
		break;
	}
}
function switch_options_side(show_side){
	//0 Klassen
	//1 Jahrgangsstufen
	//2 Einstallungen

	var sides = ["options_classes","options_school_years","options_types","options_design","options_settings","options_metrics"]

	for(i = 0; i <= sides.length;i++){
		if(show_side == i){
			$("#options_navigation_side"+i).addClass("admin_navigation_item_selected");
			$("#"+sides[i]).show();
		}else{
			$("#options_navigation_side"+i).removeClass("admin_navigation_item_selected");
			$("#"+sides[i]).hide();
		}
	}
}
function open_settings_menu(){
	Swal.fire({
		position: 'top-end',
		imageUrl: "",
		imageWidth: 0,
		imageHeight: 0,
		title: lang("settings"),
		html: "<p>baum</p>",
		customClass: "settings_menu",
		customContainerClass: "settings_menu_container",
		buttonsStyling: false,
		confirmButtonClass: 'button',
		background: "var(--navbar_color)",
	});
	$(".settings_menu").css("margin-top",$("#navbar_settings_img").height());
}
function open_mobile_sidenav(){
	document.getElementById("mobile_sidenav").style.width = "100%";
}
function close_mobile_sidenav(){
	document.getElementById("mobile_sidenav").style.width = "0";
}
function text_to_input(text_element_id){
	var input = document.createElement('input');
	$(input).attr("type","text");
	$(input).attr("id",text_element_id);
	$(input).attr("onkeypress","if (event.keyCode==13){input_to_text(this.id);}");

	input.classList.add("input_gray");
	input.classList.add("input_focus_color");

	input.value = $("#"+text_element_id).text();

	$("#"+text_element_id).parent()[0].appendChild(input);
	$("#"+text_element_id).remove();
}
function th_to_input(th_element_id){
	$("#"+th_element_id).attr("onclick","");
	var input = document.createElement('input');
	$(input).attr("type","text");
	//$(input).attr("id",th_element_id);
	$(input).attr("onkeypress","if (event.keyCode==13){input_to_th($(this).parent()[0].id);}");

	input.classList.add("input_gray");
	input.classList.add("input_focus_color");

	input.value = $("#"+th_element_id).text();

	$("#"+th_element_id).text("");
	$("#"+th_element_id)[0].appendChild(input);

	$("#"+th_element_id).children()[0].focus();
}
function input_to_text(input_element_id){
	var text = document.createElement('p');
	$(text).attr("id",input_element_id);
	$(text).attr("onclick","text_to_input(this.id);");

	$(text).text($("#"+input_element_id).val());

	$("#"+input_element_id).parent()[0].appendChild(text);
	$("#"+input_element_id).remove();
}
function input_to_th(th_element_id){
	$("#"+th_element_id).text($("#"+th_element_id).children()[0].value);
	$("#"+th_element_id).attr("onclick","th_to_input(this.id);");

	editing_input_changed(th_element_id);
}
/*window.onpopstate = function(event) {
	if(logged_in){
		switch_side_to(parseInt(findGetParameter("side")));
	}
}*/
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
// Refresh Functions
function configure_pause_on_idle(){
    return new Promise(function(resolve,reject){
        idle_time_minutes = 0;
    	setInterval(function(){idle_time_minutes++}, 60000);
    
    	function reset_idle_time(){
    		idle_time_minutes = 0;
    	}
    
    	window.onmousemove = reset_idle_time;
    	window.onmousedown = reset_idle_time;
    	window.ontouchstart = reset_idle_time;
    	window.onclick = reset_idle_time;
    	window.onkeypress = reset_idle_time;
    	
    	resolve();
    });
}
function auto_refresh(everything){
		if(everything){
			refresh_customers_list();
			refresh_media_search();
			refresh_customer_search();
			refresh_lists_overdue_tables();
			refresh_date_inputs();
			refresh_subjects_list("catalog_subject_select");
			refresh_school_years_list("catalog_school_year_select");
			refresh_classes_list("customers_class_select");
			refresh_admin_designs_list();
			if(current_customer_id != -1){
				refresh_customer_editing_details(current_customer_id);
			}
			if(current_media_id != -1){
				refresh_media_editing_details(current_media_id);
			}
		}else{
			if(document.hidden == false && idle_time_minutes < 2){
				switch(side){
					case 2:
						if(vm.media_search_side == 0){
							refresh_media_search();
						}
						break;
					case 3:
						if(vm.customer_search_side == 0){
							refresh_customer_search();
						}
						break;
					case 4:
						refresh_lists_overdue_tables();
						break;
				}
			}
		}
}
function refresh_customer_editing_details(customer_id){
	refresh_customer_editing_customer_details(customer_id);
	refresh_customer_editing_instances(customer_id);
}
function refresh_media_editing_details(media_id){
	refresh_media_editing_media_details(media_id);
	refresh_media_editing_instances(media_id);
}
function refresh_media_editing_media_details(media_id){
	get_data_request({"requested_data" : "media_details", "media_id" : media_id},true,true,3)
	.then(function(data){
		data = $(data);
		//$infos = $xml.find("media")[0];

		$("#catalog_medium_title").text(data.find("title")[0].getAttribute("value"));

		$('#catalog_medium_subject_select').empty();
		refresh_school_years_list("catalog_medium_school_year_select",data.find("school_year_id")[0].getAttribute("value"));

		$("#catalog_medium_author").text(data.find("author")[0].getAttribute("value"));
		$("#catalog_medium_publisher").text(data.find("publisher")[0].getAttribute("value"));
		$("#catalog_medium_price").text(data.find("price")[0].getAttribute("value"));
		$("#catalog_medium_miscellaneous").text(data.find("miscellaneous")[0].getAttribute("value"));
		refresh_types_list("catalog_medium_type_select",data.find("type_id")[0].getAttribute("value"));
		refresh_subjects_list("catalog_medium_subject_select",data.find("subject_id")[0].getAttribute("value"))
		//return get_data_request({"requested_data" : "subjects_list"},true,false,2);
	})
	/*.then(function(data2){
		console.log(data);
		console.log(data2);

		$subjects = $(data2).find("subject");

		for(i = 0; i < $subjects.length; i++){
			$('#catalog_medium_subject_select').append(new Option($subjects[i].getAttribute("name"),$subjects[i].getAttribute("id"),false,$subjects[i].getAttribute("id") == data.find("subject_id")[0].getAttribute("value")));
		}
	})*/
	.catch(function(){});
}
function refresh_types_list(select_id,selected_type_id){
	get_data_request({"requested_data" : "types_list"},false,true)
	.then(function(data){
		$xml = $(data);
		$types = $xml.find("type");

		$('#'+select_id).empty();

		if(!selected_type_id){
			$('#'+select_id).append(new Option(lang("select_type"),-1,false,false));
		}

		for(i = 0; i < $types.length; i++){
			$('#'+select_id).append(new Option($types[i].getAttribute("name"),$types[i].getAttribute("id"),false,$types[i].getAttribute("id") == selected_type_id));
		 }
	})
	.catch(function(){});
}
function refresh_media_editing_instances(media_id1){
	$("#media_details_instances_table").empty();
	add_row_to_table("media_details_instances_table",["<input type='checkbox' id='media_search_instance_checkbox_all' onchange='select_all_instances_checkbox_clicked(this);'></input>",lang("barcode"),lang("loaned_to"),lang("loaned_until"),lang("holiday_lend")],true,false,[true]);

	get_data_request({requested_data : "media_instances", media_id : media_id1},false,true)
	.then(function(data){
		$xml = $(data);
		$instances = $xml.find("media_instance");

		for(i = 0; i < $instances.length; i++){
			let loaned_to = $instances[i].getAttribute("loaned_to");
			let loaned_to_name = $instances[i].getAttribute("loaned_to_name");
			let barcode = $instances[i].getAttribute("barcode");
			let loaned_until = $instances[i].getAttribute("loaned_until");
			let holiday = $instances[i].getAttribute("holiday");

			let switch_to_customer = false;
			let change_date = false;
			let change_holiday = false;
			if(loaned_to != ""){
				switch_to_customer = function(){switch_customer_search_side(1,loaned_to); switch_side_to(3);};
				change_date = function(){change_media_instance_loaned_until(barcode,loaned_until);};
				change_holiday = function(){change_media_instance_holiday(barcode,holiday);};
			}

			let holiday_lang = "";
			if(holiday == "1"){
				holiday_lang = lang("yes");
			}else if(holiday == "0"){
				holiday_lang = lang("no");
			}
			add_row_to_table("media_details_instances_table",["<input type='checkbox' class='media_search_instance_checkbox' instance_barcode='"+barcode+"'></input>",barcode,loaned_to_name,loaned_until,holiday_lang],false,[false,false,switch_to_customer,change_date,change_holiday],[true]);
		}
	})
	.catch(function(){});
}
function refresh_customer_editing_customer_details(customer_id){
	get_data_request({"requested_data" : "customer_details", "customer_id" : customer_id},false,true)
	.then(function(data){
		$("#customer_editing_name").text(data.find("name")[0].getAttribute("value"));
		$("#customer_editing_miscellaneous").text(data.find("miscellaneous")[0].getAttribute("value"));
		refresh_classes_list("customer_editing_class_select",data.find("class_id")[0].getAttribute("value"));
	})
	.catch(function(){});
}
function select_all_instances_checkbox_clicked(checkbox) {
	$(".media_search_instance_checkbox").prop("checked",checkbox.checked);
}
function select_all_customer_instances_checkbox_clicked(checkbox) {
	$(".customer_search_instance_checkbox").prop("checked",checkbox.checked);
}
function refresh_customers_list(){
    return new Promise(function(resolve,reject){
    	get_data_request({requested_data : "customers_list"},true,true,5)
    	.then(function(data){
    		customers = data.find("customer");
    		$('#lend_customer_select').empty().trigger("change");
    		$('#lend_customer_select').append(new Option(lang("select_customer"),-1,false,false));
            
    		for(let i = 0; i < customers.length; i++){
    			$('#lend_customer_select').append(new Option(customers[i].getAttribute('name')+" "+customers[i].getAttribute('class'),customers[i].getAttribute('id'),false,false));
    		}
    		resolve();
        })
    	.catch(function(error){reject(error);});
    });
}
function refresh_classes_list(select_id,selected_class_id){
	get_data_request({requested_data : "classes_list"},true,false,5)
	.then(function(data , status){
		$xml = $(data);
		$classes = $xml.find("class");

		$('#'+select_id).empty();
		if(!selected_class_id){
			$('#'+select_id).append(new Option(lang("select_class"),-1,false,false));
		}

		for(i = 0; i < $classes.length; i++){
			$('#'+select_id).append(new Option($classes[i].getAttribute('name'),$classes[i].getAttribute('id'),false,$classes[i].getAttribute('id') == selected_class_id));
		}
	})
	.catch(function(){});
}
function refresh_subjects_list(select_id,selected_subject_id){
	get_data_request({requested_data : "subjects_list"},true,true,5)
	.then(function(data){
		$xml = $(data);
		$subjects = $xml.find("subject");

		$('#'+select_id).empty();
		if(!selected_subject_id){
			$('#'+select_id).append(new Option(lang("select_subject"),-1,false,false));
		}

		for(i = 0; i < $subjects.length; i++){
			$('#'+select_id).append(new Option($subjects[i].getAttribute('name'),$subjects[i].getAttribute('id'),false,$subjects[i].getAttribute('id') == selected_subject_id));
		}
	})
	.catch(function(){});
}
function refresh_school_years_list(select_id,selected_school_year_id){
	get_data_request({requested_data : "school_years_list"},true,true,5)
	.then(function(data , status){
		$xml = $(data);
		$school_years = $xml.find("school_year");

		$('#'+select_id).empty().trigger("change");
		if(!selected_school_year_id){
			$('#'+select_id).append(new Option(lang("select_school_year"),-1,false,false));
		}

		for(i = 0; i < $school_years.length; i++){
			$('#'+select_id).append(new Option($school_years[i].getAttribute('name'),$school_years[i].getAttribute('id'),false,selected_school_year_id == $school_years[i].getAttribute('id')));
		}
	})
	.catch(function(){});
}
function refresh_date_input(holiday,date_select_id,error_message_id,auto_retry){
	get_data_request({"requested_data": "dates_list","holiday": holiday},auto_retry,true,5)
	.then(function(data , status){
		$xml = $(data);
		$dates = $xml.find("date");

		$(date_select_id).empty().trigger("change");
		//$(date_select_id).append(new Option("Datum auswählen","",false,false));

		for(i = 0; i < $dates.length; i++){
			$(date_select_id).append(new Option($dates[i].getAttribute('name'),$dates[i].getAttribute('date'),false,false));
		}
	})
	.catch(function(){});
}
function refresh_date_inputs(){
	refresh_date_input(0,'#date_select','#lend_media_error_message',true);
	refresh_date_input(1,'#date_select_holiday','#lend_media_error_message',true);
}
function refresh_medias_table(){
	get_data_request({"requested_data":"medias_of_customer","customer_id": $("#lend_customer_select")[0].value},true,true,4)
	.then(function(data, status) {
		let customer_id = $("#lend_customer_select")[0].value;
		$xml = $(data);
		$books = $xml.find("media");

		$("#side1_media_instances_table").empty();
		add_row_to_table("side1_media_instances_table",[lang("name"),lang("barcode"),lang("overdue"),lang("holiday_lend")],true);
		for(i = 0; i < $books.length; i++){
			let overdue_text = "";
			if($books[i].getAttribute("holiday") == "1"){
				ferienausleihe = "ja";
			}else{
				ferienausleihe = "nein";
			}
			if(Date.parse($books[i].getAttribute("loaned_until")) > Date.now()){
				overdue_text = "nein";
			}else{
				overdue_text = "ja";
			}
			add_row_to_table("side1_media_instances_table",[$books[i].getAttribute("title"),$books[i].getAttribute("barcode"),overdue_text,ferienausleihe],false,["switch_media_search_side(1,"+$books[i].getAttribute('media_id')+"); switch_side_to(2);"]);
		}
		$("#edit_customer_button").unbind();
		if(customer_id != "-1" && customer_id != -1){
			$("#edit_customer_button").bind("click",function(){
				switch_side_to(3);
				switch_customer_search_side(1,customer_id);
			});
		}
	})
	.catch(function(){});
}
function refresh_customer_editing_instances(customer_id){
	get_data_request({"requested_data":"medias_of_customer","customer_id": customer_id},true,true,4)
	.then(function(data, status) {
		$xml = $(data);
		$books = $xml.find("media");

		$("#customer_search_medias").empty();
		add_row_to_table("customer_search_medias",["<input type='checkbox' onchange='select_all_customer_instances_checkbox_clicked(this);'></input>",lang("name"),lang("barcode"),lang("loaned_until"),lang("overdue"),lang("holiday_lend")],true,false,[true]);
		for(i = 0; i < $books.length; i++){
			let id = $books[i].getAttribute("media_id");
			let barcode = $books[i].getAttribute("barcode");
			let title = $books[i].getAttribute("title");
			let loaned_until = $books[i].getAttribute("loaned_until");
			let holiday = $books[i].getAttribute("holiday");

			let overdue_text = "";
			let ferienausleihe = "";
			if($books[i].getAttribute("holiday") == "1"){
				holiday_text = lang("yes");
			}else{
				holiday_text = lang("no");
			}
			if(Date.parse($books[i].getAttribute("loaned_until")) > Date.now()){
				overdue_text = lang("no");
			}else{
				overdue_text = lang("yes");
			}

			let switch_to_media = function(){switch_media_search_side(1,id); switch_side_to(2);}
			let change_date = function(){change_media_instance_loaned_until(barcode,loaned_until);};
			let change_holiday = function(){change_media_instance_holiday(barcode,holiday);};

			add_row_to_table("customer_search_medias",["<input type='checkbox' class='customer_search_instance_checkbox' barcode='"+barcode+"'></input>",title,barcode,loaned_until,overdue_text,holiday_text],false,[false,switch_to_media,false,change_date,false,change_holiday],[true]);
		}
	})
	.catch(function(){});
}
function refresh_overdue_table(holiday,table_id){
	get_data_request({"requested_data" : "overdue_medias","holiday" : holiday},false,false)
	.then(function(data, status){
		$xml = $(data);
		$books = $xml.find("book");

		$('#'+table_id).empty();
		add_row_to_table(table_id,[lang("title"),lang("barcode"),lang("customer_name"),lang("class")],true);

		for(let i = 0; i < $books.length; i++){
			add_row_to_table(table_id,[$books[i].getAttribute("title"),$books[i].getAttribute("barcode"),$books[i].getAttribute("loaned_to_name"),$books[i].getAttribute("class_name")],false);
		}
	})
	.catch(function(){});
}
function refresh_lists_overdue_tables(){
	refresh_overdue_table(0,"overdue_table");
	refresh_overdue_table(1,"overdue_holiday_table");
}
function refresh_customer_search(){
	get_data_request({"requested_data" : "search_customer", "order_by" : customer_search_order_by, "search" : $("#customer_search_input")[0].value, "class_id" : $("#customers_class_select")[0].value},false,true)
	.then(function(data, status){
		$xml = $(data);
		$customers = $xml.find( "customer" );

		$("#customer_search_table").empty();
		add_row_to_table("customer_search_table",[lang("identifier"),lang("name")/*,"Geburtstag"*/,lang("class")],true,["customer_search_order('id');","customer_search_order('name');"]);

		function add_row(i){
			add_row_to_table("customer_search_table",[$customers[i].getAttribute("id"),$customers[i].getAttribute("name")/*,$customers[i].getAttribute("birthday")*/,$customers[i].getAttribute("class_name")],false,"switch_customer_search_side(1,"+$customers[i].getAttribute('id')+");");
		}

		if(customer_search_reverse == true){
			for(let i = $customers.length-1; i >= 0; i--){
				add_row(i);
			}
		}else{
			for(let i = 0; i < $customers.length; i++){
				add_row(i);
			}
		}
	})
	.catch(function(){});
}
function customer_search_order(order_by){
	if(customer_search_order_by == order_by){
		customer_search_reverse = !customer_search_reverse;
	}else{
		customer_search_order_by = order_by;
		customer_search_reverse = false;
	 }
}
function media_search_order(order_by){
	if(media_search_order_by == order_by){
		media_search_reverse = !media_search_reverse;
	}else{
		media_search_order_by = order_by;
		media_search_reverse = false;
	}
}
function refresh_media_search(){
	if(vm.media_search_side == 0){
		get_data_request({"requested_data" : "search_media", "order_by": media_search_order_by, "search" : $("#media_search_input")[0].value,"subject_id" : $("#catalog_subject_select")[0].value,"school_year_id" : $("#catalog_school_year_select")[0].value},false,true)
		.then(function(data, status){
			$xml = $(data);
			$medias = $xml.find( "media" );

			$("#media_search_table").empty();
			add_row_to_table("media_search_table",[lang("identifier"),lang("title"),lang("author"),lang("publisher"),lang("price"),lang("school_year"),lang("type")],true,["media_search_order('id');","media_search_order('title');","media_search_order('author');","media_search_order('publisher');","media_search_order('price');"]);

			function add_row(i){
				add_row_to_table("media_search_table",[$medias[i].getAttribute("id"), $medias[i].getAttribute("title"), $medias[i].getAttribute("author"),$medias[i].getAttribute("publisher"),$medias[i].getAttribute("price") + " €",$medias[i].getAttribute("school_year"),$medias[i].getAttribute("type")],false,"switch_media_search_side(1,"+$medias[i].getAttribute("id")+");");
			}

			if(media_search_reverse == true){
				for(let i = $medias.length-1; i >= 0; i--){
					add_row(i);
				}
			}else{
				for(let i = 0; i < $medias.length; i++){
					add_row(i);
				}
			}
		})
		.catch(function(){});
	}
}
function isbn_lookup(isbn){
	get_request('https://openlibrary.org/api/books',{"bibkeys": "ISBN:"+isbn,"format" : "json"},"text",
	function(data , status){
		console.log(data);
	},function(){}
	);
}

// ================================================
// Actions
function editing_input_changed(input_id){
	switch(input_id){
		case "customer_editing_name":
			action_request({"action": "modify_customer", "customer_id": current_customer_id, "new_name": $("#"+input_id).text()},false,true)
			.then(function(){
				refresh_customers_list();
			});
		break;
		case "customer_editing_class_select":
			action_request({"action": "modify_customer", "customer_id": current_customer_id, "new_class_id": $("#"+input_id).val()},false,true)
			.then(function(){
				refresh_customers_list();
			});
		break;
		case "customer_editing_miscellaneous":
		action_request({"action": "modify_customer", "customer_id": current_customer_id, "new_miscellaneous": $("#"+input_id).text()},false,true)
		break;
		case "catalog_medium_title":
			action_request({"action": "modify_media", "media_id": current_media_id, "new_title": $("#"+input_id).text()},false,true);
		break;
		case "catalog_medium_subject_select":
			action_request({"action": "modify_media", "media_id": current_media_id, "new_subject_id": $("#"+input_id).val()},false,true);
		break;
		case "catalog_medium_school_year_select":
			action_request({"action": "modify_media", "media_id": current_media_id, "new_school_year_id": $("#"+input_id).val()},false,true);
		break;
		case "catalog_medium_author":
			action_request({"action": "modify_media", "media_id": current_media_id, "new_author": $("#"+input_id).text()},false,true);
		break;
		case "catalog_medium_publisher":
			action_request({"action": "modify_media", "media_id": current_media_id, "new_publisher": $("#"+input_id).text()},false,true);
		break;
		case "catalog_medium_price":
			action_request({"action": "modify_media", "media_id": current_media_id, "new_price": $("#"+input_id).text()},false,true);
		break;
		case "catalog_medium_type_select":
			action_request({"action": "modify_media", "media_id": current_media_id, "new_type_id": $("#"+input_id).val()},false,true);
		break;
		case "catalog_medium_miscellaneous":
			action_request({"action": "modify_media", "media_id": current_media_id, "new_miscellaneous": $("#"+input_id).text()},false,true);
		break;
	}
}

function change_media_instance_loaned_until(barcode,current_date){
	Swal.fire({
		title: "",
		type: 'question',
		customClass: "swal_mobile_fullscreen",
		confirmButtonText: lang("change"),
		showCancelButton: true,
		confirmButtonClass: 'button',
		cancelButtonClass: 'button',
		buttonsStyling: false,
		html: "<input id='change_media_instance_loaned_until_input' class='input_gray input_focus_color' type='text' autocomplete='off'></input>",
	}).then((result) => {
		if(result.dismiss != "cancel" && result.dismiss != "backdrop"){
			d = new Date(change_media_instance_loaned_until_picker.toString());
			new_date = d.getFullYear()+"-"+(d.getMonth()+1)+"-"+(d.getDate());
			action_request({"action": "modify_media_instance", "barcode": barcode, "new_loaned_until": new_date},false,true)
			.then(function(){
				refresh_media_editing_instances(current_media_id);
				refresh_customer_editing_instances(current_customer_id);
			});
		}
	})
	.catch(function(){});
	var change_media_instance_loaned_until_picker = new Pikaday({ field: document.getElementById('change_media_instance_loaned_until_input')});
	change_media_instance_loaned_until_picker.setDate(current_date);
}
function change_media_instance_holiday(barcode,current_holiday){
	Swal.fire({
		title: "",
		type: 'question',
		customClass: "swal_mobile_fullscreen",
		confirmButtonText: lang("change"),
		showCancelButton: true,
		confirmButtonClass: 'button',
		cancelButtonClass: 'button',
		buttonsStyling: false,
		html: "<label for='new_holiday_checkbox'>"+lang("holiday_lend")+"</label>"+
					"<input id='change_media_instance_holiday_checkbox' name='new_holiday_checkbox' type='checkbox' autocomplete='off'></input>",
	}).then((result) => {
		if(result.dismiss != "cancel" && result.dismiss != "backdrop"){
			let new_holiday;
			if($('#change_media_instance_holiday_checkbox').is(":checked")){
				new_holiday = "1";
			}else{
				new_holiday = "0";
			}
			console.log(new_holiday);
			action_request({"action": "modify_media_instance", "barcode": barcode, "new_holiday": new_holiday},false,true)
			.then(function(){
				refresh_media_editing_instances(current_media_id);
				refresh_customer_editing_instances(current_customer_id);
			});
		}
	})
	.catch(function(){});
	console.log(current_holiday);
	if(current_holiday == 1){
		$("#change_media_instance_holiday_checkbox").prop("checked",true);
	}else{
		$("#change_media_instance_holiday_checkbox").prop("checked",false);
	}
}
function remove_all_selected_media_instances(side){ //side 0 -> Media Search , 1 -> customer Search
	if(side == 0){
		remove_instances = get_selected_media_instances();
	}else{
		remove_instances = get_selected_customer_media_instances();
	}
	if(remove_instances.length != 0){
		let title;
		if(remove_instances.length == 1){
			title = lang("really_remove_media_instance");
		}else{
			title = lang("really_remove_media_instances").replace("[count]",remove_instances.length);
		}
		Swal.fire({
			//title: 'Wirklich '+remove_instances.length+' Medien Instanz(en) löschen',
			title: title,
			type: 'warning',
			customClass: "swal_mobile_fullscreen",
			confirmButtonText: 'löschen',
			showCancelButton: true,
			confirmButtonClass: 'button',
			cancelButtonClass: 'button',
			buttonsStyling: false,
		}).then((result) => {
			if(result.dismiss != "cancel" && result.dismiss != "backdrop"){
				action_request({action : "remove_media_instances", barcodes : JSON.stringify(remove_instances)},false,true)
				.then(function(data, status){
					refresh_media_editing_instances(current_media_id);
					refresh_customer_editing_instances(current_customer_id);
				})
				.catch(function(){});
			}
		});
	}
}
function return_all_selected_media_instances(side){ //side 0 -> Media Search , 1 -> customer Search
	if(side == 0){
		return_instances = get_selected_media_instances();
	}else{
		return_instances = get_selected_customer_media_instances();
	}
	if(return_instances.length != 0){
		Swal.fire({
			title: 'Wirklich '+return_instances.length+' Medien Instanzen zurückgeben',
			type: 'warning',
			customClass: "swal_mobile_fullscreen",
			confirmButtonText: 'zurückgeben',
			showCancelButton: true,
			confirmButtonClass: 'button',
			cancelButtonClass: 'button',
			buttonsStyling: false,
		}).then((result) => {
			if(result.dismiss != "cancel" && result.dismiss != "backdrop"){
				action_request({action : "return_media_instances", barcodes : JSON.stringify(return_instances)},false,true)
				.then(function(data, status){
					refresh_media_editing_instances(current_media_id);
					refresh_customer_editing_instances(current_customer_id);
				})
				.catch(function(){});
			}
		});
	}
}
function remove_media(media_id){
	Swal.fire({
		title: 'Medium wirklich löschen',
		type: 'warning',
		customClass: "swal_mobile_fullscreen",
		confirmButtonText: 'löschen',
		showCancelButton: true,
		confirmButtonClass: 'button',
		cancelButtonClass: 'button',
		buttonsStyling: false,
	}).then((result) => {
		if(result.dismiss != "cancel" && result.dismiss != "backdrop"){
			action_request({"action" : "remove_media", "media_id" : media_id},false,true)
			.then(function(data,status){
				switch_media_search_side(0);
			})
			.catch(function(){});
		}
	});
}
function remove_customer(customer_id){
	Swal.fire({
		title: 'Schüler wirklich löschen',
		type: 'warning',
		confirmButtonText: 'löschen',
		showCancelButton: true,
		confirmButtonClass: 'button',
		cancelButtonClass: 'button',
		buttonsStyling: false,
	}).then((result) => {
		if(result.dismiss != "cancel" && result.dismiss != "backdrop"){
			action_request({"action": "remove_customer", "customer_id": customer_id},false,true)
			.then(function(){
				switch_customer_search_side(0);
			})
			.catch(function(){});
		}
	});
}
function new_customer(){
	Swal.fire({
		title: 'Neuer Schüler',
		type: 'question',
		customClass: "swal_mobile_fullscreen",
		confirmButtonClass: 'button',
		cancelButtonClass: 'button',
		confirmButtonText: 'erstellen',
		showCancelButton: true,
		buttonsStyling: false,
		allowEnterKey: true,
		html: "<div onkeypress='if (event.keyCode==13){Swal.clickConfirm();}'><input id='new_customer_name' type='text' class='input_gray input_focus_color new_media_input' placeholder='"+lang("name")+"'></input><br>"+
		"<select id='new_customer_class' class='select new_customer_select'><option value='-1'>Klasse auswählen</option></select><br></div>",
		preConfirm: () => {
			returns = {
				"action" : "new_customer",
				"name" : $("#new_customer_name").val(),
				"class_id" : $("#new_customer_class").val(),
				};
			let error = false;
			if(returns["class_id"] == -1){
				$("#new_customer_class").addClass("animated pulse");
				setTimeout(function(){$("#new_customer_class").removeClass("pulse");},800);
				error = true;
			}
			if(returns["name"] == ""){
				$("#new_customer_name").addClass("animated pulse");
				setTimeout(function(){$("#new_customer_name").removeClass("pulse");},800);
				error = true;
			}
			if(error == false){
				return returns;
			}else{
				return false;
			}
		}
	}).then((result) => {
		if(result.dismiss != "cancel" && result.dismiss != "backdrop"){
			action_request(result["value"],false,true)
			.then(function(){})
			.catch(function(){});
		}
	});
	$("#new_customer_name").focus();
	refresh_classes_list("new_customer_class");
}
function new_media(){
	Swal.fire({
		title: 'Neues Medium',
		type: 'question',
		customClass: "swal_mobile_fullscreen",
		confirmButtonClass: 'button',
		cancelButtonClass: 'button',
		confirmButtonText: 'erstellen',
		showCancelButton: true,
		buttonsStyling: false,
		allowEnterKey: true,
		html: "<div onkeypress='if (event.keyCode==13){Swal.clickConfirm();}'><input id='new_media_title' type='text' class='input_gray input_focus_color new_media_input' placeholder='"+lang("title")+"'></input><br>"+
		"<input id='new_media_author' type='text' class='input_gray input_focus_color new_media_input' placeholder='"+lang("author")+"'></input><br>"+
		"<input id='new_media_publisher' type='text' class='input_gray input_focus_color new_media_input' placeholder='"+lang("publisher")+"'></input><br>"+
		"<input id='new_media_price' type='text' class='input_gray input_focus_color new_media_input' placeholder='"+lang("price")+"'></input><br>"+
		"<select id='new_media_school_year_id' class='select new_media_select'></select><br>"+
		"<select id='new_media_subject' class='select new_media_select'></select><br>"+
		"<select id='new_media_type' class='select new_media_select'></select><br></div>",
		preConfirm: () => {
			returns = {
				"action" : "new_media",
				"title" : $("#new_media_title").val(),
				"author" : $("#new_media_author").val(),
				"publisher" : $("#new_media_publisher").val(),
				"price" : $("#new_media_price").val(),
				"school_year_id" : $("#new_media_school_year_id").val(),
				"subject_id" : $("#new_media_subject").val(),
				"type_id" : $("#new_media_type").val(),
				};
			let error = false;
			if(returns["school_year_id"] == -1){
				$("#new_media_school_year_id").addClass("animated pulse");
				setTimeout(function(){$("#new_media_school_year_id").removeClass("pulse");},800);
				error = true;
			}
			if(returns["subject_id"] == -1){
				$("#new_media_subject").addClass("animated pulse");
				setTimeout(function(){$("#new_media_subject").removeClass("pulse");},800);
				error = true;
			}
			if(returns["type_id"] == -1){
				$("#new_media_type").addClass("animated pulse");
				setTimeout(function(){$("#new_media_type").removeClass("pulse");},800);
				error = true;
			}
			if(returns["title"] == ""){
				$("#new_media_title").addClass("animated pulse");
				setTimeout(function(){$("#new_media_title").removeClass("pulse");},800);
				error = true;
			}
			if(error == false){
				return returns;
			}else{
				return false;
			}
		}
	}).then((result) => {
		if(result.dismiss != "cancel" && result.dismiss != "backdrop"){
			action_request(result["value"],false,true)
			.then(function(){})
			.catch(function(){});
		}
	});
	$("#new_media_title").focus();
	refresh_school_years_list("new_media_school_year_id");
	refresh_subjects_list("new_media_subject");
	refresh_types_list("new_media_type");
}
function new_media_instance(){
	Swal.fire({
		title: 'Neues Exemplar',
		type: 'question',
		customClass: "swal_mobile_fullscreen",
		confirmButtonClass: 'button',
		cancelButtonClass: 'button',
		confirmButtonText: 'erstellen',
		showCancelButton: true,
		buttonsStyling: false,
		html: "<input id='new_media_instance_barcode' type='text' class='input_gray input_focus_color' placeholder='Barcode Anfang'></input><br><input id='new_media_instance_from' type='text' class='input_gray input_focus_color' placeholder='Von'></input><br><input id='new_media_instance_number' type='text' class='input_gray input_focus_color' placeholder='Anzahl'></input>",
		preConfirm: () => {
			return [
				document.getElementById('new_media_instance_barcode').value,
				document.getElementById('new_media_instance_from').value,
				document.getElementById('new_media_instance_number').value,
			]
		}
	}).then((result) => {
		if(result.dismiss != "cancel" && result.dismiss != "backdrop"){
			create_barcodes = [];

			let barcode = result["value"][0];
			let from = parseInt(result["value"][1]);
			let count = parseInt(result["value"][2]);

			for(i = from;i < (from+count);i++){
				create_barcodes.push(barcode+i);
			}
			action_request({"action" : "new_media_instances", "media_id" : current_media_id, "barcodes" : JSON.stringify(create_barcodes)},false,true)
			.then(function(data){
				refresh_media_editing_instances(current_media_id);
			})
			.catch(function(){});
		}
	});
	$("#new_media_instance_barcode").focus();
}
function lend_media_instance(customer_id, barcode, until, holiday, callback){
	//var jqxhr = $.post("action.php",{"action" : "lent_media_instance", "customer_id" : customer_id, "barcode" : barcode,"until" : until, "holiday": holiday},function (data){
	action_request({"action" : "lend_media_instance", "customer_id" : customer_id, "barcode" : barcode,"until" : until, "holiday": holiday},false,true)
	.then(function(response){
		callback(response);
	})
	.catch(function(response){
	});
}
function return_media_instance(barcode,callback){
	get_data_request({"requested_data" : "media_instance_infos", "barcode" : $("#media_return_input")[0].value},false,true)
	.then(function(response){
		infos = response.find("media");
		return action_request({"action" : "return_media_instance", "barcode" : barcode},false,true);
	})
	.then(function(response){
		callback();
	})
	.catch(function(exception){
	});
}
function get_selected_media_instances(){
	checkboxes = $(".media_search_instance_checkbox");
	barcodes = [];

	for(i = 0; i < checkboxes.length; i++){
		if(checkboxes[i].checked){
			barcodes.push($(checkboxes[i]).attr("instance_barcode"));
		}
	}
	return barcodes;
}
function get_selected_customer_media_instances(){
	checkboxes = $(".customer_search_instance_checkbox");
	barcodes = [];

	for(i = 0; i < checkboxes.length; i++){
		if(checkboxes[i].checked){
			barcodes.push($(checkboxes[i]).attr("barcode"));
		}
	}
	return barcodes;
}
// ================================================
// Action Button Handler
function lend_button_clicked(input_box_id, date_select_id, holiday){
	lend_media_instance($('#lend_customer_select')[0].value,$('#'+input_box_id)[0].value,$('#'+date_select_id)[0].value,holiday, function(data){
		//$('#'+input_box_id)[0].value = '';
		clear_input_box("#"+input_box_id);
		refresh_medias_table();
	});
}
function return_button_clicked(){
		enable_button_loading("media_return_button");
		let infos;
		get_data_request({"requested_data" : "media_instance_infos", "barcode" : $("#media_return_input")[0].value},false,true)
		.then(function(data){
			infos = $(data).find("media");
			return action_request({"action": "return_media_instance", "barcode": $('#media_return_input')[0].value},false,true);
		})
		.then(function(data){
			let undo = "action_request({'action': 'lend_media_instance', 'barcode': '"+$('#media_return_input')[0].value+"', 'customer_id': '"+infos[0].getAttribute("loaned_to")+"', 'holiday': '"+infos[0].getAttribute("holiday")+"', 'until': '"+infos[0].getAttribute("loaned_until")+"'},false,true); refresh_medias_table(); $(this).parent().remove();"
			add_row_to_table("return_history_table",[infos[0].getAttribute("title"),$('#media_return_input')[0].value,infos[0].getAttribute("loaned_to_name"),getHour()+":"+getMinute(),"rückgängig"],false,[false,false,false,false,undo],false,true);
			clear_input_box("#media_return_input");
			refresh_medias_table();
			disable_button_loading("media_return_button");
		})
		.catch(function(){
			disable_button_loading("media_return_button");
		})
}

// ================================================
// Table Functions
function add_row_to_table(table_id,column_array,headline,onclick_array,html_array,after_headline){
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
		if(onclick_array){
			if(Array.isArray(onclick_array)){
				if(onclick_array[i]){
					if(typeof onclick_array[i] === "string"){
						$(td).attr("onclick",onclick_array[i]);
					}else if(typeof onclick_array[i] === "function"){
						$(td).bind("click",onclick_array[i]);
					}
					td.classList.add("mouse_change_onhover");
				}
			}
			if(typeof onclick_array === "string"){
				$(td).attr("onclick",onclick_array);
				td.classList.add("mouse_change_onhover");
			}
		}

		if((typeof html_array === "boolean" && html_array == true) || (Array.isArray(html_array) && html_array[i] == true)){
			td.innerHTML = column_array[i];
		}else{
			td.appendChild(document.createTextNode(column_array[i]));
		}
		tr.appendChild(td);
	}
	if(after_headline){
		let table = $("#"+table_id)[0];
		table.insertBefore(tr,table.childNodes[2]);
	}else{
		$("#"+table_id)[0].appendChild(tr);
	}
}
// ================================================
// Miscellaneous
function clear_input_box(input){
	$(input).val("");
}
function clear_text(object_id){
	$("#"+object_id).text("");
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
function get_cookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return false;
}
function create_cookie(cname,cvalue){
	document.cookie = cname+"="+cvalue;
}
function error(error_text, reset_time){
	console.warn(error_text);

	/*if(reset_time){
		hide_time = reset_time;
	}else{
		hide_time = 3000;
	}

	$("#"+error_text_element_id).text(error_text);

	if(reset_time != -1){
		setTimeout(function(){clear_text(error_text_element_id);},hide_time);
	}*/
	Swal.fire({
		confirmButtonClass: 'button',
		title : 'Error',
		text : error_text,
		type : 'error',
		buttonsStyling: false,
		timer: reset_time
		});
}
