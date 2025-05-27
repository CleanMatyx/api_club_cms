### Service Provider Setup

The OAuth2 system is configured in the `AppServiceProvider` class with specific token expiration settings and permission scopes:

Sources: [club\_cms/app/Providers/AppServiceProvider.php21-31]()

### Authentication Guards Configuration

The system uses the `api` guard with Passport driver for API authentication:

| Guard | Driver | Provider | Purpose |
| --- | --- | --- | --- |
| `web` | `session` | `users` | Web interface (not used in API) |
| `api` | `passport` | `users` | API authentication |

Sources: [club\_cms/config/auth.php37-46]() [club\_cms/config/passport.php16]()

User Model and Roles
--------------------

### User Model Structure

The `User` model extends Laravel's `Authenticatable` class and includes the `HasApiTokens` trait for Passport integration:

Sources: [club\_cms/app/Models/User.php11-49]() [club\_cms/database/factories/UserFactory.php32]()

### Role Definitions

The system implements two distinct user roles:

| Role | Description | Default User |
| --- | --- | --- |
| `admin` | Full system access, can manage users, sports, and courts | `admin@admin.com` |
| `user` | Limited access, can manage members and reservations | Factory default |

Sources: [club\_cms/database/seeders/UsersSeeder.php16-22]() [club\_cms/database/factories/UserFactory.php49-54]()

Authorization System
--------------------

### Role-Based Access Control Middleware

The `CheckRole` middleware implements role-based authorization by validating user roles against required permissions:

Sources: [club\_cms/app/Http/Middleware/CheckRole.php16-26]() [club\_cms/bootstrap/app.php16-18]()

### Permission Matrix

The role-based access control system enforces the following permissions:

| Resource | Admin Access | User Access |
| --- | --- | --- |
| Users CRUD | ✅ Full access | ❌ No access |
| Sports CRUD | ✅ Full access | 📖 Read only |
| Courts CRUD | ✅ Full access | 📖 Read only |
| Members CRUD | ✅ Full access | ✅ Full access |
| Reservations CRUD | ✅ Full access | ✅ Full access |
| Authentication | ✅ Available | ✅ Available |

Sources: Based on role middleware implementation in [club\_cms/app/Http/Middleware/CheckRole.php19]()

Token Management
----------------

### Token Types and Expiration

The Passport configuration defines three types of tokens with different expiration policies:

Sources: [club\_cms/app/Providers/AppServiceProvider.php24-26]()

### OAuth Key Management

The system uses file-based OAuth key storage with configurable paths:

| Configuration | Default Path | Environment Variable |
| --- | --- | --- |
| OAuth Keys Directory | `/app/secrets/oauth` | - |
| Private Key | - | `PASSPORT_PRIVATE_KEY` |
| Public Key | - | `PASSPORT_PUBLIC_KEY` |
| Database Connection | default | `PASSPORT_CONNECTION` |

Sources: [club\_cms/app/Providers/AppServiceProvider.php23]() [club\_cms/config/passport.php29-44]()

Security Configuration
----------------------

### Authentication Guard Configuration

The authentication system uses Laravel's guard mechanism with Passport integration:

Sources: [club\_cms/config/auth.php15-68]() [club\_cms/app/Models/User.php47]()

### Middleware Stack

The authentication and authorization flow uses a layered middleware approach:

1. **`auth:api`** - Validates Bearer token using Passport
2. **`role:admin`** - Restricts access to admin users only
3. **`role:admin,user`** - Allows both admin and user access

Sources: [club\_cms/bootstrap/app.php17]() [club\_cms/app/Http/Middleware/CheckRole.php16]()