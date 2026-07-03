@php
    $sectionA = $leave->section_a ?? [];
    $sectionB = $leave->section_b ?? [];
    $sectionC = $leave->section_c ?? [];
    $sectionD = $leave->section_d ?? [];
    $emblemPath = public_path('images/tanzania-emblem.png');
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .center { text-align: center; }
        .title { font-size: 16px; font-weight: bold; margin-top: 10px; }
        .section-title { font-weight: bold; margin-top: 16px; border-top: 1px solid #111; padding-top: 6px; }
        .grid { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .grid td { border: 1px solid #333; padding: 5px; vertical-align: top; }
        .line { border-bottom: 1px dotted #333; min-height: 14px; display: inline-block; min-width: 150px; }
        .emblem { width: 76px; height: auto; margin-top: 10px; }
        .page-break { page-break-before: always; }
        .leave-types td { border: none; font-size: 13px; padding: 4px 6px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="center">
        <div style="font-weight: bold;">THE UNITED REPUBLIC OF TANZANIA</div>
        @if (file_exists($emblemPath))
            <img class="emblem" src="{{ $emblemPath }}" alt="Tanzania emblem">
        @endif
        <div class="title">LEAVE APPLICATION FORM</div>
        <p>To be filled in capital letters in three copies. One complete copy will be given back to the Employee as an authority to allow him/her to go on leave.</p>
    </div>

    <p><strong>Reference No.:</strong> {{ $leave->ref_no }}</p>

    <div class="section-title">SECTION A: LEAVE REQUEST (to be completed by the Employee)</div>
    <table class="grid">
        <tr>
            <td>Last Name: {{ $sectionA['last_name'] ?? '' }}</td>
            <td>Middle Name: {{ $sectionA['middle_name'] ?? '' }}</td>
            <td>First Name: {{ $sectionA['first_name'] ?? '' }}</td>
        </tr>
        <tr>
            <td>Personal File No.: {{ $sectionA['personal_file_no'] ?? '' }}</td>
            <td>Check No.: {{ $sectionA['check_no'] ?? '' }}</td>
            <td>TSD No.: {{ $sectionA['tsd_no'] ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="3">Designation: {{ $sectionA['designation'] ?? '' }}</td>
        </tr>
        <tr>
            <td>Station: {{ $sectionA['station'] ?? '' }}</td>
            <td>Institution: {{ $sectionA['institution'] ?? '' }}</td>
            <td>Division / Department: {{ $sectionA['division_department'] ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="3">Date of First Appointment: {{ $sectionA['first_appointment_date'] ?? '' }}</td>
        </tr>
    </table>

    <p>I request <strong>{{ $leave->leave_type_name }}</strong> leave for <strong>{{ $leave->total_days }}</strong> days commencing on <strong>{{ optional($leave->start_date)->format('d/m/Y') }}</strong> to <strong>{{ optional($leave->end_date)->format('d/m/Y') }}</strong>.</p>
    <p>Reason: {{ $leave->reason }}</p>
    <p>I will travel to <strong>{{ $sectionA['travel_destination'] ?? '' }}</strong> where I will stay for <strong>{{ $sectionA['travel_days'] ?? '' }}</strong> days.</p>
    <p>I am / I am not entitled to travel assistance: <strong>{{ ucfirst(str_replace('_', ' ', $sectionA['transport_assistance'] ?? 'not specified')) }}</strong></p>

    <table class="grid">
        <tr>
            <td colspan="2">Name of Spouse: {{ $sectionA['spouse_name'] ?? '' }}</td>
        </tr>
        <tr>
            <td>
                <strong>Child 1</strong><br>
                Name: {{ $sectionA['child_1_name'] ?? '' }}<br>
                Date of Birth: {{ $sectionA['child_1_dob'] ?? '' }}
            </td>
            <td>
                <strong>Child 2</strong><br>
                Name: {{ $sectionA['child_2_name'] ?? '' }}<br>
                Date of Birth: {{ $sectionA['child_2_dob'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td>
                <strong>Child 3</strong><br>
                Name: {{ $sectionA['child_3_name'] ?? '' }}<br>
                Date of Birth: {{ $sectionA['child_3_dob'] ?? '' }}
            </td>
            <td>
                <strong>Child 4</strong><br>
                Name: {{ $sectionA['child_4_name'] ?? '' }}<br>
                Date of Birth: {{ $sectionA['child_4_dob'] ?? '' }}
            </td>
        </tr>
    </table>

    <p><strong>A3) Contact Details Whilst on Leave:</strong> P.O. Box {{ $sectionA['po_box'] ?? '' }} Phone {{ $sectionA['phone'] ?? '' }} Email {{ $sectionA['email'] ?? '' }}</p>
    <p>Signature: <span class="line">{{ $sectionA['signature_name'] ?? '' }}</span> Date: <span class="line">{{ optional($leave->submitted_at)->format('d/m/Y') }}</span></p>

    <div class="section-title">SECTION B: LEAVE REVIEW (to be completed by Human Resources Officer)</div>
    <table class="grid">
        <tr>
            <td>Dates of last leave: {{ $sectionB['last_leave_start'] ?? '' }} to {{ $sectionB['last_leave_end'] ?? '' }}</td>
            <td>Number of days taken: {{ $sectionB['days_taken'] ?? '' }}</td>
        </tr>
        <tr>
            <td>Leave outstanding in previous leave period: {{ $sectionB['previous_outstanding_days'] ?? '' }}</td>
            <td>Leave outstanding from current leave period: {{ $sectionB['current_outstanding_days'] ?? '' }}</td>
        </tr>
        <tr>
            <td>Paid / Not Paid transport allowance: {{ ucfirst(str_replace('_', ' ', $sectionB['transport_allowance_status'] ?? '')) }} Paid TZS {{ $sectionB['transport_paid_amount'] ?? '' }}</td>
            <td>Debt TZS {{ $sectionB['transport_debt_amount'] ?? '' }}</td>
        </tr>
    </table>
    <p>Signature: <span class="line">{{ $sectionB['signature_name'] ?? '' }}</span> Date: <span class="line">{{ $sectionB['reviewed_at'] ?? '' }}</span></p>

    <div class="section-title">SECTION C: RECOMMENDATION FOR LEAVE (to be completed by Respective Head of Department/Unit)</div>
    <p>I recommend / Do not recommend the above leave because:</p>
    <p>{{ $sectionC['reason'] ?? '' }}</p>
    <p>Name: <span class="line">{{ $leave->supervisor?->name }}</span> Signature: <span class="line">{{ $sectionC['signature_name'] ?? '' }}</span></p>
    <p>Designation: <span class="line">{{ $sectionC['designation'] ?? '' }}</span> Date: <span class="line">{{ $sectionC['reviewed_at'] ?? '' }}</span></p>

    <div class="section-title">SECTION D: APPROVAL DECISION (To be completed by authorizing officer)</div>
    <p>I approve / deny the above leave request with / without transport allowance.</p>
    <p>Decision: <strong>{{ ucfirst($sectionD['decision'] ?? '') }}</strong></p>
    <p>Remarks: {{ $sectionD['remarks'] ?? '' }}</p>
    <p>Applicant deserve / not deserve to be paid transport allowance for the year: {{ ucfirst(str_replace('_', ' ', $sectionD['transport_allowance_deserved'] ?? '')) }}</p>
    <p>Name: <span class="line">{{ $leave->hrReviewer?->name }}</span> Signature: <span class="line">{{ $sectionD['signature_name'] ?? '' }}</span></p>
    <p>Designation: <span class="line">{{ $sectionD['designation'] ?? '' }}</span> Date: <span class="line">{{ $sectionD['reviewed_at'] ?? '' }}</span></p>

    <div class="page-break"></div>
    <h2>DESCRIPTION ON THE TYPES OF LEAVE</h2>
    <p>The applicant will select a number corresponded to the type of leave she/he applied for, and the selected number will be filled in the box available in A2.</p>
    <table class="leave-types">
        @foreach ($types as $number => $type)
            <tr>
                <td style="width: 35px;">{{ $number }}.</td>
                <td>{{ $type['name'] }}</td>
                <td style="width: 20px;">-</td>
                <td>({{ $type['standing_order'] }})</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
