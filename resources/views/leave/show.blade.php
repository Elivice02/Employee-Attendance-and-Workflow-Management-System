@extends($roleView === 'hr' ? 'layouts.hr' : ($roleView === 'supervisor' ? 'layouts.supervisor' : 'layouts.employee'))

@section('title', 'Leave Application')

@section('content')
@php
    $sectionA = $leave->section_a ?? [];
    $sectionB = $leave->section_b ?? [];
    $sectionC = $leave->section_c ?? [];
    $sectionD = $leave->section_d ?? [];
@endphp

<div class="max-w-6xl mx-auto px-4 py-8">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-between items-start mb-5">
        <div>
            <h1 class="text-2xl font-bold">Leave Application {{ $leave->ref_no }}</h1>
            <p class="text-gray-600">{{ $leave->employee?->name }} - {{ $leave->leave_type_number }}. {{ $leave->leave_type_name }}</p>
        </div>
        @if ($leave->pdf_path)
            <a href="{{ route('leave.pdf', $leave) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">View PDF</a>
        @endif
    </div>

    <div class="bg-white border rounded-lg shadow-sm p-6 space-y-6">
        <section>
            <h2 class="font-bold border-b pb-2 mb-3">SECTION A: LEAVE REQUEST</h2>
            <div class="grid md:grid-cols-3 gap-3 text-sm">
                <p><strong>Last Name:</strong> {{ $sectionA['last_name'] ?? '-' }}</p>
                <p><strong>Middle Name:</strong> {{ $sectionA['middle_name'] ?? '-' }}</p>
                <p><strong>First Name:</strong> {{ $sectionA['first_name'] ?? '-' }}</p>
                <p><strong>Personal File No.:</strong> {{ $sectionA['personal_file_no'] ?? '-' }}</p>
                <p><strong>Check No.:</strong> {{ $sectionA['check_no'] ?? '-' }}</p>
                <p><strong>TSD No.:</strong> {{ $sectionA['tsd_no'] ?? '-' }}</p>
                <p><strong>Designation:</strong> {{ $sectionA['designation'] ?? '-' }}</p>
                <p><strong>Station:</strong> {{ $sectionA['station'] ?? '-' }}</p>
                <p><strong>Institution:</strong> {{ $sectionA['institution'] ?? '-' }}</p>
                <p><strong>Department:</strong> {{ $sectionA['division_department'] ?? '-' }}</p>
                <p><strong>First Appointment:</strong> {{ $sectionA['first_appointment_date'] ?? '-' }}</p>
                <p><strong>Signature:</strong> {{ $sectionA['signature_name'] ?? '-' }}</p>
            </div>
            <p class="text-sm mt-4">I request <strong>{{ $leave->leave_type_name }}</strong> for <strong>{{ $leave->total_days }}</strong> days commencing on <strong>{{ $leave->start_date?->format('M d, Y') }}</strong> to <strong>{{ $leave->end_date?->format('M d, Y') }}</strong>.</p>
            <p class="text-sm mt-2"><strong>Reason:</strong> {{ $leave->reason }}</p>
        </section>

        <section>
            <h2 class="font-bold border-b pb-2 mb-3">SECTION B: HR LEAVE RECORD REVIEW</h2>
            @if ($sectionB)
                <div class="grid md:grid-cols-3 gap-3 text-sm">
                    <p><strong>Last Leave:</strong> {{ $sectionB['last_leave_start'] ?? '-' }} to {{ $sectionB['last_leave_end'] ?? '-' }}</p>
                    <p><strong>Days Taken:</strong> {{ $sectionB['days_taken'] ?? '-' }}</p>
                    <p><strong>Previous Outstanding:</strong> {{ $sectionB['previous_outstanding_days'] ?? '-' }}</p>
                    <p><strong>Current Outstanding:</strong> {{ $sectionB['current_outstanding_days'] ?? '-' }}</p>
                    <p><strong>Transport:</strong> {{ ucfirst(str_replace('_', ' ', $sectionB['transport_allowance_status'] ?? '-')) }}</p>
                    <p><strong>Signature:</strong> {{ $sectionB['signature_name'] ?? '-' }}</p>
                </div>
                <p class="text-sm mt-2"><strong>Remarks:</strong> {{ $sectionB['hr_review_remarks'] ?? '-' }}</p>
            @elseif ($roleView === 'hr' && $leave->current_stage === 'pending_hr_review')
                <form action="{{ route('hr.leaves.verify', $leave) }}" method="POST" class="grid md:grid-cols-3 gap-4">
                    @csrf
                    <input type="date" name="last_leave_start" class="rounded border-gray-300" placeholder="Last leave start">
                    <input type="date" name="last_leave_end" class="rounded border-gray-300" placeholder="Last leave end">
                    <input type="number" min="0" name="days_taken" class="rounded border-gray-300" placeholder="Days taken">
                    <input type="number" min="0" name="previous_outstanding_days" class="rounded border-gray-300" placeholder="Previous outstanding days">
                    <input type="number" min="0" name="current_outstanding_days" class="rounded border-gray-300" placeholder="Current outstanding days">
                    <select name="transport_allowance_status" class="rounded border-gray-300">
                        <option value="">Transport status</option>
                        <option value="paid">Paid</option>
                        <option value="not_paid">Not paid</option>
                    </select>
                    <input type="number" min="0" step="0.01" name="transport_paid_amount" class="rounded border-gray-300" placeholder="Paid TZS">
                    <input type="number" min="0" step="0.01" name="transport_debt_amount" class="rounded border-gray-300" placeholder="Debt TZS">
                    <input name="signature_name" class="rounded border-gray-300" placeholder="HR signature name" required>
                    <textarea name="hr_review_remarks" class="md:col-span-3 rounded border-gray-300" rows="3" placeholder="Remarks"></textarea>
                    <button class="md:col-span-3 bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">Submit Section B Review</button>
                </form>
            @else
                <p class="text-sm text-gray-500">Pending HR review.</p>
            @endif
        </section>

        <section>
            <h2 class="font-bold border-b pb-2 mb-3">SECTION C: HEAD OF DEPARTMENT / UNIT RECOMMENDATION</h2>
            @if ($sectionC)
                <p class="text-sm"><strong>Recommendation:</strong> {{ ucfirst(str_replace('_', ' ', $sectionC['recommendation'] ?? '-')) }}</p>
                <p class="text-sm mt-2"><strong>Reason:</strong> {{ $sectionC['reason'] ?? '-' }}</p>
                <p class="text-sm mt-2"><strong>Signature:</strong> {{ $sectionC['signature_name'] ?? '-' }} | <strong>Designation:</strong> {{ $sectionC['designation'] ?? '-' }}</p>
            @elseif ($roleView === 'supervisor' && $leave->current_stage === 'pending_supervisor_recommendation')
                <form action="{{ route('supervisor.leaves.recommend', $leave) }}" method="POST" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    <select name="recommendation" class="rounded border-gray-300" required>
                        <option value="">Select recommendation</option>
                        <option value="recommended">Recommend</option>
                        <option value="not_recommended">Do not recommend</option>
                    </select>
                    <input name="designation" class="rounded border-gray-300" placeholder="Designation">
                    <input name="signature_name" class="rounded border-gray-300" placeholder="Signature name" required>
                    <textarea name="reason" rows="3" class="md:col-span-2 rounded border-gray-300" placeholder="Reason" required></textarea>
                    <button class="md:col-span-2 bg-slate-700 text-white px-4 py-2 rounded hover:bg-slate-800">Submit Recommendation</button>
                </form>
            @else
                <p class="text-sm text-gray-500">Pending supervisor recommendation.</p>
            @endif
        </section>

        <section>
            <h2 class="font-bold border-b pb-2 mb-3">SECTION D: APPROVAL DECISION</h2>
            @if ($sectionD)
                <p class="text-sm"><strong>Decision:</strong> {{ ucfirst($sectionD['decision'] ?? '-') }}</p>
                <p class="text-sm mt-2"><strong>Remarks:</strong> {{ $sectionD['remarks'] ?? '-' }}</p>
                <p class="text-sm mt-2"><strong>Signature:</strong> {{ $sectionD['signature_name'] ?? '-' }} | <strong>Designation:</strong> {{ $sectionD['designation'] ?? '-' }}</p>
            @elseif ($roleView === 'hr' && $leave->current_stage === 'pending_final_approval')
                <form action="{{ route('hr.leaves.final-review', $leave) }}" method="POST" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    <select name="decision" class="rounded border-gray-300" required>
                        <option value="">Select decision</option>
                        <option value="approved">Approve</option>
                        <option value="denied">Deny</option>
                    </select>
                    <select name="transport_allowance_decision" class="rounded border-gray-300">
                        <option value="">Transport allowance decision</option>
                        <option value="with">With transport allowance</option>
                        <option value="without">Without transport allowance</option>
                    </select>
                    <select name="transport_allowance_deserved" class="rounded border-gray-300">
                        <option value="">Allowance entitlement</option>
                        <option value="deserve">Deserve</option>
                        <option value="not_deserve">Not deserve</option>
                    </select>
                    <input name="designation" class="rounded border-gray-300" placeholder="Designation">
                    <input name="signature_name" class="rounded border-gray-300" placeholder="Authorizing officer signature name" required>
                    <textarea name="remarks" rows="3" class="md:col-span-2 rounded border-gray-300" placeholder="Remarks"></textarea>
                    <button class="md:col-span-2 bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">Save Final Decision and Generate PDF</button>
                </form>
            @else
                <p class="text-sm text-gray-500">Pending final approval decision.</p>
            @endif
        </section>
    </div>
</div>
@endsection
