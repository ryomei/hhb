<?php
namespace api\model;

class Connection{
    
    private static $conn;
    
    public static function getConnection(){
        
        if (!self::$conn){
            $dsn = 'mysql:host=localhost;port=3306;dbname=hhb';
            $username = 'hhb_user';
            $password = 'hhb_secret_password';
            self::$conn = new \PDO($dsn, $username, $password);
        }
        
        return self::$conn;
    }
}