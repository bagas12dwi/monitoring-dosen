<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\KelasMahasiswa;
use App\Models\Penilaian;
use App\Models\Perkuliahan;
use App\Models\Semester;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $title;
    public function __construct()
    {
        $this->title = 'Dashboard Mahasiswa';
        $this->route = 'mahasiswa.dashboard';
    }
    public function index()
    {
        $mahasiswaId = auth()->user()->id;
        $semesterAktif = Semester::where('aktif', true)->first();
        $start = Carbon::parse($semesterAktif->mulai)->startOfMonth();
        $end = Carbon::parse($semesterAktif->selesai)->startOfMonth();

        $penilaians = Penilaian::where('mahasiswa_id', $mahasiswaId)->get();


        $months = collect();
        while ($start <= $end) {
            $months->push([
                'month' => $start->translatedFormat('F'), // Full month name in Indonesian
                'month_number' => $start->format('m'),
                'year' => $start->year,
                'label' => $start->translatedFormat('F Y'), // Example: "Februari 2025"
            ]);
            $start->addMonth();
        }

        // Ambil dosen unik yang mengajar mahasiswa ini, dan belum dinilai bulan ini
        $allDosens = User::with(['perkuliahan' => function ($q) use ($mahasiswaId) {
            $q->whereHas('kelasMahasiswa', function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId);
            });
        }])->whereHas('perkuliahan.kelasMahasiswa', function ($q) use ($mahasiswaId) {
            $q->where('mahasiswa_id', $mahasiswaId);
        })->get();

        // Filter dosen unik
        $dosen = $allDosens->unique('id')->values();

        $perkuliahan = KelasMahasiswa::with(['perkuliahan' => function ($q) {
            $q->whereHas('semester', function ($query) {
                $query->where('aktif', true);
            });
        }, 'perkuliahan.dosen'])->where('mahasiswa_id', $mahasiswaId)->get();

        return view(
            'mahasiswa.pages.dashboard.index',
            [
                'title' => $this->title,
                'dosens' => $dosen,
                'perkuliahans' => $perkuliahan,
                'months' => $months,
                'penilaians' => $penilaians
            ]
        );
    }
}
