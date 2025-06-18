-- Inicialización de la base de datos
CREATE DATABASE IF NOT EXISTS taskealo;
USE taskealo;

-- Crear la Tabla de Roles
CREATE TABLE IF NOT EXISTS ROLES (
    IDRol INT AUTO_INCREMENT PRIMARY KEY,
    NombreRol VARCHAR(50) NOT NULL
);

-- Crear la Tabla de Usuarios con Password cifrada
CREATE TABLE IF NOT EXISTS USUARIOS (
    IDUsuario INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Usuario VARCHAR(50) NOT NULL UNIQUE,
    Password VARBINARY(255) NOT NULL,  -- Cifrada
    CURP VARCHAR(18) NOT NULL UNIQUE,
    IDRol INT,
    FOREIGN KEY (IDRol) REFERENCES ROLES(IDRol)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Trigger para cifrar el Password en INSERT
DELIMITER //
CREATE TRIGGER encrypt_password_before_insert
BEFORE INSERT ON USUARIOS
FOR EACH ROW
BEGIN
    SET NEW.Password = AES_ENCRYPT(NEW.Password, '4h8$JzL3m9fHk2@vPqWs6uYtNc0bG1Tx');
END;
//
DELIMITER ;

-- Trigger para cifrar el Password en UPDATE
DELIMITER //
CREATE TRIGGER encrypt_password_before_update
BEFORE UPDATE ON USUARIOS
FOR EACH ROW
BEGIN
    SET NEW.Password = AES_ENCRYPT(NEW.Password, '4h8$JzL3m9fHk2@vPqWs6uYtNc0bG1Tx');
END;
//
DELIMITER ;

-- Crear la Tabla de Grupos
CREATE TABLE IF NOT EXISTS GRUPOS (
    IDGrupo INT AUTO_INCREMENT PRIMARY KEY,
    NombreGrupo VARCHAR(100) NOT NULL
);

-- Crear la Tabla de Materias
CREATE TABLE IF NOT EXISTS MATERIAS (
    IDMateria INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL
);

-- Crear la Tabla de Tareas
CREATE TABLE IF NOT EXISTS TAREAS (
    IDTarea INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(100) NOT NULL,
    Descripcion TEXT,
    IDMateria INT,
    IDAlumno INT,
    FechaCreacion DATE,
    FechaLimite DATE,
    FOREIGN KEY (IDMateria) REFERENCES MATERIAS(IDMateria)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (IDAlumno) REFERENCES USUARIOS(IDUsuario)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Modificar la tabla TAREAS para agregar la columna IDGrupo
ALTER TABLE TAREAS
    ADD IDGrupo INT,
    ADD FOREIGN KEY (IDGrupo) REFERENCES GRUPOS(IDGrupo)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

-- Crear la Tabla de Avisos
CREATE TABLE IF NOT EXISTS AVISOS (
    IDAviso INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(100) NOT NULL,
    Descripcion TEXT,
    IDMaestro INT,
    IDGrupo INT,
    FOREIGN KEY (IDMaestro) REFERENCES USUARIOS(IDUsuario)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (IDGrupo) REFERENCES GRUPOS(IDGrupo)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Crear la Tabla de Grupos_Tareas
CREATE TABLE IF NOT EXISTS GRUPOS_TAREAS (
    IDGrupoTarea INT AUTO_INCREMENT PRIMARY KEY,
    IDGrupo INT,
    IDTarea INT,
    FOREIGN KEY (IDGrupo) REFERENCES GRUPOS(IDGrupo)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (IDTarea) REFERENCES TAREAS(IDTarea)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Crear la Tabla de Grupos_Usuarios
CREATE TABLE IF NOT EXISTS GRUPOS_USUARIOS (
    IDGrupoUsuario INT AUTO_INCREMENT PRIMARY KEY,
    IDGrupo INT NOT NULL,
    IDUsuario INT NOT NULL,
    FOREIGN KEY (IDGrupo) REFERENCES GRUPOS(IDGrupo)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (IDUsuario) REFERENCES USUARIOS(IDUsuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Crear la Tabla de Detalles de Alumnos
CREATE TABLE IF NOT EXISTS DETALLES_ALUMNOS (
    IDDetallesUsuario INT AUTO_INCREMENT PRIMARY KEY,
    IDUsuario INT NOT NULL,
    Matricula VARCHAR(20),     -- Solo para alumnos
    Carrera VARCHAR(100),      -- Solo para alumnos
    FOREIGN KEY (IDUsuario) REFERENCES USUARIOS(IDUsuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Crear la Tabla de Carreras
CREATE TABLE IF NOT EXISTS CARRERAS (
    IDCarrera INT AUTO_INCREMENT PRIMARY KEY,
    NombreCarrera VARCHAR(100) NOT NULL
);

-- Modificar la tabla Detalles de Alumnos para referenciar la tabla CARRERAS
ALTER TABLE DETALLES_ALUMNOS
    ADD IDCarrera INT,
    ADD FOREIGN KEY (IDCarrera) REFERENCES CARRERAS(IDCarrera)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

INSERT INTO ROLES (IDRol, NombreRol)
VALUES (1, 'Alumno'),
(2, 'Maestro'),
(3, 'Administrador');

INSERT INTO USUARIOS (Nombre, Usuario, Password, CURP, IDRol)
VALUES
    ('admin', 'admin', '123', 'admin', (SELECT IDRol FROM ROLES WHERE NombreRol = 'Administrador'));

-- Insertar materias en la tabla MATERIAS
INSERT INTO MATERIAS (Nombre) VALUES
('Calculo Diferencial'),
('Calculo Integral'),
('Álgebra Lineal'),
('Ecuaciones Diferenciales'),
('Programación Orientada a Objetos'),
('Programación II'),
('Organización y Estructura de Datos'),
('Bases de Datos Distribuidas'),
('Ingeniería de Software'),
('Redes de Computadoras'),
('Estadística y Probabilidad'),
('Arquitectura de Computadoras');

-- Insertar carreras en la tabla CARRERAS
INSERT INTO CARRERAS (NombreCarrera) VALUES
('Ingeniería Tecnologías de la Informacion y Comunicaciones'),
('Ingeniería Sistemas Computacionales'),
('Ingeniería Informática'),
('Ingeniería Química'),
('Ingeniería Bioquímica'),
('Ingeniería Industrial'),
('Ingeniería Eléctrica'),
('Ingeniería Electrónica'),
('Ingeniería Mectrónica'),
('Ingeniería Mécanica'),
('Ingeniería Civil'),
('Ingeniería Semiconductores'),
('Ingeniería Gestión Empresarial'),
('Ingeniería Biomedicina');

    -- Índices para optimizar consultas

-- Índices en la Tabla USUARIOS
ALTER TABLE USUARIOS ADD INDEX idx_idrol (IDRol);
ALTER TABLE USUARIOS ADD INDEX idx_curp (CURP);

-- Índices en la Tabla TAREAS
ALTER TABLE TAREAS ADD INDEX idx_idmateria (IDMateria);
ALTER TABLE TAREAS ADD INDEX idx_idalumno (IDAlumno);
ALTER TABLE TAREAS ADD INDEX idx_idgrupo (IDGrupo);
ALTER TABLE TAREAS ADD INDEX idx_fecha_creacion (FechaCreacion);

-- Índices en la Tabla AVISOS
ALTER TABLE AVISOS ADD INDEX idx_idmaestro (IDMaestro);
ALTER TABLE AVISOS ADD INDEX idx_idgrupo (IDGrupo);

-- Índices en la Tabla GRUPOS_TAREAS
ALTER TABLE GRUPOS_TAREAS ADD INDEX idx_idgrupo (IDGrupo);
ALTER TABLE GRUPOS_TAREAS ADD INDEX idx_idtarea (IDTarea);

-- Índices en la Tabla GRUPOS_USUARIOS
ALTER TABLE GRUPOS_USUARIOS ADD INDEX idx_idgrupo (IDGrupo);
ALTER TABLE GRUPOS_USUARIOS ADD INDEX idx_idusuario (IDUsuario);

-- Índices en la Tabla DETALLES_ALUMNOS
ALTER TABLE DETALLES_ALUMNOS ADD INDEX idx_idusuario (IDUsuario);
ALTER TABLE DETALLES_ALUMNOS ADD INDEX idx_idcarrera (IDCarrera);

-- Crear un procedimiento almacenado para desfragmentar tablas
DELIMITER //
CREATE PROCEDURE DESFRAGMENTAR_TABLAS()
BEGIN
    -- Optimizar y analizar tablas
    OPTIMIZE TABLE ROLES;
    OPTIMIZE TABLE USUARIOS;
    OPTIMIZE TABLE GRUPOS;
    OPTIMIZE TABLE MATERIAS;
    OPTIMIZE TABLE TAREAS;
    OPTIMIZE TABLE AVISOS;
    OPTIMIZE TABLE GRUPOS_TAREAS;
    OPTIMIZE TABLE GRUPOS_USUARIOS;
    OPTIMIZE TABLE DETALLES_ALUMNOS;
    OPTIMIZE TABLE CARRERAS;

    -- Analizar índices de cada tabla
    ANALYZE TABLE ROLES;
    ANALYZE TABLE USUARIOS;
    ANALYZE TABLE GRUPOS;
    ANALYZE TABLE MATERIAS;
    ANALYZE TABLE TAREAS;
    ANALYZE TABLE AVISOS;
    ANALYZE TABLE GRUPOS_TAREAS;
    ANALYZE TABLE GRUPOS_USUARIOS;
    ANALYZE TABLE DETALLES_ALUMNOS;
    ANALYZE TABLE CARRERAS;
END //
DELIMITER ;

-- Crear el evento programado
CREATE EVENT IF NOT EXISTS DESFRAGMENTACION_DIARIA
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
    CALL DESFRAGMENTAR_TABLAS();
