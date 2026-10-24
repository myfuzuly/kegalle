# Kegalle Marketplace — coding standards

Laravel 11 app for kegalle.com (classifieds + stores for the Kegalle district). Brand is **Kegalle** — never "Kurulla".

## Structure

| What | Where |
|---|---|
| App code (controllers, models, services, views, routes) | `app_core/` → deployed to `/app_core/` on the server |
| Web root: static CSS/JS/images, `index.php` | `public_html/` → deployed to `/public_html/` |
| Vite-built CSS/JS | `app_core/resources/{css,js}` → `npx vite build --emptyOutDir=false` → `app_core/public/vite-dist` (upload to both `/app_core/public/vite-dist` and `/public_html/vite-dist`) |
| Reusable UI | `app_core/resources/views/components/` (anonymous Blade components, used as `<x-admin.bulk-bar>`, `<x-form.progress>`) |
| Business logic | `app/Services/` — controllers stay thin |

Static CSS: `kegalle-admin-ds.css` (admin + seller design system, newest rules win), `kegalle-dashboard.css`, `kegalle-frontend.css`.
Static JS: `kegalle-admin-ui.js` (admin/seller behaviours), `kegalle-actions.js` (public-site `data-*` behaviours: autosubmit, data-call, image fallbacks…), `kegalle-ad-form.js` (post-ad form).
`kegalle-migrated.css` holds former inline styles (generated, linked from all three layouts); `resources/css/kegalle-components.css` holds shared public components (push prompt, footer accordion).
Bump the `?v=N` on the `<link>`/`<script>` tag in the layout whenever a static file changes.

## Rules

1. **No inline code in Blade.** The CSP blocks `style="…"` attributes and `onclick`/`onsubmit` handlers.
   - Styles → a class in the CSS files. Dynamic values (widths, colours from data) → set via JS (`el.style.x = …`) or a data attribute.
   - Behaviour → `data-*` attributes handled in a JS file with `addEventListener` (e.g. `data-confirm`, `data-autosubmit`, `data-bulk-bar`).
   - A page-specific `<script>` must carry `nonce="{{ $cspNonce ?? '' }}"`; prefer a file in `public_html/js/`.
2. **Reuse before you write.** If a UI piece appears twice, make it a component in `resources/views/components/` with a header comment naming its JS/CSS. Existing: `admin/bulk-bar`, `admin/row-check`, `admin/export-button`, `admin/table-empty`, `form/progress`, `form/draft-banner`, `form/field-hint`.
3. **Security at the boundary.**
   - Validate every request (Form Requests or `$request->validate`). Never `$guarded = []`.
   - Uploaded files are named from their content: `$file->guessExtension()`, never the client's extension.
   - User HTML goes through `App\Helpers\HtmlSanitizer::clean()`; URLs through its allowlist.
   - No secrets or passwords in code, migrations or seeders — use `.env`.
   - Raw SQL only with bound values or `intval`-ed lists.
4. **Data integrity.** Multi-step writes go in `DB::transaction`. Use `createOrFirst`/`firstOrCreate` where a double-click could race a unique index. Columns that users must not set stay out of `$fillable`; admins set them with `forceFill()`.
5. **Performance.** Eager-load every relation a list shows (`->with([...])`) — outside production, `AppServiceProvider` logs each N+1 lazy load to `storage/logs/n1.log` with the file and line; keep that file empty. Per-row stats (ratings, counts) must be batched (see `Store::stats()`), never queried per card. Paginate, and cache expensive public queries. Guest pages may use `cache.page:<ttl>` — anything per-visitor (CSRF token, CSP nonce) is swapped by `CachePublicResponse::personalise()`; never render user-specific data on a cached route.
6. **Time.** The app clock is UTC; schedules run in `Asia/Colombo` via `->timezone('Asia/Colombo')`.
7. **Accessibility.** Every input has a label (or `aria-label`), touch targets ≥ 44px on mobile, visible `:focus-visible`, respect `prefers-reduced-motion`.

## Testing

- `php vendor/bin/phpunit` (PHP 8.3, Herd locally). CI runs on MySQL; some queries (`FIELD()`, `HAVING` aliases) only work on MySQL.
- New risky logic (money, auth, uploads, data writes) gets a feature test — see `tests/Feature/AuditFixesTest.php`.
- Check UI changes at 1366px and 390px before deploying.

## Deploying

FTP only (no SSH). Upload scripts live next to this repo (`../deploy_*.py`): they connect to `213.165.241.225` (kegalle.com is behind Cloudflare, which carries no FTP), compare every live file with git first, and upload nothing if a live file was changed on the server. Run migrations from cPanel Terminal: `cd ~/app_core && php artisan migrate --force`.
