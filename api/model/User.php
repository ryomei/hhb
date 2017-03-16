<?php
namespace api\model;

class User{    
    /** @var \PDO **/
    private $conn;
    
    public $response;
    
    public function __construct() {        
        $this->conn = Connection::getConnection();        
    }

    public function login(string $login, string $password){        
        $stmt = $this->conn->prepare('SELECT id, loginname, username FROM login WHERE loginname = ? AND password = ?');
        $stmt->execute(array($login, hash("sha256", $password)));
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC);        
        if ($fetch){
            $this->successResponse($fetch);
            $this->setSession($fetch);
        }else{
            $this->badResponse("login failed", 401);
        }
    }
    
    public function add(string $login, string $password, string $userName){
        if ($this->checkLoginExists($login)){
            return $this->badResponse("login exists", 403);
        }
        
        $stmt = $this->conn->prepare('INSERT INTO login (loginname, username, password) VALUES (?,?,?)');
        $success = $stmt->execute(array($login, $userName, hash("sha256", $password)));
        if ($success){
            $this->successResponse(array("loginname"=>$login, "username"=>$userName));
        }else{
            $this->badResponse("Error while creating new user");
        }
    }
    
    private function checkLoginExists(string $login){
        $stmt = $this->conn->prepare('SELECT id FROM login WHERE loginname = ?');
        $stmt->execute(array($login));
        return $stmt->fetch();
    }
    
    private function setSession(array $fetchInfo){ 
        $_SESSION['logged'] = true;
        $_SESSION['userName'] = $fetchInfo['username'];
        $_SESSION['login'] = $fetchInfo['loginname'];
        $_SESSION['userId'] = $fetchInfo['id'];        
    }
    
    private function badResponse(string $msg, int $code = 400){
        http_response_code($code);
        $this->response = json_encode(array("success" => false, "data" => array("message"=>$msg)));
    }
    
    private function successResponse(array $data, int $code = 200){
        http_response_code($code);
        $this->response = json_encode(array("success" => true, "data" => $data));
    }
}