<?php
// TODO: connect to the database
//TODO: get user data


class User{

    private $db;
    
    public function __construct(){
        require_once __DIR__.'/DBModel.php';
        $conn = new DBModel();
        $this->db = $conn->getDB();
    }
    
    public function getUser(){
        // require __DIR__.'/../db.php';
        $sql = 'SELECT * FROM users LIMIT 1';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        $user = $stmt->fetch();
        // print_r($user);

        return $user;
    }

    public function getUsername(){
        $sql = 'SELECT user_username FROM users LIMIT 1';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute();

        $user = $stmt->fetch();
        // print_r($user);

        return $user['user_username'];
    }
}