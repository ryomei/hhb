<?php
/**
 *
 * @author Ryomei
 */
interface IUserApp {
    
    function login(string $login, string $password);    
    function createUser(string $name, string $login, string $password);
    function updatePassword(int $id, string $password);
    
}

