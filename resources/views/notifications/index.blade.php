@extends(auth()->user()->role === 'hr' ? 'layouts.hr' : (auth()->user()->role === 'supervisor' ? 'layouts.supervisor' : 'layouts.employee'))

@section('title', 'Notifications')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    @include('components.alert')

    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Notifications</h1>
            <p class="text-sm text-gray-500">All system notifications are kept here.</p>
        </div>

        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button class="bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">
                Mark all read
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow divide-y">
        @forelse ($notifications as $notification)
            <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 {{ $notification->read ? 'bg-white' : 'bg-yellow-50' }}">
                <div>
                    <div class="flex items-center gap-2">
                        @if (! $notification->read)
                            <span class="h-2 w-2 rounded-full bg-teal-600"></span>
                        @endif
                        <h2 class="font-semibold text-gray-800">{{ $notification->title }}</h2>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->format('M d, Y H:i') }}</p>
                </div>

                <div class="flex gap-2">
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button class="rounded border border-teal-600 px-3 py-1.5 text-sm font-semibold text-teal-700 hover:bg-teal-50">
                            {{ $notification->action_url ? 'Open' : 'Mark read' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">No notifications found.</div>
        @endforelse
    </div>

    {{ $notifications->links() }}
</div>
@endsection
