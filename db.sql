-- Base de datos: comunidades_db
CREATE DATABASE IF NOT EXISTS comunidades_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE comunidades_db;

-- ========== SISTEMA DE USUARIOS, ROLES Y ORGANIZACIONES ==========
CREATE TABLE organizaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizacion_id INT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    puesto VARCHAR(150),
    telefono VARCHAR(20),
    foto_perfil VARCHAR(255),
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organizacion_id) REFERENCES organizaciones (id) ON DELETE SET NULL
);

CREATE TABLE usuario_roles (
    usuario_id INT NOT NULL,
    rol_id INT NOT NULL,
    PRIMARY KEY (usuario_id, rol_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES roles (id) ON DELETE CASCADE
);

-- ========== JERARQUÍA ESTRATÉGICA ==========
CREATE TABLE ejes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT
);

CREATE TABLE componentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    eje_id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (eje_id) REFERENCES ejes (id) ON DELETE CASCADE
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    componente_id INT NOT NULL,
    tipo_producto VARCHAR(100),
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (componente_id) REFERENCES componentes (id) ON DELETE CASCADE
);

-- ========== CATÁLOGOS Y DESPLEGABLES ==========
CREATE TABLE poligonos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    geojson TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tipos_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE tipos_poblacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE estados_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

-- ========== ACTIVIDADES ==========
CREATE TABLE actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo_actividad_id INT,
    tipo_poblacion_id INT,
    estado_actividad_id INT,
    fecha_inicio DATETIME,
    fecha_fin DATETIME,
    lugar VARCHAR(255),
    responsable_id INT,
    responsable_registro_id INT,
    modalidad VARCHAR(50),
    estatus VARCHAR(50) DEFAULT 'Programada',
    meta TEXT,
    indicador TEXT,
    poligono_id INT,
    grupo VARCHAR(100),
    cantidad_sesiones INT,
    calle VARCHAR(255),
    numero_casa VARCHAR(50),
    colonia VARCHAR(255),
    entre_calles VARCHAR(255),
    google_maps TEXT,
    latitud DECIMAL(10, 7),
    longitud DECIMAL(10, 7),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE,
    FOREIGN KEY (responsable_id) REFERENCES usuarios (id) ON DELETE SET NULL,
    FOREIGN KEY (responsable_registro_id) REFERENCES usuarios (id) ON DELETE SET NULL,
    FOREIGN KEY (poligono_id) REFERENCES poligonos (id),
    FOREIGN KEY (tipo_actividad_id) REFERENCES tipos_actividad (id),
    FOREIGN KEY (tipo_poblacion_id) REFERENCES tipos_poblacion (id),
    FOREIGN KEY (estado_actividad_id) REFERENCES estados_actividad (id)
);

-- ========== BENEFICIARIOS ==========
CREATE TABLE beneficiarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    fecha_nacimiento DATE,
    sexo VARCHAR(50),
    curp VARCHAR(18) UNIQUE,
    telefono VARCHAR(20),
    email VARCHAR(100),
    escolaridad VARCHAR(100),
    ocupacion VARCHAR(100),
    colonia VARCHAR(255),
    calle_numero VARCHAR(255),
    codigo_postal VARCHAR(10),
    municipio VARCHAR(100),
    estado VARCHAR(100),
    organizacion VARCHAR(255),
    cargo VARCHAR(100),
    capturista_id INT,
    clave_actividad_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (capturista_id) REFERENCES usuarios (id) ON DELETE SET NULL,
    FOREIGN KEY (clave_actividad_id) REFERENCES actividades (id) ON DELETE SET NULL
);

-- ========== VINCULACIÓN ACTIVIDAD - BENEFICIARIO ==========
CREATE TABLE actividad_beneficiario (
    actividad_id INT NOT NULL,
    beneficiario_id INT NOT NULL,
    fecha_asistencia DATE NOT NULL,
    observaciones TEXT,
    PRIMARY KEY (
        actividad_id,
        beneficiario_id,
        fecha_asistencia
    ),
    FOREIGN KEY (actividad_id) REFERENCES actividades (id) ON DELETE CASCADE,
    FOREIGN KEY (beneficiario_id) REFERENCES beneficiarios (id) ON DELETE CASCADE
);