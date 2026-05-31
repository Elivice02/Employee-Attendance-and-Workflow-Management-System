<?php

namespace App\Services;

use App\Models\Leave;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeavePdfGenerator
{
    public function generate(Leave $leave): string
    {
        $leave->loadMissing(['employee.department', 'supervisor', 'hrReviewer']);

        $pdf = Pdf::loadView('leave.pdf.application', [
            'leave' => $leave,
            'types' => config('leave.types', []),
        ])->setPaper('a4');

        $filename = Str::slug($leave->ref_no ?: 'leave-'.$leave->id).'.pdf';
        $path = 'leave-forms/'.$filename;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
