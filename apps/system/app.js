var app = {
    "methods": {
        "login": function(){
            action_request({action : "login" ,email : document.getElementById('login_email').value, 'passwort' : document.getElementById('login_passwort').value},false,false,false)
            .then(function(){
        		logged_in = true;
        		
        		router.push({ path: "/apps/main/test"});
        	})
        	.catch(function(reason){
        		let error_text = "";
        		for(let i = 0; i < reason.length; i++){
        			error_text += lang[reason[i]["error_lang_name"]];
        		}
        		$("#login_error_message").text(error_text);
        	});
        }
    },
    "computed": {
        
    },
    "watch": {
        
    }
}
export {app};