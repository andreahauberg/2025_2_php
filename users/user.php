<?php 
// OOP - Object Oriented Programming
// Works with monolithic applications

class User {
    private $username;
    private $userFirstName;

    public function __construct($username, $userFirstName) {
        $this->username = $username;
        $this->userFirstName = $userFirstName;
    } 
    // Instantiate the object
    // Convert the code into an object in memory

    //GETTERS
    public function getUsername(){
        return $this->username;
    }

    public function getUserFirstName(){
        return $this->userFirstName;
    }

    //SETTERS
    // public function setUsername($username){
    //     $this->username = $username;
    // }

    public function connectToDB(){
        require_once __DIR__."/db.php";

        $sql = "INSERT INTO people VALUES (:person_pk, :person_username, :person_first_name)";
        $stmt = $_db->prepare( $sql );

        $stmt->bindValue(":person_pk", null);
        $stmt->bindValue(":person_username", $this->username);
        $stmt->bindValue(":person_first_name", $this->userFirstName);
        $stmt->execute();

        return true;

    }

}

$formUsername = $_POST['user_name']  ?? "";
$formUserFirstName = $_POST['user_first_name']  ?? "";

$user = new User($formUsername , $formUserFirstName);

echo $user->getUsername();
echo $user->getUserFirstname();

$user->connectToDB();

// echo $user->getUserFirstName();
// $user->setUsername ("andreajean");
// echo $user->getUsername();
