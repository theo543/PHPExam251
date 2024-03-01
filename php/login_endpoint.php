<?php

require_once "database/db.php";

function login_endpoint():void {
    audit(array("email", "password"), array("password"));
    if(!isset($_POST["email"]) || !isset($_POST["password"])) {
        die("Missing email or password");
    }
    $email = $_POST["email"];
    $password = $_POST["password"];
    $row = fetch_one("SELECT bcrypt_password, session_token FROM sesiuni WHERE email = ?", [$email]);
    if($row === null) {
        die("Invalid username or password");
    }
    $correct_hash = $row["bcrypt_password"];
    if(!password_verify($password, $correct_hash)) {
        die("Invalid username or password");
    }
    setcookie("session_token", base64_encode($row["session_token"]), time() + 86400, "/");
    setcookie("session_email", $email, time() + 86400, "/");
    header("Location: /");
}
