function onload(){
    console.log("onload");
    
    vue_routes = [];
    logged_in = false;
    app_storages = {};
    app_langs = {};
    app_files = {};
    route_requirements = {};
    navbar_items = [];
    
    loaded_requirements = ["vue","vue-router","jquery","mobile-detect"];
    
    default_side = "/apps/main/test";
    
    md = new MobileDetect(window.navigator.userAgent);
    
    var router;
    
    check_if_logged_in()
    .then(detect_mobile)
    .then(init_routes)
    .then(print_routes)
    .then(init_design)
    .then(init_vue_router)
    .then(init_navigation_guard)
    .then(init_vue)
    .catch(function(error){
        console.error(error);
    });
}

$(document).ready(onload);

function check_if_logged_in(){
    return new Promise(function(resolve,reject){
        get_data_request({"requested_data" : "logged_in"},true,true,10)
    	.then(function(data){
    		logged_in = (data == "true");
    		resolve();
    	});
    });
}
function detect_mobile(){
    return new Promise(function(resolve,reject){
        $("html").addClass((md.mobile() ? "mobile":"no-mobile"));
        $("html").addClass((md.phone() ? "phone":"no-phone"));
        $("html").addClass((md.tablet() ? "tablet":"no-tablet"));
        resolve();
    });
}
function init_routes(){
    return new Promise(async function(resolve,reject){
        var apps = await http_get("/apps/apps.json","json");

        for (let app = 0; app < apps.length; app++){
            let app_name = apps[app].name;
            let routes = apps[app].routes;
            
            let app_file_path = "/apps/" + app_name + apps[app].app_file_path;
            
            app_storages[app_name] = apps[app].props;
            app_langs[app_name] = apps[app].langs.de;
            
            for(var i = 0; i < routes.length;i++){
                let path = "/apps/" + app_name + routes[i]["url"];
                
                if(routes[i].var){
                    path += "/:" + routes[i].var;
                }
                
                if(routes[i]["side_template_path"]){
                    
                    route_requirements[path] = apps[app].requirements;
                    
                    const side_html_path = "/apps/" + app_name + routes[i]["side_template_path"];

                    var component = () => get_component(side_html_path,app_file_path,app_name);
                    
                    let route = {};
                    route["app_name"] = app_name;
                    route["path"] = path;
                    route["component"] = component;
                    route["props"] = {
                        storage: app_storages[app_name],
                        lang: app_langs[app_name]
                    };
            
                    vue_routes.push(route);
                }else if(routes[i]["redirect"]){
                    const redirect_path = "/apps/" + app_name + routes[i]["redirect"];
                    
                    let route = {};
                    route["path"] = path;
                    route["redirect"] = redirect_path;
                    
                    vue_routes.push(route);
                }
                if(routes[i]["in_navbar"]){route_requirements
                    navbar_items.push({
                        title: routes[i]["navbar_title"],
                        path: path
                    });
                }
            }
        }
        let route = {};
        route["path"] = "*";
        route["redirect"] = default_side;
        vue_routes.push(route);
        
        resolve();
    });
}

function print_routes(){
    return new Promise(function(resolve, reject) {
        console.log(vue_routes);
        resolve();
    });
}

function init_design(){
    return new Promise(function(resolve,reject){
    	get_data_request({"requested_data": "system:active_design"},true,true,5)
    	.then(function(data,status){
            enable_design_from_xml($(data));
    	    resolve();
    	});
    });
}

function init_vue_router(){
  return new Promise(function(resolve,reject){
    router = new VueRouter({
        routes: vue_routes,
        mode: 'hash',
        scrollBehavior (to, from, savedPosition) {
            return { x: 0, y: 0 }
        }
    });
    resolve();
  });
}
function init_vue(){
  return new Promise(function(resolve,reject){
    vm = new Vue({
        router,
        data : () => ({
          navbar_items: navbar_items,
          app_storages: app_storages
        }),
        watch : {
        },
        methods : {

        }
    }).$mount("#app");
    resolve();
  });
}
function init_navigation_guard(){
    return new Promise(function(resolve,reject){
        router.beforeEach((to, from, next) => {
            for(let i = 0; i < route_requirements[to.path].length; i++){
                if(!loaded_requirements.includes(route_requirements[to.path][i])){
                    let s = document.createElement("script");
                    s.src = "/libaries/"+route_requirements[to.path][i]+".js";
                    s.async = true;
                    $("head").append(s);
                    
                    loaded_requirements.push(route_requirements[to.path][i]);
                }
            }
            
            if(to.path == '/apps/system/login'){
                if(logged_in){
                    next({path: default_side});
                }else{
                    next();
                }
            }else{
                if(logged_in){
                    next();
                }else{
                    next({path: '/apps/system/login'});
                }
            }
        });
        resolve();
    });
}

function http_get(url,type){
    return new Promise(function(resolve,reject){
        $.get({
            url,
            dataType: type
        })
        .done(function(data){
            resolve(data);
        });
    });
}
function get_component(template_url,app_file_url,app_name){
    return new Promise(function(resolve,reject){
        
        let template;
        let component;
        
        function get_template(){
            return new Promise(function(resolve,reject){
                http_get(template_url,"text")
                .then(function(data){
                    template =  data;
                    resolve();
                });
            });
        }
        function get_app_file_if_necessary(){
            return new Promise(function(resolve,reject){
                if(!app_files[app_name]){
                    import(app_file_url)
                    .then(function(data){
                        app_files[app_name] =  data.app;
                        resolve();
                    });
                }else{
                    resolve();
                }
            });
        }
        function create_component(){
            return new Promise(function(resolve,reject){
                component = {
                    template: template,
                    props: {
                        storage: {
                            type: Object,
                            default: {}
                        },
                        lang: {
                            type: Object,
                            default: {}
                        }
                    },
                    methods: app_files[app_name].methods,
                    computed : app_files[app_name].computed,
                    watch: app_files[app_name].watch
                };
                resolve();
            });
        }
        get_template()
        .then(get_app_file_if_necessary)
        .then(create_component)
        .then(function(){
            resolve(component);
        });
    });
}
function enable_design_from_xml(xml){
    console.log(xml);
    let ignore_attributes = ["id","name"]
    
    let properties = xml[0].children[0].children;

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
