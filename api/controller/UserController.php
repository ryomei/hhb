<?php
namespace api\controller;
use api\HHBController as HHBController;
use \api\HHBCriteria as HHBCriteria;
use \api\HHBCriteriaMessages as HHBCriteriaMessages;
use \api\HHBPermission as HHBPermission;

class UserController extends HHBController{
    
    public function __construct() {
        $this->model = new \api\model\User();
        
        $genericError = "unknow error";
        $genericUnauthorized = "invalid credentials calling this resource";
        $this->criteria = array(
            "post" => new HHBCriteria(false, HHBPermission::All, 
                    new HHBCriteriaMessages($genericError, $genericUnauthorized))
        );
    }
    
    public function post($request){
        $payload = $request->payload;
        // all posts on this class must have login and password
        if (isset($payload['login']) && isset($payload['password'])){
            // now if it has a username, then we are registering a new user,
            // otherwise it is a login attempt.
            if (isset($payload['username'])){
                $this->newUser($payload['login'], $payload['password'], $payload['username']);
            }else{
                $this->login($payload['login'], $payload['password']);
            }
        }
    }
    
    private function login(string $login, string $password){
        $this->model->login($login, $password);
        echo $this->model->response;
    }
    
    private function newUser(string $login, string $password, string $userName){
        $this->model->add($login, $password, $userName);
        echo $this->model->response;
    }
}