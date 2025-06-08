<?php

namespace App\Http\Controllers\Dosen;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Semester;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    protected $title;
    protected $semester;
    public function __construct()
    {
        $this->title = 'Feedback';
        $this->semester = Semester::where('aktif', true)->first();
    }

    public function index(Request $request)
    {
        $selectedSemesterId = $request->get('semester') ?? $this->semester->id;

        $semesters = Semester::all();

        $penilaians = Penilaian::with(['pertanyaan.kriteria', 'perkuliahan'])
            ->whereHas('perkuliahan', function ($query) use ($selectedSemesterId) {
                $query->where('dosen_id', auth()->user()->id)
                    ->where('semester_id', $selectedSemesterId);
            })->get();

        $finalData = Helper::getDataFeedbackDosen($penilaians);


        $finalScores = $finalData->groupBy(function ($item) {
            // Gabungkan perkuliahan_id dan kriteria_nama untuk pengelompokan unik
            return $item['perkuliahan_id'] . '-' . $item['kriteria_nama'];
        })->map(function ($group) {
            $first = $group->first();

            return [
                'dosen_id' => $first['dosen_id'],
                'perkuliahan_id' => $first['perkuliahan_id'],
                'perkuliahan_nama' => $first['perkuliahan_nama'],
                'kriteria_nama' => $first['kriteria_nama'],
                'bobot' => collect($group)->avg('bobot'),
                'komentar' => $first['komentar']
            ];
        })->values();


        $feedback = $finalScores->groupBy('perkuliahan_id')->map(function ($group) {
            $first = $group->first();
            return [
                'perkuliahan_id' => $first['perkuliahan_id'],
                'perkuliahan_nama' => $first['perkuliahan_nama'],
                'total_bobot' => round(collect($group)->sum('bobot') * 100, 2),
                'kriteria' => $group->map(function ($item) {
                    return [
                        'kriteria_nama' => $item['kriteria_nama'],
                        'bobot' => round($item['bobot'] * 100, 2), // dibulatkan 3 desimal jika perlu
                    ];
                })->values(),
                'chart' => $group->map(function ($item) {
                    return [
                        'labels' => $item['kriteria_nama'],
                        'bobot' => round($item['bobot'] * 100, 2), // <-- bulatkan 2 desimal di sini
                    ];
                })->values(),
                'komentar' => $group->map(function ($item) {
                    return [
                        'komentar' => $item['komentar']
                    ];
                })->unique()->values()
            ];
        })->values();



        return view('dosen.pages.feedback.index', [
            'title' => $this->title,
            'semesters' => $semesters,
            'feedback' => $feedback,
            'semesterAktif' => $selectedSemesterId
        ]);
    }
}
