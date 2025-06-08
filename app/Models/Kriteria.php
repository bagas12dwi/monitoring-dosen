<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'bobot' => 'double',
    ];

    public function cekBobot($newBobot)
    {
        $totalBobot = Kriteria::sum('bobot');

        if (($totalBobot + $newBobot) > 1) {
            return false;
        }

        return true;
    }

    public function pertanyaan()
    {
        return $this->hasMany(Pertanyaan::class, 'kriteria_id');
    }
}
