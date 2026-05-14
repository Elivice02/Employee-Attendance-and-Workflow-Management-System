# Implementation Guide & Code Examples

## Quick Reference

### Include Topbar in Your Layout
```blade
<!-- In your layout file (e.g., layouts/employee.blade.php) -->
<!DOCTYPE html>
<html>
<head>
    <!-- ... -->
</head>
<body>
    <div class="flex min-h-screen">
        
        <!-- Sidebar -->
        <aside>
            <!-- Your navigation -->
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">
            
            <!-- Include topbar component -->
            @include('components.topbar')
            
            <!-- Your page content -->
            <div class="p-6">
                @yield('content')
            </div>
            
        </main>
    </div>
</body>
</html>
```

---

## Accessing User Data in Views

### Get Current User Information
```blade
<!-- User name -->
{{ auth()->user()->name }}

<!-- User email -->
{{ auth()->user()->email }}

<!-- User role -->
{{ auth()->user()->role }}

<!-- User profile picture path -->
{{ auth()->user()->profile_picture }}

<!-- Check if user is logged in -->
@if(auth()->check())
    <p>Welcome, {{ auth()->user()->name }}</p>
@endif

<!-- Check user role -->
@if(auth()->user()->role === 'employee')
    <!-- Show employee-only content -->
@endif
```

---

## Logout Implementation

### Using Named Routes (Recommended)
```blade
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-danger">
        Logout
    </button>
</form>
```

### Direct URL (Alternative)
```blade
<form method="POST" action="/logout">
    @csrf
    <button type="submit">Logout</button>
</form>
```

### JavaScript Logout
```javascript
// Create and submit logout form
function logout() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('logout') }}';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = '_token';
    input.value = csrfToken;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}
```

---

## Profile Picture Display

### Upload Profile Picture (in My Profile)
```blade
<form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="mb-4">
        <label>Profile Picture</label>
        <input type="file" name="profile_picture" accept="image/*">
    </div>
    
    <button type="submit">Update</button>
</form>
```

### Display Profile Picture in Topbar
```blade
@if(auth()->user()->profile_picture)
    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" 
         alt="{{ auth()->user()->name }}" 
         class="w-8 h-8 rounded-full object-cover">
@else
    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 
                flex items-center justify-center text-white text-sm font-bold">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    </div>
@endif
```

---

## Creating Custom Page Titles

### Set Title in Your View
```blade
@extends('layouts.employee')

@section('title', 'My Dashboard')

@section('content')
    <!-- Your content -->
@endsection
```

### Access in Layout
```blade
<!-- The topbar will automatically display this -->
<h2>@yield('title', 'Dashboard')</h2>
```

---

## Conditional User Menu Items

### Show Items Based on Role
```blade
<!-- Only for employees -->
@if(auth()->user()->role === 'employee')
    <a href="{{ route('employee.profile') }}">My Profile</a>
@endif

<!-- Only for HR -->
@if(auth()->user()->role === 'hr')
    <a href="{{ route('hr.employees.index') }}">View Employees</a>
@endif

<!-- Only for supervisors -->
@if(auth()->user()->role === 'supervisor')
    <a href="#">Team Reports</a>
@endif

<!-- For all authenticated users -->
@auth
    <a href="{{ route('logout') }}">Logout</a>
@endauth
```

---

## Controller Implementation

### In Your Controller - Get User Data
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        return view('dashboard', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'profile_picture' => $user->profile_picture,
        ]);
    }
}
```

### Logout Controller (Already Implemented)
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
```

---

## Routes Setup

### Logout Route (Already Configured)
```php
// routes/web.php
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
```

### Protected Routes Example
```php
// Employee routes with role middleware
Route::middleware(['auth', 'role:employee'])->prefix('employee')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [EmployeeDashboardController::class, 'profile'])->name('profile');
});

// HR routes
Route::middleware(['auth', 'role:hr'])->prefix('hr')->group(function () {
    Route::get('/dashboard', [HRController::class, 'dashboard'])->name('dashboard');
});
```

---

## Styling Customization

### Change Topbar Colors
```blade
<!-- Modify in components/topbar.blade.php -->

<!-- Change header background -->
<header class="bg-white shadow-sm border-b border-gray-200">
    <!-- Change to: bg-blue-600 (or any color) -->
</header>

<!-- Change button hover state -->
<button class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-100">
    <!-- Change hover:bg-gray-100 to your preference -->
</button>
```

### Change Sidebar Colors
```blade
<!-- Employee Sidebar -->
<aside class="w-64 bg-white shadow-md">
    <!-- Change bg-white to bg-blue-900 for dark theme -->
</aside>

<!-- HR Sidebar -->
<aside class="w-64 bg-white shadow-lg">
    <!-- Change background and text colors -->
</aside>
```

---

## Common Issues & Solutions

### Issue: Logout button not working
```
Solution: Make sure the form has @csrf token
<form method="POST" action="{{ route('logout') }}">
    @csrf  <!-- This is required -->
    <button type="submit">Logout</button>
</form>
```

### Issue: Profile picture not showing
```
Solution: Check if file exists in storage and is properly configured
1. Check storage symlink: php artisan storage:link
2. Verify profile picture path is correct
3. Check file permissions on storage folder
```

### Issue: Dropdown not appearing on mobile
```
Solution: The current implementation uses hover. For mobile, add touch support:
@click="isOpen = !isOpen" (with Alpine.js or similar)
```

### Issue: Session not clearing after logout
```
Solution: Make sure logout controller invalidates session:
$request->session()->invalidate();
$request->session()->regenerateToken();
```

---

## Database Columns Used

### User Model Fields (app/Models/User.php)
```php
protected $fillable = [
    'created_by',
    'name',              // Used in topbar
    'email',             // Used in topbar
    'password',
    'password_changed_at',
    'gender',
    'date_of_birth',
    'phone',             // Optional profile info
    'profile_picture',   // Used in topbar avatar
    'role',              // Used in topbar role badge
    'department_id',
    'supervisor_id',
    'must_change_password'
];
```

---

## Environment Variables

### Configuration in .env (if needed)
```env
# Session configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120

# File storage
FILESYSTEM_DISK=public

# App URL (for asset storage)
APP_URL=http://localhost
```

---

## Next Steps

1. **Test the features** - Login and try logout functionality
2. **Upload profile pictures** - Test avatar display
3. **Check responsive design** - View on mobile/tablet
4. **Verify session clearing** - Ensure no data persists after logout
5. **Monitor logs** - Check for any errors

