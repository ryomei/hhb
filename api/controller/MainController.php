<?php
namespace api\controller;
use api\HHBController as HHBController;
use \api\HHBCriteria as HHBCriteria;
use \api\HHBCriteriaMessages as HHBCriteriaMessages;
use \api\HHBPermission as HHBPermission;

/**
 * Controller that is called whenever a non-controller is specified on the URI
 *
 * @author Ryomei
 */
class MainController extends HHBController{
    
    public function __construct() {
        $genericError = "unknow error";
        $genericUnauthorized = "invalid credentials calling this resource";
        
        $this->criteria = array(
            "post" => new HHBCriteria(false, HHBPermission::All, 
                    new HHBCriteriaMessages($genericError, $genericUnauthorized)),
            
            "get" => new HHBCriteria(false, HHBPermission::All, 
                    new HHBCriteriaMessages($genericError, $genericUnauthorized)),
            
            "put" => new HHBCriteria(false, HHBPermission::All,
                    new HHBCriteriaMessages($genericError, $genericUnauthorized)),
            
            "delete" => new HHBCriteria(false, HHBPermission::All, 
                    new HHBCriteriaMessages($genericError, $genericUnauthorized))
        );
    }
    
    public function get(){
        echo "MainController GET called succesfuly";
    }
    
    public function post(){
        echo "MainController POST called succesfuly";
    }
    
    public function put(){
        echo "MainController PUT called succesfuly";
    }
    
    public function delete(){
        echo "MainController DELETE called succesfuly";
    }
}



