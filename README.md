# Comunidades v2 - Sistema de Gestión Comunitaria

Sistema web para la gestión de actividades comunitarias, beneficiarios y organizaciones. Desarrollado en PHP procedural con base de datos MySQL y frontend moderno con Tailwind CSS.

## 🚀 Características

- **Sistema de Autenticación**: Login, registro y gestión de usuarios
- **Gestión de Actividades**: CRUD completo para actividades comunitarias
- **Gestión de Beneficiarios**: Registro y seguimiento de beneficiarios
- **Organizaciones**: Administración de organizaciones participantes
- **Dashboard Interactivo**: Estadísticas y vista general del sistema
- **Vista Gantt**: Cronograma visual de actividades
- **Diseño Responsivo**: Interfaz moderna con Tailwind CSS
- **Base de Datos Normalizada**: Esquema robusto con integridad referencial

## 📋 Requisitos del Sistema

- **Servidor Web**: Apache/Nginx
- **PHP**: 8.0 o superior
- **Base de Datos**: MySQL 5.7+ o MariaDB 10.2+
- **Extensiones PHP**: PDO, PDO_MySQL, mbstring

## 🛠️ Instalación

### 1. Clonar o Descargar el Proyecto

```bash
# Si usas Git
git clone <url-del-repositorio>
cd comunidades_v2

# O descargar y extraer el archivo ZIP
```

### 2. Configurar el Servidor Web

Asegúrate de que el directorio del proyecto sea accesible desde tu servidor web (ej: `htdocs/` en XAMPP).

### 3. Crear la Base de Datos

1. Abre phpMyAdmin o tu cliente MySQL preferido
2. Crea una nueva base de datos llamada `comunidades_db`
3. Importa el archivo `db.sql` para crear las tablas
4. Importa el archivo `datos_iniciales.sql` para poblar datos iniciales

```sql
-- Ejecutar en MySQL
CREATE DATABASE comunidades_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE comunidades_db;
-- Importar db.sql
-- Importar datos_iniciales.sql
```

### 4. Configurar la Conexión a la Base de Datos

Edita el archivo `includes/config.php` y actualiza las credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'comunidades_db');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 5. Configurar Permisos (si es necesario)

En sistemas Linux/Unix, asegúrate de que el servidor web tenga permisos de lectura en el directorio del proyecto.

### 6. Acceder al Sistema

1. Abre tu navegador
2. Ve a `http://localhost/comunidades_v2/`
3. Serás redirigido al login

## 👤 Credenciales por Defecto

Después de importar `datos_iniciales.sql`, puedes usar:

- **Email**: `admin@comunidades.com`
- **Contraseña**: `admin123`

## 📁 Estructura del Proyecto

```
comunidades_v2/
├── auth/                    # Sistema de autenticación
│   ├── login.php           # Página de login
│   ├── registro.php        # Página de registro
│   ├── proceso_login.php   # Procesamiento de login
│   ├── store.php           # Procesamiento de registro
│   └── logout.php          # Cierre de sesión
├── includes/               # Archivos de configuración
│   ├── config.php          # Configuración principal
│   ├── header.php          # Header común
│   ├── footer.php          # Footer común
│   ├── nav.php             # Navegación
│   └── functions.php       # Funciones auxiliares
├── actividades/            # CRUD de actividades
├── beneficiarios/          # CRUD de beneficiarios
├── organizaciones/         # CRUD de organizaciones
├── ejes/                   # CRUD de ejes estratégicos
├── componentes/            # CRUD de componentes
├── productos/              # CRUD de productos
├── tipos_actividad/        # CRUD de tipos de actividad
├── tipos_poblacion/        # CRUD de tipos de población
├── estados_actividad/      # CRUD de estados de actividad
├── poligonos/              # CRUD de polígonos
├── usuarios/               # Gestión de usuarios
├── assets/                 # Recursos estáticos
│   ├── css/
│   ├── js/
│   └── img/
├── index.php               # Dashboard principal
├── gantt.php               # Vista de cronograma
├── db.sql                  # Esquema de base de datos
├── datos_iniciales.sql     # Datos iniciales
└── README.md               # Este archivo
```

## 🔧 Configuración Avanzada

### Personalizar Colores

Edita el archivo `includes/header.php` y modifica la configuración de Tailwind:

```javascript
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: {
          50: "#eff6ff",
          500: "#3b82f6", // Color principal
          600: "#2563eb",
          700: "#1d4ed8",
        },
      },
    },
  },
};
```

### Configurar Roles y Permisos

Los roles se gestionan en la tabla `roles` y se asignan a través de `usuario_roles`. Puedes agregar nuevos roles o modificar los existentes.

### Configurar Organizaciones

Las organizaciones se gestionan en la tabla `organizaciones`. Los usuarios pueden estar asociados a una organización específica.

## 🚀 Uso del Sistema

### 1. Autenticación

- Accede a `/auth/login.php` para iniciar sesión
- Usa `/auth/registro.php` para crear nuevas cuentas
- El logout está disponible en el menú de usuario

### 2. Dashboard

- El dashboard principal muestra estadísticas y actividades recientes
- Acceso rápido a las funciones principales
- Navegación intuitiva entre módulos

### 3. Gestión de Actividades

- Crear, editar y eliminar actividades
- Asignar responsables y beneficiarios
- Establecer fechas y ubicaciones
- Seguimiento de estados

### 4. Gestión de Beneficiarios

- Registro completo de beneficiarios
- Vinculación con actividades
- Seguimiento de asistencia
- Información demográfica

### 5. Vista Gantt

- Cronograma visual de actividades
- Filtros por fecha y responsable
- Vista de dependencias

## 🔒 Seguridad

- **Contraseñas**: Hasheadas con `password_hash()`
- **SQL Injection**: Prevenido con PDO y prepared statements
- **XSS**: Prevenido con `htmlspecialchars()`
- **Sesiones**: Gestión segura de sesiones
- **Validación**: Validación tanto en cliente como servidor

## 🐛 Solución de Problemas

### Error de Conexión a la Base de Datos

- Verifica las credenciales en `includes/config.php`
- Asegúrate de que MySQL esté ejecutándose
- Verifica que la base de datos `comunidades_db` exista

### Página en Blanco

- Verifica los logs de error de PHP
- Asegúrate de que todas las extensiones PHP estén habilitadas
- Verifica los permisos de archivos

### Problemas de Estilo

- Verifica la conexión a internet (Tailwind CSS se carga desde CDN)
- Asegúrate de que JavaScript esté habilitado

## 📝 Licencia

Este proyecto es de uso interno para gestión comunitaria.

## 🤝 Contribución

Para contribuir al proyecto:

1. Haz un fork del repositorio
2. Crea una rama para tu feature
3. Realiza tus cambios
4. Envía un pull request

## 📞 Soporte

Para soporte técnico o preguntas sobre el sistema, contacta al equipo de desarrollo.

---

**Desarrollado con ❤️ para la gestión comunitaria**
