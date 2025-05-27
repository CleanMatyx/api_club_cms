
The API routes are organized into three distinct security layers based on access requirements:

**Sources:** [club\_cms/routes/api.php12-33]()

Security Middleware Structure
-----------------------------

The API implements a layered security approach using Laravel Passport authentication and custom role-based middleware:

### Access Control Matrix

| Resource | GET | POST | PUT | DELETE | Special |
| --- | --- | --- | --- | --- | --- |
| `/auth/*` | - | Public | - | - | logout: Auth Required |
| `/users` | Admin | Admin | Admin | Admin | - |
| `/sports` | Auth | Admin | Admin | Admin | - |
| `/courts` | Auth | Admin | Admin | Admin | search: Auth |
| `/members` | Auth | Auth | Auth | Auth | - |
| `/reservations` | Auth | Auth | Auth | Auth | - |

**Sources:** [club\_cms/routes/api.php15-33]()

Resource Endpoint Patterns
--------------------------

The API follows RESTful conventions using Laravel's `apiResource` routing. Each business domain follows a consistent pattern:

**Sources:** [club\_cms/routes/api.php16-31]()

Controller Mapping
------------------

Each API endpoint group maps to a specific controller in the `App\Http\Controllers\Api\v1` namespace:

| Route Group | Controller | File Path |
| --- | --- | --- |
| `/auth/*` | `AuthController` | `app/Http/Controllers/Api/v1/AuthController.php` |
| `/users` | `UserController` | `app/Http/Controllers/Api/v1/UserController.php` |
| `/sports` | `SportController` | `app/Http/Controllers/Api/v1/SportController.php` |
| `/courts` | `CourtController` | `app/Http/Controllers/Api/v1/CourtController.php` |
| `/members` | `MemberController` | `app/Http/Controllers/Api/v1/MemberController.php` |
| `/reservations` | `ReservationController` | `app/Http/Controllers/Api/v1/ReservationController.php` |

**Sources:** [club\_cms/routes/api.php4-9]()

Special Endpoints
-----------------

### Authentication Endpoints

The authentication system provides three core endpoints:

* `POST /auth/login` - User authentication with email/password
* `POST /auth/register` - New user registration (system users, not club members)
* `POST /auth/logout` - Token invalidation (requires authentication)

**Sources:** [club\_cms/routes/api.php12-22]() [club\_cms/storage/api-docs/api-docs.json19-182]()

### Court Search Endpoint

A specialized search endpoint for court availability:

* `POST /courts/search` - Complex search with sport, date, and member parameters

This endpoint differs from standard RESTful patterns as it requires POST due to complex query parameters and business logic for availability calculation.

**Sources:** [club\_cms/routes/api.php26]() [club\_cms/storage/api-docs/api-docs.json607-804]()

Route Exception Handling
------------------------

Certain routes are excluded from standard apiResource patterns using Laravel's `except()` method:

This pattern allows for granular access control where read operations are accessible to both admin and user roles, while write operations remain admin-only.

**Sources:** [club\_cms/routes/api.php17-18]()

API Documentation Integration
-----------------------------

The route structure is documented using OpenAPI 3.0 specification stored in [club\_cms/storage/api-docs/api-docs.json]() This file contains:

* Complete endpoint definitions with request/response schemas
* Security requirements for each endpoint
* Validation rules and error responses
* Example requests and responses

The documentation follows the same organizational structure as the route definitions, grouping endpoints by business domain (Auth, Courts, Members, Reservations, Sports, Users).

**Sources:** [club\_cms/storage/api-docs/api-docs.json1-11]()