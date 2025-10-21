# DISASTER ALERT & RESOURCE MANAGEMENT SYSTEM
## PART 1: ARCHITECTURE, DATABASE, MODELS, CONTROLLERS, ROUTES & MIDDLEWARE

---

## PROJECT OVERVIEW

This is a Laravel-based Disaster Management System with real-time features, AI weather monitoring, PWA support, role-based access control, and integrated notification systems (Email, SMS, Push, In-app).

### TECHNOLOGY STACK
- **Backend**: Laravel 11.x (PHP 8.2+)
- **Database**: MySQL
- **Frontend**: Blade Templates, Tailwind CSS, Leaflet.js Maps, Chart.js
- **Real-time**: Laravel Events & Listeners
- **PWA**: Service Workers for offline support
- **External APIs**: OpenWeatherMap API, Mailtrap.io (Email)
- **Maps**: Leaflet.js with OpenStreetMap

---

## SECTION 1: DATABASE ARCHITECTURE & MIGRATIONS

### MIGRATION CREATION ORDER
Follow this sequence when creating new tables:
1. **Create Migration** → 2. **Run Migration** → 3. **Create Model** → 4. **Create Controller** → 5. **Define Routes** → 6. **Create Views**

---

### 1.1 USERS TABLE

**Migration**: `database/migrations/0001_01_01_000000_create_users_table.php`  
**Purpose**: Store all users (Admin, Citizens, Relief Workers)  
**Created At**: Initial Laravel installation

#### SCHEMA

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK, auto-increment) | Primary key |
| name | string(255) | Full name |
| email | string(255, unique) | Email address |
| email_verified_at | timestamp (nullable) | Email verification |
| password | string(255, hashed) | Password |
| remember_token | string(100, nullable) | Remember me token |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Update timestamp |

#### Additional Fields
From migration `2024_01_01_000007_add_fields_to_users_table.php`:
- **role**: enum('admin', 'citizen', 'relief_worker', default: 'citizen')
- **phone**: string(20, nullable)
- **address**: text (nullable)
- **last_activity**: timestamp (nullable) - Added in `2025_10_20_042818`

#### PURPOSE
- Authentication and authorization
- Role-based access control (Admin, Citizen, Relief Worker)
- User profile management
- Track user activity for admin dashboard

#### RELATIONSHIPS
- **hasMany**: `requests` (help requests created by user)
- **hasMany**: `assignments` (relief worker assignments)
- **hasMany**: `alerts` (if admin created)
- **hasMany**: `pushSubscriptions`
- **hasMany**: `inAppNotifications`

---

### 1.2 CACHE TABLE

**Migration**: `database/migrations/0001_01_01_000001_create_cache_table.php`  
**Purpose**: Store Laravel cache data for performance optimization

#### SCHEMA
| Column | Type |
|--------|------|
| key | string(255, PK) |
| value | mediumtext |
| expiration | integer |

**Purpose**: Store temporary data, session cache, query results

---

### 1.3 JOBS TABLE (Queue System)

**Migration**: `database/migrations/0001_01_01_000002_create_jobs_table.php`  
**Purpose**: Handle background jobs (email sending, notifications, weather checks)

#### Jobs Table Schema
| Column | Type |
|--------|------|
| id | bigint (PK, auto-increment) |
| queue | string(255) |
| payload | longtext |
| attempts | unsignedTinyInteger |
| reserved_at | unsignedInteger (nullable) |
| available_at | unsignedInteger |
| created_at | unsignedInteger |

#### Failed Jobs Table Schema
| Column | Type |
|--------|------|
| id | bigint (PK, auto-increment) |
| uuid | string(255, unique) |
| connection | text |
| queue | text |
| payload | longtext |
| exception | longtext |
| failed_at | timestamp (default: CURRENT_TIMESTAMP) |

**Purpose**: Asynchronous processing of emails, SMS, push notifications

---

### 1.4 ALERTS TABLE

**Migration**: `database/migrations/2024_01_01_000003_create_alerts_table.php`  
**Purpose**: Store disaster alerts (cyclones, floods, earthquakes)

#### SCHEMA
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| title | string(255) | Alert title |
| description | text | Full description |
| severity | enum('Low', 'Medium', 'High', 'Critical') | Severity level |
| location | string(255) | Location name |
| latitude | decimal(10,8, nullable) | Coordinates |
| longitude | decimal(11,8, nullable) | Coordinates |
| is_active | boolean (default: true) | Active status |
| created_by | bigint (FK to users.id, nullable) | Creator |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Update time |

#### Additional Fields
From migration `2025_10_21_012934_add_source_to_alerts_table.php`:
- **source**: string(255, default: 'Manual') - Values: 'Manual' or 'Weather System'
- **affected_areas**: json (nullable) - Array of location names
- **instructions**: text (nullable) - Safety instructions

#### PURPOSE
- Broadcast disaster warnings to citizens
- AI-generated alerts from weather monitoring
- Manual alerts created by admin
- Display on public dashboard and alerts page

#### RELATIONSHIPS
- **belongsTo**: `creator` (User who created alert - admin)

---

### 1.5 SHELTERS TABLE

**Migration**: `database/migrations/2024_01_01_000004_create_shelters_table.php`  
**Purpose**: Store emergency shelter locations

#### SCHEMA
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| name | string(255) | Shelter name |
| location | string(255) | Address |
| capacity | integer | Max capacity |
| current_occupancy | integer (default: 0) | Current occupancy |
| facilities | text (nullable) | Comma-separated list |
| contact_person | string(255, nullable) | Contact name |
| contact_phone | string(20, nullable) | Phone number |
| latitude | decimal(10,8, nullable) | Coordinates |
| longitude | decimal(11,8, nullable) | Coordinates |
| is_active | boolean (default: true) | Active status |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Update time |

#### PURPOSE
- Display available shelters on map
- Track shelter capacity and occupancy
- Assign shelters to help requests
- Show nearest shelters to citizens

#### RELATIONSHIPS
- **hasMany**: `assignments` (people assigned to this shelter)

---

### 1.6 REQUESTS TABLE (Help Requests)

**Migration**: `database/migrations/2024_01_01_000005_create_requests_table.php`  
**Purpose**: Store emergency help requests from citizens

#### SCHEMA
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| name | string(255) | Requester name |
| phone | string(20) | Phone number |
| location | string(255) | Location text |
| latitude | decimal(10,8, nullable) | Coordinates |
| longitude | decimal(11,8, nullable) | Coordinates |
| request_type | enum('Food', 'Water', 'Medical', 'Shelter', 'Rescue', 'Other') | Type |
| urgency | enum('Low', 'Medium', 'High', 'Critical') | Priority |
| people_count | integer (default: 1) | Number of people |
| description | text (nullable) | Details |
| status | enum('Pending', 'Assigned', 'In Progress', 'Completed', 'Cancelled') | Status |
| user_id | bigint (FK to users.id, nullable) | Creator |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Update time |

#### PURPOSE
- Citizens submit emergency requests
- Admin views all requests on map
- Relief workers assigned to requests
- Track request status and completion

#### RELATIONSHIPS
- **belongsTo**: `user` (citizen who created request)
- **hasOne**: `assignment` (relief worker assignment)

---

### 1.7 ASSIGNMENTS TABLE

**Migration**: `database/migrations/2024_01_01_000006_create_assignments_table.php`  
**Purpose**: Link relief workers to help requests and shelters

#### SCHEMA
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| request_id | bigint (FK to requests.id) | Request |
| relief_worker_id | bigint (FK to users.id, nullable) | Worker |
| shelter_id | bigint (FK to shelters.id, nullable) | Shelter |
| assigned_at | timestamp | Assignment time |
| completed_at | timestamp (nullable) | Completion time |
| notes | text (nullable) | Notes |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Update time |

#### PURPOSE
- Assign relief workers to emergency requests
- Assign shelters to people
- Track assignment completion
- Enable relief worker dashboard

#### RELATIONSHIPS
- **belongsTo**: `request` (help request)
- **belongsTo**: `reliefWorker` (User with role 'relief_worker')
- **belongsTo**: `shelter` (assigned shelter)

#### INDEXES
- Foreign key: `request_id` → `requests(id)` ON DELETE CASCADE
- Foreign key: `relief_worker_id` → `users(id)` ON DELETE SET NULL
- Foreign key: `shelter_id` → `shelters(id)` ON DELETE SET NULL

---

### 1.8 PUSH SUBSCRIPTIONS TABLE

**Migration**: `database/migrations/2025_10_16_000001_create_push_subscriptions_table.php`  
**Purpose**: Store browser push notification subscriptions (PWA)

#### SCHEMA
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| user_id | bigint (FK to users.id, nullable) | User |
| endpoint | text | Browser push endpoint URL |
| public_key | string(255, nullable) | P256dh key |
| auth_token | string(255, nullable) | Auth secret |
| user_agent | text (nullable) | Browser/device info |
| is_active | boolean (default: true) | Active status |
| preferences | json (nullable) | Notification preferences |
| last_notified_at | timestamp (nullable) | Last notification |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Update time |

#### PURPOSE
- Enable browser push notifications
- Work offline with PWA
- Notify users about alerts and updates
- Track notification preferences

#### RELATIONSHIPS
- **belongsTo**: `user`

---

### 1.9 IN-APP NOTIFICATIONS TABLE

**Migration**: `database/migrations/2025_10_19_052143_create_in_app_notifications_table.php`  
**Purpose**: Store in-app notifications (4 operation types)

#### SCHEMA
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| user_id | bigint (FK to users.id) | Recipient |
| type | string(255) | Notification type |
| title | string(255) | Title |
| message | text | Message |
| data | json (nullable) | Additional metadata |
| is_read | boolean (default: false) | Read status |
| is_seen | boolean (default: false) | Seen status |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Update time |

#### NOTIFICATION TYPES
1. **alert_created** - New disaster alert
2. **request_submitted** - New help request
3. **status_updated** - Request status changed
4. **shelter_assigned** - Shelter assigned to request

#### PURPOSE
- Display notifications in admin/citizen inbox
- 4 types: Alert Created, Request Submitted, Status Updated, Shelter Assigned
- Track read/unread status
- Show notification count in header

#### RELATIONSHIPS
- **belongsTo**: `user` (recipient)

#### INDEXES
- Foreign key: `user_id` → `users(id)` ON DELETE CASCADE
- Index on: `user_id`, `is_read`, `created_at`

---

### 1.10 SMS NOTIFICATIONS TABLE

**Migration**: `database/migrations/2025_10_19_043950_create_sms_notifications_table.php`  
**Purpose**: Log SMS notifications sent (future feature)

#### SCHEMA
| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| phone | string(20) | Phone number |
| message | text | SMS content |
| status | enum('pending', 'sent', 'failed') | Status |
| sent_at | timestamp (nullable) | Send time |
| error_message | text (nullable) | Error details |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Update time |

#### PURPOSE
- Track SMS delivery status
- Retry failed SMS
- SMS notification history

---

## SECTION 2: ELOQUENT MODELS

### 2.1 USER MODEL

**Path**: `app/Models/User.php`

**Purpose**: Represents users (Admin, Citizen, Relief Worker)

#### KEY FEATURES
- Authentication (extends Authenticatable)
- Password hashing
- Role-based access control
- Activity tracking

#### FILLABLE FIELDS
```php
['name', 'email', 'password', 'role', 'phone', 'address', 'last_activity']
```

#### HIDDEN FIELDS
```php
['password', 'remember_token']
```

#### CASTS
```php
'email_verified_at' => 'datetime',
'password' => 'hashed',
'last_activity' => 'datetime'
```

#### RELATIONSHIPS
```php
public function requests() // hasMany(Request::class)
public function assignments() // hasMany(Assignment::class, 'relief_worker_id')
public function pushSubscriptions() // hasMany(PushSubscription::class)
public function notifications() // hasMany(InAppNotification::class)
```

#### HELPER METHODS
```php
isAdmin() // Check if user is admin
isCitizen() // Check if user is citizen
isReliefWorker() // Check if user is relief worker
```

---

### 2.2 ALERT MODEL

**Path**: `app/Models/Alert.php`

**Purpose**: Disaster alerts (manual or AI-generated)

#### FILLABLE FIELDS
```php
['title', 'description', 'severity', 'location', 'latitude', 'longitude', 
 'is_active', 'created_by', 'source', 'affected_areas', 'instructions']
```

#### CASTS
```php
'is_active' => 'boolean',
'latitude' => 'decimal:8',
'longitude' => 'decimal:8',
'affected_areas' => 'array',
'created_at' => 'datetime',
'updated_at' => 'datetime'
```

#### RELATIONSHIPS
```php
public function creator() // belongsTo(User::class, 'created_by')
```

#### SCOPES
```php
scopeActive() // Only active alerts
scopeBySource() // Filter by source (Manual/Weather System)
```

---

### 2.3 SHELTER MODEL

**Path**: `app/Models/Shelter.php`

**Purpose**: Emergency shelter locations

#### FILLABLE FIELDS
```php
['name', 'location', 'capacity', 'current_occupancy', 'facilities', 
 'contact_person', 'contact_phone', 'latitude', 'longitude', 'is_active']
```

#### CASTS
```php
'capacity' => 'integer',
'current_occupancy' => 'integer',
'is_active' => 'boolean',
'latitude' => 'decimal:8',
'longitude' => 'decimal:8'
```

#### RELATIONSHIPS
```php
public function assignments() // hasMany(Assignment::class)
```

#### HELPER METHODS
```php
availableCapacity() // Returns remaining capacity
isFull() // Check if shelter is at full capacity
occupancyPercentage() // Calculate occupancy percentage
```

---

### 2.4 REQUEST MODEL (Help Request)

**Path**: `app/Models/Request.php`  
**Alias**: `app/Models/HelpRequest.php` (same model, different name for clarity)

**Purpose**: Emergency help requests from citizens

#### FILLABLE FIELDS
```php
['name', 'phone', 'location', 'latitude', 'longitude', 'request_type', 
 'urgency', 'people_count', 'description', 'status', 'user_id']
```

#### CASTS
```php
'people_count' => 'integer',
'latitude' => 'decimal:8',
'longitude' => 'decimal:8',
'created_at' => 'datetime',
'updated_at' => 'datetime'
```

#### RELATIONSHIPS
```php
public function user() // belongsTo(User::class)
public function assignment() // hasOne(Assignment::class)
```

#### SCOPES
```php
scopePending() // Only pending requests
scopeByStatus() // Filter by status
scopeByUrgency() // Filter by urgency
```

#### EVENT TRIGGERS
- **On create**: Triggers `NewRequestSubmitted` event
- **On status update**: Triggers `RequestStatusUpdated` event

---

### 2.5 ASSIGNMENT MODEL

**Path**: `app/Models/Assignment.php`

**Purpose**: Link relief workers to requests and shelters

#### FILLABLE FIELDS
```php
['request_id', 'relief_worker_id', 'shelter_id', 'assigned_at', 
 'completed_at', 'notes']
```

#### CASTS
```php
'assigned_at' => 'datetime',
'completed_at' => 'datetime'
```

#### RELATIONSHIPS
```php
public function request() // belongsTo(Request::class)
public function reliefWorker() // belongsTo(User::class, 'relief_worker_id')
public function shelter() // belongsTo(Shelter::class)
```

#### HELPER METHODS
```php
isCompleted() // Check if assignment is completed
markCompleted() // Mark assignment as completed
```

---

### 2.6 PUSH SUBSCRIPTION MODEL

**Path**: `app/Models/PushSubscription.php`

**Purpose**: Browser push notification subscriptions

#### FILLABLE FIELDS
```php
['user_id', 'endpoint', 'public_key', 'auth_token', 'user_agent', 
 'is_active', 'preferences', 'last_notified_at']
```

#### CASTS
```php
'preferences' => 'array',
'is_active' => 'boolean',
'last_notified_at' => 'datetime'
```

#### RELATIONSHIPS
```php
public function user() // belongsTo(User::class)
```

#### HELPER METHODS
```php
shouldReceiveNotification($type, $urgency) // Check if should receive notification based on preferences
```

---

### 2.7 IN-APP NOTIFICATION MODEL

**Path**: `app/Models/InAppNotification.php`

**Purpose**: In-app notifications for admin and citizens

#### FILLABLE FIELDS
```php
['user_id', 'type', 'title', 'message', 'data', 'is_read', 'is_seen']
```

#### CASTS
```php
'data' => 'array',
'is_read' => 'boolean',
'is_seen' => 'boolean',
'created_at' => 'datetime'
```

#### RELATIONSHIPS
```php
public function user() // belongsTo(User::class)
```

#### SCOPES
```php
scopeUnread() // Only unread notifications
scopeForUser() // Filter by user
```

#### HELPER METHODS
```php
markAsRead() // Mark notification as read
markAsSeen() // Mark notification as seen
```

---

## SECTION 3: CONTROLLERS

### 3.1 ALERT CONTROLLER

**Path**: `app/Http/Controllers/AlertController.php`

**Purpose**: Handle all alert-related operations

#### PUBLIC METHODS

**dashboard()**
- **Route**: `GET /`
- **Purpose**: Public homepage with recent alerts and statistics
- **Returns**: `view('dashboard')` with alerts, stats
- **Logic**: Fetch 5 most recent active alerts, count total alerts/requests/shelters

**index()**
- **Route**: `GET /alerts`
- **Purpose**: Public page showing all active alerts
- **Returns**: `view('alerts.index')` with all alerts
- **Logic**: Fetch all active alerts ordered by created_at DESC

**show($id)**
- **Route**: `GET /alerts/{id}`
- **Purpose**: Show single alert details
- **Returns**: `view('alerts.show')` with alert
- **Logic**: Find alert by ID with creator relationship

**adminIndex()**
- **Route**: `GET /admin/alerts`
- **Purpose**: Admin page to manage alerts
- **Returns**: `view('admin.alerts.index')` with paginated alerts
- **Logic**: Paginate all alerts (20 per page) with creator info

**create()**
- **Route**: `GET /admin/alerts/create`
- **Purpose**: Show form to create new alert
- **Returns**: `view('admin.alerts.create')`

**store(Request $request)**
- **Route**: `POST /admin/alerts`
- **Purpose**: Create new alert (manual)
- **Validation**: title, description, severity, location required
- **Logic**: Create alert with source='Manual', trigger AlertCreatedNotification
- **Redirects**: admin.alerts with success message

**edit($id)**
- **Route**: `GET /admin/alerts/{id}/edit`
- **Purpose**: Show form to edit alert
- **Returns**: `view('admin.alerts.edit')` with alert

**update(Request $request, $id)**
- **Route**: `PUT /admin/alerts/{id}`
- **Purpose**: Update existing alert
- **Validation**: Same as store
- **Logic**: Update alert, maintain source
- **Redirects**: admin.alerts with success message

**destroy($id)**
- **Route**: `DELETE /admin/alerts/{id}`
- **Purpose**: Delete alert
- **Logic**: Soft delete or mark as inactive
- **Redirects**: admin.alerts with success message

---

### 3.2 SHELTER CONTROLLER

**Path**: `app/Http/Controllers/ShelterController.php`

**Purpose**: Handle shelter management and display

#### PUBLIC METHODS

**index()**
- **Route**: `GET /shelters`
- **Purpose**: Public page showing all shelters on map
- **Returns**: `view('shelters.index')` with shelters
- **Logic**: Fetch all active shelters with coordinates

**show($id)**
- **Route**: `GET /shelters/{id}`
- **Purpose**: Show single shelter details
- **Returns**: `view('shelters.show')` with shelter
- **Logic**: Find shelter by ID, show capacity, facilities

**adminIndex()**
- **Route**: `GET /admin/shelters`
- **Purpose**: Admin page to manage shelters
- **Returns**: `view('admin.shelters.index')` with paginated shelters
- **Logic**: Paginate all shelters (20 per page)

**create()**
- **Route**: `GET /admin/shelters/create`
- **Purpose**: Show form to create new shelter
- **Returns**: `view('admin.shelters.create')`

**store(Request $request)**
- **Route**: `POST /admin/shelters`
- **Purpose**: Create new shelter
- **Validation**: name, location, capacity, coordinates required
- **Logic**: Create shelter with is_active=true
- **Redirects**: admin.shelters with success message

**edit($id)**
- **Route**: `GET /admin/shelters/{id}/edit`
- **Purpose**: Show form to edit shelter
- **Returns**: `view('admin.shelters.edit')` with shelter

**update(Request $request, $id)**
- **Route**: `PUT /admin/shelters/{id}`
- **Purpose**: Update existing shelter
- **Validation**: Same as store
- **Logic**: Update shelter details
- **Redirects**: admin.shelters with success message

**destroy($id)**
- **Route**: `DELETE /admin/shelters/{id}`
- **Purpose**: Delete shelter
- **Logic**: Check if shelter has assignments, then delete
- **Redirects**: admin.shelters with success message

---

### 3.3 REQUEST CONTROLLER

**Path**: `app/Http/Controllers/RequestController.php`

**Purpose**: Handle emergency help requests

#### PUBLIC METHODS

**index()**
- **Route**: `GET /requests`
- **Purpose**: Public page showing user's requests (if logged in)
- **Returns**: `view('requests.index')` with requests
- **Logic**: Show requests for current user or all if admin

**create()**
- **Route**: `GET /request-help`
- **Purpose**: Show emergency request form (PUBLIC ACCESS)
- **Returns**: `view('requests.create')`
- **Logic**: Anyone can access, even without login

**store(Request $request)**
- **Route**: `POST /request-help`
- **Purpose**: Submit emergency request
- **Validation**: name, phone, location, request_type, urgency required
- **Logic**: 
  1. Create request with status='Pending'
  2. Trigger NewRequestSubmitted event
  3. Send notifications (email, in-app to admin)
  4. Auto-assign if relief worker available
- **Redirects**: requests.show with success message

**show($id)**
- **Route**: `GET /request/{id}`
- **Purpose**: Show request details and tracking
- **Returns**: `view('requests.show')` with request, assignment
- **Logic**: Find request with assignment relationship

**adminIndex()**
- **Route**: `GET /admin/requests`
- **Purpose**: Admin page to manage all requests with MAP
- **Returns**: `view('admin.requests.index')` with requests
- **Logic**: 
  1. Fetch all requests with user, assignment relationships
  2. Pass to map view for marker display
  3. Filter by status, urgency, date range

**showAssign($id)**
- **Route**: `GET /admin/requests/{id}/assign`
- **Purpose**: Show form to assign relief worker and shelter
- **Returns**: `view('admin.requests.assign')` with request, shelters, relief_workers
- **Logic**: Fetch available relief workers and shelters

**assign(Request $request, $id)**
- **Route**: `POST /admin/requests/{id}/assign`
- **Purpose**: Assign relief worker and/or shelter to request
- **Validation**: relief_worker_id or shelter_id required
- **Logic**:
  1. Create assignment
  2. Update request status to 'Assigned'
  3. Trigger notifications (email, in-app)
  4. Send ShelterAssignedNotification if shelter assigned
- **Redirects**: admin.requests with success message

**bulkAssign(Request $request)**
- **Route**: `POST /admin/requests/bulk-assign`
- **Purpose**: Assign multiple requests at once
- **Validation**: request_ids array, relief_worker_id or shelter_id
- **Logic**: Loop through request IDs and assign each
- **Redirects**: admin.requests with success message

**updateStatus(Request $request, $id)**
- **Route**: `PUT /admin/requests/{id}/status`
- **Purpose**: Update request status
- **Validation**: status enum
- **Logic**:
  1. Get old status
  2. Update request status
  3. Trigger RequestStatusUpdated event
  4. Send StatusUpdatedNotification to user
- **Returns**: JSON response for AJAX

**citizenDashboard()**
- **Route**: `GET /citizen/my-requests`
- **Purpose**: Show citizen's own requests
- **Returns**: `view('citizen.dashboard')` with user requests
- **Logic**: Fetch requests for logged-in citizen user

---

### 3.4 AUTH CONTROLLER

**Path**: `app/Http/Controllers/AuthController.php`

**Purpose**: Handle authentication and role-based redirects

#### PUBLIC METHODS

**showLogin()**
- **Route**: `GET /login`
- **Purpose**: Show login form
- **Returns**: `view('auth.login')`

**login(Request $request)**
- **Route**: `POST /login`
- **Purpose**: Authenticate user
- **Validation**: email, password required
- **Logic**:
  1. Attempt authentication
  2. Update last_activity timestamp
  3. Redirect based on role (admin/citizen/relief)
- **Redirects**: Role-based dashboard

**showRegister()**
- **Route**: `GET /register`
- **Purpose**: Show registration form
- **Returns**: `view('auth.register')`

**register(Request $request)**
- **Route**: `POST /register`
- **Purpose**: Register new user
- **Validation**: name, email, password, role
- **Logic**:
  1. Hash password
  2. Create user with role='citizen' (default)
  3. Auto-login after registration
- **Redirects**: citizen.dashboard

**logout()**
- **Route**: `GET /logout`
- **Purpose**: Logout user
- **Logic**: Clear session, logout
- **Redirects**: login page

**adminDashboard()**
- **Route**: `GET /admin/dashboard`
- **Purpose**: Admin dashboard with statistics
- **Returns**: `view('admin.dashboard')` with stats, charts
- **Logic**: Count alerts, requests by status, recent activity

**citizenDashboard()**
- **Route**: `GET /citizen/dashboard`
- **Purpose**: Citizen dashboard with their requests
- **Returns**: `view('citizen.dashboard')` with user requests
- **Logic**: Fetch user's requests and assignments

**reliefDashboard()**
- **Route**: `GET /relief/dashboard`
- **Purpose**: Relief worker dashboard with assignments
- **Returns**: `view('relief.dashboard')` with assignments
- **Logic**: Fetch assigned requests for relief worker

---

### 3.5 ANALYTICS CONTROLLER

**Path**: `app/Http/Controllers/AnalyticsController.php`

**Purpose**: Generate reports and analytics for admin

#### PUBLIC METHODS

**index()**
- **Route**: `GET /admin/analytics`
- **Purpose**: Show analytics dashboard with charts
- **Returns**: `view('admin.analytics-reports')` with data
- **Logic**:
  1. Alerts by severity (Pie chart)
  2. Requests by status (Doughnut chart)
  3. Requests by type (Bar chart)
  4. Requests trend last 7 days (Line chart)
  5. Active relief workers list
  6. Recent requests table

**exportPDF()**
- **Route**: `GET /admin/analytics/export/pdf`
- **Purpose**: Export analytics report as PDF
- **Logic**:
  1. Fetch all data (alerts, requests, shelters)
  2. Use dompdf library
  3. Load view: admin.reports.pdf-report
  4. Generate PDF with landscape orientation
- **Returns**: PDF download

**exportTXT()**
- **Route**: `GET /admin/analytics/export/txt`
- **Purpose**: Export analytics report as plain text
- **Logic**:
  1. Fetch all data
  2. Format as text with sections
  3. Set Content-Type: text/plain
- **Returns**: TXT download

---

### 3.6 NOTIFICATION CONTROLLER

**Path**: `app/Http/Controllers/NotificationController.php`

**Purpose**: Handle all notification operations (4 types)

#### PUBLIC METHODS

**adminInbox()**
- **Route**: `GET /admin/inbox`
- **Purpose**: Show admin notifications inbox
- **Returns**: `view('admin.inbox')` with notifications
- **Logic**: Fetch all notifications for admin users, grouped by type

**citizenInbox()**
- **Route**: `GET /citizen/inbox`
- **Purpose**: Show citizen notifications inbox
- **Returns**: `view('citizen.inbox')` with notifications
- **Logic**: Fetch notifications for logged-in citizen

**markAsRead($id)**
- **Route**: `POST /notifications/{id}/read`
- **Purpose**: Mark single notification as read
- **Logic**: Update is_read=true, is_seen=true
- **Returns**: JSON response

**markAllAdminAsRead()**
- **Route**: `POST /notifications/admin/mark-all-read`
- **Purpose**: Mark all admin notifications as read
- **Logic**: Update all admin user notifications
- **Returns**: JSON response

**markAllCitizenAsRead()**
- **Route**: `POST /notifications/citizen/mark-all-read`
- **Purpose**: Mark all citizen notifications as read
- **Logic**: Update all citizen user notifications
- **Returns**: JSON response

**getUnseenCount()**
- **Route**: `GET /api/notifications/unseen-count`
- **Purpose**: Get count of unseen notifications
- **Logic**: Count where is_seen=false
- **Returns**: JSON with count

**subscribe(Request $request)**
- **Route**: `POST /api/notifications/subscribe`
- **Purpose**: Subscribe to push notifications
- **Validation**: endpoint, keys required
- **Logic**: Create PushSubscription record
- **Returns**: JSON response

**unsubscribe(Request $request)**
- **Route**: `POST /api/notifications/unsubscribe`
- **Purpose**: Unsubscribe from push notifications
- **Logic**: Update is_active=false
- **Returns**: JSON response

**updatePreferences(Request $request)**
- **Route**: `POST /api/notifications/preferences`
- **Purpose**: Update notification preferences
- **Validation**: preferences array
- **Logic**: Update PushSubscription preferences JSON
- **Returns**: JSON response

**getPreferences()**
- **Route**: `GET /api/notifications/preferences`
- **Purpose**: Get user notification preferences
- **Returns**: JSON with preferences

**sendTest()**
- **Route**: `POST /api/notifications/test`
- **Purpose**: Send test push notification
- **Logic**: Send to all active subscriptions
- **Returns**: JSON response

---

## SECTION 4: ROUTES (routes/web.php)

### ROUTE ORGANIZATION

#### PUBLIC ROUTES (No authentication required)
| Method | Route | Controller@Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/` | AlertController@dashboard | Public homepage |
| GET | `/alerts` | AlertController@index | All alerts |
| GET | `/alerts/{id}` | AlertController@show | Alert details |
| GET | `/shelters` | ShelterController@index | All shelters |
| GET | `/shelters/{id}` | ShelterController@show | Shelter details |
| GET | `/request-help` | RequestController@create | **EMERGENCY ACCESS** |
| POST | `/request-help` | RequestController@store | Submit request |
| GET | `/request/{id}` | RequestController@show | Request tracking |
| GET | `/requests` | RequestController@index | User's requests |

#### AUTHENTICATION ROUTES
| Method | Route | Controller@Method |
|--------|-------|-------------------|
| GET | `/login` | AuthController@showLogin |
| POST | `/login` | AuthController@login |
| GET | `/register` | AuthController@showRegister |
| POST | `/register` | AuthController@register |
| GET | `/logout` | AuthController@logout |

#### ROLE-BASED DASHBOARD ROUTES
| Method | Route | Controller@Method |
|--------|-------|-------------------|
| GET | `/admin/dashboard` | AuthController@adminDashboard |
| GET | `/citizen/dashboard` | AuthController@citizenDashboard |
| GET | `/relief/dashboard` | AuthController@reliefDashboard |

#### ADMIN ROUTES (Prefix: /admin, Middleware: nocache)

**Alert Management**
| Method | Route | Controller@Method |
|--------|-------|-------------------|
| GET | `/admin/alerts` | AlertController@adminIndex |
| GET | `/admin/alerts/create` | AlertController@create |
| POST | `/admin/alerts` | AlertController@store |
| GET | `/admin/alerts/{id}/edit` | AlertController@edit |
| PUT | `/admin/alerts/{id}` | AlertController@update |
| DELETE | `/admin/alerts/{id}` | AlertController@destroy |

**Shelter Management**
| Method | Route | Controller@Method |
|--------|-------|-------------------|
| GET | `/admin/shelters` | ShelterController@adminIndex |
| GET | `/admin/shelters/create` | ShelterController@create |
| POST | `/admin/shelters` | ShelterController@store |
| GET | `/admin/shelters/{id}/edit` | ShelterController@edit |
| PUT | `/admin/shelters/{id}` | ShelterController@update |
| DELETE | `/admin/shelters/{id}` | ShelterController@destroy |

**Request Management**
| Method | Route | Controller@Method |
|--------|-------|-------------------|
| GET | `/admin/requests` | RequestController@adminIndex (WITH MAP) |
| GET | `/admin/requests/{id}/assign` | RequestController@showAssign |
| POST | `/admin/requests/{id}/assign` | RequestController@assign |
| POST | `/admin/requests/bulk-assign` | RequestController@bulkAssign |
| PUT | `/admin/requests/{id}/status` | RequestController@updateStatus |

**Analytics**
| Method | Route | Controller@Method |
|--------|-------|-------------------|
| GET | `/admin/analytics` | AnalyticsController@index |
| GET | `/admin/analytics/export/pdf` | AnalyticsController@exportPDF |
| GET | `/admin/analytics/export/txt` | AnalyticsController@exportTXT |

**Notifications**
| Method | Route | View/Controller |
|--------|-------|-----------------|
| GET | `/admin/notifications` | view('admin.notifications') |
| GET | `/admin/inbox` | NotificationController@adminInbox |

#### CITIZEN ROUTES (Prefix: /citizen, Middleware: nocache)
| Method | Route | Controller@Method |
|--------|-------|-------------------|
| GET | `/citizen/my-requests` | RequestController@citizenDashboard |
| GET | `/citizen/inbox` | NotificationController@citizenInbox |

#### NOTIFICATION API ROUTES
| Method | Route | Controller@Method |
|--------|-------|-------------------|
| POST | `/api/notifications/subscribe` | NotificationController@subscribe |
| POST | `/api/notifications/unsubscribe` | NotificationController@unsubscribe |
| POST | `/api/notifications/preferences` | NotificationController@updatePreferences |
| GET | `/api/notifications/preferences` | NotificationController@getPreferences |
| POST | `/api/notifications/test` | NotificationController@sendTest |
| POST | `/api/notifications/{id}/read` | NotificationController@markAsRead |
| POST | `/api/notifications/admin/mark-all-read` | NotificationController@markAllAdminAsRead |
| POST | `/api/notifications/citizen/mark-all-read` | NotificationController@markAllCitizenAsRead |
| GET | `/api/notifications/unseen-count` | NotificationController@getUnseenCount |

#### OTHER API ROUTES
| Method | Route | Returns |
|--------|-------|---------|
| GET | `/api/dashboard-stats` | JSON with statistics |
| POST | `/api/push-subscription` | Save push subscription |
| DELETE | `/api/push-subscription` | Remove push subscription |

---

## SECTION 5: MIDDLEWARE

### 5.1 NO CACHE MIDDLEWARE

**Path**: `app/Http/Middleware/NoCacheMiddleware.php`

**Purpose**: Prevent browser caching of dynamic pages

**Registered As**: `'nocache'` alias in `bootstrap/app.php`

#### LOGIC
```php
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', '0');
    
    return $response;
}
```

#### APPLIED TO ROUTES
- Dashboard (`/`) - Prevent stale alert display
- Alerts page (`/alerts`) - Always show fresh alerts
- Admin routes (`/admin/*`) - Fresh request counts, notifications
- Citizen routes (`/citizen/*`) - Fresh request status
- Notification routes - Fresh notification counts

#### WHY NEEDED
- Users reported stale data without Ctrl+F5
- Browser cached old alert counts
- Notification badges showed wrong numbers
- Request status didn't update in real-time

---

### 5.2 TRACK ADMIN ACTIVITY MIDDLEWARE

**Path**: `app/Http/Middleware/TrackAdminActivity.php`

**Purpose**: Track admin user activity for "Active Now" indicator

#### LOGIC
```php
public function handle($request, Closure $next)
{
    if (Auth::check() && Auth::user()->role === 'admin') {
        Auth::user()->update(['last_activity' => now()]);
    }
    
    return $next($request);
}
```

**Applied To**: Admin routes

**Purpose**:
- Show "Active Now" badge on admin list
- Track admin login activity
- Monitor admin dashboard usage

---

### 5.3 BUILT-IN LARAVEL MIDDLEWARE

| Middleware | Purpose |
|------------|---------|
| `auth` | Require authentication |
| `guest` | Only allow guests (not logged in) |
| `verified` | Require email verification |
| `throttle` | Rate limiting (prevent spam) |
| `web` | Session, CSRF protection, cookies |
| `api` | Stateless API requests |

---

## SECTION 6: HOW TO CREATE NEW FEATURE (STEP-BY-STEP)

### EXAMPLE: Creating a "DONATIONS" feature from scratch

#### STEP 1: CREATE MIGRATION
```bash
php artisan make:migration create_donations_table
```

File: `database/migrations/2025_XX_XX_XXXXXX_create_donations_table.php`

```php
public function up()
{
    Schema::create('donations', function (Blueprint $table) {
        $table->id();
        $table->string('donor_name');
        $table->string('phone', 20);
        $table->enum('donation_type', ['Money', 'Food', 'Clothes', 'Medicine']);
        $table->decimal('amount', 10, 2)->nullable();
        $table->text('description')->nullable();
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
        $table->timestamps();
    });
}
```

Run: `php artisan migrate`

---

#### STEP 2: CREATE MODEL
```bash
php artisan make:model Donation
```

File: `app/Models/Donation.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'donor_name', 'phone', 'donation_type', 'amount', 'description', 'user_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

#### STEP 3: CREATE CONTROLLER
```bash
php artisan make:controller DonationController
```

File: `app/Http/Controllers/DonationController.php`

```php
namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Donation::with('user')->latest()->get();
        return view('donations.index', compact('donations'));
    }

    public function create()
    {
        return view('donations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'donor_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'donation_type' => 'required|in:Money,Food,Clothes,Medicine',
            'amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();

        Donation::create($validated);

        return redirect()->route('donations.index')
            ->with('success', 'Thank you for your donation!');
    }

    public function show($id)
    {
        $donation = Donation::findOrFail($id);
        return view('donations.show', compact('donation'));
    }

    public function adminIndex()
    {
        $donations = Donation::with('user')->paginate(20);
        return view('admin.donations.index', compact('donations'));
    }
}
```

---

#### STEP 4: DEFINE ROUTES

Edit: `routes/web.php`

```php
// Public donation routes
Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
Route::get('/donate', [DonationController::class, 'create'])->name('donations.create');
Route::post('/donate', [DonationController::class, 'store'])->name('donations.store');
Route::get('/donations/{id}', [DonationController::class, 'show'])->name('donations.show');

// Admin donation routes
Route::prefix('admin')->middleware('nocache')->group(function () {
    Route::get('/donations', [DonationController::class, 'adminIndex'])->name('admin.donations');
});
```

---

#### STEP 5: CREATE VIEWS

Create folder: `resources/views/donations/`

- File 1: `resources/views/donations/index.blade.php` (List all donations)
- File 2: `resources/views/donations/create.blade.php` (Donation form)
- File 3: `resources/views/donations/show.blade.php` (Single donation details)
- File 4: `resources/views/admin/donations/index.blade.php` (Admin management)

---

#### STEP 6: UPDATE NAVIGATION

Edit: `resources/views/admin/partials/header.blade.php`

Add link:
```php
<a href="{{ route('admin.donations') }}" class="{{ request()->routeIs('admin.donations*') ? 'active' : '' }}">
    💰 Manage Donations
</a>
```

---

#### STEP 7: TEST

1. Visit `/donate`
2. Fill form and submit
3. Check `/donations` to see list
4. Login as admin
5. Visit `/admin/donations`
6. Verify data is displayed correctly

**COMPLETE! Feature is now fully integrated.**

---

## END OF PART 1

**Next**: PART-2-FEATURES-AND-IMPLEMENTATION.md will cover:
- AI Weather Integration (OpenWeatherMap)
- PWA Implementation (Service Workers, Offline Mode)
- Notification System (4 types)
- Map Integration (Leaflet.js)
- PDF Generation (dompdf)
- Email Integration (Mailtrap.io)
- Role-Based Access Control
- All View Files Documentation
