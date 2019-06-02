var vm = new Vue({
    el: "#app",
    data : {
        logged_in : false,
	    
    	media_search_order_by : "title",
    	media_search_reverse : false,
    	customer_search_order_by : "name",
    	customer_search_reverse : false,
    
    	media_search_side : 0,
    	customer_search_side : 0,
    	current_media_id : -1,
    	current_customer_id : -1,
    	
    	lang : {},
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
                        vm.lang[children[i].tagName] = $(children[i]).attr("value");
                    }
            		
                    resolve();
            	})
            	.catch(function(error){reject(error);});
            });
        }
    }
});