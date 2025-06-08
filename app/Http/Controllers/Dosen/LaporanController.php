<?php

namespace App\Http\Controllers\Dosen;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\Semester;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    protected $semester;
    public function __construct()
    {
        $this->title = 'Laporan';
        $this->semester = Semester::where('aktif', true)->first();
    }

    public function index()
    {

        $semesterId = $this->semester;
        $penilaian = Penilaian::with(['perkuliahan.matkul', 'pertanyaan.kriteria'])
            ->whereHas('perkuliahan', function ($query) use ($semesterId) {
                $query->where('dosen_id', auth()->user()->id)
                    ->where('semester_id', $semesterId->id);
            })->get();
        $jumlah = Helper::getJumlahResponden($penilaian);
        $semester = Semester::all();

        return view('dosen.pages.laporan.index', [
            'title' => $this->title,
            'jumlah' => $jumlah ?? 0,
            'semesters' => $semester
        ]);
    }

    public function data(Request $request)
    {
        $semesterId = $request->semester;

        $penilaian = Penilaian::with(['perkuliahan.matkul', 'pertanyaan.kriteria'])
            ->whereHas('perkuliahan', function ($query) use ($semesterId) {
                $query->where('dosen_id', auth()->user()->id)
                    ->where('semester_id', $semesterId);
            })->get();

        // Get score per bulan
        $score = Helper::getDataDashboard($penilaian);

        $grouped = Helper::getDataLaporan($penilaian);
        $labels = Helper::getLabelsLaporan();
        $scoreByPerkuliahan = collect($score)->groupBy('perkuliahan_id')->map(function ($items) {
            $avgPercentage = $items->avg('percentage');
            return round($avgPercentage, 2) . '%';
        });


        // Format output
        $result = [];
        $idCounter = 1;

        foreach ($grouped as $perkuliahan_id => $data) {
            $rows = [];

            foreach ($data['detail'] as $kriteria => $mahasiswaGroup) {
                $row = ['kriteria' => $kriteria];
                $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

                foreach ($mahasiswaGroup as $nilai) {
                    if (isset($counts[$nilai])) {
                        $counts[$nilai]++;
                    }
                }

                $totalMahasiswa = array_sum($counts) ?: 1;

                foreach ([5, 4, 3, 2, 1] as $scoreKey) {
                    $count = $counts[$scoreKey];
                    $percentage = round(($count / $totalMahasiswa) * 100);
                    $label = $labels[$scoreKey];
                    $row[$label] = "$count ({$percentage}%)";
                }

                $row['total'] = $totalMahasiswa;
                $rows[] = $row;
            }

            $result[] = [
                'id' => $idCounter++,
                'perkuliahan_id' => $perkuliahan_id,
                'matkul' => $data['matkul'],
                'jumlah' => $totalMahasiswa . ' Mahasiswa',
                'score' => $scoreByPerkuliahan[$perkuliahan_id] ?? '0%',
                'detail' => $rows
            ];
        }

        return datatables()
            ->of($result)
            ->addIndexColumn()
            ->make(true);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Penilaian $penilaian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penilaian $penilaian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penilaian $penilaian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penilaian $penilaian)
    {
        //
    }

    public function cetak()
    {
        $semesterId = request()->query('semester');
        $semester = Semester::findOrFail($semesterId);

        $penilaian = Penilaian::with(['perkuliahan.matkul', 'pertanyaan.kriteria'])
            ->whereHas('perkuliahan', function ($query) use ($semesterId) {
                $query->where('dosen_id', auth()->user()->id)
                    ->where('semester_id', $semesterId);
            })->get();

        // Get score per bulan
        $score = Helper::getDataDashboard($penilaian);

        $grouped = Helper::getDataLaporan($penilaian);
        $labels = Helper::getLabelsLaporan();
        $scoreByPerkuliahan = collect($score)->groupBy('perkuliahan_id')->map(function ($items) {
            $avgPercentage = $items->avg('percentage');
            return round($avgPercentage, 2) . '%';
        });


        // Format output
        $result = [];
        $idCounter = 1;

        foreach ($grouped as $perkuliahan_id => $data) {
            $rows = [];

            foreach ($data['detail'] as $kriteria => $mahasiswaGroup) {
                $row = ['kriteria' => $kriteria];
                $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

                foreach ($mahasiswaGroup as $nilai) {
                    if (isset($counts[$nilai])) {
                        $counts[$nilai]++;
                    }
                }

                $totalMahasiswa = array_sum($counts) ?: 1;

                foreach ([5, 4, 3, 2, 1] as $scoreKey) {
                    $count = $counts[$scoreKey];
                    $percentage = round(($count / $totalMahasiswa) * 100);
                    $label = $labels[$scoreKey];
                    $row[$label] = "$count ({$percentage}%)";
                }

                $row['total'] = $totalMahasiswa;
                $rows[] = $row;
            }

            $result[] = [
                'id' => $idCounter++,
                'perkuliahan_id' => $perkuliahan_id,
                'matkul' => $data['matkul'],
                'jumlah' => $totalMahasiswa . ' Mahasiswa',
                'score' => $scoreByPerkuliahan[$perkuliahan_id] ?? '0%',
                'detail' => $rows
            ];
        }

        $catatan = Penilaian::whereHas('perkuliahan', function ($query) {
            $query->where('dosen_id', auth()->user()->id);
        })->whereNotNull('komentar')->get()->unique('komentar');

        $pdf = Pdf::loadView('dosen.pages.laporan.pdf', [
            'title' => 'LAPORAN PERSENTASE KEPUASAN MAHASISWA TERHADAP KINERJA DOSEN',
            'data' => $result,
            'semester' => $semester,
            'jumlah' => Helper::getJumlahResponden($penilaian) . ' Mahasiswa',
            'catatan' => $catatan
        ]);

        return $pdf->download('laporan.pdf');
    }
}
