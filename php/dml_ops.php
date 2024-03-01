<?php

require_once "audit.php";

$permitted_emails = array("theodor.negrescu@gmail.com");

$email = "";

function cfg_email(string $email1): void {
    global $email;
    $email = $email1;
}

function validate_email() {
    global $permitted_emails;
    global $email;
    if(!in_array($email, $permitted_emails)) {
        die("Email not permitted");
    }
}

function echo_result(int|null $rows, string $action): void {
    if ($rows === null) {
        die("$action: Eroare baza de date. Te rog, introdu date valide. Nota: nu poti sterge un angajat daca o specializare sau un pacient depinde de el."
        . "<br>Daca eroarea persista, contacteaza administratorul.");
    }
    echo "$action: Afectat " . $rows . " randuri";
}

function log_treatment_endpoint() {
    audit(array("patient_name", "doctor_name", "zile"));
    validate_email();
    if(!isset($_POST["patient_name"]) || !isset($_POST["doctor_name"]) || !isset($_POST["zile"])) {
        die("Date formular incomplete");
    }
    $patient_name = $_POST["patient_name"];
    $doctor_name = $_POST["doctor_name"];
    $zile = $_POST["zile"];
    $rows = execute("
    INSERT INTO trateaza (id_salariat, id_pacient, data_start, data_end)
    SELECT id_salariat, id_pacient, NOW(), NOW() + INTERVAL ? DAY
    FROM personal, pacienti
    WHERE STRCMP(CONCAT(personal.nume, ' ', personal.prenume), ?) = 0 AND STRCMP(CONCAT(pacienti.nume, ' ', pacienti.prenume), ?) = 0",
    [$zile, $doctor_name, $patient_name]);
    echo_result($rows, 'adauagare info tratament');
}

function fire_employee_endpoint() {
    audit(array("employee_name"));
    validate_email();
    if(!isset($_POST["employee_name"])) {
        die("Date formular incomplete");
    }
    $employee_name = $_POST["employee_name"];
    $rows = execute("
    DELETE FROM personal
    WHERE STRCMP(CONCAT(nume, ' ', prenume), ?) = 0",
    [$employee_name]);
    echo_result($rows, 'concendiere angajat');
}

function set_specialty_manager_endpoint() {
    audit(array("specialty_name", "manager_name"));
    validate_email();
    if(!isset($_POST["specialty_name"]) || !isset($_POST["manager_name"])) {
        die("Date formular incomplete");
    }
    $specialty_name = $_POST["specialty_name"];
    $manager_name = $_POST["manager_name"];
    $id_manager = fetch_one("SELECT id_salariat FROM personal WHERE STRCMP(CONCAT(nume, ' ', prenume), ?) = 0", [$manager_name]);
    if($id_manager === null) {
        die("Nu exista un angajat cu numele " . $manager_name);
    }
    $rows = execute("
    UPDATE specializare
    SET id_manager = ?
    WHERE STRCMP(nume_specializare, ?) = 0",
    [$id_manager["id_salariat"], $specialty_name]);
    echo_result($rows, 'setare manager specializare');
}
