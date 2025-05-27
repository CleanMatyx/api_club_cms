## Documentation Architecture

The Club CMS uses the L5 Swagger package to generate OpenAPI 3.0 documentation from PHP annotations. The documentation system consists of centralized schema definitions, controller annotations, and automated generation.

**Sources:** [club\_cms/app/OpenApi/Schema.php1-333]() [club\_cms/config/l5-swagger.php1-321]()

## Configuration and Setup

The documentation system is configured through the `l5-swagger.php` configuration file, which defines scanning paths, UI settings, and generation options.

### Key Configuration Settings

| Setting | Value | Purpose |
| --- | --- | --- |
| `routes.api` | `api/documentation` | Swagger UI access route |
| `paths.docs_json` | `api-docs.json` | Generated JSON specification filename |
| `paths.annotations` | `app/Http/Controllers/Api/v1`, `app/OpenApi` | Directories to scan for annotations |
| `generate_always` | Environment controlled | Whether to regenerate docs on each request |
| `format_to_use_for_docs` | `json` | Primary documentation format |

The system scans two primary locations for OpenAPI annotations:

* Controller files in [app/Http/Controllers/Api/v1]()
* Schema definitions in [app/OpenApi]()

**Sources:** [club\_cms/config/l5-swagger.php14-50]() [club\_cms/config/l5-swagger.php240-247]()

## API Information and Security Schema

The main API information is defined in the `Schema` class, which establishes the base documentation structure.

### API Metadata

The API uses JWT Bearer token authentication through Laravel Passport, defined in the security scheme at [app/OpenApi/Schema.php23-28]()

**Sources:** [club\_cms/app/OpenApi/Schema.php8-28]()

## Schema Definitions

The system defines comprehensive schemas for all API entities, organized into request and response schemas.

### Entity Schema Structure

**Sources:** [club\_cms/app/OpenApi/Schema.php33-333]()

### Core Entity Schemas

#### Member Schema

The member schemas include comprehensive field definitions with validation rules and relationships:

| Field | Type | Required | Validation |
| --- | --- | --- | --- |
| `name` | string | Yes | - |
| `email` | string | No | email format |
| `phone` | string | No | - |
| `membership_date` | string | Yes | date format |
| `status` | string | Yes | enum: active, inactive, suspended |

The `MemberResource` schema includes additional computed fields like `has_system_access` and optional `user` relationship data at [app/OpenApi/Schema.php78-87]()

#### Reservation Schema

Reservation schemas enforce business rules through their structure:

| Field | Type | Required | Notes |
| --- | --- | --- | --- |
| `member_id` | integer | Yes | Foreign key to members |
| `court_id` | integer | Yes | Foreign key to courts |
| `date` | string | Yes | date format |
| `hour` | string | Yes | time format |

**Sources:** [club\_cms/app/OpenApi/Schema.php57-89]() [club\_cms/app/OpenApi/Schema.php112-134]()

## Error Response Schemas

The system defines standardized error response formats for consistent API behavior.

### Error Schema Types

The validation error schema provides detailed field-level error messages, supporting business rule enforcement defined in the request validation layer.

**Sources:** [club\_cms/app/OpenApi/Schema.php228-275]()

## Controller Documentation Integration

Controllers integrate with the schema system through OpenAPI annotations that reference the centralized schema definitions.

### Authentication Controller Example

The `AuthController` demonstrates the annotation pattern used throughout the API:

Each endpoint uses the `security={{"bearerAuth":{}}}` annotation to require JWT authentication, except for public authentication endpoints.

**Sources:** [club\_cms/app/Http/Controllers/Api/v1/AuthController.php24-48]() [club\_cms/app/Http/Controllers/Api/v1/AuthController.php85-106]()

## Documentation Access and Usage

The generated documentation is accessible through multiple interfaces:

### Access Methods

| Interface | URL | Purpose |
| --- | --- | --- |
| Swagger UI | `/api/documentation` | Interactive documentation browser |
| JSON Spec | `/docs/api-docs.json` | Machine-readable OpenAPI specification |
| YAML Spec | `/docs/api-docs.yaml` | Alternative specification format (if enabled) |

The Swagger UI provides an interactive interface for exploring endpoints, testing requests, and understanding response schemas. The JSON specification enables integration with API testing tools and code generation utilities.

**Sources:** [club\_cms/config/l5-swagger.php14-16]() [club\_cms/config/l5-swagger.php30-41]()

## Schema Validation and Business Rules

The documentation schemas align with the application's validation rules, providing accurate representations of API behavior and constraints.

### Validation Integration

The schemas document validation rules enforced by the request validation system, including:

* Required field constraints
* Format validations (email, date, time)
* Enumeration restrictions (status values, roles)
* Business rule violations (reservation limits, time restrictions)

This integration ensures the documentation accurately reflects the API's actual behavior and validation requirements.

**Sources:** [club\_cms/app/OpenApi/Schema.php244-275]()

Auto-refresh not enabled yet

Try DeepWiki on your private codebase with [Devin]()

### On this page

* [API Documentation & Schemas]()
* [Documentation Architecture]()
* [Configuration and Setup]()
* [Key Configuration Settings]()
* [API Information and Security Schema]()
* [API Metadata]()
* [Schema Definitions]()
* [Entity Schema Structure]()
* [Core Entity Schemas]()
* [Member Schema]()
* [Reservation Schema]()
* [Error Response Schemas]()
* [Error Schema Types]()
* [Controller Documentation Integration]()
* [Authentication Controller Example]()
* [Documentation Access and Usage]()
* [Access Methods]()
* [Schema Validation and Business Rules]()
* [Validation Integration]()