| --- | --- |
| Image | `caddy:2.7.4-alpine` |
| Container Name | `club_cms_webserver` |
| Exposed Ports | 80 (HTTP), 443 (HTTPS) |
| Restart Policy | `unless-stopped` |

**Volume Mounts:**

* `./club_cms:/srv` - Application code access
* `caddy_data:/data` - Caddy data persistence
* `caddy_config:/config` - Caddy configuration persistence
* `./Caddyfile:/etc/caddy/Caddyfile` - Caddy server configuration

**Sources:** [docker-compose.yml2-18]()

### PHP Application Container

The `club_cms_php` container runs PHP 8.2-FPM with Laravel and all necessary PHP extensions for the Club CMS application.

**Sources:** [dockerfile1-72]() [docker-compose.yml19-30]()

### Database Container (MySQL)

The `club_cms_database` container provides MySQL 8.1.0 with the `club_cms_db` database pre-configured.

| Configuration | Value |
| --- | --- |
| Image | `mysql:8.1.0` |
| Container Name | `club_cms_database` |
| Database Name | `club_cms_db` |
| External Port | 3307 → 3306 |
| Root Password | `dwes` |

**Sources:** [docker-compose.yml31-44]()

Installation Steps
------------------

### Prerequisites

Ensure Docker and Docker Compose are installed on your system.

### 1. Clone and Setup

### 2. Build and Start Containers

### 3. Application Setup

**Sources:** [docker-compose.yml1-54]() [dockerfile55-66]()

Environment Configuration
-------------------------

### Database Connection

The Laravel application connects to the MySQL container using the internal Docker network. Key database configuration from [club\_cms/.env24-29]():

| Variable | Value | Description |
| --- | --- | --- |
| `DB_CONNECTION` | `mysql` | Database driver |
| `DB_HOST` | `database` | Container hostname |
| `DB_PORT` | `3306` | Internal MySQL port |
| `DB_DATABASE` | `club_cms_db` | Database name |
| `DB_USERNAME` | `root` | Database user |
| `DB_PASSWORD` | `dwes` | Database password |

### Application Configuration

Critical application settings from [club\_cms/.env1-11]():

```
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=https://matiasborra.es/club
APP_LOCALE=es
APP_TIMEZONE=Europe/Madrid

```

### Authentication Keys

Laravel Passport OAuth2 authentication uses RSA key pairs defined in [club\_cms/.env71-137]() These keys enable JWT token generation and validation for API authentication.

**Sources:** [club\_cms/.env1-137]()

Network Architecture
--------------------

The `laravel_network` Docker network enables internal communication between containers while exposing only necessary ports to the host system:

* **Port 80/443**: HTTP/HTTPS traffic to Caddy
* **Port 3307**: Direct MySQL access for development tools
* **Internal**: PHP-FPM and MySQL communicate over internal network

**Sources:** [docker-compose.yml17-18]() [docker-compose.yml29-30]() [docker-compose.yml43-44]() [docker-compose.yml52-53]()

Volume Management
-----------------

### Persistent Data Volumes

| Volume Name | Purpose | Mount Point |
| --- | --- | --- |
| `caddy_data` | Caddy runtime data and certificates | `/data` |
| `caddy_config` | Caddy configuration cache | `/config` |
| `db_data` | MySQL database files | `/var/lib/mysql/` |

### Application Code Volume

The `./club_cms` directory is mounted to `/srv` in both the Caddy and PHP containers, enabling:

* Live code reloading during development
* Shared access to Laravel application files
* Persistent application state

**Sources:** [docker-compose.yml10-12]() [docker-compose.yml27-28]() [docker-compose.yml41-42]() [docker-compose.yml46-50]()

Container Dependencies and Startup Order
----------------------------------------

The `depends_on` configuration ensures proper startup sequence:

1. **database** container starts first
2. **php** container starts after database is ready
3. **webserver** container starts last, after both dependencies are available

This prevents connection errors during container initialization and ensures the application can establish database connections immediately.

**Sources:** [docker-compose.yml14-16]()