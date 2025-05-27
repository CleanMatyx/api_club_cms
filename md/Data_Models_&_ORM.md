This document covers the Eloquent models, database relationships, and data layer architecture that form the core data persistence layer of the Club CMS system. It explains how Laravel's ORM handles entity relationships, mass assignment security, and data transformation for API responses.

For information about request validation and business rules, see [Request Validation Framework](). For API endpoints that utilize these models, see [API Routes & Structure]().

## Data Layer Architecture Overview

The Club CMS uses Laravel's Eloquent ORM to manage data persistence and relationships. The data layer consists of four primary domain entities that represent the core business logic of a sports club management system.

**Sources:** [club\_cms/app/Models/Member.php1-48]() [club\_cms/app/Models/Sport.php1-34]() [club\_cms/app/Models/Court.php1-42]() [club\_cms/app/Models/Reservation.php1-51]() [club\_cms/app/Http/Resources/MemberResource.php1-24]() [club\_cms/database/seeders/MembersSeeder.php1-20]()

## Core Model Classes and Database Mapping

Each Eloquent model maps to a specific database table and defines the fillable attributes for mass assignment protection:

| Model Class | Table Name | Key Attributes | Factory Support |
| --- | --- | --- | --- |
| `Sport` | `sports` | `name`, `description` | ✅ |
| `Court` | `courts` | `sport_id`, `name`, `location` | ✅ |
| `Member` | `members` | `name`, `email`, `phone`, `user_id`, `membership_date`, `status`, `role` | ✅ |
| `Reservation` | `reservations` | `member_id`, `court_id`, `date`, `hour` | ✅ |

All models use the `HasFactory` trait for test data generation and follow Laravel's naming conventions for table mapping.

**Sources:** [club\_cms/app/Models/Sport.php18-26]() [club\_cms/app/Models/Court.php18-27]() [club\_cms/app/Models/Member.php19-32]() [club\_cms/app/Models/Reservation.php18-28]()

## Eloquent Relationships Architecture

The system implements a hierarchical relationship structure where sports contain courts, members make reservations, and reservations link members to courts:

**Sources:** [club\_cms/app/Models/Sport.php31-33]() [club\_cms/app/Models/Court.php32-41]() [club\_cms/app/Models/Member.php37-46]() [club\_cms/app/Models/Reservation.php33-49]()

## Mass Assignment Protection

All models implement Laravel's mass assignment protection through the `$fillable` property, explicitly defining which attributes can be mass-assigned during model creation or updates:

### Sport Model Fillable Attributes

### Court Model Fillable Attributes

### Member Model Fillable Attributes

### Reservation Model Fillable Attributes

**Sources:** [club\_cms/app/Models/Sport.php23-26]() [club\_cms/app/Models/Court.php23-27]() [club\_cms/app/Models/Member.php24-32]() [club\_cms/app/Models/Reservation.php23-28]()

## API Resource Transformation

The system uses Laravel API Resources to transform model data for consistent API responses. Currently, only the `Member` model has a dedicated resource class:

The `MemberResource` class selectively exposes only public-safe attributes (`id`, `name`, `membership_date`, `status`) while filtering out sensitive information like `email`, `phone`, and `user_id`.

**Sources:** [club\_cms/app/Http/Resources/MemberResource.php16-23]()

## Database Seeding Infrastructure

The system includes factory-based seeding for generating test data. The `MembersSeeder` demonstrates the pattern used across the application:

The seeder creates 25 active members and 5 inactive members using factory states, providing realistic test data for development and testing environments.

**Sources:** [club\_cms/database/seeders/MembersSeeder.php14-19]()

## Model Usage Patterns

All models follow consistent Laravel conventions:

* Use the `HasFactory` trait for test data generation
* Implement explicit table name mapping via `$table` property
* Define mass assignment protection through `$fillable` arrays
* Use descriptive relationship method names that clearly indicate the relationship type
* Follow Laravel naming conventions for foreign key relationships (e.g., `sport_id` references `sports.id`)

The models form a complete data layer that supports the club management domain, enabling operations like creating reservations that link members to courts, organizing courts by sport type, and maintaining member information with proper access control.

**Sources:** [club\_cms/app/Models/Member.php13-14]() [club\_cms/app/Models/Court.php12-13]() [club\_cms/app/Models/Sport.php12-13]() [club\_cms/app/Models/Reservation.php12-13]()

Auto-refresh not enabled yet

Try DeepWiki on your private codebase with [Devin]()

### On this page

* [Data Models & ORM]()
* [Data Layer Architecture Overview]()
* [Core Model Classes and Database Mapping]()
* [Eloquent Relationships Architecture]()
* [Mass Assignment Protection]()
* [Sport Model Fillable Attributes]()
* [Court Model Fillable Attributes]()
* [Member Model Fillable Attributes]()
* [Reservation Model Fillable Attributes]()
* [API Resource Transformation]()
* [Database Seeding Infrastructure]()
* [Model Usage Patterns]()