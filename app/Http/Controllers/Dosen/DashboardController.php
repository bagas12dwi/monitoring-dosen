<?php

namespace App\Http\Controllers\Dosen;

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

        $penilaians = Penilaian::with(['pertanyaan.kriteria', 'perkuliahan'])
            ->whereHas('perkuliahan', function ($query) use ($semester) {
                $query->where('dosen_id', auth()->user()->id)->where('semester_id', $semester->id);
            })->get();

        $finalScore = Helper::getDataDashboard($penilaians);

        $chart1 = $finalScore->groupBy('perkuliahan_id')->map(function ($group, $perkuliahanId) {
            $perkuliahan = Perkuliahan::with('matkul')->find($perkuliahanId);

            return [
                'nama' => $perkuliahan?->matkul?->nama ?? 'N/A',
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

        $chart2 = $finalScore
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

        // Group by pertanyaan_id and calculate sum and average
        $summary = $penilaians->groupBy('pertanyaan_id')->map(function ($group) {
            $kriteria = Kriteria::where('id', $group->first()->pertanyaan->kriteria_id)->first();
            return [
                'total_nilai' => $group->sum('nilai'),
                'rata_rata' => round($group->avg('nilai'), 2), // 2 decimal places
                'pertanyaan' => $group->first()->pertanyaan->pertanyaan ?? null,
                'kriteria' => $kriteria->nama
            ];
        });
        $maxEntry = $summary->sortByDesc('rata_rata')->first();
        $minEntry = $summary->sortBy('rata_rata')->first();




        $feedback = Penilaian::whereHas('perkuliahan', function ($query) {
            $query->where('dosen_id', auth()->user()->id);
        })->count();

        $TotalCatatanTambahan = Penilaian::whereHas('perkuliahan', function ($query) {
            $query->where('dosen_id', auth()->user()->id);
        })->whereNotNull('komentar')->count();

        $catatan = Penilaian::whereHas('perkuliahan', function ($query) {
            $query->where('dosen_id', auth()->user()->id);
        })->whereNotNull('komentar')->get();

        $catatan = $catatan->unique('komentar');

        return view('dosen.pages.dashboard.index', [
            'title' => $this->title,
            'skorTertinggi' => $maxEntry,
            'skorTerendah' => $minEntry,
            'totalFeedback' => $feedback / 10,
            'totalCatatan' => $TotalCatatanTambahan / 10,
            'catatan' => $catatan,
            'chart1Data' => $chart1,
            'chart2Data' => $chart2,
            'monthLabels' => $monthLabels,
            'semester' => $semester
        ]);
    }
}
