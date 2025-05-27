----------------------------

### Basic Field Validation

All validation classes follow consistent patterns for common field types and constraints:

| Validation Pattern | Usage | Example Classes |
| --- | --- | --- |
| `required|string|max:255` | Standard text fields | `SportRequest`, `CourtRequest`, `MemberRequest` |
| `nullable|string|max:N` | Optional text fields | `CourtRequest` (location), `SportRequest` (description) |
| `required|integer|exists:table,id` | Foreign key references | `CourtRequest` (sport\_id), `ReservationRequest` (member\_id, court\_id) |
| `required|date` | Date fields | `MemberRequest` (membership\_date), `ReservationRequest` (date) |
| `required|string|in:value1,value2` | Enum-like fields | `MemberRequest` (status) |

### Unique Constraint Handling

The framework implements different uniqueness strategies based on HTTP method:

**Create Operations (POST)**

**Update Operations (PUT/PATCH)**

**Advanced Uniqueness with Rule Class**

Sources: [club\_cms/app/Http/Requests/MemberRequest.php22-42]() [club\_cms/app/Http/Requests/SportRequest.php23-33]()

Business Rules Enforcement
--------------------------

### Reservation Business Logic

The `ReservationRequest` class implements the most complex business rules through the `withValidator()` hook method, which allows custom validation logic after standard field validation:

**Business Hours Validation**
The system enforces operating hours between 08:00 and 21:00 through time comparison logic [club\_cms/app/Http/Requests/ReservationRequest.php60-71]()

**Member Daily Reservation Limit**
Each member is limited to 3 reservations per day, with special handling for update operations to exclude the current reservation from the count [club\_cms/app/Http/Requests/ReservationRequest.php80-92]()

**Court Conflict Prevention**
The system prevents double-booking by checking for existing reservations on the same court at the same date and time [club\_cms/app/Http/Requests/ReservationRequest.php94-107]()

### Search Request Validation

The `SearchRequest` class validates court search parameters with specific requirements for sport name, date, and member identification [club\_cms/app/Http/Requests/SearchRequest.php22-29]()

Sources: [club\_cms/app/Http/Requests/ReservationRequest.php39-108]() [club\_cms/app/Http/Requests/SearchRequest.php1-61]()

Custom Error Messages and Internationalization
----------------------------------------------

### Message Localization Strategy

All validation classes implement Spanish-language error messages through the `messages()` method, providing user-friendly feedback for API consumers:

| Validation Class | Message Categories | Example |
| --- | --- | --- |
| `CourtRequest` | Field requirements, data types, relationships | "El campo deporte es obligatorio." |
| `MemberRequest` | Uniqueness, status values, date formats | "El correo electrónico ya está registrado por otro miembro." |
| `ReservationRequest` | Business rules, time constraints | "El miembro ya tiene el máximo de 3 reservas permitidas para esta fecha." |
| `SportRequest` | Uniqueness, length limits | "Ya existe un deporte con este nombre." |
| `SearchRequest` | Required fields, data types | "El nombre del deporte es obligatorio." |

### Attribute Name Mapping

Some validation classes provide custom attribute names through the `attributes()` method for clearer error messages [club\_cms/app/Http/Requests/SearchRequest.php53-60]():

Sources: [club\_cms/app/Http/Requests/CourtRequest.php36-48]() [club\_cms/app/Http/Requests/ReservationRequest.php113-128]() [club\_cms/app/Http/Requests/SearchRequest.php36-60]()

Authorization and Security
--------------------------

### Request Authorization

All validation classes implement the `authorize()` method, which currently returns `true` for all requests [club\_cms/app/Http/Requests/CourtRequest.php12-15]() This indicates that authorization is handled at the middleware level rather than within individual request classes.

The authorization pattern suggests that:

* Authentication is handled by Laravel Passport middleware (see [Authentication & Authorization]())
* Role-based access control is implemented in route middleware
* Request-level authorization is not used for fine-grained permissions

### Data Integrity Protection

The validation framework provides multiple layers of data integrity protection:

1. **Type Safety**: All inputs are validated for correct data types (`integer`, `string`, `date`)
2. **Relationship Integrity**: Foreign keys are validated using `exists:table,id` rules
3. **Business Rule Compliance**: Custom validation ensures business constraints are met
4. **Length Limits**: String fields have maximum length constraints to prevent database overflow

Sources: [club\_cms/app/Http/Requests/CourtRequest.php12-15]() [club\_cms/app/Http/Requests/MemberRequest.php12-15]() [club\_cms/app/Http/Requests/ReservationRequest.php16-18]()