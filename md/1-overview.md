The Laravel application implements a versioned REST API structure with comprehensive authentication and authorization:

Sources: [README.md382-390]() [club\_cms/app/Http/Requests/UserRequest.php1-61]()

Business Domain Model
---------------------

The system manages five core entities with specific relationships and business rules:

Sources: [README.md32-39]() [README.md42-44]()

Technology Stack
----------------

| Component | Technology | Version | Purpose |
| --- | --- | --- | --- |
| **Backend Framework** | Laravel | 12.x | REST API development |
| **Runtime** | PHP | 8.2 | Application execution |
| **Database** | MySQL | 8.1 | Data persistence |
| **Authentication** | Laravel Passport | OAuth2 | Token-based auth |
| **Web Server** | Caddy | 2.7.4-alpine | HTTP/HTTPS termination |
| **Containerization** | Docker | - | Application packaging |
| **Orchestration** | Docker Compose | - | Multi-container management |
| **Documentation** | Swagger/OpenAPI | - | API specification |

Sources: [README.md368-374]()

Access Control and Roles
------------------------

The system implements two distinct user roles with different permission levels:

| Role | Access Level | Capabilities |
| --- | --- | --- |
| **admin** | Full system access | User CRUD, Sports CRUD, Courts CRUD, Members CRUD, Reservations CRUD |
| **user** | Operational access | Members CRUD, Reservations CRUD, Sports read-only, Courts read-only |

All endpoints except `/auth/login` and `/auth/register` require authentication. Administrative operations (create/update/delete for Sports and Courts, User management) require `admin` role.

Sources: [README.md357-364]()

API Endpoints Structure
-----------------------

The API follows RESTful conventions with the base URL `/api/v1`:

Sources: [README.md100-299]()

Development and Testing Tools
-----------------------------

The system includes comprehensive development and testing infrastructure:

* **API Documentation**: Swagger UI accessible at `http://localhost/api/documentation`
* **Testing Collection**: `ClubCMS.postman_collection.json` with example requests
* **Database Schema**: `er-diagram.pdf` visual representation
* **Seeded Data**: Pre-populated test data including admin user (`admin@admin.com` / `admin`)

The OpenAPI specification is automatically generated and stored in `storage/api-docs/api-docs.json`.

Sources: [README.md338-353]()

System Integration Points
-------------------------

Key integration and configuration files:

* **Container Configuration**: `docker-compose.yml`, `dockerfile`, `Caddyfile`
* **Application Configuration**: Laravel `.env` file, `routes/api.php`
* **Database Structure**: `database/migrations/`, `database/seeders/`
* **API Documentation**: `app/OpenApi/` annotations, `storage/api-docs/`

The system is designed for easy deployment and development with all dependencies containerized and configuration externalized.

Sources: [README.md376-391]()