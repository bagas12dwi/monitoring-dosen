<?php

namespace App\Imports;

use App\Models\Penilaian;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PenilaianImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $collection)
    {
        $nilaiMap = [
            'SS' => 5,
            'S'  => 4,
            'N'  => 3,
            'TS' => 2,
            'STS' => 1,
        ];

        foreach ($collection as $row) {
            $user = User::where('nim', $row['nim'])->first();

            if (!$user) {
                continue;
            }

            $perkuliahanId = $row['perkuliahan_id'];

            // Looping dari pertanyaan_1 sampai pertanyaan_10
            for ($i = 1; $i <= 10; $i++) {
                $jawaban = $row['pertanyaan_' . $i];

                // Skip jika jawaban tidak sesuai mapping
                if (!isset($nilaiMap[$jawaban])) {
                    continue;
                }

                Penilaian::create([
                    'mahasiswa_id'    => $user->id,
                    'perkuliahan_id'  => $perkuliahanId,
                    'pertanyaan_id'   => $i, // asumsi pertanyaan_id = nomor pertanyaan
                    'nilai'           => $nilaiMap[$jawaban],
                ]);
            }
        }
    }
}
