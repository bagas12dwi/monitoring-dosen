<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\Semester;
use App\Models\User;
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
                $query->where('semester_id', $selectedSemesterId);
            })->get();

        $finalData = Helper::getDataFeedbackDosen($penilaians);

        $finalScores = $finalData->groupBy(function ($item) {
            // Gabungkan perkuliahan_id dan kriteria_nama untuk pengelompokan unik
            return $item['dosen_id'] . '-' . $item['kriteria_nama'];
        })->map(function ($group) {
            $first = $group->first();

            return [
                'dosen_id' => $first['dosen_id'],
                'dosen_nama' => User::where('id', $first['dosen_id'])->first()->nama,
                'kriteria_nama' => $first['kriteria_nama'],
                'bobot' => collect($group)->avg('bobot'),
                'komentar' => $first['komentar']
            ];
        })->values();

        $feedback = $finalScores->groupBy('dosen_id')->map(function ($group) {
            $first = $group->first();
            return [
                'dosen_id' => $first['dosen_id'],
                'dosen_nama' => $first['dosen_nama'],
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


        return view('admin.pages.feedback.index', [
            'title' => $this->title,
            'semesters' => $semesters,
            'feedback' => $feedback,
            'semesterAktif' => $selectedSemesterId
        ]);
    }
}
