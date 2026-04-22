# ⚽ MatchPoint

> **Field and Public Match Booking Platform**
> A web-based application for booking sports fields and joining public matches.

Built with Laravel 12 · MySQL · Blade · Tailwind CSS · Filament Admin

---

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
- [Database Structure](#database-structure)
- [User Roles](#user-roles)
- [Business Rules](#business-rules)
- [Routes](#routes)
- [Project Status](#project-status)
- [Author](#author)

---

## About

MatchPoint is a web-based platform that allows users to search and book sports fields, create public matches, and invite other players to join. Field Owners manage their venues and confirm bookings, while Admins oversee the entire platform.

This project is developed as a final project (PBL II) for the D-4 Informatics Engineering Study Program at **State Polytechnic of Malang (Polinema)**.

---

## Features

### For Users
- Register and log in securely
- Search and filter sports fields by location and sport type
- View field time slot availability by date
- Book a field with 50% down payment (manual upload)
- Reschedule outdoor bookings before the booking date
- Create public matches linked to confirmed bookings
- Join public matches by paying the full participant fee
- Receive in-app and email notifications

### For Field Owners
- Register as a Field Owner (requires Admin approval)
- Add and manage fields, pricing, and time slots
- Confirm bookings by verifying payment proofs
- Cancel pending bookings

### For Admins
- Approve or reject Field Owner registrations
- Manage all user accounts (activate / deactivate / delete)
- Verify all payment proofs
- View full audit logs

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 (PHP 8.2+) |
| Frontend | Blade Templates + Tailwind CSS |
| Admin Panel | Filament |
| Database | MySQL |
| Authentication | Laravel Sanctum |
| Notifications | Laravel Mail + SMTP |
| Dev Environment | Laragon / XAMPP |

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- MySQL
- Node.js + NPM
- Laragon or XAMPP

### Installation

**1. Clone the repository**
```bash
git clone https://github.com/YOUR_USERNAME/matchpoint.git
cd matchpoint
```

**2. Install dependencies**
```bash
composer install
npm install
```

**3. Copy environment file**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Configure your database in `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=matchpoint_db
DB_USERNAME=root
DB_PASSWORD=
```

**5. Run migrations and seeders**
```bash
php artisan migrate:fresh --seed
```

**6. Build assets**
```bash
npm run dev
```

**7. Start the server**
```bash
php artisan serve
```

Visit `http://localhost:8000`

### Default Accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@matchpoint.test | Admin@123456 |
| Field Owner | budi@matchpoint.test | Owner@123456 |
| Field Owner | siti@matchpoint.test | Owner@123456 |
| User | joseph@matchpoint.test | User@123456 |
| User | ahmad@matchpoint.test | User@123456 |

---

## Database Structure

The system has 10 database entities:

```
users
├── fields
│   └── time_slots
│       └── booked_slots ──── bookings
│                                 ├── payments (BookingDP)
│                                 └── matches
│                                       └── match_participants
│                                               └── payments (MatchFee)
├── notifications
└── audit_logs
```

---

## User Roles

| Role | Description | Status on Register |
|---|---|---|
| `User` | Search fields, book, join matches | Active immediately |
| `FieldOwner` | Manage fields, confirm bookings | Pending Admin approval |
| `Admin` | Full platform access | Pre-seeded |

---

## Business Rules

| Rule | Description |
|---|---|
| BR-01 | 50% down payment required to confirm a field booking |
| BR-02 | Match participation fee paid in full (no DP rule) |
| BR-03 | Booking auto-cancelled if no payment uploaded within 24 hours |
| BR-04 | Only outdoor bookings can be rescheduled |
| BR-05 | Indoor bookings cannot be rescheduled under any circumstances |
| BR-06 | No refund once payment is verified |
| BR-07 | Confirmed booking auto-set to Completed after match date |
| BR-08 | Field Owner must be Admin-approved before fields appear publicly |

---

## Routes

All routes are defined in `routes/web.php` and return Blade views. No external API is used — everything is server-rendered.

### Public routes (no login required)
| Method | URL | Description |
|---|---|---|
| GET | `/` | Landing page |
| GET | `/fields` | Search & browse fields |
| GET | `/fields/{id}` | Field detail + available slots |
| GET | `/matches` | Public match listing |
| GET | `/help` | Help / FAQ page |
| GET | `/register` | Register page |
| POST | `/register` | Submit registration |
| GET | `/login` | Login page |
| POST | `/login` | Submit login |

### User routes (login required)
| Method | URL | Description |
|---|---|---|
| POST | `/logout` | Logout |
| GET | `/bookings` | My bookings list |
| POST | `/bookings` | Create new booking |
| POST | `/bookings/{id}/reschedule` | Reschedule outdoor booking |
| POST | `/bookings/{id}/cancel` | Cancel pending booking |
| POST | `/payments` | Upload payment proof |
| GET | `/matches/{id}` | Match detail |
| POST | `/matches` | Create public match |
| POST | `/matches/{id}/join` | Join a public match |
| GET | `/notifications` | My notifications |
| GET | `/profile` | View profile |
| PUT | `/profile` | Update profile |

### Field Owner routes
| Method | URL | Description |
|---|---|---|
| GET | `/owner/fields` | My fields list |
| POST | `/owner/fields` | Add new field |
| PUT | `/owner/fields/{id}` | Edit field |
| DELETE | `/owner/fields/{id}` | Delete field |
| GET | `/owner/bookings` | Incoming bookings |
| POST | `/owner/bookings/{id}/confirm` | Confirm booking |
| POST | `/owner/payments/{id}/verify` | Verify payment proof |

### Admin routes (via Filament panel)
| Method | URL | Description |
|---|---|---|
| GET | `/admin` | Admin dashboard |
| GET | `/admin/users` | Manage all users |
| POST | `/admin/users/{id}/approve` | Approve Field Owner |
| GET | `/admin/payments` | All payment proofs |
| GET | `/admin/audit-logs` | View audit logs |

---

## Project Status

This project is currently in active development as part of PBL II.

| Phase | Description | Status |
|---|---|---|
| Phase 1 | Foundation — migrations, models, seeders, auth | 🟡 In progress |
| Phase 2 | Core booking — search, book field, payments, scheduler | ⬜ Not started |
| Phase 3 | Match & admin — public matches, Filament admin panel | ⬜ Not started |
| Phase 4 | Polish — notifications, audit logs, testing, UI | ⬜ Not started |

---

## Author

**Joseph Atem Deng Aruei**
Student ID: 244107020242
D-4 Informatics Engineering — State Polytechnic of Malang (Polinema)
April 2026

---

<p align="center">Made with ❤️ for PBL II · Polinema 2026</p>
