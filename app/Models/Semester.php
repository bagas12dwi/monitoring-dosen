<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function perkuliahan()
    {
        return $this->hasMany(Perkuliahan::class, 'semester_id');
    }

    public function kelasMahasiswa()
    {
        return $this->hasMany(KelasMahasiswa::class, 'semester_id');
    }
}
