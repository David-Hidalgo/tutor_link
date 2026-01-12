-- Seed / sample data for tutor_link (MySQL)
-- Run AFTER tutor_link.sql

SET NAMES utf8mb4;
SET time_zone = "+00:00";

USE tutor_link;

START TRANSACTION;

-- ------------------------------------------------------------
-- escuela
-- ------------------------------------------------------------
INSERT INTO escuela (id, escuela) VALUES
  (1, 'Ingeniería de Sistemas'),
  (2, 'Administración');

-- ------------------------------------------------------------
-- subject
-- ------------------------------------------------------------
INSERT INTO subject (id, materia_des) VALUES
  (1, 'Matemática I'),
  (2, 'Programación I'),
  (3, 'Física I');

-- ------------------------------------------------------------
-- carrera_materia (relación carrera/escuela <-> materia)
-- ------------------------------------------------------------
INSERT INTO carrera_materia (id, idcarrera, idmateria) VALUES
  (1, 1, 1),
  (2, 1, 2),
  (3, 1, 3),
  (4, 2, 1);

-- ------------------------------------------------------------
-- users
-- NOTA: el sistema valida el login contra `users.pass` usando Hash::getHash(..., HASH_KEY).
-- Como HASH_KEY no está aquí, estos passwords son solo para poblar datos.
-- Ajusta `pass` al hash real si vas a autenticar con estas cuentas.
-- ------------------------------------------------------------
INSERT INTO users (id, name, apellido, cedula, telefono, email, carrera, role, date, pass, foto, about) VALUES
  (1, 'Ana', 'Pérez',  'V-10000001', '0414000001', 'ana.student@example.com', 1, 1, NOW(), 'CHANGE_ME_HASH', NULL, NULL),
  (2, 'Luis', 'Gómez',  'V-10000002', '0414000002', 'luis.student@example.com', 2, 1, NOW(), 'CHANGE_ME_HASH', NULL, NULL),
  (3, 'Carlos', 'Díaz', 'V-20000001', '0414000003', 'carlos.tutor@example.com', 1, 2, NOW(), 'CHANGE_ME_HASH', 'public/img/profiles/profile.jfif', 'Tutor de matemáticas y programación.'),
  (4, 'María', 'Ruiz',  'V-20000002', '0414000004', 'maria.tutor@example.com', 2, 2, NOW(), 'CHANGE_ME_HASH', NULL, 'Clases particulares y apoyo académico.');

-- ------------------------------------------------------------
-- tutor / student (tablas redundantes usadas al registrar)
-- ------------------------------------------------------------
INSERT INTO student (id, name, apellido, cedula, email, carrera) VALUES
  (1, 'Ana', 'Pérez',  'V-10000001', 'ana.student@example.com', 1),
  (2, 'Luis', 'Gómez', 'V-10000002', 'luis.student@example.com', 2);

INSERT INTO tutor (id, name, apellido, cedula, email, telefono, foto) VALUES
  (3, 'Carlos', 'Díaz', 'V-20000001', 'carlos.tutor@example.com', '0414000003', 'public/img/profiles/profile.jfif'),
  (4, 'María', 'Ruiz',  'V-20000002', 'maria.tutor@example.com', '0414000004', NULL);

-- ------------------------------------------------------------
-- sessions
-- inicio/final se usan como horas decimales (ej: 8.00, 9.50)
-- ------------------------------------------------------------
INSERT INTO sessions (id_horario, id_tutor, id_materia, dia, inicio, final) VALUES
  (1, 3, 1, 'Lunes',   8.00,  9.00),
  (2, 3, 2, 'Miércoles', 10.00, 11.50),
  (3, 4, 1, 'Martes',  14.00, 15.00),
  (4, 4, 3, 'Jueves',  16.00, 17.00);

-- ------------------------------------------------------------
-- preferencias
-- ------------------------------------------------------------
INSERT INTO preferencias (id_preferencia, id_student, id_materia) VALUES
  (1, 1, 2),
  (2, 1, 1),
  (3, 2, 1);

-- ------------------------------------------------------------
-- enrollment
-- OJO: la columna es id_horaio (tal cual en el código)
-- ------------------------------------------------------------
INSERT INTO enrollment (id, id_estudiante, id_horaio) VALUES
  (1, 1, 1),
  (2, 1, 2),
  (3, 2, 3);

-- ------------------------------------------------------------
-- particulares
-- status: 0=pending, 1=canceled (según uso en controllers/models)
-- ------------------------------------------------------------
INSERT INTO particulares (id_particulares, id_estudiante, id_tutor, id_materia, memo, status) VALUES
  (1, 1, 3, 1, 'Hola, quisiera agendar una clase particular', 0),
  (2, 2, 4, 3, 'Necesito refuerzo para el examen', 0);

-- ------------------------------------------------------------
-- posts (tabla usada por indexModel.php)
-- ------------------------------------------------------------
INSERT INTO posts (id, title, body) VALUES
  (1, 'Bienvenido', 'Primer post de prueba.'),
  (2, 'Aviso', 'Este contenido es de ejemplo.');

COMMIT;
