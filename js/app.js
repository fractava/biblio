$(document).ready(onload);
async function onload(){
    var routes = await import("/apps/routes.js");
    routes = routes.routes;
    console.log(routes);
    
    var vue_routes = [];
    
    for(var i = 0; i < routes.length;i++){
        var path = /* "/apps" + */ routes[i]["url"];
        console.log(path);
        var side_json_url = "/apps" + routes[i]["side_json_url"];
        var side_json = await http_get(side_json_url,"json");
        var side_html_path = "/apps/" + path + "/" + side_json["side_html_url"];
        
        var component = {template: await http_get(side_html_path,"text")};
        
        var route = {};
        
        route["path"] = path;
        route["component"] = component;
        
        vue_routes.push(route);
    }
    
    console.log(vue_routes);
    
    router = new VueRouter({
        routes: vue_routes
    });
        
    init_vue();
    //init();
}
function http_get(url,type){
    return new Promise(function(resolve,reject){
        $.get({
            url,
            dataType: type
        })
        .done(function(data){
            resolve(data);
        })
        .fail(function(){
            reject();
        })
    })
}
function init_vue(){
    console.log(router);
    vm = new Vue({
        router,
        data : {
            logged_in : false,
	    
        	media_search_order_by : "title",
        	media_search_reverse : false,
        	customer_search_order_by : "name",
        	customer_search_reverse : false,
            
            side : "0",
    	
    	    lang : {}
        },
        watch : {
        },
        methods : {
            switch_language : function(id){
                return new Promise(function(resolve, reject) {
            	    get_data_request({"requested_data" : "language", "language_id" : id},true,false,-1)
            	    .then(function(data , status){
            		    create_cookie("lang",id);
                    
                        let xml = $(data);
                        let children = xml[0].children[0].children;
                    
                        for(let i = 0; i < children.length; i++){
                            Vue.set(vm.lang , children[i].tagName , $(children[i]).attr("value"));
                        }
            		
                        resolve();
            	    })
            	    .catch(function(error){reject(error);});
                });
            }
        }
    }).$mount("#app");
}