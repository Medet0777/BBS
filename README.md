# BBS - Barbershop Booking System (Backend)

Backend API for a barbershop booking app. Built with Laravel.

## What it does

Users can find barbershops, see services and barbers, book a slot, leave reviews.
Owners get a dashboard with stats, calendar, bookings management.

The problem: in Almaty most small barbershops take orders by phone or Instagram DMs.
There is no normal way to see free slots or compare shops by rating and price.
BBS solves this.

## Features

Auth:
- Register with email + OTP code
- Login by email/password
- Google login (id_token)
- Forgot password / reset password
- Edit profile (name, phone)

Barbershops:
- List with filters (search by name, sort by rating or distance, only open now)
- Pagination
- Distance from user (Haversine formula)
- Detail page with services, barbers, reviews
- Available slots for booking

Bookings:
- Create booking (multiple services, optional barber - "any" mode picks first free)
- My bookings (upcoming / past)
- Cancel
- Reschedule
- Reminder email 2 hours before (Brevo HTTP API)

Reviews:
- Leave review for barbershop
- See my reviews

Owner panel (mobile/web):
- Dashboard with stats (today bookings, week revenue, new clients, etc)
- Calendar (day/week/month)
- Analytics with period selector (week/month/year) + change percent vs previous period
- Bookings list with filter (pending/confirmed/cancelled/completed)
- Confirm / Cancel / Complete booking
- Services CRUD

Admin panel (Backpack):
- CRUD for: Barbershops, Service Categories, Services, Barbers, Reviews, Users
- Owner assignment to barbershops

Other:
- Swagger API docs
- Demo data seeder (50 real Almaty barbershops with services and barbers)
- Sanctum tokens for auth
- CORS for frontend on Vercel

## Tech stack

- Laravel 13
- PHP 8.4
- MySQL 8
- Laravel Sanctum (auth)
- Backpack (admin panel)
- L5-Swagger (API docs)
- Spatie Laravel Data (DTOs)
- Google API client (Google login)
- Brevo HTTP API (transactional emails)
- Repository + Service pattern with contracts

## Project structure

```
BBS/
├── app/
│   ├── Contracts/        # interfaces for repos and services
│   ├── Enums/            # BookingStatus
│   ├── Http/
│   │   ├── Controllers/  # Api/V1/* and Admin/* (Backpack)
│   │   ├── Requests/     # Form requests
│   │   └── Resources/    # API resources
│   ├── Jobs/             # SendBookingReminderJob
│   ├── Mail/             # OtpCodeMail, BookingReminderMail
│   ├── Models/           # User, Barbershop, Service, Barber, Booking, Review, etc
│   ├── Repositories/     # data access layer
│   └── Services/
│       ├── Http/Api/V1/  # business logic (Auth, Barbershop, Booking, Owner, Review)
│       └── Mail/         # BrevoMailService
├── database/
│   ├── migrations/
│   └── seeders/          # DemoDataSeeder + barbershops.json
├── docker/               # nginx, supervisord, entrypoint
├── routes/api/v1/        # auth.php, barbershop.php, booking.php, owner.php
├── Dockerfile
├── docker-compose.yml
└── README.md
```

## Installation (local)

Requirements: PHP 8.4, Composer, MySQL 8

```
git clone https://github.com/Medet0777/BBS.git
cd BBS
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
- DB credentials
- BREVO_API_KEY and BREVO_FROM_EMAIL (for sending emails)
- GOOGLE_CLIENT_ID (for google login)

```
php artisan migrate --seed
php artisan storage:link
php artisan l5-swagger:generate
php artisan serve
```

Queue worker for reminders:
```
php artisan queue:work
```

## Installation (Docker)

```
docker compose up --build
```

App runs on port 8080. MySQL on 3307.

## Deployment

Deployed on Railway. Production URL: https://bbs-production-6580.up.railway.app

## Usage

Swagger: `/api/documentation`
Admin panel: `/admin`
API base: `/api/v1`

Quick test:
```
curl https://bbs-production-6580.up.railway.app/api/v1/barbershops
```

## Demo accounts (after seeder)

Password for all: `password`

- admin@bbs.kz - admin
- owner1@bbs.kz - owner of BarbarossA barbershop
- owner2@bbs.kz - owner of Chop-Chop barbershop
- medet@bbs.kz - regular client

## API endpoints

Full list and schemas in Swagger. Short overview:

Auth (`/api/v1/auth/*`):
- POST register, verify-email, resend-code
- POST login, google, logout
- GET me, PUT me, GET me/reviews
- POST forgot-password, reset-password

Barbershops (`/api/v1/barbershops`):
- GET / (list with filters)
- GET /{slug} (detail)
- GET /{slug}/available-slots
- POST /{slug}/reviews

Bookings (`/api/v1/bookings`):
- GET / (my bookings)
- POST / (create)
- GET /{id}
- POST /{id}/cancel
- POST /{id}/reschedule

Owner (`/api/v1/owner/*`):
- GET dashboard
- GET calendar
- GET analytics?period=week|month|year
- GET bookings
- POST bookings/{id}/confirm | cancel | complete
- GET services, POST services
- PUT services/{id}, DELETE services/{id}

## Notes

- All API responses wrapped in `{success, message, data}`
- Bookings store price snapshot in pivot, so old bookings keep old price even if owner changes it later
- Reminder email is sent via Brevo (Gmail/SMTP blocked by Railway)
- Distance calculated with Haversine formula in SQL

## License

MIT

## Authors

Student IDs:
- 230103176
- 230103002
- 230103282
- 230103130
