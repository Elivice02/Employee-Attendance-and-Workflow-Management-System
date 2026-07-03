<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class LateAttendanceLetterGenerator
{
    public function generateDraft(Attendance $attendance): string
    {
        return $this->generate($attendance, 'draft');
    }

    public function generateFinal(Attendance $attendance): string
    {
        return $this->generate($attendance, 'final');
    }

    private function generate(Attendance $attendance, string $type): string
    {
        $attendance->loadMissing('user.department');
        $settings = AttendanceSetting::current();

        $path = sprintf(
            'attendance-letters/%s/%s.pdf',
            $type,
            str_replace('/', '-', $attendance->late_letter_reference)
        );

        $pdf = Pdf::loadView('attendance.pdf.late-letter', [
            'attendance' => $attendance,
            'companyName' => $settings->company_name,
            'companyAddress' => $settings->company_address,
            'letterType' => $type,
        ])->setPaper('a4');

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
