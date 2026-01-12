-- Schema for tutor_link (MySQL)
-- Generated from the PHP models/entities found in this project.

SET NAMES utf8mb4;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS tutor_link
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE tutor_link;

-- ------------------------------------------------------------
-- Catalog tables
-- ------------------------------------------------------------

DROP TABLE IF EXISTS carrera_materia;
DROP TABLE IF EXISTS particulares;
DROP TABLE IF EXISTS preferencias;
DROP TABLE IF EXISTS enrollment;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS tutor;
DROP TABLE IF EXISTS student;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS subject;
DROP TABLE IF EXISTS escuela;
DROP TABLE IF EXISTS posts;

CREATE TABLE escuela (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  escuela VARCHAR(191) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subject (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  materia_des VARCHAR(191) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_subject_materia_des (materia_des)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Used by indexModel.php
CREATE TABLE posts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(191) NOT NULL,
  body TEXT NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NOTE: The application does INSERT INTO users VALUES (...)
-- so the column order here must match userModel::registerUser().
CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  cedula VARCHAR(30) NOT NULL,
  telefono VARCHAR(30) NULL,
  email VARCHAR(191) NOT NULL,
  carrera INT UNSIGNED NULL,
  role TINYINT UNSIGNED NOT NULL,
  date DATETIME NOT NULL,
  pass VARCHAR(255) NOT NULL,
  foto VARCHAR(255) NULL,
  about TEXT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_cedula (cedula),
  KEY idx_users_carrera (carrera),
  CONSTRAINT fk_users_carrera FOREIGN KEY (carrera) REFERENCES escuela(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- These two tables are written on register, but most reads use `users`.
CREATE TABLE tutor (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  cedula VARCHAR(30) NOT NULL,
  email VARCHAR(191) NOT NULL,
  telefono VARCHAR(30) NULL,
  foto VARCHAR(255) NULL,
  PRIMARY KEY (id),
  KEY idx_tutor_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE student (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  cedula VARCHAR(30) NOT NULL,
  email VARCHAR(191) NOT NULL,
  carrera INT UNSIGNED NULL,
  PRIMARY KEY (id),
  KEY idx_student_email (email),
  KEY idx_student_carrera (carrera),
  CONSTRAINT fk_student_carrera FOREIGN KEY (carrera) REFERENCES escuela(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
  id_horario INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_tutor INT UNSIGNED NOT NULL,
  id_materia INT UNSIGNED NOT NULL,
  dia VARCHAR(15) NOT NULL,
  inicio DECIMAL(4,2) NOT NULL,
  final DECIMAL(4,2) NOT NULL,
  PRIMARY KEY (id_horario),
  KEY idx_sessions_tutor (id_tutor),
  KEY idx_sessions_materia (id_materia),
  CONSTRAINT fk_sessions_tutor FOREIGN KEY (id_tutor) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_sessions_subject FOREIGN KEY (id_materia) REFERENCES subject(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- IMPORTANT: the code queries enrollment.id_horaio (typo), so the column is kept as id_horaio.
CREATE TABLE enrollment (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_estudiante INT UNSIGNED NOT NULL,
  id_horaio INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_enrollment_student_session (id_estudiante, id_horaio),
  KEY idx_enrollment_session (id_horaio),
  CONSTRAINT fk_enrollment_student FOREIGN KEY (id_estudiante) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_enrollment_session FOREIGN KEY (id_horaio) REFERENCES sessions(id_horario)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE preferencias (
  id_preferencia INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_student INT UNSIGNED NOT NULL,
  id_materia INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_preferencia),
  UNIQUE KEY uq_preferencias_student_materia (id_student, id_materia),
  KEY idx_preferencias_materia (id_materia),
  CONSTRAINT fk_preferencias_student FOREIGN KEY (id_student) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_preferencias_subject FOREIGN KEY (id_materia) REFERENCES subject(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE particulares (
  id_particulares INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_estudiante INT UNSIGNED NOT NULL,
  id_tutor INT UNSIGNED NOT NULL,
  id_materia INT UNSIGNED NOT NULL,
  memo TEXT NOT NULL,
  status TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id_particulares),
  KEY idx_particulares_estudiante (id_estudiante),
  KEY idx_particulares_tutor (id_tutor),
  KEY idx_particulares_materia (id_materia),
  CONSTRAINT fk_particulares_estudiante FOREIGN KEY (id_estudiante) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_particulares_tutor FOREIGN KEY (id_tutor) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_particulares_subject FOREIGN KEY (id_materia) REFERENCES subject(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE carrera_materia (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  idcarrera INT UNSIGNED NOT NULL,
  idmateria INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_carrera_materia (idcarrera, idmateria),
  KEY idx_cm_materia (idmateria),
  CONSTRAINT fk_cm_carrera FOREIGN KEY (idcarrera) REFERENCES escuela(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_cm_materia FOREIGN KEY (idmateria) REFERENCES subject(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
