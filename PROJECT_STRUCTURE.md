# Project Structure Diagram

## Application Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    HTTP REQUEST                             │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                    ROUTER (web.php)                         │
│  Dispatches request to appropriate controller               │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              FORM REQUEST VALIDATION                        │
│  Validates & authorize - returns errors if invalid          │
│  - StoreDepartmentRequest                                   │
│  - StoreEmployeeRequest                                     │
│  - StoreLeaveRequest                                        │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   CONTROLLER                                │
│  HTTP Layer - Routes request to service method              │
│  - DashboardController                                      │
│  - DepartmentController                                     │
│  - EmployeeController                                       │
│  - LeaveRequestController                                   │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   SERVICE LAYER                             │
│  Business Logic - Core application logic                    │
│  - DashboardService                                         │
│  - DepartmentService                                        │
│  - EmployeeService                                          │
│  - LeaveService                                             │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   MODELS                                    │
│  Database Layer - ORM & query building                      │
│  - User                                                     │
│  - Department                                               │
│  - Employee                                                 │
│  - LeaveRequest                                             │
│  - AttendanceRecord                                         │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   DATABASE                                  │
│  Persistent data storage                                    │
└─────────────────────────────────────────────────────────────┘

                    RESPONSE FLOW
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   CONTROLLER (return)                       │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   VIEW RENDERING                            │
│  - layouts/app.blade.php (Master)                          │
│  - components/*.blade.php (Reusable)                       │
│  - employees/*.blade.php (Resource-specific)               │
│  - departments/*.blade.php (Resource-specific)             │
│  - leaves/*.blade.php (Resource-specific)                  │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   HTTP RESPONSE                             │
└─────────────────────────────────────────────────────────────┘
```

---

## Directory Structure Tree

```
hrmsai/
│
├── 📁 app/
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/
│   │   │   ├── DashboardController.php       ⭐ Dashboard display
│   │   │   ├── DepartmentController.php      ⭐ Department CRUD
│   │   │   ├── EmployeeController.php        ⭐ Employee CRUD
│   │   │   ├── LeaveRequestController.php    ⭐ Leave management
│   │   │   └── Controller.php
│   │   │
│   │   ├── 📁 Requests/
│   │   │   ├── StoreDepartmentRequest.php    ✅ Department validation
│   │   │   ├── StoreEmployeeRequest.php      ✅ Employee validation
│   │   │   └── StoreLeaveRequest.php         ✅ Leave validation
│   │   │
│   │   ├── 📁 Middleware/
│   │   └── Kernel.php
│   │
│   ├── 📁 Services/
│   │   ├── DashboardService.php              🔧 Dashboard logic
│   │   ├── DepartmentService.php             🔧 Department logic
│   │   ├── EmployeeService.php               🔧 Employee logic
│   │   └── LeaveService.php                  🔧 Leave logic
│   │
│   ├── 📁 Models/
│   │   ├── User.php
│   │   ├── Department.php
│   │   ├── Employee.php
│   │   ├── LeaveRequest.php
│   │   └── AttendanceRecord.php
│   │
│   ├── 📁 Providers/
│   └── Console/
│
├── 📁 resources/
│   ├── 📁 views/
│   │   └── 📁 hrms/
│   │       ├── 📁 layouts/
│   │       │   └── app.blade.php             🎨 Master layout
│   │       │
│   │       ├── 📁 components/
│   │       │   ├── navbar.blade.php          🔧 Navigation
│   │       │   ├── footer.blade.php          🔧 Footer
│   │       │   ├── alert.blade.php           🔧 Alert messages
│   │       │   ├── stats-cards.blade.php     🔧 Statistics
│   │       │   ├── department-chart.blade.php 🔧 Chart
│   │       │   ├── quick-add-department.blade.php 🔧 Form
│   │       │   ├── recent-employees.blade.php 🔧 List
│   │       │   └── latest-leaves.blade.php   🔧 List
│   │       │
│   │       ├── dashboard.blade.php           📋 Dashboard page
│   │       │
│   │       ├── 📁 employees/
│   │       │   ├── index.blade.php           📑 List employees
│   │       │   ├── create.blade.php          📝 Create form
│   │       │   ├── edit.blade.php            ✏️  Edit form
│   │       │   └── show.blade.php            👁️  View details
│   │       │
│   │       ├── 📁 departments/
│   │       │   ├── index.blade.php           📑 List departments
│   │       │   ├── create.blade.php          📝 Create form
│   │       │   ├── edit.blade.php            ✏️  Edit form
│   │       │   └── show.blade.php            👁️  View details
│   │       │
│   │       └── 📁 leaves/
│   │           ├── index.blade.php           📑 All requests
│   │           ├── create.blade.php          📝 Create form
│   │           ├── pending.blade.php         ⏳ Pending requests
│   │           └── show.blade.php            👁️  View details
│   │
│   ├── 📁 css/
│   │   └── app.css
│   │
│   └── 📁 js/
│       ├── app.js
│       └── bootstrap.js
│
├── 📁 routes/
│   ├── web.php                               🛣️  Web routes
│   ├── api.php
│   └── auth.php
│
├── 📁 database/
│   ├── 📁 migrations/
│   ├── 📁 seeders/
│   └── 📁 factories/
│
├── 📁 config/
├── 📁 bootstrap/
├── 📁 storage/
├── 📁 tests/
├── 📁 vendor/
│
├── .env
├── artisan
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
├── phpunit.xml
│
├── 📄 ARCHITECTURE.md          📖 Detailed architecture guide
├── 📄 MIGRATION_GUIDE.md       📖 How to use new structure
└── 📄 README.md                📖 Project setup guide
```

---

## Class Relationships Diagram

```
┌──────────────────────────────────────────────────────────────┐
│                      CONTROLLERS                             │
├──────────────────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ DashboardController                                    │  │
│ │ • index() → Dashboard                                 │  │
│ │ Dependencies: DashboardService                        │  │
│ └─────────────────────────────────────────────────────────┘  │
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ DepartmentController                                  │  │
│ │ • index() → List departments                         │  │
│ │ • create() → Create form                             │  │
│ │ • store() → Save department                          │  │
│ │ • show() → Show details                              │  │
│ │ • edit() → Edit form                                 │  │
│ │ • update() → Update department                       │  │
│ │ • destroy() → Delete department                      │  │
│ │ Dependencies: DepartmentService                      │  │
│ └─────────────────────────────────────────────────────────┘  │
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ EmployeeController                                    │  │
│ │ • index() → List employees                           │  │
│ │ • create() → Create form                             │  │
│ │ • store() → Save employee                            │  │
│ │ • show() → Show details                              │  │
│ │ • edit() → Edit form                                 │  │
│ │ • update() → Update employee                         │  │
│ │ • destroy() → Delete employee                        │  │
│ │ • search() → Search employees                        │  │
│ │ Dependencies: EmployeeService, DepartmentService    │  │
│ └─────────────────────────────────────────────────────────┘  │
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ LeaveRequestController                                │  │
│ │ • index() → List leave requests                      │  │
│ │ • pending() → Show pending requests                  │  │
│ │ • create() → Create form                             │  │
│ │ • store() → Submit request                           │  │
│ │ • approve() → Approve leave                          │  │
│ │ • reject() → Reject leave                            │  │
│ │ Dependencies: LeaveService, EmployeeService         │  │
│ └─────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────┐
│                      SERVICES (Business Logic)               │
├──────────────────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ DashboardService                                      │  │
│ │ • getDashboardStats()                                │  │
│ │ • getDepartmentBreakdown()                           │  │
│ │ • getRecentEmployees()                               │  │
│ │ • getLatestLeaveRequests()                           │  │
│ └─────────────────────────────────────────────────────────┘  │
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ DepartmentService                                    │  │
│ │ • createDepartment()                                 │  │
│ │ • updateDepartment()                                 │  │
│ │ • deleteDepartment()                                 │  │
│ │ • getAllDepartments()                                │  │
│ │ • getDepartmentById()                                │  │
│ │ • getDepartmentWithEmployees()                       │  │
│ └─────────────────────────────────────────────────────────┘  │
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ EmployeeService                                      │  │
│ │ • createEmployee()                                   │  │
│ │ • updateEmployee()                                   │  │
│ │ • deleteEmployee()                                   │  │
│ │ • getAllEmployees()                                  │  │
│ │ • getEmployeeById()                                  │  │
│ │ • getEmployeesByDepartment()                        │  │
│ │ • searchEmployees()                                  │  │
│ └─────────────────────────────────────────────────────────┘  │
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ LeaveService                                          │  │
│ │ • createLeaveRequest()                               │  │
│ │ • updateLeaveRequest()                               │  │
│ │ • deleteLeaveRequest()                               │  │
│ │ • getLeaveRequestsForEmployee()                     │  │
│ │ • getPendingLeaveRequests()                         │  │
│ │ • getAllLeaveRequests()                             │  │
│ │ • approveLeaveRequest()                             │  │
│ │ • rejectLeaveRequest()                              │  │
│ └─────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────┐
│                    VALIDATION (Form Requests)                │
├──────────────────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ StoreDepartmentRequest                               │  │
│ │ • rules() → Department validation rules             │  │
│ │ • messages() → Custom error messages                │  │
│ └─────────────────────────────────────────────────────────┘  │
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ StoreEmployeeRequest                                 │  │
│ │ • rules() → Employee validation rules               │  │
│ │ • messages() → Custom error messages                │  │
│ └─────────────────────────────────────────────────────────┘  │
│ ┌─────────────────────────────────────────────────────────┐  │
│ │ StoreLeaveRequest                                    │  │
│ │ • rules() → Leave validation rules                  │  │
│ │ • messages() → Custom error messages                │  │
│ └─────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────┐
│                       MODELS (Database)                      │
├──────────────────────────────────────────────────────────────┤
│ • Department → departments table                             │
│ • Employee → employees table                                 │
│ • LeaveRequest → leave_requests table                        │
│ • AttendanceRecord → attendance_records table               │
│ • User → users table                                         │
└──────────────────────────────────────────────────────────────┘
```

---

## Data Flow Example: Creating an Employee

```
User navigates to /employees/create
                    ↓
DepartmentController@create()
  └─ Fetches departments from DepartmentService
      └─ return view('hrms.employees.create')
                    ↓
              Display Form
                    ↓
       User fills and submits form
                    ↓
         POST /employees
                    ↓
EmployeeController@store(StoreEmployeeRequest $request)
  ├─ Laravel auto-validates using StoreEmployeeRequest
  │   └─ Check email unique, department exists, etc.
  ├─ If validation fails → back() with errors
  └─ If validation passes → validated() data sent to:
      ├─ EmployeeService@createEmployee($data)
      │   └─ Employee::create($data)
      │       └─ INSERT into database
      └─ Redirect to employees.index with success message
                    ↓
             Display success
```

---

## View Component Hierarchy

```
app.blade.php (Master Layout)
├── navbar.blade.php
├── @yield('content')
│   ├── dashboard.blade.php
│   │   ├── stats-cards.blade.php
│   │   ├── department-chart.blade.php
│   │   ├── quick-add-department.blade.php
│   │   ├── recent-employees.blade.php
│   │   └── latest-leaves.blade.php
│   │
│   └── [Resource Views]
│       ├── employees/index.blade.php
│       ├── employees/create.blade.php
│       ├── employees/edit.blade.php
│       ├── employees/show.blade.php
│       ├── departments/index.blade.php
│       ├── departments/create.blade.php
│       ├── departments/edit.blade.php
│       ├── departments/show.blade.php
│       ├── leaves/index.blade.php
│       ├── leaves/create.blade.php
│       ├── leaves/pending.blade.php
│       └── leaves/show.blade.php
│
└── footer.blade.php
```

---

## Routing Structure

```
/                           GET  DashboardController@index
                                → displays dashboard

/departments
├── /                       GET  DepartmentController@index
├── /create                 GET  DepartmentController@create
├── /                       POST DepartmentController@store
├── /{id}                   GET  DepartmentController@show
├── /{id}/edit              GET  DepartmentController@edit
├── /{id}                   PATCH DepartmentController@update
└── /{id}                   DELETE DepartmentController@destroy

/employees
├── /                       GET  EmployeeController@index
├── /create                 GET  EmployeeController@create
├── /                       POST EmployeeController@store
├── /{id}                   GET  EmployeeController@show
├── /{id}/edit              GET  EmployeeController@edit
├── /{id}                   PATCH EmployeeController@update
├── /{id}                   DELETE EmployeeController@destroy
└── /search                 GET  EmployeeController@search

/leaves
├── /                       GET  LeaveRequestController@index
├── /pending                GET  LeaveRequestController@pending
├── /create                 GET  LeaveRequestController@create
├── /                       POST LeaveRequestController@store
├── /{id}                   GET  LeaveRequestController@show
├── /{id}/approve           PATCH LeaveRequestController@approve
└── /{id}/reject            PATCH LeaveRequestController@reject
```

---

## Legend

| Symbol | Meaning |
|--------|---------|
| 📁 | Directory/Folder |
| 📄 | File |
| ⭐ | Controller |
| ✅ | Form Request/Validation |
| 🔧 | Service |
| 🎨 | View Component |
| 📋 | Page View |
| 📑 | List View |
| 📝 | Create Form |
| ✏️ | Edit Form |
| 👁️ | Detail View |
| 🛣️ | Routes |
| 📖 | Documentation |
| ⏳ | Status-specific View |
| 🔄 | Relationship |
| ↓ | Data Flow |

---

This visual guide helps understand how components interact and where to find specific functionality.
