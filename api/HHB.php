<?php
namespace api;

spl_autoload_register(function($className){
    include $className . '.php';
});

/**
 * Household Budget
 * @author Ryomei <du.ryomei@gmail.com>
 **/
class HHB{
    
    private static $hhb;
    private static $request;
    
    public static function init(){
        if (!self::$hhb){
            self::$hhb = new HHB();
            self::$request = new HHBRequest();
        }
        if (self::$request->method == "OPTIONS"){
            return header(200);
        }
        self::$hhb->start();
    }
    
    private function start(){
        $name = self::$request->controller ?? 'Main';
        $controllerName = __NAMESPACE__."\\controller\\" . ucfirst($name)."Controller";
        if (!is_file(__NAMESPACE__."\\controller\\" . ucfirst($name)."Controller.php")){
            return header("HTTP/1.0 404 Not Found");
        }
        $controller = new $controllerName();
        if (method_exists($controller, "processRequest")){
            $controller->processRequest(self::$request);
        }
    }
}

class HHBRequest{
    
    public $controller;
    public $method;
    public $payload;
    /**
     *
     * @var array
     */
    public $fq;
    
    public function __construct() {
        $this->setControllerAndFq();
        $this->setMethod();
        $this->setPayload();
    }
    
    private function setControllerAndFq(){
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        if (!\preg_match('/api/', $uri)) {
            return;
        }        
        $q = explode('/api/', $uri);
        $fq = explode('/', $q[1]);
        $this->controller = array_shift($fq);
        $this->fq = $fq;
    }
    
    private function setMethod(){
        $this->method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    }
    
    private function setPayload(){
        $this->payload = json_decode(file_get_contents('php://input'), true);        
    }
}

abstract class HHBController{
    /**
     *
     * @var HHBCriteria
     */
    protected $criteria;
    
    public function processRequest(HHBRequest $request){
        $method_name = strtolower($request->method);
        if (method_exists($this, $method_name) && $this->checkCriteria($method_name)){            
            $this->$method_name($request);
        }else{
            http_response_code(401);
            if (isset($this->criteria[$method_name])){
                echo $this->criteria[$method_name]->messages->unauthorized;
            }else{
                echo "criteria not defined for '$method_name'";
            }
        }
    }
    
    protected function checkCriteria($method_name){
        $validation = false;
        if (isset($this->criteria[$method_name])){
            
            if (isset($_SESSION["logged"]) && $this->criteria[$method_name]->login && $_SESSION["logged"]){
                $validation = true;
            }elseif (!$this->criteria[$method_name]->login){
                $validation = true;
            }
            
            if (isset($_SESSION["permissionLevel"]) && $this->criteria->$method_name->permissionLevel < $_SESSION["permissionLevel"]){
                $validation = false;
            }
        }
        return $validation;
    }
}

class HHBCriteria {
    
    /**
     *
     * @var bool
     */
    public $login;
    /**
     *
     * @var int
     */
    public $permissionLevel;
    /**
     *
     * @var HHBCriteriaMessages 
     */
    public $messages;
    
    public function __construct(bool $login, int $permissionLevel, HHBCriteriaMessages $messages) {
        $this->login = $login;
        $this->permissionLevel = $permissionLevel;
        $this->messages = $messages;
    }
}

class HHBCriteriaMessages {
    
    /**
     *
     * @var string
     */
    public $error;
    /**
     *
     * @var string
     */
    public $unauthorized;
    
    public function __construct(string $error, string $unauthorized) {
        $this->error = $error;
        $this->unauthorized = $unauthorized;
    }
}

abstract class HHBPermission{
    const All = 0;
    const User = 1;
    const Admin = 2;
}
