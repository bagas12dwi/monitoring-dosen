<?php

namespace App\Imports;

use App\Models\KelasMahasiswa;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasMahasiswaImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $collection)
    {
        for ($i = 0; $i < 2; $i++) {
            foreach ($collection as $row) {
                $user = User::where('nim', $row['nim'])->first();

                if (!$user) {
                    continue;
                }
                if ($i == 0) {
                    $perkuliahan_id = 1;
                } else {
                    $perkuliahan_id = 2;
                }

                KelasMahasiswa::create([
                    'mahasiswa_id' => $user->id,
                    'perkuliahan_id' => $perkuliahan_id,
                    'semester_id' => 2
                ]);
            }
        }
    }
}
