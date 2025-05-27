### Courts API Controller Structure

Sources: [club\_cms/app/Http/Controllers/Api/v1/CourtController.php23-539]()

CRUD Operations Implementation
------------------------------

### Court Listing with Pagination

The `index()` method provides paginated court listings with 15 courts per page:

The response includes pagination metadata: `page`, `total_pages`, and `total_courts` alongside the court collection.

Sources: [club\_cms/app/Http/Controllers/Api/v1/CourtController.php65-89]()

### Court Creation and Updates

Both creation and update operations use the `CourtRequest` validation class and follow similar patterns:

Sources: [club\_cms/app/Http/Controllers/Api/v1/CourtController.php142-159]() [club\_cms/app/Http/Controllers/Api/v1/CourtController.php281-304]()

### Court Deletion

The deletion process includes proper error handling for non-existent courts:

Sources: [club\_cms/app/Http/Controllers/Api/v1/CourtController.php346-368]()

Court Availability Search System
--------------------------------

The most complex functionality is the availability search system that integrates with the reservations system to provide real-time court availability.

### Search Request Flow

Sources: [club\_cms/app/Http/Controllers/Api/v1/CourtController.php469-539]()

### Hours Availability Logic

The system calculates availability using a range-based approach:

1. **Available Hours Range**: 8:00 to 21:00 (hours 8-21)
2. **Reserved Hours Query**: `court.reservations().whereDate('date', date).pluck('hour')`
3. **Free Hours Calculation**: `collect(range(8, 21)).diff(reserved).values()`

This provides granular hour-by-hour availability for each court.

Sources: [club\_cms/app/Http/Controllers/Api/v1/CourtController.php498-509]()

Data Model Relationships
------------------------

The Courts system integrates with other entities through well-defined relationships:

These relationships enable the search functionality to cross-reference sports, courts, and existing reservations to calculate real-time availability.

Sources: [club\_cms/app/Http/Controllers/Api/v1/CourtController.php482-509]()

Error Handling and Response Patterns
------------------------------------

The system implements consistent error handling across all operations:

| Error Type | HTTP Status | Response Pattern |
| --- | --- | --- |
| Validation Error | 422 | `ValidationErrorResponse` schema |
| Not Found | 404 | `{"ok": false, "message": "descriptive message"}` |
| Server Error | 500 | `{"ok": false, "message": "generic error message"}` |
| Success | 200/201 | `{"ok": true, "data": {...}}` |

### Exception Handling Implementation

Each controller method wraps operations in try-catch blocks to handle these exception types appropriately.

Sources: [club\_cms/app/Http/Controllers/Api/v1/CourtController.php83-88]() [club\_cms/app/Http/Controllers/Api/v1/CourtController.php210-220]() [club\_cms/app/Http/Controllers/Api/v1/CourtController.php520-537]()

Request Validation and API Resources
------------------------------------

The system uses dedicated classes for request validation and response formatting:

* **`CourtRequest`**: Validates court creation and update requests
* **`SearchRequest`**: Validates availability search parameters
* **`CourtResource`**: Formats court data for API responses

These classes ensure consistent data validation and response formatting across all court operations.

Sources: [club\_cms/app/Http/Controllers/Api/v1/CourtController.php7]() [club\_cms/app/Http/Controllers/Api/v1/CourtController.php10-11]()