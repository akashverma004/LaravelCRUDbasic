# PeopleFlow HRMS - Architecture Documentation

## Project Structure Overview

This document describes the restructured, scalable, and enterprise-ready architecture of the PeopleFlow HRMS application.

---

## 📁 Directory Structure

```
hrmsai/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php      # Dashboard logic
│   │   │   ├── DepartmentController.php     # Department CRUD
│   │   │   ├── EmployeeController.php       # Employee CRUD
│   │   │   ├── LeaveRequestController.php   # Leave management
│   │   │   └── Controller.php
│   │   ├── Requests/
│   │   │   ├── StoreDepartmentRequest.php   # Department validation
│   │   │   ├── StoreEmployeeRequest.php     # Employee validation
│   │   │   └── StoreLeaveRequest.php        # Leave validation
│   │   ├── Kernel.php
│   │   └── Middleware/
│   ├── Services/
│   │   ├── DashboardService.php             # Dashboard business logic
│   │   ├── DepartmentService.php            # Department business logic
│   │   ├── EmployeeService.php              # Employee business logic
│   │   └── LeaveService.php                 # Leave business logic
│   ├── Models/
│   │   ├── User.php
│   │   ├── Department.php
│   │   ├── Employee.php
│   │   ├── LeaveRequest.php
│   │   └── AttendanceRecord.php
│   └── Providers/
├── resources/
│   ├── views/
│   │   └── hrms/
│   │       ├── layouts/
│   │       │   └── app.blade.php            # Main layout template
│   │       ├── components/
│   │       │   ├── navbar.blade.php
│   │       │   ├── footer.blade.php
│   │       │   ├── alert.blade.php
│   │       │   ├── stats-cards.blade.php
│   │       │   ├── department-chart.blade.php
│   │       │   ├── quick-add-department.blade.php
│   │       │   ├── recent-employees.blade.php
│   │       │   └── latest-leaves.blade.php
│   │       ├── dashboard.blade.php
│   │       ├── employees/
│   │       │   ├── index.blade.php
│   │       │   ├── create.blade.php
│   │       │   ├── edit.blade.php
│   │       │   └── show.blade.php
│   │       ├── departments/
│   │       │   ├── index.blade.php
│   │       │   ├── create.blade.php
│   │       │   ├── edit.blade.php
│   │       │   └── show.blade.php
│   │       └── leaves/
│   │           ├── index.blade.php
│   │           ├── create.blade.php
│   │           ├── pending.blade.php
│   │           └── show.blade.php
│   └── css/, js/
├── routes/
│   └── web.php                              # Structured routes
└── database/
    ├── migrations/
    └── seeders/
```

---

## 🎯 Architecture Patterns

### 1. **Services Layer** (`app/Services/`)
Handles all business logic, separating concerns from controllers.

**Benefits:**
- Reusable logic across multiple controllers
- Easy unit testing
- Single Responsibility Principle
- Centralized business rules

**Example:**
```php
// DepartmentService.php
class DepartmentService
{
    public function createDepartment(array $data): Department
    public function getAllDepartments(): Collection
    public function getDepartmentWithEmployees(int $id)
}
```

### 2. **Forms Request Validation** (`app/Http/Requests/`)
Dedicated validation classes instead of inline validation in controllers.

**Benefits:**
- Reusable validation logic
- Custom error messages
- Authorization checks (authorize() method)
- Cleaner controllers

**Example:**
```php
// StoreEmployeeRequest.php
class StoreEmployeeRequest extends FormRequest
{
    public function rules(): array
    public function messages(): array
}
```

### 3. **Specialized Controllers** (`app/Http/Controllers/`)
Each controller focuses on one resource - follows REST principles.

**Structure:**
- `DashboardController` - Dashboard display
- `DepartmentController` - Department CRUD
- `EmployeeController` - Employee CRUD
- `LeaveRequestController` - Leave management

**Benefits:**
- Single responsibility per controller
- RESTful routing
- Easy to test
- Clear separation of concerns

### 4. **Component-Based Views** (`resources/views/hrms/`)
Views are modularized into reusable components.

**Structure:**
- **layouts/** - Master templates
- **components/** - Reusable view components
- **[resource]/** - Resource-specific views (employees/, departments/, leaves/)

**Benefits:**
- DRY principle
- Reusable UI components
- Easy maintenance
- Better organization

---

## 🔄 Request Flow

```
Request
  ↓
Router (web.php)
  ↓
Controller
  ↓
Form Request (Validation)
  ↓
Service (Business Logic)
  ↓
Model (Database)
  ↓
Response/View
```

---

## 📋 Routing Structure

Modern RESTful routing with resource grouping:

```php
// Departments
Route::prefix('departments')->group(function () {
    Route::get('/', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('/', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/{id}', [DepartmentController::class, 'show'])->name('departments.show');
    Route::get('/{id}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::patch('/{id}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
});

// Similar for Employees and Leave Requests
```

---

## 🔌 Dependency Injection

Controllers use constructor injection for services:

```php
class EmployeeController extends Controller
{
    public function __construct(
        private EmployeeService $employeeService,
        private DepartmentService $departmentService
    ) {}
}
```

---

## 📝 View Organization

### Master Layout (`layouts/app.blade.php`)
Contains HTML structure, navigation, footer, and alert messages.

### Reusable Components (`components/`)
- `navbar.blade.php` - Navigation bar
- `footer.blade.php` - Footer
- `alert.blade.php` - Alert messages
- `stats-cards.blade.php` - Dashboard statistics
- `department-chart.blade.php` - Chart visualization

### Resource Views
Each resource has dedicated CRUD views:
- `index.blade.php` - List all records
- `create.blade.php` - Form to create new record
- `edit.blade.php` - Form to update record
- `show.blade.php` - Display single record details

---

## 🛡️ Data Validation

All input is validated using Form Requests:

```php
class StoreEmployeeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:employees,email'],
            'joined_on' => ['required', 'date', 'before_or_equal:today'],
            // ...
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already in use.',
            'joined_on.before_or_equal' => 'Join date cannot be in the future.',
        ];
    }
}
```

---

## 🚀 Scalability Features

### 1. **Service Layer**
- Business logic is separated from HTTP concerns
- Easy to add new features without modifying existing code
- Can be extended with caching, logging, events

### 2. **Repository Pattern Ready**
- Services can be enhanced with repositories for advanced queries
- Easy to switch database implementations

### 3. **Event Driven**
- Services can emit events (employee created, leave approved)
- Listeners can handle notifications, logging, etc.

### 4. **Middleware Support**
- Can add authentication, authorization middleware
- Throttling, CORS, etc.

### 5. **API Ready**
- Same services can power API endpoints
- Routes can be duplicated for `/api` prefix
- Controllers can return JSON or views

---

## 📊 Database Models

### Employee Relationships
```php
Employee::with('department')           // Has relationship
         ->with('leaveRequests')       // Has many relationship
         ->with('attendanceRecords');  // Has many relationship
```

### Department Relationships
```php
Department::withCount('employees')  // Count employees
          ->with('employees');      // Get all employees
```

---

## 🔍 Usage Examples

### Creating an Employee

**Before (Monolithic):**
```php
public function storeEmployee(Request $request)
{
    $data = $request->validate([...]);
    Employee::create($data);
    return back()->with('status', 'Employee added successfully.');
}
```

**After (Structured):**
```php
// Controller
public function store(StoreEmployeeRequest $request): RedirectResponse
{
    $this->employeeService->createEmployee($request->validated());
    return redirect()->route('employees.index')->with('status', 'Success');
}

// Service handles logic
public function createEmployee(array $data): Employee
{
    return Employee::create($data);
}

// Request handles validation
public function rules(): array
{
    return ['email' => ['required', 'email', 'unique:employees,email']];
}
```

---

## 🎨 Component Usage

### Dashboard Component Example
```blade
@include('hrms.components.stats-cards', [
    'stats' => [
        ['label' => 'Total Employees', 'value' => $employeeCount],
        ['label' => 'Departments', 'value' => $departmentCount],
    ]
])
```

### Reusable Form Component
All forms follow consistent styling and error handling pattern.

---

## 📦 Dependencies

The application uses:
- **Laravel 10** - Web framework
- **Tailwind CSS** - Styling
- **Blade** - Templating
- **Alpine.js** - DOM manipulation (via Vite)
- **Chart.js** - Data visualization

---

## 🔐 Future Enhancements

1. **Authentication & Authorization**
   - Add gates and policies
   - Role-based access control (Manager, HR, Employee roles)

2. **Logging & Monitoring**
   - Log all CRUD operations
   - Track who modified what and when

3. **Events & Notifications**
   - Emit events for important actions
   - Send email notifications for leave approvals

4. **API Endpoints**
   - Create `/api` routes using the same services
   - Support for mobile apps or third-party integrations

5. **Advanced Features**
   - Attendance tracking with biometric integration
   - Payroll calculation system
   - Performance review system
   - Asset management

6. **Testing**
   - Unit tests for services
   - Feature tests for controllers
   - Form request tests

---

## ✅ Advantages of This Structure

| Aspect | Benefit |
|--------|---------|
| **Maintainability** | Clear separation of concerns |
| **Scalability** | Easy to add new features |
| **Testability** | Services can be mocked and tested |
| **Reusability** | Services used by multiple controllers |
| **Code Quality** | Single responsibility principle |
| **Team Collaboration** | Clear structure for developers |
| **Performance** | Optimization at service layer |
| **Security** | Validation in dedicated Form Requests |

---

## 📞 Quick Reference

### Add a New Feature

1. Create a Form Request for validation
2. Create Service methods for business logic
3. Create Controller with injected Service
4. Add routes to `web.php`
5. Create views for UI

### Modify Validation

Edit the corresponding `app/Http/Requests/*.php` file.

### Change Business Logic

Edit the corresponding `app/Services/*.php` file.

### Update UI

Edit the corresponding `resources/views/hrms/**/*.blade.php` file.

---

## 🎓 Learning Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Design Patterns**: Repository, Service, Factory patterns
- **SOLID Principles**: Well-organized code structure
- **REST API Standards**: Routing and naming conventions

---

**Last Updated**: March 2026
**Version**: 2.0 - Restructured & Scalable
