# API Reference

Base URL: `http://localhost:8000`

All API endpoints return JSON. Endpoints marked `(auth)` require a Sanctum token in the header:

```
Authorization: Bearer <your_token>
```

For full request/response examples open Swagger after starting the server:

```
http://localhost:8000/api/documentation
```

## Auth (v1)

Prefix: `/api/v1/auth`

### POST `/register`
Create a new account. Sends a 6-digit code to the user's email.

Body:
```json
{
  "name": "John",
  "email": "user@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

### POST `/verify-email`
Confirm the email with the code from the inbox.

Body:
```json
{ "email": "user@example.com", "code": "123456" }
```

### POST `/resend-code`
Send the verification code again if it expired.

Body:
```json
{ "email": "user@example.com" }
```

### POST `/login`
Returns a token for next requests.

Body:
```json
{ "email": "user@example.com", "password": "secret123" }
```

Response:
```json
{
  "token": "1|abcdef...",
  "user": { "id": 1, "name": "John", "email": "user@example.com" }
}
```

### POST `/google`
Login with a Google ID token. Basic version for now.

### POST `/logout`  (auth)
Invalidates the current token.

### GET `/me`  (auth)
Returns the current user.

## Barbershops (v1)

Prefix: `/api/v1/barbershops`

### GET `/`
Returns a paginated list of barbershops.

Query params:
- `page` (default 1)
- `per_page` (default 15)

### GET `/{slug}`
Returns one barbershop with its services and categories.

## Admin panel (Backpack)

Admin panel runs at `http://localhost:8000/admin`. You log in there with a normal browser, not via API. It is a web UI, not a JSON API.

Main pages:

| URL | What it does |
|---|---|
| `/admin/login` | Admin login form |
| `/admin/dashboard` | Main dashboard |
| `/admin/barbershop` | List of barbershops |
| `/admin/barbershop/create` | Create a new barbershop |
| `/admin/barbershop/{id}/edit` | Edit a barbershop |
| `/admin/barbershop/{id}` | Show one barbershop |
| `/admin/service-category` | List of service categories |
| `/admin/service-category/create` | Create a category |
| `/admin/service-category/{id}/edit` | Edit a category |
| `/admin/service` | List of services |
| `/admin/service/create` | Create a service |
| `/admin/service/{id}/edit` | Edit a service |

Each CRUD page also supports `DELETE` via the admin UI.

Login credentials for local dev are seeded by `database/seeders`.

## Errors

All errors look like this:

```json
{
  "message": "Something went wrong",
  "errors": {
    "field_name": ["validation message"]
  }
}
```

Common status codes:
- `200` OK
- `201` Created
- `401` Not authenticated
- `403` Forbidden
- `404` Not found
- `422` Validation error
- `500` Server error
