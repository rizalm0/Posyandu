<?php

namespace Database\Seeders;

use App\Models\Balita;
use App\Models\Examination;
use App\Models\Immunization;
use App\Models\Posyandu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $password = Hash::make('password');

        $posyandu = Posyandu::create([
            'name' => 'Posyandu Gegersuni',
            'code' => 'PSD-GGS-01',
            'address' => 'Lapang Voli RT 07 RW 03, Gegerkalong',
            'phone' => '021-5550147',
            'alert_no_exam_days' => 60,
        ]);

        $kader = User::create([
            'posyandu_id' => $posyandu->id,
            'name' => 'Sari Wulandari',
            'email' => 'kader@posyandu.test',
            'phone' => '081234567890',
            'role' => 'kader',
            'email_verified_at' => now(),
            'password' => $password,
            'is_active' => true,
        ]);

        $kaderDua = User::create([
            'posyandu_id' => $posyandu->id,
            'name' => 'Dewi Lestari',
            'email' => 'dewi@posyandu.test',
            'phone' => '081234567891',
            'role' => 'kader',
            'email_verified_at' => now(),
            'password' => $password,
            'is_active' => true,
        ]);

        $rina = User::create([
            'posyandu_id' => $posyandu->id,
            'name' => 'Rina Kartika',
            'email' => 'rina@example.test',
            'phone' => '081298765432',
            'role' => 'parent',
            'email_verified_at' => now(),
            'password' => $password,
            'is_active' => true,
        ]);

        $budi = User::create([
            'posyandu_id' => $posyandu->id,
            'name' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'phone' => '081298765433',
            'role' => 'parent',
            'email_verified_at' => now(),
            'password' => $password,
            'is_active' => true,
        ]);

        $siti = User::create([
            'posyandu_id' => $posyandu->id,
            'name' => 'Siti Aminah',
            'email' => 'siti@example.test',
            'phone' => '081298765434',
            'role' => 'parent',
            'email_verified_at' => now(),
            'password' => $password,
            'is_active' => true,
        ]);

        $fitri = User::create([
            'posyandu_id' => $posyandu->id,
            'name' => 'Fitri Handayani',
            'email' => 'fitri@example.test',
            'phone' => '081298765435',
            'role' => 'parent',
            'email_verified_at' => now(),
            'password' => $password,
            'is_active' => true,
        ]);

        $dimas = User::create([
            'posyandu_id' => $posyandu->id,
            'name' => 'Dimas Prakoso',
            'email' => 'dimas@example.test',
            'phone' => '081298765436',
            'role' => 'parent',
            'email_verified_at' => now(),
            'password' => $password,
            'is_active' => true,
        ]);

        $children = [
            [
                'full_name' => 'Aisyah Putri Santoso',
                'date_of_birth' => '2023-11-18',
                'gender' => 'P',
                'parent_name' => 'Rina Kartika',
                'phone' => '081298765432',
                'address' => 'Jl. Melati III No. 8, RT 04/RW 07',
                'registered_at' => '2024-01-12',
                'registration_number' => 'PSD-2024-0001',
                'exams' => [
                    ['2025-12-10', 11.2, 85.9, 47.5, null],
                    ['2026-02-11', 11.6, 87.3, 47.8, null],
                    ['2026-04-09', 11.9, 88.6, 48.0, null],
                    ['2026-06-14', 12.2, 90.0, 48.2, 'Nafsu makan baik.'],
                    ['2026-08-12', 12.5, 91.2, 48.4, null],
                    ['2026-10-10', 12.8, 92.0, 48.5, 'Aktif dan tidak ada keluhan.'],
                ],
            ],
            [
                'full_name' => 'Bima Aditya Santoso',
                'date_of_birth' => '2025-04-09',
                'gender' => 'L',
                'parent_name' => 'Rina Kartika',
                'phone' => '081298765432',
                'address' => 'Jl. Melati III No. 8, RT 04/RW 07',
                'registered_at' => '2025-05-05',
                'registration_number' => 'PSD-2025-0002',
                'exams' => [
                    ['2026-02-12', 8.4, 73.8, 45.0, null],
                    ['2026-04-15', 8.8, 75.6, 45.5, null],
                    ['2026-06-10', 9.2, 77.3, 45.8, 'Sudah mulai MPASI.'],
                    ['2026-08-12', 9.5, 78.8, 46.0, null],
                    ['2026-10-10', 9.8, 80.0, 46.2, null],
                ],
            ],
            [
                'full_name' => 'Citra Kirana',
                'date_of_birth' => '2024-08-02',
                'gender' => 'P',
                'parent_name' => 'Siti Aminah',
                'phone' => '081298765434',
                'address' => 'Jl. Kenanga No. 21, RT 02/RW 07',
                'registered_at' => '2024-09-03',
                'registration_number' => 'PSD-2024-0003',
                'exams' => [
                    ['2026-02-19', 9.8, 81.0, 46.5, null],
                    ['2026-04-20', 10.2, 82.9, 46.8, null],
                    ['2026-06-18', 10.6, 84.5, 47.0, 'Ibu menyampaikan anak sempat batuk pilek.'],
                ],
            ],
            [
                'full_name' => 'Muhammad Rizky Pratama',
                'date_of_birth' => '2022-06-14',
                'gender' => 'L',
                'parent_name' => 'Andi Pratama',
                'phone' => '081298765437',
                'address' => 'Jl. Anggrek No. 5, RT 01/RW 07',
                'registered_at' => '2022-07-04',
                'registration_number' => 'PSD-2022-0004',
                'exams' => [
                    ['2026-03-18', 15.2, 100.5, 48.7, null],
                    ['2026-05-20', 15.6, 101.8, 48.9, null],
                    ['2026-07-21', 16.0, 103.0, 49.0, null],
                    ['2026-09-22', 16.4, null, 49.0, 'Tinggi badan belum diukur saat kunjungan.'],
                ],
            ],
            [
                'full_name' => 'Nadia Safitri',
                'date_of_birth' => '2025-12-20',
                'gender' => 'P',
                'parent_name' => 'Fitri Handayani',
                'phone' => '081298765435',
                'address' => 'Jl. Dahlia No. 17, RT 05/RW 07',
                'registered_at' => '2026-01-10',
                'registration_number' => 'PSD-2026-0005',
                'exams' => [
                    ['2026-06-15', 7.5, 67.4, 43.7, null],
                    ['2026-07-20', 7.9, 68.7, 44.0, null],
                    ['2026-08-17', 8.2, 69.8, 44.3, null],
                    ['2026-09-14', 8.6, 70.9, 44.6, 'ASI eksklusif, mulai MPASI.'],
                    ['2026-10-12', 8.9, 72.0, 44.8, null],
                ],
            ],
            [
                'full_name' => 'Arka Pratama',
                'date_of_birth' => '2023-03-05',
                'gender' => 'L',
                'parent_name' => 'Dimas Prakoso',
                'phone' => '081298765436',
                'address' => 'Jl. Mawar No. 3, RT 03/RW 07',
                'registered_at' => '2023-04-02',
                'registration_number' => 'PSD-2023-0006',
                'exams' => [
                    ['2026-01-07', 13.4, 94.2, 48.8, null],
                    ['2026-03-04', 13.7, 95.0, 49.0, null],
                    ['2026-05-06', 14.0, 96.0, 49.2, null],
                    ['2026-07-08', 14.3, 97.0, 49.4, null],
                    ['2026-09-05', 14.6, 98.0, 49.5, 'Imunisasi lanjutan dicatat pada kunjungan ini.'],
                ],
            ],
            [
                'full_name' => 'Zahra Aulia',
                'date_of_birth' => '2024-12-11',
                'gender' => 'P',
                'parent_name' => 'Lina Marlina',
                'phone' => '081298765438',
                'address' => 'Jl. Cempaka No. 9, RT 06/RW 07',
                'registered_at' => '2025-01-15',
                'registration_number' => 'PSD-2025-0007',
                'exams' => [
                    ['2026-04-25', 9.5, 78.1, 46.1, null],
                    ['2026-06-24', 9.8, 79.6, 46.4, null],
                    ['2026-08-22', 10.1, 80.8, 46.6, null],
                    ['2026-10-18', 10.4, 82.0, 46.8, null],
                ],
            ],
            [
                'full_name' => 'Fajar Nugroho',
                'date_of_birth' => '2022-12-01',
                'gender' => 'L',
                'parent_name' => 'Wulan Nugroho',
                'phone' => '081298765439',
                'address' => 'Jl. Flamboyan No. 14, RT 02/RW 07',
                'registered_at' => '2023-01-09',
                'registration_number' => 'PSD-2023-0008',
                'exams' => [
                    ['2026-03-26', 14.6, 96.9, null, null],
                    ['2026-05-28', 14.9, 98.2, null, null],
                    ['2026-07-30', 15.2, 99.5, null, null],
                ],
            ],
            [
                'full_name' => 'Keisha Anindya',
                'date_of_birth' => '2025-07-28',
                'gender' => 'P',
                'parent_name' => 'Maya Anindya',
                'phone' => '081298765440',
                'address' => 'Jl. Teratai No. 11, RT 04/RW 07',
                'registered_at' => '2025-09-02',
                'registration_number' => 'PSD-2025-0009',
                'exams' => [
                    ['2026-04-18', 8.3, 73.2, 44.9, null],
                    ['2026-06-17', 8.7, 74.9, 45.2, null],
                    ['2026-08-15', 9.0, 76.3, 45.4, null],
                    ['2026-10-12', 9.3, 77.5, 45.6, null],
                ],
            ],
            [
                'full_name' => 'Raka Saputra',
                'date_of_birth' => '2023-08-17',
                'gender' => 'L',
                'parent_name' => 'Yudi Saputra',
                'phone' => '081298765441',
                'address' => 'Jl. Palem No. 6, RT 01/RW 07',
                'registered_at' => '2023-09-11',
                'registration_number' => 'PSD-2023-0010',
                'exams' => [
                    ['2026-04-11', 13.0, 92.0, 48.3, null],
                    ['2026-06-13', 13.3, 93.1, 48.5, null],
                    ['2026-08-12', 13.6, 94.2, 48.6, null],
                    ['2026-10-10', 13.9, 95.2, 48.8, null],
                ],
            ],
        ];

        foreach ($children as $index => $data) {
            $balita = Balita::create([
                'posyandu_id' => $posyandu->id,
                'registration_number' => $data['registration_number'],
                'full_name' => $data['full_name'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'parent_name' => $data['parent_name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'registered_at' => $data['registered_at'],
                'is_active' => true,
            ]);

            foreach ($data['exams'] as $visitIndex => $exam) {
                Examination::create([
                    'posyandu_id' => $posyandu->id,
                    'balita_id' => $balita->id,
                    'examined_at' => $exam[0],
                    'weight_kg' => $exam[1],
                    'height_cm' => $exam[2],
                    'head_circumference_cm' => $exam[3],
                    'notes' => $exam[4],
                    'recorded_by' => ($index + $visitIndex) % 3 === 0 ? $kaderDua->id : $kader->id,
                ]);
            }

            $schedule = [
                ['Hepatitis B-0', 0],
                ['BCG', 30],
                ['Polio 1', 60],
                ['DPT-HB-Hib 1', 60],
                ['Polio 2', 90],
                ['DPT-HB-Hib 2', 90],
                ['Polio 3', 120],
                ['DPT-HB-Hib 3', 120],
                ['Polio 4', 150],
                ['Campak/MR', 270],
            ];

            $birth = Carbon::parse($data['date_of_birth']);

            foreach ($schedule as $immunizationIndex => [$name, $offset]) {
                $date = $birth->copy()->addDays($offset);

                if ($date->isFuture()) {
                    continue;
                }

                $needsReview = $index === 2 && $immunizationIndex >= 4;

                Immunization::create([
                    'balita_id' => $balita->id,
                    'name' => $name,
                    'administered_at' => $date,
                    'status' => $needsReview ? 'needs_review' : 'recorded',
                    'notes' => $needsReview ? 'Tanggal perlu dikonfirmasi dengan buku KIA.' : null,
                    'recorded_by' => $kader->id,
                ]);
            }
        }

        $aisyah = Balita::where('registration_number', 'PSD-2024-0001')->firstOrFail();
        $bima = Balita::where('registration_number', 'PSD-2025-0002')->firstOrFail();
        $citra = Balita::where('registration_number', 'PSD-2024-0003')->firstOrFail();
        $nadia = Balita::where('registration_number', 'PSD-2026-0005')->firstOrFail();
        $arka = Balita::where('registration_number', 'PSD-2023-0006')->firstOrFail();

        $aisyah->guardians()->attach($rina->id, [
            'relationship' => 'mother',
            'is_primary' => true,
            'verified_at' => now()->subMonths(20),
        ]);
        $aisyah->guardians()->attach($budi->id, [
            'relationship' => 'father',
            'is_primary' => false,
            'verified_at' => now()->subMonths(19),
        ]);

        $bima->guardians()->attach($rina->id, [
            'relationship' => 'mother',
            'is_primary' => true,
            'verified_at' => now()->subMonths(17),
        ]);
        $bima->guardians()->attach($budi->id, [
            'relationship' => 'father',
            'is_primary' => false,
            'verified_at' => now()->subMonths(17),
        ]);

        $citra->guardians()->attach($siti->id, [
            'relationship' => 'mother',
            'is_primary' => true,
            'verified_at' => null,
        ]);

        $nadia->guardians()->attach($fitri->id, [
            'relationship' => 'mother',
            'is_primary' => true,
            'verified_at' => now()->subMonths(9),
        ]);

        $arka->guardians()->attach($dimas->id, [
            'relationship' => 'father',
            'is_primary' => true,
            'verified_at' => now()->subMonths(30),
        ]);
    }
}
