<?php
class User {
    public static function create($db, $userPk, $username, $fullName, $email, $password) {
        $sql = "INSERT INTO users (user_pk, user_username, user_full_name, user_email, user_password, created_at) VALUES (:user_pk, :user_username, :full_name, :email, :password, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":user_pk", $userPk);
        $stmt->bindValue(":user_username", $username);
        $stmt->bindValue(":full_name", $fullName);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":password", $password);
        $stmt->execute();
    }
}
