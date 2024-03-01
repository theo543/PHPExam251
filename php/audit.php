<?php

/*
CREATE TABLE logs (
    id_log INT(10) AUTO_INCREMENT,
    email VARCHAR(255),
    id_sesiune INT(10),
    adresa VARCHAR(255) NOT NULL,
    post_data VARCHAR(255) NOT NULL,
    data_log DATE NOT NULL,
    PRIMARY KEY (id_log)
);
*/

$current_email = null;
$current_session_id = null;

function prepare_audit_system($email, $session_id): void {
    global $current_email, $current_session_id;
    $current_email = $email;
    $current_session_id = $session_id;
}

function audit(array $expected_post_keys, array $confidential_post_keys = array()): void {
    global $current_email, $current_session_id;
    $post_data = 'Date trimise formularului: ';
    foreach($expected_post_keys as $key) {
        if(!isset($_POST[$key])) {
            $post_data .= "$key nu a fost trimis, ";
        } else if (in_array($key, $confidential_post_keys)) {
            $post_data .= "$key a fost trimis, ";
        } else {
            $post_data .= "$key: " . $_POST[$key] . ", ";
        }
    }
    $db = connect_to_db();
    $query = $db->prepare("INSERT INTO logs (email, id_sesiune, adresa_ip, post_data, data_log, adresa_web) VALUES (?, ?, ?, ?, NOW(), ?)");
    $query->bind_param("sisss", $current_email, $current_session_id, $_SERVER["REMOTE_ADDR"], $post_data, $_SERVER["REQUEST_URI"]);
    $success = $query->execute();
    if($success === null) {
        die("Log system is down - contact the administrator");
    }
}
