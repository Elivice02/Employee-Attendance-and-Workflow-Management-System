<?php

namespace App\Services;

use App\Models\ReferenceCounter;
use Illuminate\Support\Facades\DB;

class LeaveReferenceNumberGenerator
{
    public function generate(string $departmentCode, ?int $year = null): string
    {
        $type = 'LV';
        $year ??= (int) now()->format('Y');
        $departmentCode = strtoupper(trim($departmentCode ?: 'GEN'));

        return DB::transaction(function () use ($type, $year, $departmentCode) {
            $counter = ReferenceCounter::query()->firstOrCreate(
                [
                    'type' => $type,
                    'year' => $year,
                    'department_code' => $departmentCode,
                ],
                ['last_number' => 0]
            );

            $counter = ReferenceCounter::query()
                ->whereKey($counter->id)
                ->lockForUpdate()
                ->firstOrFail();

            $counter->increment('last_number');
            $counter->refresh();

            return sprintf('%s/%d/%s/%03d', $type, $year, $departmentCode, $counter->last_number);
        });
    }
}
