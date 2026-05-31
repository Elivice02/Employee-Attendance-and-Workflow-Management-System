<?php

namespace App\Services;

use App\Models\Attendance;
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

        $path = sprintf(
            'attendance-letters/%s/%s.pdf',
            $type,
            str_replace('/', '-', $attendance->late_letter_reference)
        );

        $pdf = Pdf::loadView('attendance.pdf.late-letter', [
            'attendance' => $attendance,
            'companyName' => config('attendance.company_name', 'ABC Company Ltd'),
            'companyAddress' => config('attendance.company_address', 'P.O. Box 123, Morogoro'),
            'letterType' => $type,
        ])->setPaper('a4');

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
