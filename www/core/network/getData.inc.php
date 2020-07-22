<?php
namespace network;

abstract class getData extends \core\network\networkRequest{
    public $returnType = "xml";
    public $params;
    public $clearOutput = true;
    
    function __construct() {
        $this->params = $_GET;
    }
}
       
/*Usage (action and get_data):
    *   Override functions:
    *       -init
    *       -run
    *   Don't ovveride function:
    *       -__construct
    *   Override values in init:
    *       -$errors
    *       -$returnType
    *   Call on execute:
    *       -init
    *       -run
*/
