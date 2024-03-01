<?php

require_once "phpmailer/mail_cod.php";
require_once "audit.php";

function email_endpoint():void {
    audit(array("email"));
    if(!isset($_POST["email"])) {
        die("Missing email address");
    }
    $email = $_POST["email"];
    $new_password = create_session($email);
    email_pwd('Parola noua este ' . $new_password, $email);
    echo "Email trimis";
}
