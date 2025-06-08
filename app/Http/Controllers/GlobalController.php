<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Perkuliahan;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    public function getMatkul()
    {
        $mahasiswaId = auth()->user()->id;

        $perkuliahan = Perkuliahan::with(['dosen', 'matkul', 'semester', 'kelas'])
            ->whereRelation('semester', 'aktif', 1)
            ->whereDoesntHave('kelasMahasiswa', function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId);
            })
            ->get();

        return $this->generateDatatable($perkuliahan, 'admin.perkuliahan');
    }

    public function getJumlahResponden($semesterId)
    {
        $penilaian = Penilaian::with(['perkuliahan', 'pertanyaan.kriteria'])
            ->whereHas('perkuliahan', function ($query) use ($semesterId) {
                $query->where('dosen_id', auth()->user()->id)->where('semester_id', $semesterId);
            })->get();

        // $perkuliahan = $penilaian->unique('perkuliahan_id');

        // Group penilaian by perkuliahan_id
        $mahasiswaCountPerPerkuliahan = $penilaian
            ->groupBy('perkuliahan_id')
            ->map(function ($group) {
                return $group->pluck('mahasiswa_id')->unique()->count();
            });

        $jumlah = 0;
        foreach ($mahasiswaCountPerPerkuliahan as $value) {
            $jumlah += $value;
        }

        return $jumlah;
    }

    public function getJumlahRespondenAdmin($semesterId)
    {
        $penilaian = Penilaian::with(['perkuliahan', 'pertanyaan.kriteria'])
            ->whereHas('perkuliahan', function ($query) use ($semesterId) {
                $query->where('semester_id', $semesterId);
            })->get();
        $mahasiswaCount = $penilaian
            ->groupBy(function ($item) {
                return $item->dosen_id . '-' . $item->perkuliahan_id;
            })
            ->map(function ($group) {
                return $group->pluck('mahasiswa_id')->unique()->count();
            });

        $jumlah = 0;
        foreach ($mahasiswaCount as $value) {
            $jumlah += $value;
        }

        return $jumlah;
    }
}
