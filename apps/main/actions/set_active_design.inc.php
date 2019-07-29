<?php
    if(isset($_POST["id"])){
        if(design_exists($_POST["id"])){
            set_config("active_design",$_POST["id"]);
    	}
    }
?>