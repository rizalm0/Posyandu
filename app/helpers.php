<?php

use Carbon\Carbon;
use Carbon\CarbonInterface;

if (! function_exists('umur_balita')) {
    function umur_balita(CarbonInterface|string $dateOfBirth, CarbonInterface|string|null $at = null): string
    {
        $birth = $dateOfBirth instanceof CarbonInterface ? $dateOfBirth->copy() : Carbon::parse($dateOfBirth);
        $reference = $at instanceof CarbonInterface ? $at->copy() : Carbon::parse($at ?? today());

        if ($reference->lt($birth)) {
            return '0 hari';
        }

        $diff = $birth->diff($reference);
        $totalMonths = ($diff->y * 12) + $diff->m;

        if ($diff->y > 0) {
            return $diff->m > 0
                ? "{$diff->y} tahun {$diff->m} bulan"
                : "{$diff->y} tahun";
        }

        if ($totalMonths > 0) {
            return "{$totalMonths} bulan";
        }

        return max(0, $diff->d).' hari';
    }
}

if (! function_exists('tanggal_id')) {
    function tanggal_id(CarbonInterface|string|null $date, bool $withDay = true): string
    {
        if ($date === null) {
            return '-';
        }

        $carbon = $date instanceof CarbonInterface ? $date->copy() : Carbon::parse($date);
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $formatted = $withDay ? $carbon->day.' ' : '';
        $formatted .= $months[(int) $carbon->month].' '.$carbon->year;

        return $formatted;
    }
}

if (! function_exists('tanggal_singkat_id')) {
    function tanggal_singkat_id(CarbonInterface|string|null $date): string
    {
        if ($date === null) {
            return '-';
        }

        $carbon = $date instanceof CarbonInterface ? $date->copy() : Carbon::parse($date);
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        return $carbon->day.' '.$months[(int) $carbon->month].' '.$carbon->year;
    }
}

if (! function_exists('waktu_relatif_id')) {
    function waktu_relatif_id(CarbonInterface|string|null $date): string
    {
        if ($date === null) {
            return '-';
        }

        $carbon = $date instanceof CarbonInterface ? $date->copy() : Carbon::parse($date);
        $days = (int) $carbon->startOfDay()->diffInDays(today(), false);

        return match (true) {
            $days === 0 => 'Hari ini',
            $days === 1 => 'Kemarin',
            $days > 1 && $days < 30 => "{$days} hari lalu",
            default => tanggal_singkat_id($carbon),
        };
    }
}

if (! function_exists('bulan_id')) {
    function bulan_id(int $month): string
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ][$month] ?? '-';
    }
}
