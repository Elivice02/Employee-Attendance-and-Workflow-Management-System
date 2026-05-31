@extends(auth()->user()->role === 'hr' ? 'layouts.hr' : (auth()->user()->role === 'supervisor' ? 'layouts.supervisor' : 'layouts.employee'))

@section('title', 'Review Late Letter')

@section('content')
@php
    $departmentName = $user->department?->name ?? match ($user->role) {
        'hr' => 'Human Resources',
        'supervisor' => 'Supervisor',
        default => 'Not assigned',
    };
@endphp

<div class="max-w-5xl mx-auto space-y-6">
    @include('components.alert')

    <div class="bg-white rounded-lg shadow p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Review Letter Before Submission</h1>
            <p class="text-sm text-gray-500 mt-1">This letter has not been submitted to HR yet.</p>
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

            <p class="mb-5 text-justify">{{ $payload['late_opening_paragraph'] }}</p>
            <p class="mb-5 text-justify">{{ $payload['late_explanation'] }}</p>
            <p class="mb-5 text-justify">{{ $payload['late_closing_paragraph'] }}</p>
            <p>Thank you for your understanding.</p>

            <div class="mt-10">
                <p>Yours faithfully,</p>
                <p class="mt-5 italic">{{ $payload['late_signature_name'] }}</p>
                <p>Full Name: {{ $user->name }}</p>
                <p>Employee ID: EMP{{ str_pad((string) $user->id, 3, '0', STR_PAD_LEFT) }}</p>
                <p>Department/Role: {{ $departmentName }}</p>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('attendance.late.create') }}" class="bg-white border border-gray-300 hover:bg-gray-50 px-5 py-2 rounded-lg">
            Edit Letter
        </a>

        <form method="POST" action="{{ route('attendance.late.store') }}">
            @csrf
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded-lg shadow">
                Submit to HR
            </button>
        </form>

        <a href="{{ route($backRoute) }}" class="bg-gray-100 border border-gray-300 hover:bg-gray-200 px-5 py-2 rounded-lg">
            Cancel
        </a>
    </div>
</div>
@endsection
