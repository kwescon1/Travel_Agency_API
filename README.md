# Travel Agency API

The Travel Agency API is a simple RESTful web service that provides various endpoints to manage and retrieve information related to travels and tours. It offers both private (admin) endpoints for managing data and public (no auth) endpoints for accessing publicly available information.

## Private Endpoints

### Create User Endpoint

-   Endpoint: `/api/admin/users`
-   Method: POST
-   Description: Creates a new user in the system.
-   Authentication: Requires admin authentication.
-   Request Body: JSON object containing user details.
-   Response: JSON response with the created user's information.

### Create Travel Endpoint

-   Endpoint: `/api/admin/travels`
-   Method: POST
-   Description: Creates a new travel in the system.
-   Authentication: Requires admin authentication.
-   Request Body: JSON object containing travel details.
-   Response: JSON response with the created travel's information.

### Create Tour Endpoint

-   Endpoint: `/api/admin/travels/{travelSlug}/tours`
-   Method: POST
-   Description: Creates a new tour for a specific travel.
-   Authentication: Requires admin authentication.
-   Request Body: JSON object containing tour details.
-   Response: JSON response with the created tour's information.

### Update Travel Endpoint

-   Endpoint: `/api/editor/travels/{travel}`
-   Method: PUT/PATCH
-   Description: Updates the details of a specific travel.
-   Authentication: Requires editor authentication.
-   Request Body: JSON object containing the updated travel details.
-   Response: JSON response with the updated travel's information.

## Public Endpoints

### Get Paginated Travels Endpoint

-   Endpoint: `/api/travels`
-   Method: GET
-   Description: Retrieves a paginated list of public travels.
-   Authentication: No authentication required.
-   Query Parameters: page (pagination page number), limit (number of items per page)
-   Response: JSON response with a paginated list of public travels.

### Get Paginated Tours by Travel Slug Endpoint

-   Endpoint: `/api/travels/{travelSlug}/tours`
-   Method: GET
-   Description: Retrieves a paginated list of tours for a specific travel, filtered and sorted based on user-provided parameters.
-   Authentication: No authentication required.
-   Query Parameters: priceFrom, priceTo, dateFrom, dateTo (filter parameters), sort (price sorting: asc/desc)
-   Response: JSON response with a paginated list of tours meeting the provided filters and sorted by starting date.

Please note that the API requires appropriate authentication and authorization for private (admin and editor) endpoints to ensure secure data management. The public endpoints are accessible without authentication and provide necessary information to users who wish to explore available travels and tours.

Feel free to integrate and utilize this Travel Agency API to create a comprehensive travel booking system or enhance existing travel-related applications.
