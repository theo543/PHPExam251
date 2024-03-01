<?php

require_once "database/db.php";

class ValidatedSession {
    public function __construct(
        public string $email,
        public int $session_id
    ) {}
}

function create_session($email): string {
    $db = connect_to_db();
    execute("DELETE FROM sesiuni WHERE email = ?", [$email], $db);
    $token = random_bytes(32);
    $token = base64_encode($token);
    $password = random_bytes(32);
    $password = base64_encode($password);
    $bcrypt_opts = [
        "cost" => 12,
    ];
    $password_bcrypt = password_hash($password, PASSWORD_BCRYPT, $bcrypt_opts);
    $query = $db->prepare("INSERT INTO sesiuni (email, bcrypt_password, session_token) VALUES (?, ?, ?)");
    $query->bind_param("sss", $email, $password_bcrypt, $token);
    $success = $query->execute();
    if($success === null) {
        die("Could not create session");
    }
    return $password;
}

function check_session(): null | ValidatedSession {
    if(!isset($_COOKIE["session_token"]) || !isset($_COOKIE["session_email"])) {
        return null;
    }
    $session_token = $_COOKIE["session_token"];
    $session_email = $_COOKIE["session_email"];
    $row = fetch_one("
        SELECT id_sesiune, email
        FROM sesiuni
        WHERE email = ? AND
        session_token = FROM_BASE64(?)",
        [$session_email, $session_token]
    );
    if($row === null || $row["email"] !== $session_email) {
        return null;
    }
    return new ValidatedSession($session_email, $row["id_sesiune"]);
}

function end_session(string $user_id, string $token): bool {
    $db = connect_to_db();
    $query = $db->prepare("DELETE FROM sesiuni WHERE email = ? AND session_token = FROM_BASE64(?)");
    $query->bind_param("ss", $user_id, $token);
    $success = $query->execute();
    return $success !== null && ($db->affected_rows !== 0);
}