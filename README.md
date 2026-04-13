# BBS - Barbershop Booking System

Backend for a barbershop booking app. Built with Laravel.

## What it does

Lets users find barbershops, see their services, and (later) book a time slot.
Shop owners get an admin panel to manage their shops.

The problem: small barbershops usually take orders by phone or Instagram DMs. There is no normal way to see free slots or compare shops. BBS is the backend that solves this.

## Features

Done:
- Register / login with email + password
- Email verification with 6-digit code
- Google login (basic version)
- Sanctum tokens for auth
- List barbershops, view single barbershop
- Admin panel (Backpack) for managing shops, services, categories, users
- Swagger API docs

Planned:
- Booking endpoints
- Working hours and free slots
- Reviews

## Tech stack

- Laravel 13
- PHP 8.3
- MySQL 8
- Laravel Sanctum (auth)
- Backpack (admin panel)
- L5-Swagger (API docs)
- Repository + Service pattern

## Project structure

Standard Laravel structure:

```
BBS/
├── app/             # main code: Models, Http, Services, Repositories, Dto, Contracts
├── database/        # migrations, seeders, factories
├── docs/            # extra documentation
├── resources/       # views, css, js
├── routes/          # api/v1, web, backpack
├── tests/           # Feature + Unit
├── README.md
├── LICENSE
└── AUDIT.md
```

## Installation

Requirements: PHP 8.3, Composer, MySQL, Node.js 20+

```bash
git clone https://github.com/Medet0777/BBS.git
cd BBS

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Open `.env` and set DB credentials and SMTP (Gmail works fine in dev).

```bash
php artisan migrate --seed
php artisan l5-swagger:generate
php artisan serve
```

In another terminal:
```bash
npm run dev
```

## Usage

Swagger docs: `http://localhost:8000/api/documentation`

Admin panel: `http://localhost:8000/admin`

Quick test:
```bash
curl http://localhost:8000/api/v1/barbershops
```

Register a user:
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"u@example.com","password":"secret123","password_confirmation":"secret123"}'
```

## API endpoints (v1)

Auth:
- POST `/api/v1/auth/register`
- POST `/api/v1/auth/verify-email`
- POST `/api/v1/auth/resend-code`
- POST `/api/v1/auth/login`
- POST `/api/v1/auth/google`
- POST `/api/v1/auth/logout` (auth)
- GET  `/api/v1/auth/me` (auth)

Barbershops:
- GET `/api/v1/barbershops`
- GET `/api/v1/barbershops/{slug}`

Full request and response schemas are in Swagger.

## Tests

```bash
php artisan test
```

## License

MIT. See LICENSE.

## Author

Medet Muratbek, SDU University
