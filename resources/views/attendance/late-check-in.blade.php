@extends(auth()->user()->role === 'hr' ? 'layouts.hr' : (auth()->user()->role === 'supervisor' ? 'layouts.supervisor' : 'layouts.employee'))

@section('title', 'Late Check-In Explanation')

@section('content')
@php
    $user = auth()->user();
    $departmentName = $user->department?->name ?? match ($user->role) {
        'hr' => 'Human Resources',
        'supervisor' => 'Supervisor',
        default => 'Not assigned',
    };
@endphp

<div class="max-w-5xl mx-auto">
    @include('components.alert')

    @if (session('warning'))
        <div class="bg-yellow-100 text-yellow-800 p-3 mb-4 rounded">
            {{ session('warning') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('attendance.late.preview') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Late Attendance Explanation Letter</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Review the full letter before submitting it to HR.
                </p>
            </div>

            <div class="font-serif text-[16px] leading-8 text-gray-900 border border-gray-200 rounded-lg p-6 bg-gray-50">
                <div class="text-right mb-8">
                    <p class="font-semibold">{{ strtoupper($companyName) }}</p>
                    <p>{{ $companyAddress }}</p>
                    <p>{{ now()->format('jS F, Y') }}</p>
                </div>

                <p class="mb-5">REF NO: {{ $referencePreview }}</p>

                <div class="mb-5">
                    <p>The Human Resource Manager,</p>
                    <p>{{ $companyName }},</p>
                    <p>{{ $companyAddress }}.</p>
                </div>

                <p class="mb-5">Dear Sir/Madam,</p>

                <p class="text-center font-bold mb-5">REF: EXPLANATION FOR LATE REPORTING TO WORK</p>

                <div class="mb-5">
                    <label class="block text-xs font-sans font-semibold uppercase text-gray-500 mb-1">Opening paragraph</label>
                    <textarea name="late_opening_paragraph" rows="3" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 font-serif leading-7 focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('late_opening_paragraph', $draftPayload['late_opening_paragraph'] ?? $openingParagraph) }}</textarea>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-sans font-semibold uppercase text-gray-500 mb-1">Explanation details</label>
                    <textarea name="late_explanation" rows="6" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 font-serif leading-7 focus:ring-2 focus:ring-teal-500 focus:outline-none"
                        placeholder="Explain clearly and factually why you were late today.">{{ old('late_explanation', $draftPayload['late_explanation'] ?? '') }}</textarea>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-sans font-semibold uppercase text-gray-500 mb-1">Closing paragraph</label>
                    <textarea name="late_closing_paragraph" rows="3" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 font-serif leading-7 focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('late_closing_paragraph', $draftPayload['late_closing_paragraph'] ?? $closingParagraph) }}</textarea>
                </div>

                <p>Thank you for your understanding.</p>

                <div class="mt-10">
                    <p>Yours faithfully,</p>
                    <div class="mt-5 mb-3">
                        <label class="block text-xs font-sans font-semibold uppercase text-gray-500 mb-1">Digital signature</label>
                        <input name="late_signature_name" value="{{ old('late_signature_name', $draftPayload['late_signature_name'] ?? $user->name) }}" required
                            class="w-full md:w-80 border border-gray-300 rounded-lg px-3 py-2 font-serif focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <p>Full Name: {{ $user->name }}</p>
                    <p>Employee ID: EMP{{ str_pad((string) $user->id, 3, '0', STR_PAD_LEFT) }}</p>
                    <p>Department/Role: {{ $departmentName }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Evidence File</label>
            <input type="file" name="late_evidence" {{ $attendanceSetting->late_evidence_required ? 'required' : '' }}
                class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
            <p class="text-xs text-gray-500 mt-1">
                Allowed: PDF, JPG, PNG, DOC, DOCX up to 4MB.
                {{ $attendanceSetting->late_evidence_required ? 'Evidence is required.' : 'Evidence is optional.' }}
            </p>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded-lg shadow">
                Review Letter
            </button>

            <a href="{{ route($backRoute) }}" class="bg-white border border-gray-300 hover:bg-gray-50 px-5 py-2 rounded-lg">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
