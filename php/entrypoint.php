<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once "Router.php";
require_once "logout_endpoint.php";
require_once "session.php";
require_once "email.php";
require_once "login_endpoint.php";
require_once "dml_ops.php";

$pre_auth = new Router;
$pre_auth->post("/request_email", fn() => email_endpoint());
$pre_auth->post("/login", fn() => login_endpoint());

if($pre_auth->run()) {
    exit;
}

$session = check_session();

if($session === null) {
    header("Location: /auth.html");
    exit;
}

cfg_email($session->email);
prepare_audit_system($session->email, $session->session_id);

$post_auth = new Router;
$post_auth->post("/logout", fn() => logout_endpoint());
$post_auth->get("/", fn() => include "logged_in.html");
$post_auth->post("/log_treatment", fn() => log_treatment_endpoint());
$post_auth->post("/set_specialty_manager", fn() => set_specialty_manager_endpoint());
$post_auth->post("/fire_employee", fn() => fire_employee_endpoint());

if(!$post_auth->run()) {
    http_response_code(404);
    echo "404 Not Found";
}
