| --- | --- | --- | --- |
| GET | `/v1/reservations` | `index()` | List paginated reservations |
| POST | `/v1/reservations` | `store()` | Create new reservation |
| GET | `/v1/reservations/{id}` | `show()` | Get specific reservation |
| PUT | `/v1/reservations/{id}` | `update()` | Update existing reservation |
| DELETE | `/v1/reservations/{id}` | `destroy()` | Delete reservation |

### API Flow Diagram

**Sources:** [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php22-59]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php97-132]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php167-204]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php237-279]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php313-351]()

Business Rules and Validation
-----------------------------

The reservation system enforces several critical business rules through the `ReservationRequest` validation class and controller logic.

### Business Rules Implementation

| Rule | Implementation | Validation Location |
| --- | --- | --- |
| Maximum 3 reservations per day | Custom validation rule | `ReservationRequest` |
| Operating hours 08:00-21:00 | Time range validation | `ReservationRequest` |
| No double booking | Unique constraint validation | `ReservationRequest` |
| Valid member reference | Foreign key constraint | `ReservationRequest` |
| Valid court reference | Foreign key constraint | `ReservationRequest` |

### Validation Flow

**Sources:** [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php134-164]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php281-310]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php147-153]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php293-299]()

Data Operations
---------------

### Pagination Implementation

The `index()` method implements pagination with 15 reservations per page, returning pagination metadata alongside the reservation data.

**Sources:** [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php61-86]()

### CRUD Operations

| Operation | Method | Model Action | Response Code |
| --- | --- | --- | --- |
| Create | `store()` | `Reservation::create()` | 201 Created |
| Read (single) | `show()` | `Reservation::findOrFail()` | 200 OK |
| Read (list) | `index()` | `Reservation::paginate()` | 200 OK |
| Update | `update()` | `reservation->update()` | 200 OK |
| Delete | `destroy()` | `reservation->delete()` | 200 OK |

**Sources:** [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php138]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php209]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php64]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php286]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php356]()

Error Handling
--------------

The controller implements comprehensive error handling for different failure scenarios with appropriate HTTP status codes.

### Error Response Patterns

| Exception Type | HTTP Code | Response Pattern | Controller Methods |
| --- | --- | --- | --- |
| `ValidationException` | 422 | Validation errors array | `store()`, `update()` |
| `ModelNotFoundException` | 404 | Resource not found | All methods |
| `Exception` | 500 | Internal server error | All methods |
| Empty result | 404 | No reservations available | `index()` |

### Exception Handling Flow

**Sources:** [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php147-153]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php153-158]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php158-163]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php215-220]() [club\_cms/app/Http/Controllers/Api/v1/ReservationController.php220-225]()