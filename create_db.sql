CREATE DATABASE spital;
USE spital;

CREATE TABLE specializare(
    id_specializare INT(10) AUTO_INCREMENT NOT NULL,
    nume_specializare VARCHAR(255) NOT NULL,
    id_manager INT(10) NOT NULL,
    PRIMARY KEY (id_specializare)
);

CREATE TABLE personal (
    id_salariat INT(10) AUTO_INCREMENT,
    nume VARCHAR(255) NOT NULL,
    prenume VARCHAR(255) NOT NULL,
    adresa VARCHAR(255) NOT NULL,
    data_nastere DATE NOT NULL,
    salariu INT(10) NOT NULL,
    id_specializare INT(10) NOT NULL,
    PRIMARY KEY (id_salariat)
);


CREATE TABLE pacienti (
    id_pacient INT(10) AUTO_INCREMENT,
    nume VARCHAR(255) NOT NULL,
    prenume VARCHAR(255) NOT NULL,
    data_nastere DATE NOT NULL,
    PRIMARY KEY (id_pacient)
);

CREATE TABLE trateaza (
    id_salariat INT(10) NOT NULL,
    id_pacient INT(10) NOT NULL,
    data_start DATE NOT NULL,
    data_end DATE NOT NULL,
    PRIMARY KEY (id_salariat, id_pacient)
);

CREATE TABLE logs (
    id_log INT(10) AUTO_INCREMENT,
    email VARCHAR(255),
    id_sesiune INT(10),
    adresa_ip VARCHAR(255) NOT NULL,
    adresa_web VARCHAR(255) NOT NULL,
    post_data VARCHAR(255) NOT NULL,
    data_log DATE NOT NULL,
    PRIMARY KEY (id_log)
);

ALTER TABLE specializare ADD CONSTRAINT foreign_key_manager FOREIGN KEY (id_manager) REFERENCES personal(id_salariat);
ALTER TABLE personal ADD CONSTRAINT foreign_key_specializare FOREIGN KEY (id_specializare) REFERENCES specializare(id_specializare);
ALTER TABLE trateaza ADD CONSTRAINT foreign_key_salariat FOREIGN KEY (id_salariat) REFERENCES personal(id_salariat);
ALTER TABLE trateaza ADD CONSTRAINT foreign_key_pacient FOREIGN KEY (id_pacient) REFERENCES pacienti(id_pacient);

CREATE TABLE sesiuni (
    id_sesiune INT(10) NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    bcrypt_password VARCHAR(255) NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_sesiune)
);

SET FOREIGN_KEY_CHECKS = 0;
INSERT INTO specializare (nume_specializare, id_manager) VALUES ('Cardiologie', 1);
INSERT INTO specializare (nume_specializare, id_manager) VALUES ('Neurologie', 2);
INSERT INTO personal (nume, prenume, adresa, data_nastere, salariu, id_specializare) VALUES ('Popescu', 'Ion', 'Str. Mihai Eminescu, nr. 1', '1990-01-01', 10000, 1);
INSERT INTO personal (nume, prenume, adresa, data_nastere, salariu, id_specializare) VALUES ('Ionescu', 'Mihai', 'Str. Mihai Eminescu, nr. 2', '1990-01-01', 10000, 1);
INSERT INTO personal (nume, prenume, adresa, data_nastere, salariu, id_specializare) VALUES ('Alexandra', 'Maria', 'Str. Mihai Eminescu, nr. 3', '1990-01-01', 10000, 2);
INSERT INTO personal (nume, prenume, adresa, data_nastere, salariu, id_specializare) VALUES ('Daria', 'Andreea', 'Str. Mihai Eminescu, nr. 4', '1990-01-01', 10000, 2);
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO pacienti (nume, prenume, data_nastere) VALUES ('Georgescu', 'Ion', '1990-01-01');
INSERT INTO pacienti (nume, prenume, data_nastere) VALUES ('Lazar', 'Mihai', '1990-01-01');
