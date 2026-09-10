# Build & Change Log

---

## Build 043 — 2026-07-20 — Security fixes + PayHere removed

**Type:** Security  
**Files changed:**
- `app/Http/Controllers/PayHereController.php` — deleted (file removed from server)
- `app/Services/PayHereService.php` — deleted (file removed from server)
- `config/services.php` — payhere block removed
- `bootstrap/app.php` — `payhere/notify` CSRF exception removed
- `routes/web.php` — PayHere routes removed; price-alert exception no longer leaks $e->getMessage()
- `app/Http/Controllers/Auth/AuthController.php` — password reset no longer reveals whether email exists
- `app/Http/Controllers/Admin/UserManagementController.php` — makeAdmin() now requires super_admin
- `app/Http/Controllers/Admin/AdminStoreController.php` — User::get() capped at 500
- `app/Http/Controllers/Auth/PhoneOtpController.php` — OTP now stored as bcrypt hash
- `database/migrations/2026_07_20_000003_widen_phone_otps_code_column.php` — widens code column to varchar(255)

**What changed:**
- PayHere payment system removed entirely (forged IPN vulnerability + no longer in use)
- OTP codes hashed at rest; column widened; Hash::check() used for verification
- Password reset returns generic "if an account exists…" message to prevent email enumeration
- Price-alert route no longer exposes raw exception messages to the browser
- Only super_admin can promote users to admin role (previously any admin could)
- Admin store create/edit user dropdowns capped at 500 rows

**Post-deploy steps:**
- Delete `/app_core/app/Http/Controllers/PayHereController.php` on server
- Delete `/app_core/app/Services/PayHereService.php` on server
- `artisan migrate --force`
- `artisan config:clear && artisan cache:clear`

---

Every deployment to the live server is recorded here.  
Format: `## Build NNN — YYYY-MM-DD — short description`

---

## Build 042 — 2026-07-20 — PHP 8.3 FPM live, OPcache + JIT tuned for 8.3

**Type:** Infrastructure — PHP version upgrade  
**Server changes (no FTP deploy):**
- Created PHP 8.3 FPM pool: `/opt/alt/php-fpm83/usr/etc/php-fpm.d/kegalle.conf`
  - Workers run as `kegalle` user; socket owned by `nobody` (Apache user)
  - Socket: `/opt/alt/php-fpm83/usr/var/sockets/kegalle.sock`
  - `pm = ondemand`, `pm.max_children = 25`, `pm.max_requests = 500`
- CWP had already wired Apache vhost with `SetHandler proxy:unix:...kegalle.sock`
- Replaced OPcache config at `/opt/alt/php-fpm83/usr/php/php.d/opcache.ini`:
  - `memory_consumption=192`, `interned_strings_buffer=24`, `max_accelerated_files=8000`
  - `opcache.jit=tracing`, `opcache.jit_buffer_size=64M`
  - `optimization_level=0x7FFFBFFF`, `enable_file_override=1`
- Restarted PHP 8.3 FPM master process

**Result:** Site serving PHP 8.3.31 confirmed via live probe. Previous PHP 8.2 (suPHP) no longer handling requests.

---

## Build 041 — 2026-07-20 — OPcache tuned for PHP 8.2 (superseded by Build 042)

**Type:** Infrastructure  
**Server changes:**
- Updated `/usr/local/php/php.d/opcache.ini` (PHP 8.2 system CLI)
- Enabled JIT tracing, 192 MB memory, max optimization passes
- Note: This was the system CLI PHP, not the PHP actually serving the site. Superseded by Build 042.

---

## Build 040 — 2026-07-20 — Session & cache switched to database driver

**Type:** Infrastructure change  
**Files changed:**
- `database/migrations/2026_07_20_000001_create_sessions_table.php` *(new)*
- `database/migrations/2026_07_20_000002_create_cache_table.php` *(new)*
- `.env` on server: `SESSION_DRIVER=database`, `CACHE_STORE=database`

**What changed:**
- Sessions now stored in `sessions` database table instead of flat files
- Cache now stored in `cache` + `cache_locks` tables instead of flat files
- Eliminates file-lock contention under concurrent requests

**Post-deploy steps run:**
- `artisan migrate --force`
- `artisan config:clear && artisan cache:clear`

---

## Build 039 — 2026-07-20 — Dark mode default set to light

**Type:** Frontend fix  
**Files changed:**
- `resources/js/app.js`

**What changed:**
- Removed duplicate dark-mode handler from Vite `app.js` (was using key `theme`)
- Single handler now lives in `layouts/app.blade.php` inline script (key `k_theme`)
- First-visit default is always `light` — no flash
- Rebuilt Vite: `app-DrOMSPya.js`

---

## Build 038 — 2026-07-20 — Vite pipeline deployed and built on server

**Type:** Build tooling  
**Files changed:**
- `package.json`
- `vite.config.js`
- `resources/css/app.css`, `dashboard.css`, `admin.css`
- `resources/js/app.js`, `dashboard.js`, `admin.js`
- `resources/views/layouts/app.blade.php` — added `@vite()`
- `resources/views/layouts/dashboard.blade.php` — added `@vite()`
- `resources/views/layouts/admin.blade.php` — added `@vite()`

**What changed:**
- Replaced `?v=9` manual cache-busting with Vite content-hash filenames
- 6 entry points: app/dashboard/admin × css/js
- Server-side build: `npm install && npm run build`
- Symlink created: `public_html/build → app_core/public/build`

---

## Build 037 — 2026-07-14 — Pass 3 audit fixes (medium/low)

**Type:** Security / quality  
**Files changed:**
- `app/Http/Controllers/Admin/ModerationController.php` — paginate(50) all sections
- `app/Http/Controllers/Admin/AdminListingController.php` — 500-row user/store select cap, 30-field cap
- `app/Http/Controllers/Admin/EventManagementController.php` — 500-row select cap
- `app/Http/Controllers/Dashboard/ChatController.php` — listing ownership check
- `app/Http/Controllers/Dashboard/DealController.php` — null price guard, starts_at fix, admin delete fix
- `app/Http/Controllers/Frontend/StoreController.php` — reviews limited to 20
- `app/Models/Listing.php` — removed phantom `is_active` cast
- `app/Models/Store.php` — removed phantom `payment_methods` cast
- `app/Models/User.php` — fail-closed `hasPermission()`
- `app/Http/Controllers/Admin/MembershipController.php` — payment record guard on delete
- `app/Http/Controllers/Dashboard/AiAssistController.php` — key not exposed in diag
- `app/Http/Controllers/Admin/AdminStoreController.php` — store approval notification
- `app/Models/Offer.php` — `responded_at` fillable + cast
- `app/Http/Controllers/OfferController.php` — `min:1`, null listing guard
- `routes/web.php` — chat poll throttle 120/min
- `routes/console.php` — chunk/lazy on all scheduled queries, deal expiry job added

---

## Build 036 — 2026-07-13 — Pass 2 audit fixes (critical/high)

**Type:** Security  
**Files changed:**
- `app/Http/Controllers/Admin/UserManagementController.php` — super_admin guard
- `app/Http/Controllers/Admin/AdminNotificationController.php` — link host validation
- `app/Http/Middleware/EnsureAccountIsActive.php` — merged status checks, added `banned`
- `app/Http/Controllers/Dashboard/StoreProductController.php` — min:0, category exists
- `config/services.php` — PayHere sandbox default `false`
- `bootstrap/app.php` — narrowed CSRF exceptions
- `routes/web.php` — `account.active` guards, ad banner URL validation, CSV cap, mail queue, chat throttle

---

## Build 035 — 2026-07-13 — Chat tables migration fix

**Type:** Bug fix  
**Files changed:**
- `database/migrations/2026_07_13_000001_create_chat_tables.php`

**What changed:**
- Wrapped both `Schema::create()` calls in `if (!Schema::hasTable(...))` guards
- Fix for: `SQLSTATE table 'chat_threads' already exists` on live server

---

## Build 034 — 2026-07-13 — Chat, user notifications, PayHere migrations

**Type:** Feature  
**Files changed:**
- `database/migrations/2026_07_13_000001_create_chat_tables.php` *(new)*
- `database/migrations/2026_07_14_000001_create_user_notifications_table.php` *(new)*
- `database/migrations/2026_07_14_000002_create_payhere_payments_table.php` *(new)*
- `database/migrations/2026_07_12_143634_create_jobs_table.php` *(new)*

**What changed:**
- `chat_threads` and `chat_messages` tables created
- `user_notifications` table created
- `payhere_payments` table created (existed on server — migration guarded)
- `jobs` table created for database queue driver

---

## Build 033 — 2026-07-12 — Queue driver set to database, cron installed

**Type:** Infrastructure  
**What changed:**
- `QUEUE_CONNECTION=database` set in `.env`
- Cron jobs added to root crontab: `schedule:run` + `queue:work --stop-when-empty`

---

## How to add a new build entry

When you deploy changes, add a new entry at the **top** of this file:

```markdown
## Build NNN — YYYY-MM-DD — short description

**Type:** Feature | Bug fix | Security | Infrastructure | Build tooling  
**Files changed:**
- path/to/file.php — what changed

**What changed:**
- bullet points describing the change

**Post-deploy steps run:**
- artisan commands or other steps
```

Increment the build number by 1 from the previous entry. Keep entries brief — one line per file, 2–4 bullet points for changes.
