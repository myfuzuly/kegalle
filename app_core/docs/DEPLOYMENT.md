# Deployment Guide

## Server details

| Item | Value |
|---|---|
| Host | 213.165.241.225 |
| Hostname | vps133166.inmotionhosting.com |
| Panel | CWP (CentOS Web Panel) |
| Web server | Nginx 1.14 |
| PHP | 8.3.31 (FPM: `/opt/alt/php-fpm83/`, CLI: `/usr/bin/php`) |
| Laravel root | `/home/kegalle/app_core/` |
| Web root | `/home/kegalle/public_html/` |
| Vite build | `/home/kegalle/app_core/public/build/` |
| Vite web URL | `/build/` → symlink from `public_html/build` |

---

## FTP deployment (app_core files)

**Critical rule: app_core files must upload to `/app_core/` — NOT `/public_html/app_core/`.**

PHP files, views, migrations, config, and JS/CSS source files all go to `/app_core/`.  
Only static compiled assets (images, legacy CSS/JS bundles) go to `/public_html/`.

### FTP credentials

Stored separately by the project owner. Connection uses `FTP_TLS` on port 21.

### What to deploy where

| Local path | Remote path |
|---|---|
| `app_core/app/**` | `/app_core/app/` |
| `app_core/config/**` | `/app_core/config/` |
| `app_core/database/migrations/**` | `/app_core/database/migrations/` |
| `app_core/resources/**` | `/app_core/resources/` |
| `app_core/routes/**` | `/app_core/routes/` |
| `app_core/bootstrap/**` | `/app_core/bootstrap/` |
| `app_core/vite.config.js` | `/app_core/vite.config.js` |
| `app_core/package.json` | `/app_core/package.json` |
| `public_html/css/**` | `/public_html/css/` |
| `public_html/js/**` | `/public_html/js/` |
| `public_html/images/**` | `/public_html/images/` |

---

## Standard deploy procedure

### 1. Upload changed files via FTP

Use the Python FTP deploy script (see project root). Upload only changed files to avoid overwriting unnecessary content.

### 2. Run database migrations (if any new migrations)

```bash
# SSH into server
php /home/kegalle/app_core/artisan migrate --force
```

### 3. Build Vite assets (if CSS/JS source changed)

```bash
cd /home/kegalle/app_core
npm run build
```

Vite output lands in `app_core/public/build/`. The symlink at `public_html/build` makes it web-accessible at `/build/`.

### 4. Clear Laravel caches

Always run after any config, route, or view change:

```bash
php /home/kegalle/app_core/artisan config:clear
php /home/kegalle/app_core/artisan cache:clear
php /home/kegalle/app_core/artisan view:clear
```

### 5. Verify

Check the site at https://kurulla.com. For page-source check:
- `@vite()` tags should resolve to `/build/css/app-[hash].css` and `/build/js/app-[hash].js`
- No 500 errors in `/home/kegalle/app_core/storage/logs/laravel.log`

---

## First-time server setup (reference)

These steps were completed once and do not need to be repeated.

```bash
# Create the build symlink
ln -s /home/kegalle/app_core/public/build /home/kegalle/public_html/build

# Install npm dependencies
cd /home/kegalle/app_core && npm install

# Set environment variables in .env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Crontab (root crontab — already installed)

```
* * * * * /usr/bin/php /home/kegalle/app_core/artisan schedule:run >> /home/kegalle/app_core/storage/logs/scheduler.log 2>&1
* * * * * /usr/bin/php /home/kegalle/app_core/artisan queue:work --stop-when-empty --tries=3 --max-time=55 >> /home/kegalle/logs/queue.log 2>&1
```

---

## Environment variables (`.env` — server only)

Never committed to any repo or doc. Key variables:

| Variable | Purpose |
|---|---|
| `APP_KEY` | Laravel encryption key |
| `DB_DATABASE` | `kegalle_kegalle` |
| `QUEUE_CONNECTION` | `database` |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `mail.kurulla.com` |
| `MAIL_PORT` | `587` |
| `PAYHERE_SANDBOX` | `false` (production) |

---

## Rollback procedure

There is no automated rollback. To revert a bad deploy:

1. Re-upload the previous version of changed files via FTP.
2. If a migration needs reversing: `php artisan migrate:rollback --step=1`
3. Run `config:clear`, `cache:clear`, `view:clear`.

Keep a local copy of the previous file before each deploy.

---

## Log locations

| Log | Path |
|---|---|
| Laravel application | `/home/kegalle/app_core/storage/logs/laravel.log` |
| Queue worker | `/home/kegalle/logs/queue.log` |
| Scheduler | `/home/kegalle/app_core/storage/logs/scheduler.log` |
| Nginx access | `/var/log/nginx/access.log` |
| Nginx error | `/var/log/nginx/error.log` |
