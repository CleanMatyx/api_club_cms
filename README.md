# Club CMS

Un sistema de gestión de contenido (CMS) para club deportivo, construido con **Laravel 12.x**, **PHP 8.2**, **MySQL 8.1** y desplegado mediante **Docker** + **Docker Compose**, con **Caddy** como servidor web.

## Tabla de contenidos
- [Descripción del proyecto](#-descripción-del-proyecto)
- [Funcionalidades](#-funcionalidades)
- [Lógica de negocio](#-lógica-de-negocio)
- [Instalación y puesta en marcha](#-instalación-y-puesta-en-marcha)
- [Ejemplos de uso de la API](#-ejemplos-de-uso-de-la-api)
- [Comandos útiles para desarrollo](#️-comandos-útiles-para-desarrollo)
- [Documentación y herramientas](#-documentación-y-herramientas)
- [Autenticación y autorización](#-autenticación-y-autorización)
- [Arquitectura técnica](#️-arquitectura-técnica)
- [Contribución](#-contribución)
- [Contacto](#-contacto)

## Descripción del proyecto

Este proyecto proporciona una **API REST** para la gestión completa de un club deportivo. Consta de dos áreas principales:

- **Panel de gestión:** Control de acceso, usuarios, permisos y administración general de la aplicación.
- **Gestión de club polideportivo:** CRUD de socios, pistas, reservas, y lógica de negocio para el sistema de reservas.

## Funcionalidades

### Gestión de Usuarios
- **CRUD de Usuarios:** Registro, login, logout y gestión de roles/permisos.
- Autenticación OAuth2 con Laravel Passport
- Roles: `admin` y `user`

### Gestión de Club
- **CRUD de Deportes:** (Tenis, Pádel, Fútbol, Baloncesto...)
- **CRUD de Pistas:** Cada pista asociada a un único deporte
- **CRUD de Socios:** Gestión de datos de los miembros del club
- **CRUD de Reservas:** Un socio reserva una pista para una franja horaria
- **Buscador de Pistas:** Dada una fecha, deporte y socio, devuelve las pistas disponibles
- **Listado de Reservas del Día:** Dada una fecha, devuelve todas las reservas confirmadas

## Lógica de negocio

- Reservas disponibles entre las **08:00** y las **22:00** de lunes a domingo en franjas de 1 hora
- No se permiten dos reservas de la misma pista en la misma franja horaria
- Un socio no puede reservar más de 2 pistas simultáneamente y un máximo de 3 reservas por día

## Instalación y puesta en marcha

### Prerequisitos
- Docker ≥ 20.10
- Docker Compose ≥ 1.29
- Git ≥ 2.30

### Pasos de instalación

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/CleanMatyx/api_club_cms.git
   cd api_club_cms
   ```

2. **Arranca los contenedores:**
   ```bash
   docker compose up -d
   ```
   
   Servicios levantados:
   - `club_cms_webserver` (Caddy puertos 80/443)
   - `club_cms_php` (PHP-FPM 8.2)
   - `club_cms_database` (MySQL 8.1)

3. **Acceder al contenedor PHP:**
   ```bash
   docker compose exec -it club_cms_php sh
   ```

4. **Instalar dependencias:**
   ```bash
   composer install
   ```

5. **Crear y poblar la base de datos:**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Configurar Laravel Passport:**
   ```bash
   php artisan passport:client --personal
   ```
   - Presiona Enter para aceptar el nombre por defecto "Laravel"
   - Presiona Enter nuevamente para el provider "users"

### Verificación de la instalación

- **API Documentation:** http://localhost/api/documentation
- **Health Check:** Hacer una petición GET a http://localhost/api/v1/sports

## Ejemplos de uso de la API

### Base URL
```
http://localhost/api/v1
```

### 1. Autenticación

<details>
<summary>Ver ejemplos de autenticación</summary>

#### Iniciar sesión
```bash
POST /auth/login
Content-Type: application/json

{
  "email": "admin@admin.com",
  "password": "admin"
}
```

**Respuesta:**
```json
{
  "ok": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

#### Cerrar sesión
```bash
POST /auth/logout
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "ok": true,
  "message": "Sesión cerrada correctamente"
}
```

</details>

### 2. Gestión de Deportes

<details>
<summary>Ver ejemplos de gestión de deportes</summary>

#### Listar deportes
```bash
GET /sports
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "ok": true,
  "sports": [
    {
      "id": 1,
      "name": "Tenis",
      "description": "Deporte de raqueta"
    },
    {
      "id": 2,
      "name": "Pádel",
      "description": "Deporte de pala"
    }
  ],
  "page": 1,
  "total_pages": 1,
  "total_sports": 2
}
```

#### Crear deporte (Solo Admins)
```bash
POST /sports
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Fútbol",
  "description": "Deporte de equipo con balón"
}
```

</details>

### 3. Gestión de Pistas

<details>
<summary>Ver ejemplos de gestión de pistas</summary>

#### Listar pistas
```bash
GET /courts
Authorization: Bearer {token}
```

#### Crear pista (Solo Admins)
```bash
POST /courts
Authorization: Bearer {token}
Content-Type: application/json

{
  "sport_id": 1,
  "name": "Pista Central",
  "location": "Zona Norte del club"
}
```

</details>

### 4. Gestión de Socios

<details>
<summary>Ver ejemplos de gestión de socios</summary>

#### Listar socios
```bash
GET /members
Authorization: Bearer {token}
```

#### Crear socio (Solo Admins)
```bash
POST /members
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@email.com",
  "phone": "+34 123 456 789",
  "membership_date": "2024-01-01",
  "status": "active"
}
```

</details>

### 5. Gestión de Reservas

<details>
<summary>Ver ejemplos de gestión de reservas</summary>

#### Crear reserva
```bash
POST /reservations
Authorization: Bearer {token}
Content-Type: application/json

{
  "member_id": 1,
  "court_id": 1,
  "date": "2024-12-25",
  "hour": "14:00"
}
```

#### Listar reservas
```bash
GET /reservations
Authorization: Bearer {token}
```

</details>

### 6. Búsqueda de disponibilidad

<details>
<summary>Ver ejemplo de búsqueda de disponibilidad</summary>

#### Buscar pistas disponibles
```bash
POST /courts/search?sport_name=tenis&date=25/12/2024&member_id=1
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "ok": true,
  "available_hours": [
    {
      "id": 1,
      "name": "Pista Central",
      "hours_free": [8, 9, 10, 11, 15, 16, 17],
      "hours_reserved": [12, 13, 14]
    }
  ]
}
```

</details>

## Comandos útiles para desarrollo

### Shells de contenedores
```bash
# Acceder al contenedor PHP
docker compose exec -it club_cms_php sh
```
```bash
# Acceder a MySQL
docker compose exec -it club_cms_database mysql -u root -p
```

### Comandos Artisan
```bash
# Limpiar rutas
docker compose exec -it club_cms_php php artisan route:clear
```
```bash
# Listar rutas
docker compose exec -it club_cms_php php artisan route:list
```
```bash
# Refrescar base de datos con seeders
docker compose exec -it club_cms_php php artisan migrate:fresh --seed
```

### Parar y limpiar
```bash
# Detener contenedores
docker-compose down
```
```bash
# Detener y eliminar volúmenes
docker-compose down -v
```

## Documentación y herramientas

### Documentación de la API
- **Swagger UI:** http://localhost/api/documentation
- **Especificación OpenAPI:** `storage/api-docs/api-docs.json`

### Herramientas de prueba
- **Colección Postman:** `ClubCMS.postman_collection.json`
- **Esquema ER:** `er-diagram.pdf`

### Datos de prueba incluidos
El sistema incluye seeders con datos de ejemplo:
- Usuario admin: `admin@admin.com` / `admin`
- Deportes: Tenis, Pádel, Fútbol, Baloncesto
- Pistas de ejemplo para cada deporte
- Socios de prueba
- Reservas de ejemplo

## Autenticación y autorización

### Tipos de usuario
- **Admin:** Acceso completo a todas las funcionalidades
- **User:** Acceso al CRUD de miembros, reservas, obtener pistas y deportes.

### Endpoints protegidos
- Todos los endpoints requieren autenticación excepto `/auth/login` y `/auth/register`
- Endpoints de creación/edición/eliminación requieren rol `admin`
- Las reservas pueden ser creadas por cualquier usuario autenticado

## Arquitectura técnica

### Stack tecnológico
- **Backend:** Laravel 12.x con PHP 8.2
- **Base de datos:** MySQL 8.1
- **Autenticación:** Laravel Passport (OAuth2)
- **Contenedores:** Docker + Docker Compose
- **Servidor web:** Caddy
- **Documentación:** Swagger/OpenAPI

### Estructura del proyecto
```
├── Caddyfile                    # Configuración Caddy
├── dockerfile                  # Imagen Docker de la aplicación
├── docker-compose.yml          # Orquestación de servicios
└── club_cms/                   # Aplicación Laravel
    ├── app/
    │   ├── Http/Controllers/    # Controladores de la API
    │   ├── Models/             # Modelos Eloquent
    │   └── OpenApi/            # Anotaciones Swagger
    ├── database/
    │   ├── migrations/         # Migraciones de BD
    │   └── seeders/           # Datos de prueba
    ├── routes/api.php         # Rutas de la API
    └── storage/api-docs/      # Documentación generada
```

## Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## Contacto

- **GitHub:** [CleanMatyx](https://github.com/CleanMatyx)
- **Email:** [mtsbrr07@gmail.com](mailto:mtsbrr07@gmail.com)

