<?php
    namespace api\network{
        abstract class network_request{
            
            public $requirements = array();
            public $params = array();
            public $return_type = "text";
            
            public function init(){}
            
            public function check_requirements(){
                if(is_array($requirements)){
                    $errors = array();
                    foreach($requirements as $requirement){
                        $result = $requirement();
                        if($result != true){
                            $errors[] = $result;
                        }
                    }
                }
            }
            
            public function run(){}
            
            public function get_this(){
                return $this;
            }
        }
    
        class action extends network_request{
            function __construct() {
                $requirements = array();
                $params = $_POST;
                $return_type = "text";
            }
        }
        class get_data extends network_request{
            function __construct() {
                $requirements = array();
                $params = $_GET;
                $return_type = "text";
            }
        }
        
        /*Usage (action and get_data):
        *   Override functions:
        *       -init
        *       -run
        *   Override values in init:
        *       -$requirements
        *       -$return_type
        *   Call on execute:
        *       -init
        *       -check_requirements
        *       -run
        */
    }
?>