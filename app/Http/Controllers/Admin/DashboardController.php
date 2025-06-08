<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Perkuliahan;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $title;
    protected $semester;
    public function __construct()
    {
        $this->title = 'Dashboard';
        $this->semester = Semester::where('aktif', true)->first();
    }

    public function index()
    {
        $semester = $this->semester;

        $penilaians = Penilaian::with(['pertanyaan.kriteria', 'perkuliahan'])->whereHas('perkuliahan', function ($query) use ($semester) {
            $query->where('semester_id', $semester->id);
        })->get();

        $finalScores = Helper::getDataDashboard($penilaians);

        $averageByDosen = $finalScores->groupBy('dosen_id')->map(function ($group, $dosenId) {
            return [
                'nama' => User::where('id', $dosenId)->first()->nama,
                'dosen_id' => $dosenId,
                'percentage' => $group->avg('percentage'),
                'total_perkuliahan' => $group->count(),
                'total_nilai_akhir' => $group->sum('nilai_akhir')
            ];
        })->values();


        $chart1 = $finalScores->groupBy('dosen_id')->map(function ($group, $dosenId) {
            return [
                'nama' => User::where('id', $dosenId)->first()->nama,
                'dosen_id' => $dosenId,
                'percentage' => $group->avg('percentage'),
                'total_perkuliahan' => $group->count(),
                'total_nilai_akhir' => $group->avg('nilai_akhir'),
            ];
        })->sortByDesc('percentage')->values()->take(10);

        $start = \Carbon\Carbon::parse($semester->mulai)->startOfMonth();
        $end = \Carbon\Carbon::parse($semester->selesai)->endOfMonth();

        $monthLabels = collect();
        while ($start <= $end) {
            $monthLabels->push($start->translatedFormat('F'));
            $start->addMonth();
        }

        $chart2 = $finalScores
            ->groupBy(function ($item) {
                return $item['dosen_id'] . '-' . $item['bulan'];
            })
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'dosen_id' => $first['dosen_id'],
                    'nama' => User::find($first['dosen_id'])?->nama ?? 'N/A',
                    'bulan' => $first['bulan'],
                    'percentage' => $group->avg('percentage'),
                    'average_nilai_akhir' => $group->avg('nilai_akhir'),
                    'jumlah_perkuliahan' => $group->count(),
                ];
            })
            ->sortByDesc('percentage')
            ->values();


        // foreach ($finalScores as $item) {
        //     dump("Perkuliahan: {$item['perkuliahan_nama']} (ID: {$item['perkuliahan_id']}), Nilai Akhir: {$item['nilai_akhir']}, Percentage: {$item['percentage']}");
        // }

        // $skorTertinggiItem = $finalScores->sortByDesc('percentage')->first();
        // $skorTerendahItem = $finalScores->sortBy('percentage')->first();

        $skorTertinggiItem = $averageByDosen->sortByDesc('percentage')->first();
        $skorTerendahItem = $averageByDosen->sortBy('percentage')->first();

        // 2. Get the related perkuliahan + dosen
        // $perkuliahanTertinggi = Perkuliahan::with('dosen')->find($skorTertinggiItem['perkuliahan_id']);
        // $perkuliahanTertinggi = Perkuliahan::with('dosen')->find($skorTertinggiItem['perkuliahan_id']);
        $perkuliahanTertinggi = null;
        $perkuliahanTerendah = null;

        if ($skorTertinggiItem) {
            $perkuliahanTertinggi = User::find($skorTertinggiItem['dosen_id']);
        }

        if ($skorTerendahItem) {
            $perkuliahanTerendah = User::find($skorTerendahItem['dosen_id']);
        }

        // 3. Build final result
        // $skorTertinggi = [
        //     'perkuliahan_id' => $perkuliahanTertinggi->id,
        //     'perkuliahan_nama' => $skorTertinggiItem['perkuliahan_nama'],
        //     'dosen_nama' => $perkuliahanTertinggi->dosen->nama ?? 'N/A',
        //     'nilai_akhir' => $skorTertinggiItem['percentage'],
        // ];

        // $skorTerendah = [
        //     'perkuliahan_id' => $perkuliahanTerendah->id,
        //     'perkuliahan_nama' => $skorTerendahItem['perkuliahan_nama'],
        //     'dosen_nama' => $perkuliahanTerendah->dosen->nama ?? 'N/A',
        //     'nilai_akhir' => $skorTerendahItem['percentage'],
        // ];
        // $skorTertinggi = [
        //     'dosen_nama' => $perkuliahanTertinggi->dosen->nama ?? 'N/A',
        //     'nilai_akhir' => $skorTertinggiItem['percentage'],
        // ];

        // $skorTerendah = [
        //     'dosen_nama' => $perkuliahanTerendah->dosen->nama ?? 'N/A',
        //     'nilai_akhir' => $skorTerendahItem['percentage'],
        // ];
        $skorTertinggi = [
            'dosen_nama' => $perkuliahanTertinggi->nama ?? 'N/A',
            'nilai_akhir' => $skorTertinggiItem['percentage'] ?? 0,
        ];

        $skorTerendah = [
            'dosen_nama' => $perkuliahanTerendah->nama ?? 'N/A',
            'nilai_akhir' => $skorTerendahItem['percentage'] ?? 0,
        ];

        // dump("Skor Tertinggi:");
        // dump("Perkuliahan: {$skorTertinggi['perkuliahan_nama']} (ID: {$skorTertinggi['perkuliahan_id']})");
        // dump("Dosen: {$skorTertinggi['dosen_nama']}");
        // dump("Nilai Akhir: {$skorTertinggi['nilai_akhir']}");

        // dump("Skor Terendah:");
        // dump("Perkuliahan: {$skorTerendah['perkuliahan_nama']} (ID: {$skorTerendah['perkuliahan_id']})");
        // dump("Dosen: {$skorTerendah['dosen_nama']}");
        // dump("Nilai Akhir: {$skorTerendah['nilai_akhir']}");

        $feedback = Penilaian::count();

        $TotalCatatanTambahan = Penilaian::whereNotNull('komentar')->count();

        $catatan = Penilaian::whereNotNull('komentar')->get();

        $catatan = $catatan->unique('komentar');

        return view('admin.pages.dashboard.index', [
            'title' => $this->title,
            'skorTertinggi' => $skorTertinggi,
            'skorTerendah' => $skorTerendah,
            'totalFeedback' => $feedback / 10,
            'totalCatatan' => $TotalCatatanTambahan / 10,
            'catatan' => $catatan,
            'chart1Data' => $chart1,
            'monthLabels' => $monthLabels,
            'chart2Data' => $chart2,
            'semester' => $semester
        ]);
    }
}
