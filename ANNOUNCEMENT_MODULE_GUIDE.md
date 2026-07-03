# EAWMS Announcement Module - Implementation Guide

**Module Status:** Planning Phase  
**Build Target:** Complete workflow with SMS infrastructure testing  
**Priority:** Phase 2 (after core HR/Attendance modules)

---

## 📋 Module Overview

The Announcement module enables HR and Admin users to create, manage, and track organizational announcements with optional SMS notifications. It's designed as a **complete workflow** (not just CRUD) with role-based access and read tracking.

### Key Features
- ✓ Draft → Publish → Archive workflow
- ✓ Targeted audience selection
- ✓ SMS notification support
- ✓ Read tracking per recipient
- ✓ Audit trail (creator tracking)
- ✓ Dashboard widget integration

---

## 🗄️ Database Schema

### Migration 1: `announcements` table

**File:** `database/migrations/[timestamp]_create_announcements_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('message');

            // Creator audit trail
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Workflow status
            $table->enum('status', [
                'draft',
                'published',
                'archived'
            ])->default('draft');

            // SMS flag for job dispatch
            $table->boolean('send_sms')
                  ->default(false);

            // Track publish moment
            $table->timestamp('published_at')
                  ->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('created_by');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
```

---

### Migration 2: `announcement_recipients` table

**File:** `database/migrations/[timestamp]_create_announcement_recipients_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_recipients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('announcement_id')
                  ->constrained('announcements')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Read tracking
            $table->timestamp('read_at')
                  ->nullable();

            $table->timestamps();

            // Composite unique key (one recipient per announcement)
            $table->unique(['announcement_id', 'user_id']);

            // Indexes for queries
            $table->index('user_id');
            $table->index('read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_recipients');
    }
};
```

---

## 🏗️ Models

### Announcement Model

**File:** `app/Models/Announcement.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'message',
        'created_by',
        'status',
        'send_sms',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'send_sms' => 'boolean',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'announcement_recipients'
        )->withPivot('read_at', 'created_at')
          ->withTimestamps();
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    // Helpers
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function getRecipientCount(): int
    {
        return $this->recipients()->count();
    }

    public function getReadCount(): int
    {
        return $this->recipients()
                    ->whereNotNull('read_at')
                    ->count();
    }

    public function getUnreadCount(): int
    {
        return $this->getRecipientCount() - $this->getReadCount();
    }
}
```

---

### Update User Model

**File:** `app/Models/User.php` (Add to existing model)

```php
// In User model class, add this relationship:

public function receivedAnnouncements(): BelongsToMany
{
    return $this->belongsToMany(
        Announcement::class,
        'announcement_recipients'
    )->withPivot('read_at', 'created_at')
      ->withTimestamps();
}

// Scope for getting unread announcements
public function scopeUnreadAnnouncements($query)
{
    return $this->receivedAnnouncements()
                ->whereNull('read_at')
                ->orderBy('published_at', 'desc');
}

// Helper method
public function getUnreadAnnouncementCount(): int
{
    return $this->receivedAnnouncements()
                ->whereNull('read_at')
                ->count();
}
```

---

## 🛣️ Routes

**File:** `routes/web.php`

### HR Routes (Announcement Management)

```php
Route::middleware(['auth', 'role:hr,admin'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {

        // Announcement CRUD
        Route::get(
            '/announcements',
            [AnnouncementController::class, 'index']
        )->name('announcements.index');

        Route::get(
            '/announcements/create',
            [AnnouncementController::class, 'create']
        )->name('announcements.create');

        Route::post(
            '/announcements',
            [AnnouncementController::class, 'store']
        )->name('announcements.store');

        Route::get(
            '/announcements/{announcement}/edit',
            [AnnouncementController::class, 'edit']
        )->name('announcements.edit');

        Route::put(
            '/announcements/{announcement}',
            [AnnouncementController::class, 'update']
        )->name('announcements.update');

        // Publish workflow
        Route::post(
            '/announcements/{announcement}/publish',
            [AnnouncementController::class, 'publish']
        )->name('announcements.publish');

        // Archive
        Route::post(
            '/announcements/{announcement}/archive',
            [AnnouncementController::class, 'archive']
        )->name('announcements.archive');

        // Admin only: Delete
        Route::delete(
            '/announcements/{announcement}',
            [AnnouncementController::class, 'destroy']
        )->middleware('role:admin')
          ->name('announcements.destroy');

        // Reports
        Route::get(
            '/announcements/{announcement}/recipients',
            [AnnouncementController::class, 'showRecipients']
        )->name('announcements.recipients');
    });
```

### Employee Routes (View Announcements)

```php
Route::middleware('auth')
    ->group(function () {

        // View all announcements
        Route::get(
            '/announcements',
            [AnnouncementController::class, 'userIndex']
        )->name('announcements.user-index');

        // View single announcement
        Route::get(
            '/announcements/{announcement}',
            [AnnouncementController::class, 'userShow']
        )->name('announcements.user-show');

        // Mark as read
        Route::post(
            '/announcements/{announcement}/read',
            [AnnouncementController::class, 'markAsRead']
        )->name('announcements.read');
    });
```

---

## 🎮 Controllers

### AnnouncementController

**File:** `app/Http/Controllers/AnnouncementController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * HR: List all announcements (all statuses)
     */
    public function index()
    {
        $announcements = Announcement::with('creator')
            ->latest()
            ->paginate(15);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * HR: Show create form
     */
    public function create()
    {
        return view('announcements.create');
    }

    /**
     * HR: Store new draft announcement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_roles' => 'required|array',
            'target_roles.*' => 'in:employee,supervisor,hr',
            'send_sms' => 'boolean',
        ]);

        // Create announcement as draft
        $announcement = Announcement::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'created_by' => auth()->id(),
            'status' => 'draft',
            'send_sms' => $validated['send_sms'] ?? false,
        ]);

        return redirect()
            ->route('hr.announcements.edit', $announcement)
            ->with('success', 'Announcement saved as draft.');
    }

    /**
     * HR: Show edit form
     */
    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        return view('announcements.edit', compact('announcement'));
    }

    /**
     * HR: Update draft announcement
     */
    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        // Only allow editing if draft
        if (!$announcement->isDraft()) {
            return back()->with('error', 'Cannot edit published announcements.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_roles' => 'required|array',
            'target_roles.*' => 'in:employee,supervisor,hr',
            'send_sms' => 'boolean',
        ]);

        $announcement->update([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'send_sms' => $validated['send_sms'] ?? false,
        ]);

        return back()->with('success', 'Announcement updated.');
    }

    /**
     * HR: Publish announcement
     * 1. Change status to published
     * 2. Set published_at timestamp
     * 3. Create recipient records based on target roles
     * 4. Optional: Dispatch SMS job
     */
    public function publish(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        if (!$announcement->isDraft()) {
            return back()->with('error', 'Only draft announcements can be published.');
        }

        // Get target roles from request
        $validated = $request->validate([
            'target_roles' => 'required|array',
            'target_roles.*' => 'in:employee,supervisor,hr',
        ]);

        DB::transaction(function () use ($announcement, $validated) {
            // Update announcement status
            $announcement->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

            // Get users matching target roles
            $users = User::whereIn('role', $validated['target_roles'])
                ->pluck('id');

            // Create recipient records
            $recipients = $users->map(fn ($userId) => [
                'announcement_id' => $announcement->id,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            AnnouncementRecipient::insert($recipients->toArray());

            // Dispatch SMS if flagged
            if ($announcement->send_sms) {
                // TODO: Dispatch SendAnnouncementSmsJob
                // dispatch(new SendAnnouncementSmsJob($announcement));
            }
        });

        return redirect()
            ->route('hr.announcements.index')
            ->with('success', 'Announcement published successfully.');
    }

    /**
     * HR: Archive announcement
     */
    public function archive(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $announcement->update(['status' => 'archived']);

        return back()->with('success', 'Announcement archived.');
    }

    /**
     * Admin only: Delete announcement
     */
    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        $title = $announcement->title;
        $announcement->delete();

        return redirect()
            ->route('hr.announcements.index')
            ->with('success', "Announcement '{$title}' deleted.");
    }

    /**
     * HR: View recipient list and read status
     */
    public function showRecipients(Announcement $announcement)
    {
        $this->authorize('view', $announcement);

        $recipients = $announcement->recipients()
            ->with('announcements')
            ->paginate(20);

        $readCount = $announcement->getReadCount();
        $totalCount = $announcement->getRecipientCount();

        return view('announcements.recipients', compact(
            'announcement',
            'recipients',
            'readCount',
            'totalCount'
        ));
    }

    /**
     * Employee: View all announcements sent to user
     */
    public function userIndex()
    {
        $announcements = auth()->user()
            ->receivedAnnouncements()
            ->latest('published_at')
            ->paginate(15);

        return view('announcements.user-index', compact('announcements'));
    }

    /**
     * Employee: View single announcement detail
     */
    public function userShow(Announcement $announcement)
    {
        // Check if user has received this announcement
        if (!auth()->user()->receivedAnnouncements->contains($announcement)) {
            abort(403, 'Unauthorized');
        }

        return view('announcements.user-show', compact('announcement'));
    }

    /**
     * Employee: Mark announcement as read
     */
    public function markAsRead(Announcement $announcement)
    {
        $recipient = AnnouncementRecipient::where([
            'announcement_id' => $announcement->id,
            'user_id' => auth()->id(),
        ])->first();

        if (!$recipient) {
            return response()->json(
                ['error' => 'You did not receive this announcement'],
                403
            );
        }

        if (!$recipient->read_at) {
            $recipient->update(['read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Announcement marked as read.',
        ]);
    }
}
```

---

## 🔐 Authorization (Policy)

**File:** `app/Policies/AnnouncementPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['hr', 'admin']);
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return in_array($user->role, ['hr', 'admin'])
            || $announcement->recipients->contains($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['hr', 'admin']);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return in_array($user->role, ['hr', 'admin'])
            && $user->id === $announcement->created_by;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->role === 'admin';
    }
}
```

---

## 🎨 Views (Structure)

### Views File Structure
```
resources/views/announcements/
├── index.blade.php           (HR: List all)
├── create.blade.php          (HR: Create form)
├── edit.blade.php            (HR: Edit form)
├── recipients.blade.php      (HR: View recipients & read status)
├── user-index.blade.php      (Employee: List announcements)
├── user-show.blade.php       (Employee: View detail)
└── partials/
    ├── form.blade.php        (Shared form partial)
    └── target-roles.blade.php (Target audience selector)
```

---

## 📊 Dashboard Widget Integration

**File:** Add to Employee Dashboard Controller

```php
// In EmployeeDashboardController

public function dashboard()
{
    $unreadAnnouncements = auth()->user()
        ->receivedAnnouncements()
        ->whereNull('announcement_recipients.read_at')
        ->latest('published_at')
        ->take(5)
        ->get();

    $unreadCount = auth()->user()->getUnreadAnnouncementCount();

    return view('employee.dashboard', compact(
        'unreadAnnouncements',
        'unreadCount'
    ));
}
```

**Widget in Dashboard:**
```blade
<div class="announcement-widget">
    <h3>Announcements ({{ $unreadCount }})</h3>
    <ul class="announcement-list">
        @forelse ($unreadAnnouncements as $ann)
            <li>
                <a href="{{ route('announcements.user-show', $ann) }}">
                    {{ $ann->title }}
                </a>
                <small>{{ $ann->published_at->diffForHumans() }}</small>
            </li>
        @empty
            <li class="text-muted">No unread announcements</li>
        @endforelse
    </ul>
    <a href="{{ route('announcements.user-index') }}" class="btn-small">
        View All
    </a>
</div>
```

---

## ⏰ Build Order (Phase-by-Phase)

### Phase 1: Database & Models (1-2 hours)
- [ ] Create `announcements` migration
- [ ] Create `announcement_recipients` migration
- [ ] Run migrations
- [ ] Create `Announcement` model with relationships
- [ ] Update `User` model with relationships
- [ ] Create `AnnouncementPolicy`

### Phase 2: HR Management (3-4 hours)
- [ ] Create `AnnouncementController` (basic CRUD)
- [ ] Create HR routes
- [ ] Build `announcements/index.blade.php` (list)
- [ ] Build `announcements/create.blade.php`
- [ ] Build `announcements/edit.blade.php`
- [ ] Test draft creation and editing

### Phase 3: Publishing Workflow (2-3 hours)
- [ ] Implement `publish()` method
- [ ] Create `announcements/show-publish.blade.php` (publish dialog)
- [ ] Implement target role selection logic
- [ ] Test recipient creation
- [ ] Build `announcements/recipients.blade.php` (view recipients)

### Phase 4: Employee View (2 hours)
- [ ] Create employee routes
- [ ] Implement `userIndex()` and `userShow()`
- [ ] Build `announcements/user-index.blade.php`
- [ ] Build `announcements/user-show.blade.php`
- [ ] Test visibility logic

### Phase 5: Read Tracking (1 hour)
- [ ] Implement `markAsRead()` method
- [ ] Add read marker in views
- [ ] Test read status updates

### Phase 6: Dashboard Widget (1 hour)
- [ ] Update `EmployeeDashboardController`
- [ ] Build widget partial
- [ ] Display unread count
- [ ] Link to announcements

### Phase 7: Testing & Refinement (2 hours)
- [ ] Write feature tests
- [ ] Test authorization policies
- [ ] Test read tracking
- [ ] Performance testing (large recipient lists)

### Phase 8: SMS Integration (1-2 hours) ⏳ Later
- [ ] Create `SendAnnouncementSmsJob`
- [ ] Update `publish()` to dispatch job
- [ ] Test SMS sending
- [ ] Add SMS delivery tracking

---

## 🧪 Testing Checklist

### Unit Tests
- [ ] Announcement model scopes
- [ ] Helper methods (read count, unread count)
- [ ] User relationships

### Feature Tests
- [ ] HR can create draft announcement
- [ ] HR can edit draft announcement
- [ ] HR can publish announcement with role targeting
- [ ] Recipients created correctly on publish
- [ ] Employee receives correct announcements
- [ ] Employee can mark announcement as read
- [ ] Admin can delete announcements
- [ ] Read status updates correctly

### Authorization Tests
- [ ] Only HR/Admin can create
- [ ] Only creator/admin can edit
- [ ] Only admin can delete
- [ ] Employees can only view their announcements
- [ ] Only recipients can mark as read

---

## 🚀 Future Enhancements

- **SMS Gateway Integration** - Send SMS notifications on publish
- **Email Notifications** - Send email to recipients
- **Advanced Targeting** - By department, team, custom filters
- **Scheduling** - Schedule announcements for future publish
- **Templates** - Reusable announcement templates
- **Analytics** - Read rate tracking, engagement metrics
- **Push Notifications** - Mobile app notifications
- **Attachments** - Include files/documents with announcements
- **Rich Text Editor** - WYSIWYG message editor
- **Archive Search** - Search and filter archived announcements

---

## 📝 Key Design Decisions

### Why Separate Recipients Table?
1. **Read Tracking** - Track when each user reads
2. **Auditability** - Full history of who received what
3. **Reporting** - Easy queries for analytics
4. **Scalability** - Handles large audiences efficiently
5. **Future Features** - Foundation for push notifications, email tracking

### Why Draft → Publish Pattern?
- Allows review before sending to audience
- Prevents accidental sends
- Can target specific roles at publish time
- Clear workflow for HR users

### Why Role-Based Targeting?
- Simple, scalable approach
- Covers 80% of use cases
- Can extend to custom filtering later
- Easy to understand for users

---

## 📚 Additional Resources

- [Laravel Eloquent Relationships](https://laravel.com/docs/eloquent-relationships)
- [Laravel Authorization Policies](https://laravel.com/docs/authorization#creating-policies)
- [Laravel Transactions](https://laravel.com/docs/database#transactions)
- [Laravel Jobs & Queues](https://laravel.com/docs/queues) (for SMS)

---

**Module Author:** EAWMS Development Team  
**Last Updated:** June 16, 2026  
**Status:** Ready for Implementation
