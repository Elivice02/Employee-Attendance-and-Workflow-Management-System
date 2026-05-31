<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #111;
        }

        .header {
            text-align: right;
            margin-bottom: 24px;
        }

        .reference {
            margin-bottom: 20px;
        }

        .recipient {
            margin-bottom: 20px;
        }

        .subject {
            text-align: center;
            font-weight: bold;
            margin: 22px 0;
        }

        .paragraph {
            text-align: justify;
            margin-bottom: 14px;
        }

        .signature {
            margin-top: 42px;
        }

        .signature-name {
            font-style: italic;
            margin-top: 18px;
            margin-bottom: 0;
        }

        .decision-mark {
            position: fixed;
            top: 40%;
            left: 15%;
            right: 15%;
            text-align: center;
            font-size: 48pt;
            color: #d9d9d9;
            transform: rotate(-25deg);
            z-index: -1;
        }
    </style>
</head>
<body>
    @if ($letterType === 'final' && in_array($attendance->late_review_status, ['approved', 'rejected'], true))
        <div class="decision-mark">{{ strtoupper($attendance->late_review_status) }}</div>
    @endif

    <div class="header">
        <div>{{ strtoupper($companyName) }}</div>
        <div>{{ $companyAddress }}</div>
        <div>{{ $attendance->late_submitted_at?->format('jS F, Y') ?? now()->format('jS F, Y') }}</div>
    </div>

    <div class="reference">
        REF NO: {{ $attendance->late_letter_reference }}
    </div>

    <div class="recipient">
        <div>The Human Resource Manager,</div>
        <div>{{ $companyName }},</div>
        <div>{{ $companyAddress }}.</div>
    </div>

    <div>Dear Sir/Madam,</div>

    <div class="subject">
        REF: EXPLANATION FOR LATE REPORTING TO WORK
    </div>

    <p class="paragraph">
        {{ $attendance->late_opening_paragraph ?? 'I hereby write this letter to explain my late arrival to work on ' . $attendance->attendance_date->format('jS F Y') . '.' }}
    </p>

    <p class="paragraph">
        {{ $attendance->late_explanation }}
    </p>

    <p class="paragraph">
        {{ $attendance->late_closing_paragraph ?? 'I sincerely apologize for the inconvenience caused and assure management that I will take appropriate measures to avoid similar incidents in future.' }}
    </p>

    <p>Thank you for your understanding.</p>

    <div class="signature">
        <p>Yours faithfully,</p>
        <p class="signature-name">{{ $attendance->late_signature_name ?? $attendance->user->name }}</p>
        <p>Full Name: {{ $attendance->user->name }}</p>
        <p>Employee ID: EMP{{ str_pad((string) $attendance->user->id, 3, '0', STR_PAD_LEFT) }}</p>
        <p>Department/Role: {{ $attendance->user->department?->name ?? ucfirst($attendance->user->role) }}</p>
    </div>
</body>
</html>
