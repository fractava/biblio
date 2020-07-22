<?php
namespace core\network;

abstract class action extends \core\network\networkRequest{
    public $returnType = "xml";
    public $params;
    public $clearOutput = true;
    
    function __construct() {
        $this->params = $_POST;
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
