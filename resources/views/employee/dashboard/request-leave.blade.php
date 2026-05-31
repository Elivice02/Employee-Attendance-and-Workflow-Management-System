@extends('layouts.employee')

@section('title', 'Leave Application Form')

@section('content')
@php
    $nameParts = preg_split('/\s+/', trim($employee->name));
    $firstName = old('first_name', $nameParts[0] ?? '');
    $lastName = old('last_name', count($nameParts) > 1 ? end($nameParts) : '');
    $middleName = old('middle_name', count($nameParts) > 2 ? implode(' ', array_slice($nameParts, 1, -1)) : '');
    $emblemPath = public_path('images/tanzania-emblem.png');
@endphp

<div class="max-w-6xl mx-auto px-4 py-8">
    @if ($errors->any())
        <div class="mb-5 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-700">{{ session('error') }}</div>
    @endif

    <form action="{{ route('employee.leave.preview') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <section class="bg-white border rounded-lg shadow-sm p-6">
            <div class="text-center border-b pb-5 mb-6">
                <p class="font-bold tracking-wide">THE UNITED REPUBLIC OF TANZANIA</p>
                @if (file_exists($emblemPath))
                    <img class="emblem" src="{{ $emblemPath }}" alt="Tanzania emblem">
                @endif
                <h1 class="text-xl font-bold mt-3">LEAVE APPLICATION FORM</h1>
                <p class="text-sm text-gray-600 mt-2">To be filled in capital letters in three copies.</p>
            </div>

            <h2 class="font-bold text-gray-900 mb-4">SECTION A: LEAVE REQUEST</h2>

            <div class="grid md:grid-cols-3 gap-4">
                <label class="block">
                    <span class="text-sm font-semibold">Last Name</span>
                    <input name="last_name" value="{{ $lastName }}" class="mt-1 w-full rounded border-gray-300" required>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Middle Name</span>
                    <input name="middle_name" value="{{ $middleName }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">First Name</span>
                    <input name="first_name" value="{{ $firstName }}" class="mt-1 w-full rounded border-gray-300" required>
                </label>
            </div>

            <div class="grid md:grid-cols-3 gap-4 mt-4">
                <label class="block">
                    <span class="text-sm font-semibold">Personal File No.</span>
                    <input name="personal_file_no" value="{{ old('personal_file_no') }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Check No.</span>
                    <input name="check_no" value="{{ old('check_no') }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">TSD No.</span>
                    <input name="tsd_no" value="{{ old('tsd_no') }}" class="mt-1 w-full rounded border-gray-300">
                </label>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mt-4">
                <label class="block">
                    <span class="text-sm font-semibold">Designation</span>
                    <input name="designation" value="{{ old('designation', ucfirst($employee->role)) }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Station</span>
                    <input name="station" value="{{ old('station') }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Institution</span>
                    <input name="institution" value="{{ old('institution') }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Division / Department</span>
                    <input name="division_department" value="{{ old('division_department', $employee->department?->name) }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Date of First Appointment</span>
                    <input type="date" name="first_appointment_date" value="{{ old('first_appointment_date') }}" class="mt-1 w-full rounded border-gray-300">
                </label>
            </div>
        </section>

        <section class="bg-white border rounded-lg shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-4">A2: Leave Request</h2>

            <div class="grid md:grid-cols-3 gap-4">
                <label class="block md:col-span-3">
                    <span class="text-sm font-semibold">Type of Leave Number</span>
                    <select name="leave_type_number" class="mt-1 w-full rounded border-gray-300" required>
                        <option value="">Select leave type</option>
                        @foreach ($types as $number => $type)
                            <option value="{{ $number }}" @selected((string) old('leave_type_number') === (string) $number)>
                                {{ $number }}. {{ $type['name'] }} - {{ $type['standing_order'] }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Commencing On</span>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="mt-1 w-full rounded border-gray-300" required>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">To</span>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="mt-1 w-full rounded border-gray-300" required>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Travel Destination</span>
                    <input name="travel_destination" value="{{ old('travel_destination') }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Days at Destination</span>
                    <input type="number" min="0" name="travel_days" value="{{ old('travel_days') }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Travel Assistance</span>
                    <select name="transport_assistance" class="mt-1 w-full rounded border-gray-300">
                        <option value="">Not specified</option>
                        <option value="entitled" @selected(old('transport_assistance') === 'entitled')>I am entitled</option>
                        <option value="not_entitled" @selected(old('transport_assistance') === 'not_entitled')>I am not entitled</option>
                    </select>
                </label>
            </div>

            <label class="block mt-4">
                <span class="text-sm font-semibold">Reason / Remarks</span>
                <textarea name="reason" rows="4" class="mt-1 w-full rounded border-gray-300" required>{{ old('reason') }}</textarea>
            </label>
        </section>

        <section class="bg-white border rounded-lg shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-4">Family and Contact Details</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold">Name of Spouse</span>
                    <input name="spouse_name" value="{{ old('spouse_name') }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                @for ($i = 1; $i <= 4; $i++)
                    <label class="block">
                        <span class="text-sm font-semibold">Child {{ $i }} Name</span>
                        <input name="child_{{ $i }}_name" value="{{ old('child_'.$i.'_name') }}" class="mt-1 w-full rounded border-gray-300">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold">Child {{ $i }} Date of Birth</span>
                        <input type="date" name="child_{{ $i }}_dob" value="{{ old('child_'.$i.'_dob') }}" class="mt-1 w-full rounded border-gray-300">
                    </label>
                @endfor
                <label class="block">
                    <span class="text-sm font-semibold">P.O. Box Number</span>
                    <input name="po_box" value="{{ old('po_box') }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Phone Number</span>
                    <input name="phone" value="{{ old('phone', $employee->phone) }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Email Address</span>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="mt-1 w-full rounded border-gray-300">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Digital Signature Name</span>
                    <input name="signature_name" value="{{ old('signature_name', $employee->name) }}" class="mt-1 w-full rounded border-gray-300" required>
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold">Supporting Document</span>
                    <input type="file" name="attachment_path" class="mt-1 w-full rounded border-gray-300" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </label>
            </div>
        </section>

        <div class="flex gap-3">
            <button class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Review Application</button>
            <a href="{{ route('employee.leave.index') }}" class="bg-gray-600 text-white px-5 py-2 rounded hover:bg-gray-700">Back</a>
        </div>
    </form>
</div>
@endsection
