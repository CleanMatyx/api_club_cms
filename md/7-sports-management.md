| --- | --- | --- | --- |
| `GET` | `/api/v1/sports` | Authenticated Users | List all sports with pagination |
| `GET` | `/api/v1/sports/{id}` | Authenticated Users | Get specific sport details |
| `POST` | `/api/v1/sports` | Admin Only | Create new sport |
| `PUT` | `/api/v1/sports/{id}` | Admin Only | Update existing sport |
| `DELETE` | `/api/v1/sports/{id}` | Admin Only | Delete sport |

### Endpoint Implementation Details

**Sources:** [club\_cms/app/Http/Controllers/Api/v1/SportController.php60-84]() [club\_cms/app/Http/Controllers/Api/v1/SportController.php136-151]() [club\_cms/app/Http/Controllers/Api/v1/SportController.php193-213]() [club\_cms/app/Http/Controllers/Api/v1/SportController.php272-293]() [club\_cms/app/Http/Controllers/Api/v1/SportController.php335-357]()

Controller Implementation
-------------------------

The `SportController` class implements comprehensive error handling and response formatting:

### Response Structure

All endpoints return a consistent JSON structure:

* **Success responses** include an `ok: true` field and relevant data
* **Error responses** include an `ok: false` field and descriptive error messages
* **Pagination responses** include metadata: `page`, `total_pages`, `total_sports`

### Error Handling

The controller implements three types of exception handling:

**Sources:** [club\_cms/app/Http/Controllers/Api/v1/SportController.php78-83]() [club\_cms/app/Http/Controllers/Api/v1/SportController.php202-212]() [club\_cms/app/Http/Controllers/Api/v1/SportController.php282-292]()

Data Structures and Validation
------------------------------

The Sports Management system uses several key components for data handling:

### Core Components

| Component | Purpose | File Reference |
| --- | --- | --- |
| `Sport` | Eloquent model for database operations | Referenced in controller |
| `SportResource` | API response transformation | Referenced in controller |
| `SportRequest` | Input validation for create/update operations | Referenced in controller |

### Request Validation

Based on the Postman collection examples, sports require the following fields:

* `name` (string) - The sport name
* `description` (string) - Sport description

**Sources:** [club\_cms/app/Http/Controllers/Api/v1/SportController.php8]() [club\_cms/app/Http/Controllers/Api/v1/SportController.php7]() [ClubCMS.postman\_collection.json520]()

Integration with Other Systems
------------------------------

The Sports Management system serves as a dependency for other systems within the Club CMS:

**Sources:** [ClubCMS.postman\_collection.json129]() [ClubCMS.postman\_collection.json399]()

OpenAPI Documentation
---------------------

The controller includes comprehensive OpenAPI/Swagger annotations for all endpoints. Each endpoint documents:

* Request/response schemas
* HTTP status codes (200, 201, 401, 404, 422, 500)
* Authentication requirements (`bearerAuth`)
* Parameter specifications
* Example responses

The documentation is structured under the "Sports" tag for API organization.

**Sources:** [club\_cms/app/Http/Controllers/Api/v1/SportController.php13-16]() [club\_cms/app/Http/Controllers/Api/v1/SportController.php21-58]() [club\_cms/app/Http/Controllers/Api/v1/SportController.php95-134]()

Testing Examples
----------------

The Postman collection provides complete testing scenarios for all sports endpoints:

### Sample Operations

**Create Sport (Admin required):**

**Update Sport (Admin required):**

The collection includes authentication token management and proper Bearer token configuration for all protected endpoints.

**Sources:** [ClubCMS.postman\_collection.json472-591]() [ClubCMS.postman\_collection.json520]() [ClubCMS.postman\_collection.json549]()