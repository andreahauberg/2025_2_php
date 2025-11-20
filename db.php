<?php
// PDO
// try{
//   $dbUserName = 'root';
//   $dbPassword = 'password'; // root | admin
//   $dbConnection = 'mysql:host=mariadb; dbname=company; charset=utf8mb4'; 
//   // utf8 every character in the world
//   // mb4 every character and also emojies
//   $options = [
//     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // try-catch
//     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // ['nickname']
//     // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ // ->nickname
//     // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM // [[2],[],[]]
//   ];
//   $_db = new PDO(  $dbConnection, 
//                   $dbUserName, 
//                   $dbPassword , 
//                   $options );
  
// }catch(PDOException $ex){
//   echo $ex;  
//   exit(); //die
// }


// PDO
try {
  $dbUserName = 'michelleenoe_com';
  $dbPassword = '3cmR4Fdpna9AHEBbtxDG';
  $dbConnection = 'mysql:host=mysql53.unoeuro.com;dbname=michelleenoe_com_db_php;charset=utf8mb4';

  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ];

  $_db = new PDO(
      $dbConnection,
      $dbUserName,
      $dbPassword,
      $options
  );

} catch(PDOException $ex) {
  echo $ex;
  exit();
}

// https://www.simply.com/dk/mysql/?login

// skriv: dbUserName + password 