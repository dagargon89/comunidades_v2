-- Datos iniciales para comunidades_v2
-- Ejecutar después de crear la base de datos con db.sql

USE comunidades_db;

-- Insertar roles básicos
INSERT INTO
    roles (nombre, descripcion)
VALUES (
        'admin',
        'Administrador del sistema con acceso completo'
    ),
    (
        'usuario',
        'Usuario estándar con acceso limitado'
    ),
    (
        'coordinador',
        'Coordinador de actividades y proyectos'
    ),
    (
        'capturista',
        'Capturista de datos y beneficiarios'
    );

-- Insertar organizaciones de ejemplo
INSERT INTO
    organizaciones (nombre)
VALUES (
        'Secretaría de Desarrollo Social'
    ),
    (
        'Instituto Nacional de Desarrollo Social'
    ),
    ('DIF Municipal'),
    ('Organización Civil A.C.'),
    ('Fundación Comunitaria'),
    ('Asociación de Vecinos'),
    ('Centro Comunitario'),
    ('ONG Desarrollo Local');

-- Insertar ejes estratégicos
INSERT INTO
    ejes (nombre, descripcion)
VALUES (
        'Desarrollo Social',
        'Programas y actividades enfocados al desarrollo social de las comunidades'
    ),
    (
        'Educación',
        'Iniciativas educativas y de capacitación'
    ),
    (
        'Salud',
        'Programas de salud y bienestar comunitario'
    ),
    (
        'Empleo',
        'Generación de empleo y desarrollo económico local'
    ),
    (
        'Infraestructura',
        'Mejora de infraestructura comunitaria'
    );

-- Insertar componentes para cada eje
INSERT INTO
    componentes (eje_id, nombre, descripcion)
VALUES (
        1,
        'Inclusión Social',
        'Programas para la inclusión de grupos vulnerables'
    ),
    (
        1,
        'Desarrollo Comunitario',
        'Fortalecimiento del tejido social'
    ),
    (
        2,
        'Educación Básica',
        'Apoyo a la educación básica'
    ),
    (
        2,
        'Capacitación Laboral',
        'Programas de capacitación para el empleo'
    ),
    (
        3,
        'Salud Preventiva',
        'Programas de prevención y promoción de la salud'
    ),
    (
        3,
        'Atención Primaria',
        'Servicios básicos de salud'
    ),
    (
        4,
        'Microempresas',
        'Apoyo a la creación de microempresas'
    ),
    (
        4,
        'Empleo Temporal',
        'Programas de empleo temporal'
    ),
    (
        5,
        'Infraestructura Básica',
        'Mejora de servicios básicos'
    ),
    (
        5,
        'Espacios Públicos',
        'Rehabilitación de espacios públicos'
    );

-- Insertar productos para algunos componentes
INSERT INTO
    productos (
        componente_id,
        tipo_producto,
        nombre,
        descripcion
    )
VALUES (
        1,
        'Programa',
        'Programa de Inclusión Social',
        'Programa integral para la inclusión de grupos vulnerables'
    ),
    (
        2,
        'Proyecto',
        'Desarrollo Comunitario Participativo',
        'Proyecto de fortalecimiento comunitario'
    ),
    (
        3,
        'Servicio',
        'Apoyo Educativo',
        'Servicios de apoyo a la educación básica'
    ),
    (
        4,
        'Capacitación',
        'Capacitación en Oficios',
        'Capacitación en oficios tradicionales'
    ),
    (
        5,
        'Campaña',
        'Campaña de Salud Preventiva',
        'Campaña de prevención de enfermedades'
    ),
    (
        7,
        'Programa',
        'Programa de Microempresas',
        'Apoyo a la creación y desarrollo de microempresas'
    ),
    (
        9,
        'Proyecto',
        'Mejora de Infraestructura',
        'Proyecto de mejora de infraestructura básica'
    );

-- Insertar tipos de actividad
INSERT INTO
    tipos_actividad (nombre)
VALUES ('Capacitación'),
    ('Taller'),
    ('Sesión Informativa'),
    ('Evento Comunitario'),
    ('Visita Domiciliaria'),
    ('Reunión de Trabajo'),
    ('Evaluación'),
    ('Seguimiento'),
    ('Difusión'),
    ('Coordinación');

-- Insertar tipos de población
INSERT INTO
    tipos_poblacion (nombre)
VALUES ('Niños y Niñas'),
    ('Adolescentes'),
    ('Jóvenes'),
    ('Adultos'),
    ('Adultos Mayores'),
    ('Mujeres'),
    ('Hombres'),
    ('Familias'),
    ('Comunidad General'),
    ('Grupos Vulnerables');

-- Insertar estados de actividad
INSERT INTO
    estados_actividad (nombre)
VALUES ('Programada'),
    ('En Proceso'),
    ('Completada'),
    ('Cancelada'),
    ('Suspendida'),
    ('Pendiente de Aprobación'),
    ('Aprobada'),
    ('Rechazada');

-- Insertar polígonos de ejemplo
INSERT INTO
    poligonos (nombre, descripcion)
VALUES (
        'Zona Centro',
        'Zona centro de la ciudad'
    ),
    (
        'Colonia Norte',
        'Colonia ubicada al norte'
    ),
    (
        'Colonia Sur',
        'Colonia ubicada al sur'
    ),
    (
        'Colonia Este',
        'Colonia ubicada al este'
    ),
    (
        'Colonia Oeste',
        'Colonia ubicada al oeste'
    ),
    (
        'Zona Rural',
        'Zona rural del municipio'
    );

-- Insertar un usuario administrador de ejemplo
-- Contraseña: admin123 (hash generado con password_hash)
INSERT INTO
    usuarios (
        organizacion_id,
        nombre,
        email,
        password,
        apellido_paterno,
        apellido_materno,
        puesto,
        telefono,
        activo
    )
VALUES (
        1,
        'Administrador',
        'admin@comunidades.com',
        '$2y$10$ZEH0ypm/oAR2VAjwNPnfNOuSNtEvGmESoJLPiGxyY/nQj6Ke6ZH/6',
        'Sistema',
        'Admin',
        'Administrador del Sistema',
        '555-0001',
        1
    );

-- Asignar rol de administrador al usuario admin
INSERT INTO usuario_roles (usuario_id, rol_id) VALUES (1, 1);

-- Insertar algunos beneficiarios de ejemplo
INSERT INTO
    beneficiarios (
        nombre,
        apellido_paterno,
        apellido_materno,
        fecha_nacimiento,
        sexo,
        telefono,
        email,
        escolaridad,
        ocupacion,
        colonia,
        calle_numero,
        codigo_postal,
        municipio,
        estado,
        capturista_id
    )
VALUES (
        'María',
        'García',
        'López',
        '1985-03-15',
        'Femenino',
        '555-1001',
        'maria.garcia@email.com',
        'Licenciatura',
        'Empleada',
        'Centro',
        'Av. Principal 123',
        '12345',
        'Ciudad',
        'Estado',
        1
    ),
    (
        'Juan',
        'Pérez',
        'Martínez',
        '1990-07-22',
        'Masculino',
        '555-1002',
        'juan.perez@email.com',
        'Preparatoria',
        'Estudiante',
        'Norte',
        'Calle Norte 456',
        '12346',
        'Ciudad',
        'Estado',
        1
    ),
    (
        'Ana',
        'Rodríguez',
        'González',
        '1978-11-08',
        'Femenino',
        '555-1003',
        'ana.rodriguez@email.com',
        'Secundaria',
        'Ama de casa',
        'Sur',
        'Calle Sur 789',
        '12347',
        'Ciudad',
        'Estado',
        1
    );

-- Insertar algunas actividades de ejemplo
INSERT INTO
    actividades (
        producto_id,
        nombre,
        descripcion,
        tipo_actividad_id,
        tipo_poblacion_id,
        estado_actividad_id,
        fecha_inicio,
        fecha_fin,
        lugar,
        responsable_id,
        responsable_registro_id,
        modalidad,
        estatus,
        meta,
        indicador,
        poligono_id
    )
VALUES (
        1,
        'Taller de Inclusión Social',
        'Taller para promover la inclusión social en la comunidad',
        2,
        10,
        3,
        '2024-01-15 09:00:00',
        '2024-01-15 12:00:00',
        'Centro Comunitario',
        1,
        1,
        'Presencial',
        'Completada',
        'Capacitar a 30 personas',
        '30 personas capacitadas',
        1
    ),
    (
        3,
        'Apoyo Educativo para Niños',
        'Sesiones de apoyo educativo para niños de primaria',
        1,
        1,
        2,
        '2024-01-20 14:00:00',
        '2024-01-20 16:00:00',
        'Escuela Primaria',
        1,
        1,
        'Presencial',
        'En Proceso',
        'Apoyar a 25 niños',
        '25 niños atendidos',
        2
    ),
    (
        4,
        'Capacitación en Carpintería',
        'Curso básico de carpintería para jóvenes',
        1,
        3,
        1,
        '2024-02-01 08:00:00',
        '2024-02-15 17:00:00',
        'Taller Comunitario',
        1,
        1,
        'Presencial',
        'Programada',
        'Capacitar a 15 jóvenes',
        '15 jóvenes capacitados',
        3
    );

-- Vincular beneficiarios con actividades
INSERT INTO
    actividad_beneficiario (
        actividad_id,
        beneficiario_id,
        fecha_asistencia,
        observaciones
    )
VALUES (
        1,
        1,
        '2024-01-15',
        'Asistió puntualmente'
    ),
    (
        1,
        2,
        '2024-01-15',
        'Participó activamente'
    ),
    (
        2,
        3,
        '2024-01-20',
        'Buen desempeño'
    ),
    (
        3,
        1,
        '2024-02-01',
        'Inició capacitación'
    );

-- Mensaje de confirmación
SELECT 'Datos iniciales insertados correctamente' as mensaje;