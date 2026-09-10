# Kurulla.com — Kegalle Marketplace

**Live site:** https://kurulla.com  
**Server:** InMotion VPS · 213.165.241.225  
**Stack:** Laravel 11 · PHP 8.2 · MariaDB 10.6 · Nginx 1.14 · Vite 6

---

## What is this?

Kurulla.com is a local online marketplace for the Kegalle district of Sri Lanka. It lets residents buy, sell, and discover products, services, stores, deals, and events within the district.

---

## Directory layout

```
/home/kegalle/
├── app_core/          ← Laravel application root (not web-accessible)
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   ├── Admin/       ← 25 controllers, /admin/* routes
│   │   │   ├── Dashboard/   ← 10 controllers, /dashboard/* routes
│   │   │   ├── Frontend/    ← 13 controllers, public routes
│   │   │   ├── Api/         ← chat poll, push subscriptions
│   │   │   └── Auth/        ← login, register, OTP, social
│   │   ├── Models/          ← 31 Eloquent models
│   │   └── Http/Middleware/ ← 3 custom middleware
│   ├── bootstrap/app.php
│   ├── config/
│   ├── database/migrations/ ← 22 migrations
│   ├── resources/
│   │   ├── css/             ← Vite entry: app / dashboard / admin
│   │   ├── js/              ← Vite entry: app / dashboard / admin
│   │   └── views/
│   │       ├── layouts/     ← app · dashboard · admin · card · home
│   │       ├── frontend/
│   │       ├── dashboard/
│   │       ├── admin/
│   │       ├── auth/
│   │       ├── emails/      ← 9 email templates
│   │       └── pages/       ← static pages
│   ├── routes/
│   │   ├── web.php          ← 314 routes
│   │   ├── api.php
│   │   └── console.php      ← 4 scheduled tasks
│   ├── storage/
│   ├── public/build/        ← Vite compiled output
│   ├── docs/                ← this folder
│   ├── vite.config.js
│   └── package.json
│
└── public_html/             ← Nginx web root
    ├── index.php            ← boots app_core
    ├── css/                 ← legacy hand-written CSS bundles
    ├── js/                  ← legacy JS bundles
    ├── images/
    └── build/ →             ← symlink → app_core/public/build
```

---

## Local development

```bash
cd app_core
npm install
npm run dev        # Vite dev server with HMR
php artisan serve  # Laravel dev server
```

---

## Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for the full deploy procedure.

---

## Documentation index

| File | Purpose |
|---|---|
| [README.md](README.md) | This file — project overview |
| [FEATURES.md](FEATURES.md) | Every feature and where it lives in the code |
| [DEPLOYMENT.md](DEPLOYMENT.md) | How to build and deploy to the server |
| [BUILDS.md](BUILDS.md) | Chronological build and change log |

---

## Key contacts / credentials storage

Credentials are **not** stored in this repo. All secrets live in `/home/kegalle/app_core/.env` on the server.  
FTP and SSH access details are stored separately by the project owner.
