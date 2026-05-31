@extends('layouts.employee')

@section('title', 'Review Leave Application')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded px-4 py-3 mb-5">
        Review this official leave application before submitting it to HR.
    </div>

    <div class="bg-white border rounded-lg shadow-sm p-8 text-sm leading-7">
        <div class="text-center border-b pb-5 mb-6">
            <p class="font-bold">THE UNITED REPUBLIC OF TANZANIA</p>
            <h1 class="text-xl font-bold mt-3">LEAVE APPLICATION FORM</h1>
            <p>To be filled in capital letters in three copies.</p>
        </div>

        <h2 class="font-bold mb-3">SECTION A: LEAVE REQUEST</h2>
        <div class="grid md:grid-cols-3 gap-3">
            <p><strong>Last Name:</strong> {{ $payload['last_name'] }}</p>
            <p><strong>Middle Name:</strong> {{ $payload['middle_name'] ?? '-' }}</p>
            <p><strong>First Name:</strong> {{ $payload['first_name'] }}</p>
            <p><strong>Personal File No.:</strong> {{ $payload['personal_file_no'] ?? '-' }}</p>
            <p><strong>Check No.:</strong> {{ $payload['check_no'] ?? '-' }}</p>
            <p><strong>TSD No.:</strong> {{ $payload['tsd_no'] ?? '-' }}</p>
            <p><strong>Designation:</strong> {{ $payload['designation'] ?? '-' }}</p>
            <p><strong>Station:</strong> {{ $payload['station'] ?? '-' }}</p>
            <p><strong>Institution:</strong> {{ $payload['institution'] ?? '-' }}</p>
            <p><strong>Division / Department:</strong> {{ $payload['division_department'] ?? '-' }}</p>
            <p><strong>First Appointment:</strong> {{ $payload['first_appointment_date'] ?? '-' }}</p>
        </div>

        <div class="mt-5 border-t pt-5">
            <p>I request <strong>{{ $type['name'] }}</strong> ({{ $type['standing_order'] }}) for <strong>{{ $payload['total_days'] }}</strong> days commencing on <strong>{{ $payload['start_date'] }}</strong> to <strong>{{ $payload['end_date'] }}</strong>.</p>
            <p><strong>Reason:</strong> {{ $payload['reason'] }}</p>
            <p>I will travel to <strong>{{ $payload['travel_destination'] ?? '-' }}</strong> where I will stay for <strong>{{ $payload['travel_days'] ?? '-' }}</strong> days.</p>
            <p><strong>Travel Assistance:</strong> {{ ucfirst(str_replace('_', ' ', $payload['transport_assistance'] ?? 'not specified')) }}</p>
        </div>

        <div class="mt-5 border-t pt-5">
            <p><strong>Spouse:</strong> {{ $payload['spouse_name'] ?? '-' }}</p>
            <p><strong>Contact:</strong> P.O. Box {{ $payload['po_box'] ?? '-' }}, Phone {{ $payload['phone'] ?? '-' }}, Email {{ $payload['email'] ?? '-' }}</p>
            <p><strong>Digital Signature:</strong> {{ $payload['signature_name'] }}</p>
        </div>
    </div>

    <div class="flex gap-3 mt-5">
        <form action="{{ route('employee.leave.store') }}" method="POST">
            @csrf
            <button class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Submit to HR</button>
        </form>
        <a href="{{ route('employee.leave.create') }}" class="bg-gray-600 text-white px-5 py-2 rounded hover:bg-gray-700">Edit Form</a>
    </div>
</div>
@endsection
