function get_request(url,parameters,type,callback_sucess,callback_fail){
	let jqxhr = $.get(url, parameters, function(data,textStatus,jqXHR){
		//if($xml = $(data)){
			//callback_sucess(data,textStatus,$xml);
		//}else{
			callback_sucess(data,textStatus);
		//}
	});
	jqxhr.fail(function(jqXHR, exception){callback_fail(jqXHR, exception);});
}
function post_request(url,parameters,type,callback_sucess,callback_fail){
	let jqxhr = $.post(url, parameters, function(data,textStatus,jqXHR){callback_sucess(data,textStatus,jqXHR);});
	jqxhr.fail(function(jqXHR, exception){callback_fail(jqXHR, exception);});
}
function get_data_request(parameters,retry,show_error,max_retries){
	return new Promise(function(resolve, reject) {

		if(!max_retries){
			max_retries = 5;
		}
		retries = 0;
		retry_wait = 3000;
		execute();

		function request(parameters){
			let jqxhr = $.get("/getData.php", parameters, function(data,textStatus,jqXHR){
				let request_return;
			try{
				$xml = $(data);
				if($xml.length > 0){
					request_return = $xml;
				}else{
					request_return = data;
				}
			}catch(e){
				request_return = data;
			}
			resolve(request_return);
				//sucess(data,textStatus,$xml);
			});
			jqxhr.fail(function(jqXHR,exception){
				fail(jqXHR, exception);
			});
		}
		function execute(){
			request(parameters);
		}
		function sucess(data,status,$xml){
			resolve();
		}
		function fail(jqXHR, exception){
			if(jqXHR.status == 400){
				$xml = $(jqXHR.responseText);
				$errors = $xml.find("error");
				error_text = "";
				if(show_error){
					for(let i = 0; i< $errors.length; i++){
						new_error = (lang("error_"+$errors[i].getAttribute("id"))+"\n");
						console.log($errors[i].getAttribute("extra_detail"));
						if($errors[i].getAttribute("extra_detail")){
							new_error = new_error.replace("[extra_info]",$errors[i].getAttribute("extra_detail"));
						}
						error_text += new_error;
					}
					error(error_text);
				}
				let reject_array = [];
				for(let i = 0; i< $errors.length; i++){
					reject_array.push({"error_lang_name": "error_"+$errors[i].getAttribute("id"),"extra_detail": $errors[i].getAttribute("extra_detail"),"type" : "internal", "error": $errors[i].getAttribute("id")});
				}
				reject(reject_array);
			}else{
				if(retry && retries < max_retries){
					retries++;
					setTimeout(function(){execute()},retry_wait);
				}else{
					if(show_error){
						error(lang("http_error_"+jqXHR.status));
					}
					reject([{"error_lang_name": "http_error_"+jqXHR.status, "type": "http", "error": jqXHR.status}]);
				}
			}
		}
	});
}

function action_request(parameters,retry,show_error,max_retries){
	return new Promise(function(resolve, reject) {

		if(!max_retries){
			max_retries = 5;
		}
		retries = 0;
		retry_wait = 3000;
		execute();

		function request(parameters){
			let jqxhr = $.post("/action.php", parameters, function(data,textStatus,jqXHR){
				if($xml = $(data)){
					resolve($xml);
				}else{
					resolve(data);
				}
				//sucess(data,textStatus,$xml);
			});
			jqxhr.fail(function(jqXHR,exception){
				fail(jqXHR, exception);
			});
		}
		function execute(){
			request(parameters);
		}
		function sucess(data,status,$xml){
			resolve();
		}
		function fail(jqXHR, exception){
			if(jqXHR.status == 400){
				$xml = $(jqXHR.responseText);
				$errors = $xml.find("error");
				error_text = "";
				if(show_error){
					for(let i = 0; i< $errors.length; i++){
						new_error = (lang("error_"+$errors[i].getAttribute("id"))+"\n");
						console.log($errors[i].getAttribute("extra_detail"));
						if($errors[i].getAttribute("extra_detail")){
							new_error = new_error.replace("[extra_info]",$errors[i].getAttribute("extra_detail"));
						}
						error_text += new_error;
					}
					error(error_text);
				}
				let reject_array = [];
				for(let i = 0; i< $errors.length; i++){
					reject_array.push({"error_lang_name": "error_"+$errors[i].getAttribute("id"),"extra_detail": $errors[i].getAttribute("extra_detail"),"type" : "internal", "error": $errors[i].getAttribute("id")});
				}
				reject(reject_array);
			}else{
				if(retry && retries < max_retries){
					retries++;
					setTimeout(function(){execute()},retry_wait);
				}else{
					if(show_error){
						error(lang("http_error_"+jqXHR.status));
					}
					reject([{"error_lang_name": "http_error_"+jqXHR.status, "type": "http", "error": jqXHR.status}]);
				}
			}
		}
	});
}
