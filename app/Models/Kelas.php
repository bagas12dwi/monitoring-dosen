<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function perkuliahan()
    {
        return $this->hasMany(Perkuliahan::class, 'kelas_id');
    }
}
