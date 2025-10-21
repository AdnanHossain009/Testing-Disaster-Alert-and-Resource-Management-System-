FULL PROJECT DOCUMENTATION
Testing Disaster Alert & Resource Management System

Date: 2025-10-21

PURPOSE
-------
This document explains the full project structure, implementation details, logic locations, data flows, and step-by-step instructions to create database tables, run the system and demonstrate features (including AI weather alerts, mapping logic, PWA offline behavior, PDF export, Mailtrap email testing, role-based access, notifications, and middleware).

Overview
--------
This Laravel app provides:
- Public citizen portal to submit emergency requests and view alerts & shelters
- Admin panel to manage alerts, shelters, requests, notifications, analytics
- Relief worker dashboard
- Auto-assignment of requests to shelters when admin inactive
- AI Weather Alert Prediction using OpenWeatherMap and automated Alert creation
- PWA features with offline caching & background sync
- Push/in-app notifications, email notifications via Mailtrap (for testing)
- PDF and TXT export of analytics

Top-level folders of interest
-----------------------------
- app/
  - Models/
  - Http/Controllers/
  - Http/Middleware/
  - Services/
  - Notifications/
  - Events/
  - Console/Commands/
- config/ (app, auth, mail, weather, queue, etc.)
- database/
  - migrations/ (all migration files)
  - seeders/
- resources/
  - views/ (Blade templates: admin, alerts, requests, shelters, emails)
  - js/ (app.js, live-dashboard.js, push-notifications.js)
- public/
  - service-worker.js (PWA)
  - manifest.json
- routes/
  - web.php
  - console.php

Tables (migrations) & purpose
-----------------------------
Key migrations (database/migrations):
- 0001_01_01_000000_create_users_table.php
  - Users table: base user fields + role (admin, citizen, relief_worker), last_activity (via later migration)
- 0001_01_01_000001_create_cache_table.php, 0002_create_jobs_table.php — standard Laravel housekeeping
- 2024_01_01_000003_create_alerts_table.php
  - Alerts table: auto or admin-created alerts. Fields: title, description, type, severity, location, lat/lon, status, issued_at, expires_at, created_by. Used for all public alerts.
- 2024_01_01_000004_create_shelters_table.php
  - Shelters: location, capacity, current_occupancy, contact, status, facilities. Used to host affected people and map to assignments.
- 2024_01_01_000005_create_requests_table.php
  - Requests (HelpRequest): citizen-submitted emergency requests. Fields: user_id, name, phone, request_type, location, lat/lon, people_count, urgency, status, assigned_at, assigned_by, admin_notes.
- 2024_01_01_000006_create_assignments_table.php
  - Assignments: Links requests to shelters. Fields: request_id, shelter_id, assigned_by, assigned_at, status, checked_in_at, checked_out_at.
- 2025_10_19_052143_create_in_app_notifications_table.php
  - In-app notifications stored for admin and citizens
- 2025_10_19_043950_create_sms_notifications_table.php
  - SMS notification logs
- 2025_10_16_000001_create_push_subscriptions_table.php
  - Push subscriptions for browser push notifications
- 2025_10_20_042818_add_last_activity_to_users_table.php
  - Adds last_activity to users (used to detect admin inactivity)

Models and where logic lives
---------------------------
(app/Models)
- User.php
  - Standard Laravel model, plus roles (admin, citizen, relief_worker), last_activity field used by TrackAdminActivity middleware and scheduler checks.
- Request.php (HelpRequest / Request)
  - Represents citizen requests. Methods: assignToShelter(), scopes (pending, urgent, byType, recent), accessors for UI colors & icons. Relationships: user(), assignedBy(), assignment(). Business logic: assignToShelter creates Assignment and updates request status.
- Assignment.php
  - Links a request to a shelter. Tracks assigned_by, assigned_at, status. Used by NotificationService to notify assignment.
- Alert.php
  - AI or admin-generated alerts. Methods: isActive(), isExpired(), scopes (active, recent), UI helpers (severity color, icon). Created by admin via AlertController::store or automatically by WeatherAlertService.
- Shelter.php
  - Shelter logic: available capacity, occupancy percentage, distanceFrom() helper (Haversine formula), scopeAvailable(), updateOccupancy(). Used heavily by AutoAssignService and RequestController.
- PushSubscription.php, InAppNotification.php
  - Push subscription records and in-app notification model; used by NotificationService and push API routes.

Controllers & responsibilities
-----------------------------
(app/Http/Controllers)
- AlertController.php
  - Public index/show, adminIndex (paginate), create/store/edit/update/destroy for admin. When store() creates an alert, NotificationService->notifyAlertCreated() is called to enqueue in-app notifications and optionally email.
- RequestController.php
  - Public create/store/show/index; Admin adminIndex, showAssign, assign, bulkAssign, updateStatus. Key logic: getCoordinatesFromLocation (simple mapping), autoAssignShelter (Haversine SQL via selectRaw), checkAdminAvailability() (demo simulation in code), event broadcasting (NewRequestSubmitted, RequestStatusUpdated), NotificationService usage for in-app notifications & email via Notifications.
- ShelterController.php
  - CRUD for shelters (public index/show and admin index/create/edit). Shelter availability and maps displayed through view integration.
- AnalyticsController.php
  - Generates analytics data, exportPDF (uses barryvdh/laravel-dompdf -> Pdf::loadView()), exportTXT. Aggregates data from alerts, shelters, requests, users, assignments.
- NotificationController.php
  - API endpoints to get inbox counts, mark read, subscribe/unsubscribe push, update preferences, send test push. Called by frontend JS (push-notifications.js).
- AuthController.php
  - Simple auth scaffolding: login, register, dashboards by role (admin, citizen, relief worker).
- SMSController.php
  - SMS gateway integration (SMSGatewayService). Logs SMS in sms_notifications table.

Services (business logic & integrations)
----------------------------------------
(app/Services)
- AutoAssignService.php
  - Periodically checks pending requests and auto-assigns to nearest available shelters when admin is inactive. Uses Request::whereDoesntHave('assignment') and Shelter Haversine calculation. Updates shelter occupancy and creates Assignment records or calls Request::assignToShelter.
- WeatherService.php
  - HTTP client wrapper for OpenWeatherMap. Methods: getCurrentWeather(lat,lon) (cached), getForecast, parseWeatherData, analyzeDangerousConditions (temperature, wind, rain, pressure, visibility checks). Caching via Cache::remember.
- WeatherAlertService.php
  - Orchestrator: checkAllLocations() or checkWeatherForLocations(), runs WeatherService, detects threats, prevents duplicate alerts (within 6 hours), creates Alert records (issued_at, expires_at), calls NotificationService->notifyAlertCreated(). Integrated with CheckWeatherAlerts command and scheduled hourly.
- NotificationService.php
  - Centralized in-app notification creation (InAppNotification), mapping of severities to colors. Methods: notifyAlertCreated(), notifyRequestSubmitted(), notifyShelterAssigned(), notifyStatusUpdated(), markAllAsSeen(), getUnseenCount().
- SMSGatewayService.php
  - Abstraction for SMS provider (for production), stores logs in sms_notifications table.

Console Commands & Scheduling
-----------------------------
(app/Console/Commands)
- AutoAssignRequests.php or similar: runs auto-assignment manually. Scheduled every 5 minutes in routes/console.php.
- CheckWeatherAlerts.php: `php artisan weather:check` manual command. Calls WeatherAlertService to check monitored_locations (config/weather.php). Scheduled hourly in routes/console.php.

Routes & route-level logic
--------------------------
(routes/web.php)
- Public routes: dashboard (alerts), alerts.index/show, shelters.index/show, requests.create/store/index/show.
- Auth routes: login, register, logout.
- Role-based dashboards: /admin/dashboard, /citizen/dashboard, /relief/dashboard.
- Admin group (prefix 'admin', middleware 'nocache'): admin.alerts CRUD, admin.shelters CRUD, admin.requests management, admin analytics export (pdf/txt), admin notifications view.
- Citizen group (prefix 'citizen') for citizen-only pages.
- Notification API endpoints (api/notifications/subscribe etc.) used by JS to register push subscriptions.
- Test routes: /test-pusher, /test-status-update, /test-push-notification for demo/testing.

Middleware
----------
(app/Http/Middleware)
- NoCacheMiddleware.php
  - Adds headers to prevent caching (used on admin routes to ensure fresh nav & counts)
- TrackAdminActivity.php
  - If authenticated user role == 'admin', set last_activity = now() on every request. Used by AutoAssignService to detect admin inactivity (if last_activity older than threshold, auto-assign kicks in).
- Other standard Laravel middleware: session, CSRF, auth, etc. Middleware aliases registered in bootstrap/app.php.

Events & Notifications
----------------------
(app/Events)
- NewRequestSubmitted.php — Fired when citizen creates a request, listened by real-time code via broadcasting (Pusher or Laravel Echo) to update admin dashboard.
- RequestStatusUpdated.php — Fired on status changes; broadcast to update dashboards.

(app/Notifications)
- RequestSubmittedNotification.php
- ShelterAssignedNotification.php
- StatusUpdatedNotification.php
- AlertCreatedNotification.php

These use Laravel's Notification system. Email delivery in .env is configured to Mailtrap (sandbox) in development. Example `.env` entries:
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=... MAIL_PASSWORD=...

Mailtrap integration
--------------------
- Mailtrap is configured in `.env` and `config/mail.php` to use the Mailtrap SMTP credentials. All outgoing emails (notifications like request submitted, shelter assigned, status updated) are routed to Mailtrap inbox so you can inspect messages during development.
- To test, check Mailtrap dashboard for the configured inbox credentials.

Push & In-app Notifications
---------------------------
- `resources/js/push-notifications.js` handles registration for push notifications and sends the subscription to `/api/push-subscription`.
- Service worker `public/service-worker.js` handles push events and displays notifications. The NotificationService writes in-app notifications to DB.
- The NotificationController exposes endpoints for subscribing/unsubscribing and retrieving counts used in the nav.

Maps & Distance Logic
---------------------
- Mapping is integrated via plain coordinates stored on shelters and requests.
- Shelter::distanceFrom($latitude, $longitude) uses Haversine calculation in PHP to compute distance for display.
- SQL-level Haversine used in selectRaw to find nearest shelters in RequestController::autoAssignShelter() and AutoAssignService: selectRaw(...) with 6371 * acos( ... ) as distance and orderBy('distance').
- Views embed map libraries (e.g., Leaflet or Google Maps in JS views) to show shelters/requests using their lat/lon.

AI (Weather) Integration & Alert creation flow
---------------------------------------------
1. `config/weather.php` defines monitored_locations, thresholds, map of conditions → disaster type.
2. `app/Services/WeatherService.php` calls OpenWeatherMap API (using Http::get) for current weather, caches results for 30 minutes, and analyzes dangerous conditions via analyzeDangerousConditions().
3. `app/Services/WeatherAlertService.php` orchestrates checks across locations and calls createWeatherAlert() when threats detected. It prevents duplicates by checking Alerts table for similar recent alerts.
4. `app/Console/Commands/CheckWeatherAlerts.php` manually triggers WeatherAlertService. The scheduler in `routes/console.php` schedules hourly.
5. Alerts are created by inserting rows into `alerts` table with source = 'Weather System' and created_by = admin id (system admin user). NotificationService->notifyAlertCreated() is invoked to push in-app notifications and optionally email.

PWA & Offline behavior
----------------------
- `public/service-worker.js` (and SW in resources) implements:
  - install event: caches essential pages (dashboard, alerts, shelters, admin pages)
  - activate: clears old caches
  - fetch: network-first for API endpoints (to always try fresh data) and cache-first for assets/pages
  - push event: handles push payload to show notification
  - background sync: tags sync-alerts, sync-shelters to refresh cached API responses when the device comes online.
- `manifest.json` under `public/manifest.json` configures the PWA (icons, name, theme).
- Frontend registers service worker and uses IndexedDB/cache to store data for offline viewing.

PDF / Reports
-------------
- `AnalyticsController::exportPDF()` uses `barryvdh/laravel-dompdf` (Pdf facade) to render `resources/views/admin/reports/pdf-report.blade.php` into PDF and return download.
- `exportTXT()` builds a text report and returns as plain attachment.

Admin Operations (Notifications & Routes)
-----------------------------------------
- Admin has an inbox (`admin/inbox`) which reads in-app notifications, with routes for marking read & marking all read. These actions call NotificationController methods via POST routes defined in `routes/web.php`.
- Admin notifications are created by NotificationService when alerts are created, when new requests arrive, when requests are assigned, and when status changes.

Role-based system & Middleware
-----------------------------
- Roles are stored in `users.role` and used across controllers to gate pages and logic.
  - AuthController routes to different dashboards based on role.
  - AnalyticsController::index and admin actions check `Auth::user()->role === 'admin'` before proceeding.
- TrackAdminActivity middleware updates `users.last_activity`. AutoAssignService checks last_activity to determine admin inactivity.
- NoCacheMiddleware ensures admin pages aren't cached by browsers so badges/counts remain up-to-date.

Mailtrap & Email flows
----------------------
- Notification classes send mail via Laravel Notification system. Since `.env` config uses Mailtrap SMTP, all emails are captured by Mailtrap (development/test). The emails templates are in `resources/views/emails/*.blade.php` (request-submitted, shelter-assigned, status-updated, alert-created).

PDF Files and Paths
-------------------
- Template: `resources/views/admin/reports/pdf-report.blade.php`
- Controller: `app/Http/Controllers/AnalyticsController.php` -> exportPDF()
- Library: `barryvdh/laravel-dompdf` (Pdf facade) referenced in composer.json dependencies.

How to create DB tables & make features visible (step-by-step)
--------------------------------------------------------------
This section shows the exact workflow a developer/student should follow when adding a new feature that touches model, migration, controller, route and view.

General sequence for adding a feature and making it visible on the site
1. Create Migration + Model
   - php artisan make:model MyModel -m
   - Edit migration in database/migrations/*_create_my_models_table.php
   - Add fields and constraints
2. Run migrations
   - php artisan migrate
3. Create Controller
   - php artisan make:controller MyModelController
   - Add methods: index, show, create, store, edit, update, destroy as needed
4. Add Routes
   - Edit routes/web.php — add public and admin routes and protect with middleware if necessary
5. Create Views
   - resources/views/my_models/*.blade.php — list, detail, form templates
6. Add Logic/Services
   - If complex logic needed, create a service in app/Services and unit test it
7. Unit/Feature Tests (optional)
   - Write tests in tests/Unit or tests/Feature
8. Seed sample data (optional)
   - Add a Seeder and run php artisan db:seed --class=MySeeder
9. Test on browser
   - php artisan serve
   - Open the route to verify UI

Commands for this project specifically (migrate, seed, run):
```bash
cd "d:\xampp\htdocs\Testing Disaster Alert Real\disaster-alert"
# Install composer dependencies (if needed)
composer install

# Ensure .env is configured (DB connection, MAIL settings, OPENWEATHER_API_KEY)
php artisan key:generate

# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed --class=DemoDataSeeder

# Optionally seed users
php artisan db:seed --class=AuthUsersSeeder

# Start local server
php artisan serve --host=127.0.0.1 --port=8000

# Clear config (after .env changes)
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Manually trigger weather check and auto-assign
php artisan weather:check
php artisan requests:auto-assign

# Run scheduler in dev
php artisan schedule:work
```

Paths: model -> migration -> controller -> route -> view example
----------------------------------------------------------------
Example: Alert feature
1. Model: `app/Models/Alert.php`
2. Migration: `database/migrations/2024_01_01_000003_create_alerts_table.php`
3. Controller: `app/Http/Controllers/AlertController.php` (methods index, show, adminIndex, create, store, update, destroy)
4. Routes: `routes/web.php` — public `/alerts` and admin `/admin/alerts` CRUD routes
5. Views: `resources/views/alerts/index.blade.php`, `resources/views/alerts/show.blade.php`, admin views in `resources/views/admin/alerts/*`.
6. Notifications: `app/Services/NotificationService::notifyAlertCreated()` and `app/Notifications/AlertCreatedNotification.php` for emails/in-app.

Detailed file-by-file list (major files & purpose)
-------------------------------------------------
(Only app-level and project files; vendor is excluded)

app/Models:
- User.php — user model: auth fields, role, last_activity, helper methods
- Alert.php — alert model: scopes active/recent, UI helpers
- Request.php or HelpRequest.php — help request model: assignToShelter, scopes, relationships
- Assignment.php — assignment model linking request to shelter
- Shelter.php — shelter model: occupancy, distance calculations, availability
- InAppNotification.php — in-app notification store
- PushSubscription.php — browser push subscription data

app/Http/Controllers:
- AlertController.php — public & admin alert management
- RequestController.php — citizen requests management, auto-assignment logic, event broadcasting
- ShelterController.php — shelter CRUD and public details
- AnalyticsController.php — analytics, PDF/txt export
- NotificationController.php — in-app & push API endpoints
- AuthController.php — authentication and role-based dashboards
- SMSController.php — simple SMS gateway controller for tests

app/Http/Middleware:
- TrackAdminActivity.php — updates last_activity for admin
- NoCacheMiddleware.php — prevents page caching for admin areas

app/Services:
- WeatherService.php — OpenWeather API client and analyzer
- WeatherAlertService.php — orchestrates creating alerts from weather
- AutoAssignService.php — auto-assigns pending requests to shelters when admin inactive
- NotificationService.php — creates in-app notifications and provides counts
- SMSGatewayService.php — abstraction over SMS provider

app/Console/Commands:
- CheckWeatherAlerts.php — artisan command: `php artisan weather:check`
- AutoAssignRequests.php — artisan command for auto-assign (if present)

app/Notifications:
- RequestSubmittedNotification.php — email to citizen on request submission (views in resources/views/emails)
- ShelterAssignedNotification.php — email to citizen upon assignment
- StatusUpdatedNotification.php — email to citizen when request status changes
- AlertCreatedNotification.php — optional email to admin or subscribed users

resources/views (high-level):
- alerts/index.blade.php, alerts/show.blade.php
- requests/create.blade.php, requests/index.blade.php, requests/show.blade.php, requests/success.blade.php
- shelters/index.blade.php, shelters/show.blade.php
- admin/* — admin dashboards, alerts, shelters, requests, analytics, notifications views
- emails/*.blade.php — Mail templates used by notifications
- admin/reports/pdf-report.blade.php — template for PDF export

resources/js:
- app.js — main frontend bootstrap
- live-dashboard.js — handles realtime updates (Pusher/Echo)
- push-notifications.js — registers service worker and handles push subscriptions

public/
- service-worker.js — PWA service worker: caching, push, sync
- manifest.json — PWA manifest

config/
- weather.php — weather monitoring config (monitored locations, thresholds, mapping)
- mail.php / services.php — mail & service provider configurations (Mailtrap in .env)

How AI-generated alerts are created (step-by-step)
-------------------------------------------------
1. Scheduler triggers `weather:check` every hour (in routes/console.php).
2. `CheckWeatherAlerts` command calls `WeatherAlertService->checkWeatherForLocations()`.
3. For each monitored location in `config/weather.php`, WeatherService fetches current weather and analyzeDangerousConditions() determines threats.
4. If threats found and no duplicate alert recently, WeatherAlertService->createWeatherAlert() inserts a row into `alerts` with `source = 'Weather System'`.
5. NotificationService->notifyAlertCreated() is called to write InAppNotification and optionally call Notification classes (email) which are delivered via Mailtrap in dev.
6. Admin dashboard & public dashboard will reflect the new alert (AlertController::dashboard/index use Alert::active()).

How offline PWA works (summary)
-------------------------------
- Service worker caches essential pages & API responses; assets and pages served via cache-first strategy.
- API calls use network-first strategy (fetched and cached) so cached responses are used when offline.
- Background sync syncs alerts and shelters when connectivity returns.
- Frontend uses service worker messages to refresh UI when cached data is updated.

Mailtrap test setup
-------------------
1. Sign up on Mailtrap and get SMTP credentials.
2. Add the credentials to `.env` (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS).
3. On actions that send emails (request submitted, shelter assigned), check Mailtrap inbox to view the test emails.

Role-based logic & middleware summary
------------------------------------
- Role field in users table: controllers check user role via `Auth::user()->role`.
- AnalyticsController and admin-only routes explicitly redirect if role !== 'admin'.
- TrackAdminActivity middleware updates last_activity timestamp used by AutoAssignService.

PDF report generation (where implemented)
-----------------------------------------
- `app/Http/Controllers/AnalyticsController::exportPDF()` composes data and calls `Pdf::loadView('admin.reports.pdf-report', $data)` to generate PDF (barryvdh/laravel-dompdf must be installed).

Notifications (4 operations in admin)
-------------------------------------
Admin inbox supports: view, mark read single (`notifications.read`), mark all as read (`notifications.admin.read-all`), and test send (`notifications.test`). Routes are in `routes/web.php` and handlers in `NotificationController` which rely on `NotificationService` for DB operations. In-app notifications are stored in `in_app_notifications` table.

Testing & demo tips
-------------------
- Use `php artisan tinker` to query models: `App\Models\Alert::where('source','Weather System')->latest()->take(5)->get()`
- Trigger test requests via `/test-pusher` and `/test-status-update` routes to demonstrate real-time updates.
- Run `php artisan weather:check` and `php artisan requests:auto-assign` manually to demo AI + auto-assignment.
- Use Mailtrap inbox to show email notifications generated by the system.
- Use Chrome DevTools Application > Service Workers and Cache Storage to demonstrate PWA caching and service worker registration.

If you want every single file verbatim
-------------------------------------
This document focuses on the application-level files. If you need an exact, exhaustive per-file listing including small helpers and blade partials, I can generate an expanded file list and summary (or include the full contents). Tell me which format you prefer (single large file, zip of docs, or separate file per section).

Deliverable created
-------------------
- CREATED: `FULL_PROJECT_DOCUMENTATION.md` (this file)

Next steps I can do for you
--------------------------
- Produce a per-file single-line summary for every file in the repo (I can auto-generate by scanning the project).
- Generate a short demo script and slide-ready bullet points.
- Create a README.md with quick start commands and demo checklist (shorter than this doc).
- Export this documentation to downloadable TXT/PDF via the project's export flow.

Feedback & changes
------------------
If you'd like to tweak level of detail (more/less), or want me to expand specific areas (e.g., exact SQL schema for each migration, full list of blade files with purpose, or to include code snippets for each major function), tell me which area to expand and I'll update the file.


End of FULL_PROJECT_DOCUMENTATION.md
