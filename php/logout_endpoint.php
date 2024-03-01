<?php

require_once "database/db.php";
require_once "audit.php";

function logout_endpoint():void {
    audit(array());
    if(isset($_COOKIE["session_token"]) && isset($_COOKIE["session_email"])) {
        if(!end_session($_COOKIE["session_email"], $_COOKIE["session_token"])) {
            echo "Could not delete your session in the database. It might not exist.";
            return;
        }
        setcookie("session_token", "", time() - 3600, "/");
        setcookie("session_email", "", time() - 3600, "/");
        header("Location: /");
    } else {
        echo "Token or ID not specified.";
    }
}
