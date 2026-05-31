@extends(auth()->user()->role === 'hr' ? 'layouts.hr' : (auth()->user()->role === 'supervisor' ? 'layouts.supervisor' : 'layouts.employee'))

@section('title', 'Submit More Evidence')

@section('content')
<div class="max-w-3xl mx-auto">
    @include('components.alert')

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Submit More Evidence</h1>
            <p class="text-sm text-gray-500 mt-1">
                Update your explanation and upload a new evidence file for {{ $attendance->attendance_date->format('M d, Y') }}.
            </p>
        </div>

        @if ($attendance->late_review_note)
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg mb-5">
                <p class="text-sm font-semibold">Reviewer note</p>
                <p class="text-sm mt-1">{{ $attendance->late_review_note }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('attendance.evidence.update', $attendance) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Opening Paragraph</label>
                <textarea name="late_opening_paragraph" rows="3" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('late_opening_paragraph', $attendance->late_opening_paragraph ?? 'I hereby write this letter to explain my late arrival to work on ' . $attendance->attendance_date->format('jS F Y') . '.') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Explanation Details</label>
                <textarea name="late_explanation" rows="7" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('late_explanation', $attendance->late_explanation) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Closing Paragraph</label>
                <textarea name="late_closing_paragraph" rows="3" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('late_closing_paragraph', $attendance->late_closing_paragraph ?? 'I sincerely apologize for the inconvenience caused and assure management that I will take appropriate measures to avoid similar incidents in future.') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Digital Signature</label>
                <input name="late_signature_name" value="{{ old('late_signature_name', $attendance->late_signature_name ?? auth()->user()->name) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                <p class="text-xs text-gray-500 mt-1">Full Name: {{ auth()->user()->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Evidence File</label>
                <input type="file" name="late_evidence" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                <p class="text-xs text-gray-500 mt-1">Allowed: PDF, JPG, PNG, DOC, DOCX up to 4MB.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded-lg shadow">
                    Submit Evidence
                </button>

                <a href="{{ route($backRoute) }}" class="bg-white border border-gray-300 hover:bg-gray-50 px-5 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
