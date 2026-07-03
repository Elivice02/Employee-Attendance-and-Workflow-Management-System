<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    /**
     * HR: List all announcements (all statuses)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Announcement::class);

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $announcements = Announcement::with('creator')
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('announcements.index', compact('announcements'));
    }

    /**
     * HR: Show create form
     */
    public function create()
    {
        $this->authorize('create', Announcement::class);

        return view('announcements.create');
    }

    /**
     * HR: Store new draft announcement
     */
    public function store(Request $request)
    {
        $this->authorize('create', Announcement::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
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
     * HR: Show publish dialog with target role selection
     */
    public function showPublish(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        if (!$announcement->isDraft()) {
            return back()->with('error', 'Only draft announcements can be published.');
        }

        return view('announcements.publish', compact('announcement'));
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

            if ($recipients->isNotEmpty()) {
                AnnouncementRecipient::insert($recipients->toArray());
            }

            // Dispatch SMS if flagged (TODO: Implement SendAnnouncementSmsJob)
            // if ($announcement->send_sms) {
            //     dispatch(new SendAnnouncementSmsJob($announcement));
            // }
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
            ->with('department')
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
        $announcement = auth()->user()
            ->receivedAnnouncements()
            ->with('creator')
            ->where('announcements.id', $announcement->id)
            ->firstOrFail();

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
