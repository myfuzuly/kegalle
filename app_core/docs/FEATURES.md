# Feature Implementation Reference

This document maps every major feature to its implementation files.  
Last updated: 2026-07-20

---

## 1. Authentication & Accounts

### Registration / Login
- **Controller:** `app/Http/Controllers/Auth/AuthController.php`
- **Routes:** `GET /login`, `POST /login`, `GET /register`, `POST /register`, `POST /logout`
- **Views:** `resources/views/auth/login.blade.php`, `register.blade.php`
- Users register with name, email, phone, and password. Account starts as `inactive` until admin approves.

### Email Verification
- **Controller:** `Auth/AuthController.php` — `verifyEmail()`
- **Migration:** `2026_06_17_000001_add_email_verification_to_users_table.php`
- **Middleware:** `app/Http/Middleware/EnsureEmailIsVerifiedCustom.php`
- Custom verification flow; token stored on `users` table, sent via `emails/verify-account.blade.php`.

### Phone OTP Verification
- **Controller:** `app/Http/Controllers/Auth/PhoneOtpController.php`
- **Route:** `GET|POST /auth/verify-phone`
- **View:** `resources/views/auth/verify-phone.blade.php`
- OTP delivered via Hutch SMS gateway (sender: Kainglobe).

### Social Login (Google / Facebook)
- **Controller:** `app/Http/Controllers/Auth/SocialLoginController.php`
- **Migration:** `2026_06_17_000002_add_social_login_to_users_table.php`
- Columns: `social_provider`, `social_id`, `avatar` on `users`.

### Password Reset
- **Controller:** `Auth/AuthController.php` — `forgotPassword()`, `resetPassword()`
- **Views:** `auth/forgot-password.blade.php`, `auth/reset-password.blade.php`
- **Email:** `emails/password-reset.blade.php`

### Account Status Gates
- **Middleware:** `app/Http/Middleware/EnsureAccountIsActive.php`
- Blocks `inactive` → `/account-pending`, `suspended`/`banned` → `/account-suspended`.
- Applied to all dashboard and admin route groups.

---

## 2. User Roles & Permissions

- **Model:** `app/Models/Role.php`, `app/Models/User.php`
- **Controller:** `app/Http/Controllers/Admin/RoleController.php`
- **Migration:** `2026_06_28_000001_create_roles_table.php`
- Three built-in roles: `user`, `admin`, `super_admin`.
- Custom roles stored in `roles` table with per-section permission flags.
- `User::hasPermission(string $permission)` — fails closed; admins without a custom role get all sections except `roles`.
- Only `super_admin` can promote another user to `super_admin`.

---

## 3. Listings (Classifieds)

### Public listing pages
- **Controller:** `app/Http/Controllers/Frontend/ListingController.php`
- **Routes:** `GET /listings`, `GET /listings/{slug}`

### Seller listing management
- **Controller:** `app/Http/Controllers/Dashboard/ListingController.php`
- **Routes:** `GET /dashboard/listings`, `POST /dashboard/listings`, `PUT /dashboard/listings/{id}`, `DELETE /dashboard/listings/{id}`
- **Views:** `resources/views/dashboard/listings/`
- Validation: title, description, price (`min:0`), category (`exists:categories,id`), images.
- Supports listing images (`listing_images` table), variants (`listing_variants`), and custom fields (`listing_field_values`).

### Admin listing management
- **Controller:** `app/Http/Controllers/Admin/AdminListingController.php`
- **Routes:** `/admin/listings/*`
- Admin can approve, reject, feature, mark as top, or delete any listing.
- CSV import (max 500 rows; strips HTML from title/description/location/condition).
- Custom field count capped at 30 per listing.

### Custom fields per category
- **Controllers:** `Admin/CustomFieldController.php`, `Admin/FieldManagementController.php`
- **Models:** `CustomField`, `CustomFieldGroup`, `ListingFieldValue`
- Fields are assigned per category; rendered dynamically on listing create/edit forms.

---

## 4. Stores

### Public store pages
- **Controller:** `app/Http/Controllers/Frontend/StoreController.php`
- **Routes:** `GET /stores`, `GET /stores/{slug}`
- Store reviews limited to 20 most recent on public page.

### Seller store dashboard
- **Controller:** `app/Http/Controllers/Dashboard/StoreController.php`, `StoreProductController.php`
- Products have `min:0` price validation and `exists:categories,id` category check.

### Admin store management
- **Controller:** `app/Http/Controllers/Admin/AdminStoreController.php`
- Status filter covers: pending, active, rejected, suspended.
- On approve → sends `UserNotification` to store owner.

---

## 5. Deals

### Seller deal creation
- **Controller:** `app/Http/Controllers/Dashboard/DealController.php`
- Listing must have a price > 0 to create a deal.
- `starts_at` must be today or later for non-admin sellers.

### Admin deal management
- **Controller:** `app/Http/Controllers/Admin/DealManagementController.php`
- Admin can approve, reject, feature, or mark as flash deal.
- Admin can delete deals in any status.

### Public deals page
- **Controller:** `app/Http/Controllers/Frontend/DealsController.php`
- **Route:** `GET /deals`

### Deal expiry
- **Scheduled task** in `routes/console.php` — runs daily.
- Approved deals with `ends_at < now()` are automatically set to `expired`.

---

## 6. Offers (Buy Now / Make Offer)

- **Controller:** `app/Http/Controllers/OfferController.php`
- **Model:** `app/Models/Offer.php`
- **Routes:** `POST /offers`, `POST /offers/{id}/accept`, `POST /offers/{id}/decline`
- `offered_price` minimum is 1 (not 0).
- Seller ownership verified before accept/decline — null listing guard added.
- `responded_at` timestamp recorded on accept/decline.

---

## 7. Messaging (Chat)

### Buyer–seller chat
- **Controller:** `app/Http/Controllers/Dashboard/ChatController.php`
- **Models:** `ChatThread`, `ChatMessage`
- **Migration:** `2026_07_13_000001_create_chat_tables.php`
- Chat thread is tied to a listing and a seller. Listing ownership verified on thread creation.
- Rate limit: `throttle:60,1` on send; poll endpoint `throttle:120,1`.

### Real-time poll API
- **Controller:** `app/Http/Controllers/Api/ChatPollController.php`
- **Routes:** `GET /api/chat/{thread}/messages`, `GET /api/chat/unread`
- CSRF exempted (GET poll endpoint).

### Admin chat view
- **Controller:** `app/Http/Controllers/Admin/ChatController.php`
- Admin can view, close, and delete any thread.

---

## 8. Notifications

### User notifications (in-app)
- **Model:** `app/Models/UserNotification.php`
- **Migration:** `2026_07_14_000001_create_user_notifications_table.php`
- Static helper: `UserNotification::send(int $userId, string $type, string $title, string $body, string $url, ?int $relatedId)`
- Used on store approval. All calls wrapped in try/catch.

### Admin notifications
- **Model:** `app/Models/AdminNotification.php`
- **Controller:** `app/Http/Controllers/Admin/AdminNotificationController.php`
- Notification link validated — only same-host or relative URLs allowed.

### Push notifications
- **Controller:** `app/Http/Controllers/Api/PushSubscriptionController.php`

---

## 9. Membership Plans

- **Controller:** `app/Http/Controllers/Admin/MembershipController.php`
- **Model:** `app/Models/MembershipPlan.php`
- Plans cannot be deleted if they have existing payment records — deactivate instead.
- **Dashboard:** `app/Http/Controllers/Dashboard/` — membership purchase flow.

---

## 10. Payments (PayHere)

- **Controller:** `app/Http/Controllers/PayHereController.php`
- **Model:** `app/Models/Payment.php`
- **Migration:** `2026_07_14_000002_create_payhere_payments_table.php`
- PayHere sandbox mode defaults to `false` (production).
- Notify webhook (`/payhere/notify`) is CSRF-exempt.
- **Return URL:** `/g-return` (CSRF-exempt GET).

---

## 11. Events

- **Admin Controller:** `app/Http/Controllers/Admin/EventManagementController.php`
- **Frontend Controller:** `app/Http/Controllers/Frontend/EventController.php`
- Admin can approve, reject, and feature events.
- User/store selects on create/edit limited to 500 records.

---

## 12. Explore / Tourism

- **Controllers:** `Admin/ExploreItemController.php`, `Admin/ExploreSubItemController.php`
- **Frontend Controller:** `Frontend/ExploreController.php`
- **Models:** `ExploreItem`, `ExploreSubItem`
- Sections: Tourist Places, Historic Places, Natural Resources, Activities.

---

## 13. Government Services

- **Controllers:** `Admin/GovernmentServiceController.php`, `Admin/GovernmentServiceItemController.php`
- **Frontend Controller:** `Frontend/GovernmentServiceController.php`
- **Models:** `GovernmentService`, `GovernmentServiceItem`

---

## 14. Blog / Posts

- **Controller:** `Admin/PostManagementController.php`, `Frontend/BlogController.php`
- **Model:** `app/Models/Post.php`

---

## 15. Ad Banners

- **Controller:** `app/Http/Controllers/Admin/AdBannerController.php`
- **Model:** `app/Models/AdBanner.php`
- Banner link scheme validated — only `http`/`https` with same host allowed.

---

## 16. AI Assist

- **Controller:** `app/Http/Controllers/Dashboard/AiAssistController.php`
- **Route:** `GET|POST /dashboard/ai-assist`
- Protected by both `account.active` and `verified.custom` middleware.
- Diagnostics endpoint (`/dashboard/ai-assist/diag`) returns `key_configured: bool` — never exposes the raw key.

---

## 17. Favorites / Saved Items

- **Controller:** `app/Http/Controllers/Dashboard/FavoriteController.php`
- **Model:** `app/Models/Favorite.php`
- **Migration:** `2026_06_23_000001_create_favorites_table.php`
- Saved items count shown in navbar badge (read from `localStorage: k_saved`).

---

## 18. Reviews

- **Admin Controller:** `app/Http/Controllers/Admin/ReviewController.php`
- **Model:** `app/Models/Review.php`

---

## 19. Locations

- **Controller:** `app/Http/Controllers/Admin/LocationManagementController.php`
- **Frontend:** `Frontend/LocationPageController.php`, `Frontend/TownController.php`
- **Model:** `app/Models/Location.php`
- **Migration:** `2026_06_16_000101_create_locations_table.php`

---

## 20. Brands & Models

- **Controller:** `app/Http/Controllers/Admin/BrandManagementController.php`
- **Models:** `Brand`, `BrandModel`
- Used in vehicle/electronics listing categories via custom field groups.

---

## 21. Scheduled Tasks (background automation)

All defined in `routes/console.php`, triggered by cron every minute via `artisan schedule:run`.

| Task | Schedule | Method |
|---|---|---|
| Membership renewal reminders | Daily 09:00 | `chunk(100)` |
| Saved-search email alerts | Daily 08:00 | `lazy(100)` |
| Price-drop email alerts | Daily 10:00 | `lazy(200)` |
| Deal expiry cleanup | Daily 00:00 | Batch `update()` |

Email templates: `resources/views/emails/`

---

## 22. Queue

- **Driver:** `database` — jobs stored in `jobs` table.
- **Migration:** `2026_07_12_143634_create_jobs_table.php`
- **Worker:** `artisan queue:work --stop-when-empty --tries=3 --max-time=55` — runs every minute via cron.
- Contact-admin form sends mail via `->queue()` (non-blocking).

---

## 23. Sessions & Cache

- **Session driver:** `database` → `sessions` table.
- **Cache driver:** `database` → `cache` + `cache_locks` tables.
- Switched from `file` on 2026-07-20 for reliability under concurrent load.

---

## 24. Dark Mode / Theming

- Default theme: `light` (set as `data-theme="light"` on `<html>`).
- Toggle button: `#kDarkToggle` in navbar.
- Preference persisted to `localStorage` key `k_theme`.
- CSS variables drive the full theme: `--k-bg`, `--k-surface`, `--k-text-primary`, etc.

---

## 25. Sitemap

- **Controller:** `app/Http/Controllers/Frontend/SitemapController.php`
- **Route:** `GET /sitemap.xml`

---

## 26. Moderation

- **Controller:** `app/Http/Controllers/Admin/ModerationController.php`
- **Routes:** `GET /admin/approvals`, `GET /admin/inactive`
- All listing/store/deal tables paginated at 50 per page with named page params to avoid collision.

---

## 27. Settings

- **Controller:** `app/Http/Controllers/Admin/SettingController.php`
- **Model:** `app/Models/Setting.php`
- **Migration:** `2026_06_16_000201_create_settings_table.php`

---

## 28. Security hardening (applied July 2026)

| Item | Where |
|---|---|
| Super-admin promotion guard | `Admin/UserManagementController.php` |
| Notification link host validation | `Admin/AdminNotificationController.php` |
| CSRF exception narrowed to specific routes | `bootstrap/app.php` |
| Chat message rate limiting | `routes/web.php` |
| Category/price validation on products | `Dashboard/StoreProductController.php` |
| Ad banner URL scheme validation | `routes/web.php` |
| CSV import 500-row cap + strip_tags | `routes/web.php` |
| Listing ownership check in chat | `Dashboard/ChatController.php` |
| Fail-closed `hasPermission()` | `app/Models/User.php` |
| AI assist key not exposed in diagnostics | `Dashboard/AiAssistController.php` |
| PayHere sandbox default `false` | `config/services.php` |
