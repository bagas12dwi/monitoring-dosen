<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            User::create([
                'nama' => ucwords(strtolower($row['nama'])), // Convert to lowercase first, then capitalize
                'nim' => $row['nim'],
                'angkatan' => $row['angkatan'] ?? null,
                'role' => $row['role'],
                'password' => bcrypt('12345678'),
            ]);
        }
    }
}
