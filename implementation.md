## Plan: HK Travel — Production-Ready WordPress-style Laravel Travel Platform (Revised)

**TL;DR** Single Laravel 13 + Livewire 4 + Tailwind v4 app, packaged WordPress-style. Every travel vertical is an **independent toggleable module**. **Both admin and public theme use a custom in-house Tailwind v4 + Livewire/Alpine component library** (no Flux, no third-party UI lib) for total design and behavior control. SPA via `wire:navigate`. File-driver caching (no Redis). **Local-disk storage only this release**, with a `StorageManager` abstraction and S3/Spaces/GCS adapters scaffolded-but-disabled for future expansion. **Cloudflare Turnstile** captcha shipped on all public forms (hCaptcha + reCAPTCHA v3 adapters scaffolded). Security, PageSpeed-100, accessibility, SEO baked in from step 1. Flat ordered checklist below — no phases.

### Architecture principles
- Single file, single responsibility; logic in services/actions; thin controllers.
- No noisy comments; document non-obvious "why" only.
- Feature flags everywhere; disabled = invisible everywhere.
- SPA with `wire:navigate` + `@persist` + skeletons.
- File-driver cache, observer-based invalidation, `responsecache` for guests.
- Security baseline: CSRF, signed URLs, rate limits, 2FA, CSP/HSTS, encrypted PII, Eloquent-only, HTMLPurifier, signed updater, OWASP Top-10 audit.
- Performance: Tailwind v4 JIT, defer JS, critical CSS, WebP/AVIF + srcset, self-hosted fonts, indexed FKs/slugs.
- WCAG 2.1 AA.
- **Custom UI kit principle:** every interactive element lives in `resources/views/components/ui/*` (Tailwind v4 + Alpine), consumed identically by admin and themes.
- Do not use manual file editing (e.g. for config or env); build admin UIs and artisan commands to manage everything, with sensible defaults so a clean install is usable out of the box.
- Do not create or edit migrations files manually; build artisan commands that generate them with the correct boilerplate, and only edit those generated files if necessary. always use the cli to create migrations to ensure consistency and avoid human error.

### Steps (flat, ordered)

**Foundation**
1. `laravel new hk-travel --database=sqlite --livewire --npm --boost --no-interaction`; init git.
2. Install core packages (Spatie permission/translatable/medialibrary/activitylog/responsecache/sluggable/backup/sitemap, mcamara/laravel-localization, intervention/image, dompdf, google2fa, secure-headers, mews/purifier, sanctum, scout). **No Flux.** npm: tailwindcss v4, `@tailwindcss/vite`, alpinejs + plugins (focus, collapse, intersect, sort).
3. Tailwind v4 with shared `tokens.css` (`@theme {}`) + two entries: `app.css` (public) and `admin.css`.
4. **Custom UI kit scaffold** in `resources/views/components/ui/` — button, input, textarea, select, checkbox, radio, switch, badge, card, modal, drawer, dropdown, menu, table, tabs, accordion, tooltip, toast, alert, breadcrumbs, pagination, skeleton, spinner, progress, datepicker, daterangepicker, file-uploader, image-cropper, rich-text, kanban-board, calendar, color-picker, code-editor.
5. Folder layout: `app/Core/{Settings,Theme,Installer,Localization,Modules,Permalink,Cache,Security,Storage,Captcha,Notifications,Email}` + `app/Modules/`. Build `ModuleManager` + base `ModuleServiceProvider`.
6. `config/hk.php` + DB-overridable file-cached `SettingsRepository`.
7. **`StorageManager`** wrapping Laravel filesystem; ships `local` (public + private); S3/Spaces/GCS adapters present but disabled with admin "Coming soon" placeholders. Every admin image input goes through `<x-ui.image-picker>` → `MediaUploadController` → `StorageManager::publicDisk()`, so swapping in a cloud disk later requires no view edits.
8. Web installer wizard (lock file): requirements → DB choice → URL/locale/timezone → admin user → modules to enable → captcha keys (skippable) → write `.env` → migrate+seed → cache warm → write lock.

**Auth, Users, Security, Captcha**
9. Auth & user management (verify, throttle, profile, avatar, GDPR delete/export).
10. 2FA TOTP + recovery codes, role-enforceable.
11. RBAC roles (super-admin/admin/manager/agent/editor/customer/`developer`) + per-module auto-permissions.
12. Security headers + HTTPS-redirect + per-group rate limiters + honeypots + HTMLPurifier.
13. **Captcha layer** — `CaptchaService` contract; **Cloudflare Turnstile shipped driver**; hCaptcha + reCAPTCHA v3 scaffolded. Admin → Security → Captcha (keys, enable globally, pick which forms). `<x-ui.captcha />` component + server `captcha` validation rule. No-op when keys missing.
14. Audit log on admin + auth events.

**Settings, Branding, Localization, Permalinks**
15. Settings UI (custom kit): General, Contact, SEO defaults, Analytics, Cookie banner.
16. Branding (color CSS vars, font picker, header/footer toggles, logo/favicon).
17. Localization: URL prefixes, language manager (RTL), translatable fields, locale switcher, browser auto-detect.
18. Custom permalink structures via `PermalinkRouter` (collision detection + 301 log).
19. Email template manager (DB, per-locale, preview, test send, restricted templating).
20. Notification configuration (per-event channel matrix, drivers, per-user prefs, queued).

**Theme & Page Builder**
21. Theme engine + admin Theme browser.
22. Default theme `themes/default` (Tailwind v4, **consumes custom UI kit**, fully responsive, dark mode, micro-interactions, WebP/AVIF + LQIP, skeletons).
23. **Block-based page builder** (drag/drop via `@alpinejs/sort`, mobile/tablet/desktop visibility, all built-in blocks + Custom HTML/CSS/JS gated behind `developer` permission).
24. Menu manager (nested, per location, per-locale).
25. Widget zones (`@zone(...)`).
26. SEO + JSON-LD + sitemap split + robots editor + breadcrumbs.

**CRM, Contact, Leads**
27. Drag/drop contact form builder (Turnstile + honeypot).
28. Advanced Leads/CRM (pipelines, kanban via custom UI kit, activities, reminders, auto-assignment, merge, CSV import/export, conversion).
29. Customer dashboard.

**Travel modules (each independent + toggleable)**
30. Tours · 31. Hotels · 32. **Flights** (Stub + Amadeus + Duffel) · 33. **Trains** (Stub + GDS/Trainline scaffolds) · 34. Buses · 35. Taxi/Transfers · 36. Car rentals · 37. Cruises · 38. Activities · 39. Visa · 40. Destinations.

**Reviews, Comments, Blog**
41. Reviews & ratings (polymorphic, sub-criteria, moderation, schema.org).
42. Threaded comments (Blog + Pages opt-in) with Turnstile.
43. Blog (categories, tags, scheduled, related, RSS, auto-TOC).

**Bookings, Payments, Packages**
44. Cart & checkout (polymorphic, guest, promos, taxes/fees, multi-currency display).
45. Bookings core (state machine, voucher PDF, ICS, admin calendar+list+timeline).
46. Payment gateway framework — Stripe + Bank + Cash shipped; PayPal/Razorpay/Paystack scaffolded-disabled.
47. Advanced package manager (composes items across enabled modules, seasonal pricing, brochures, enquiry-only mode).

**Search, Performance, Caching, Updater, Deployment**
48. Unified `SearchService` (Scout + database engine; Meilisearch opt-in).
49. `CacheService` wrapper + `hk:cache:warm` + observer invalidation + HTTP cache headers + `responsecache`.
50. Frontend perf budget + **`hk:lighthouse` CI script targeting 100** on key pages.
51. Loaders & skeleton component library; Livewire `wire:loading` + skeleton fallbacks.
52. Queues (`database`) + scheduler + supervisor sample.
53. Backups (admin UI, local disk this release).
54. **Updater** (`hk:update`, signed manifest, rollback) + packager (`npm run package` zip).
55. Module marketplace hooks (admin zip drop-in, signature verified).
56. Deployment docs/scripts.
57. Pest test suite + Pint + PHPStan L6 + Lighthouse CI.
58. Pre-release security & perf audit (OWASP, audits, axe, CSP, rate limits, file-upload checks).

### Relevant files
- composer.json, package.json, vite.config.js
- config/hk.php, config/hk-modules.php
- resources/css/tokens.css, resources/css/app.css, resources/css/admin.css
- resources/views/components/ui/ — custom UI kit
- app/Core/Modules/ModuleManager.php, app/Core/Theme/ThemeManager.php, app/Core/Theme/PageBuilder.php, app/Core/Permalink/PermalinkRouter.php, app/Core/Settings/SettingsRepository.php, app/Core/Cache/CacheService.php, app/Core/Storage/StorageManager.php, app/Core/Captcha/CaptchaService.php, app/Core/Installer/InstallerController.php
- app/Modules/ — one folder per module
- resources/themes/default/ + resources/themes/default/theme.json
- routes/web.php, routes/admin.php, routes/install.php
- database/seeders/CoreSeeder.php + per-module seeders
- tests/Feature/

### Verification
- Installer completes on a clean machine and lands on a working seeded site.
- Toggling any module off removes routes, admin menu, public listings, sitemap, and search results.
- Theme switch swaps layout without losing pages/widgets.
- Custom HTML/CSS/JS blocks render only for `developer` users; sanitized otherwise.
- Permalink change auto-creates 301; collisions blocked at save.
- Email templates editable per locale; test send works.
- Turnstile blocks submissions with invalid/expired tokens; silent no-op when keys not configured.
- Stripe E2E: tour pick → checkout → test payment → email + voucher PDF + bookings visible.
- All uploads land on local disk; private files served via signed URLs.
- Lighthouse CI 100/100/100/100 on home/tour/hotel/blog/checkout (mobile).
- `composer audit`, `npm audit` clean; secure-headers verified; auth+checkout rate-limited; 2FA round-trip works.
- Pest green, PHPStan L6 clean, Pint clean.

### Decisions
- **Custom in-house UI kit only** — no Flux, no third-party UI library — admin and public theme share the same Blade/Alpine components driven by Tailwind v4 tokens.
- **Local-disk storage only this release**; S3/Spaces/GCS adapters scaffolded-but-disabled, swap-in later without API changes.
- **Cloudflare Turnstile captcha** shipped + enabled by default on every public form when keys configured; hCaptcha + reCAPTCHA v3 scaffolded.
- Stripe primary; Bank/Cash always; PayPal/Razorpay/Paystack scaffolded-disabled.
- Flights & Trains ship Stub + 2 real-provider scaffolds; user enables/picks/disables.
- Custom HTML/CSS/JS blocks gated behind `developer` permission.
- Excluded for now: AI itinerary, native mobile apps, multi-tenant SaaS DB, channel-manager sync, deep GDS, remote storage drivers.

Plan written to implementation.md is now superseded by this revision (saved in session memory). Reply with any tweaks, or approve and I can hand off to implementation.