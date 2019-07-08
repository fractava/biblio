function onload(){
  console.log("onload");
  
  vue_routes = [];
  lang = {};
  var router;

  init_routes()
  .then(print_routes)
  .then(init_lang)
  .then(init_design)
  .then(init_vue_router)
  .then(init_vue)
  .catch(function(error){
      console.error(error);
  })
}

$(document).ready(onload);

function init_routes(){
  return new Promise(async function(resolve,reject){
    var routes = await import("/apps/routes.js");
    routes = routes.routes;

    for(var i = 0; i < routes.length;i++){
      const path = routes[i]["url"];
      const side_json_url = "/apps" + routes[i]["side_json_url"];
      const side_json = await http_get(side_json_url,"json");
      const side_html_path = "/apps/" + path + "/" + side_json["side_html_url"];

      var component = () => get_component(side_html_path);

      var route = {};
      route["path"] = path;
      route["component"] = component;

      vue_routes.push(route);
    }
    resolve();
  });
}

function print_routes(){
    return new Promise(function(resolve, reject) {
        console.log(vue_routes);
        resolve();
    });
}

function init_lang(){
    return new Promise(function(resolve, reject) {
      let lang_id = 1;
      
      if(get_cookie("lang")){
        lang_id = get_cookie("lang");
      }

      get_data_request({"requested_data" : "language", "language_id" : lang_id},true,false,-1)
      .then(function(data , status){
        create_cookie("lang", lang_id);

            let xml = $(data);
            let children = xml[0].children[0].children;

            for(let i = 0; i < children.length; i++){
                lang[children[i].tagName] = $(children[i]).attr("value");
            }
            resolve();
      })
      .catch(function(error){
          reject(error);
      });
    });
}

function init_design(){
    console.log("testa");
    return new Promise(function(resolve,reject){
    	get_data_request({"requested_data": "active_design"},true,true,5)
    	.then(function(data,status){
            console.log("testb");
            enable_design_from_xml($(data));
    	    resolve();
    	})
    	.catch(function(){reject();});
    });
}

function init_vue_router(){
  return new Promise(function(resolve,reject){
    router = new VueRouter({
        routes: vue_routes,
        mode: 'hash'
    });
    resolve();
  });
}
function init_vue(){
  return new Promise(function(resolve,reject){
    vm = new Vue({
        router,
        data : () => ({
          logged_in : false,

          lang: lang
        }),
        watch : {
        },
        methods : {

        }
    }).$mount("#app");
    resolve();
  });
}

//Move to functions:
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
        });
    })
}
function get_component(url){
    return new Promise(function(resolve,reject){
        http_get(url,"text")
        .then(function(data){
            var component = {
                template: data,
                data: () => ({lang})
            };
           resolve(component); 
        });
    });
}
function enable_design_from_xml(xml){
    let ignore_attributes = ["id","name"]
    
    let properties = xml[0].children[0].children;
    console.log(properties);
    
    console.log("testc");
    for(let i = 0; i < properties.length; i++){
        let name= properties[i].nodeName;
        let value = properties[i].attributes[0].nodeValue;
        
        if(!ignore_attributes.includes(name)){
            let dict = {};
            dict[name] = value;
            
            set_design(dict);
        }
    }
}
function set_design(values){
    for(var key in values){
        let value = values[key];

        $(":root").css("--"+key,value);
        switch(key){
            case "browser_theme_color":
                browser_theme_color = value;
                $("#theme_color_meta").prop("content",browser_theme_color);
            break;
        }
    }
}
