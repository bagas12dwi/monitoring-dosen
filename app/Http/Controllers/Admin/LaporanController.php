<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\Semester;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
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
        $penilaian = Penilaian::with(['perkuliahan', 'pertanyaan.kriteria'])
            ->whereHas('perkuliahan', function ($query) use ($semesterId) {
                $query->where('semester_id', $semesterId->id);
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

        $semester = Semester::all();

        return view('admin.pages.laporan.index', [
            'title' => $this->title,
            'jumlah' => $jumlah ?? 0,
            'semesters' => $semester
        ]);
    }

    public function data(Request $request)
    {
        $semesterId = $request->semester;

        $penilaian = Penilaian::with(['perkuliahan.matkul', 'perkuliahan.dosen', 'pertanyaan.kriteria'])
            ->whereHas('perkuliahan', function ($query) use ($semesterId) {
                $query->where('semester_id', $semesterId);
            })->get();

        // Get score per bulan
        $score = Helper::getDataDashboard($penilaian);

        // Kelompokkan penilaian untuk membuat tabel distribusi nilai
        $grouped = [];

        foreach ($penilaian as $item) {
            $dosen_id = $item->perkuliahan->dosen->id;
            $dosen_nama = $item->perkuliahan->dosen->nama ?? 'Tidak Diketahui';
            $kriteriaNama = $item->pertanyaan->kriteria->nama ?? 'Tidak Diketahui';
            $mahasiswaId = $item->mahasiswa_id;
            $nilai = $item->nilai;
            $concatMahasiswaId = $mahasiswaId . '-' . $item->perkuliahan->id;

            // Group only by dosen_id
            $grouped[$dosen_id]['dosen'] = $dosen_nama;
            $grouped[$dosen_id]['detail'][$kriteriaNama][$concatMahasiswaId] = $nilai;

            // Optional: Track perkuliahan if needed later
            $grouped[$dosen_id]['perkuliahan_ids'][] = $item->perkuliahan->id;
        }

        // dd($grouped);

        // Ambil label
        $labels = Helper::getLabelsLaporan();

        // Score by dosen (aggregate all perkuliahan_ids for the dosen)
        $scoreByDosen = [];

        foreach ($grouped as $dosen_id => $data) {
            $relatedPerkuliahanIds = array_unique($data['perkuliahan_ids'] ?? []);

            $dosenScores = collect($score)->filter(function ($item) use ($dosen_id, $relatedPerkuliahanIds) {
                return $item['dosen_id'] == $dosen_id && in_array($item['perkuliahan_id'], $relatedPerkuliahanIds);
            });

            $avgPercentage = $dosenScores->avg('percentage') ?? 0;
            $scoreByDosen[$dosen_id] = round($avgPercentage, 2) . '%';
        }

        // Final result format
        $result = [];
        $idCounter = 1;

        foreach ($grouped as $dosen_id => $data) {
            $rows = [];
            $uniqueMahasiswaIds = [];

            foreach ($data['detail'] as $kriteria => $mahasiswaGroup) {
                $row = ['kriteria' => $kriteria];
                $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

                foreach ($mahasiswaGroup as $mahasiswaId => $nilai) {
                    $uniqueMahasiswaIds[$mahasiswaId] = true; // only keep unique mahasiswa per dosen
                    if (isset($counts[$nilai])) {
                        $counts[$nilai]++;
                    }
                }

                $total = array_sum($counts) ?: 1;

                foreach ([5, 4, 3, 2, 1] as $scoreKey) {
                    $count = $counts[$scoreKey];
                    $percentage = round(($count / $total) * 100);
                    $label = $labels[$scoreKey];
                    $row[$label] = "$count ({$percentage}%)";
                }

                $row['total'] = $total;
                $rows[] = $row;
            }

            $totalMahasiswa = count($uniqueMahasiswaIds); // count unique mahasiswa who rated this dosen

            $result[] = [
                'id' => $idCounter++,
                'dosen_id' => $dosen_id,
                'dosen' => $data['dosen'],
                'jumlah' => $totalMahasiswa . ' Mahasiswa',
                'score' => $scoreByDosen[$dosen_id] ?? '0%',
                'detail' => $rows
            ];
        }


        return datatables()
            ->of($result)
            ->addIndexColumn()
            ->make(true);
    }

    public function cetak()
    {
        $semesterId = request()->query('semester');
        $semester = Semester::findOrFail($semesterId);
        $penilaian = Penilaian::with(['perkuliahan.matkul', 'perkuliahan.dosen', 'pertanyaan.kriteria'])
            ->whereHas('perkuliahan', function ($query) use ($semesterId) {
                $query->where('semester_id', $semesterId);
            })->get();

        // Get score per bulan
        $score = Helper::getDataDashboard($penilaian);

        // Kelompokkan penilaian untuk membuat tabel distribusi nilai
        $grouped = [];

        foreach ($penilaian as $item) {
            $dosen_id = $item->perkuliahan->dosen->id;
            $dosen_nama = $item->perkuliahan->dosen->nama ?? 'Tidak Diketahui';
            $kriteriaNama = $item->pertanyaan->kriteria->nama ?? 'Tidak Diketahui';
            $mahasiswaId = $item->mahasiswa_id;
            $nilai = $item->nilai;
            $concatMahasiswaId = $mahasiswaId . '-' . $item->perkuliahan->id;

            // Group only by dosen_id
            $grouped[$dosen_id]['dosen'] = $dosen_nama;
            $grouped[$dosen_id]['detail'][$kriteriaNama][$concatMahasiswaId] = $nilai;

            // Optional: Track perkuliahan if needed later
            $grouped[$dosen_id]['perkuliahan_ids'][] = $item->perkuliahan->id;
        }

        // dd($grouped);

        // Ambil label
        $labels = Helper::getLabelsLaporan();

        // Score by dosen (aggregate all perkuliahan_ids for the dosen)
        $scoreByDosen = [];

        foreach ($grouped as $dosen_id => $data) {
            $relatedPerkuliahanIds = array_unique($data['perkuliahan_ids'] ?? []);

            $dosenScores = collect($score)->filter(function ($item) use ($dosen_id, $relatedPerkuliahanIds) {
                return $item['dosen_id'] == $dosen_id && in_array($item['perkuliahan_id'], $relatedPerkuliahanIds);
            });

            $avgPercentage = $dosenScores->avg('percentage') ?? 0;
            $scoreByDosen[$dosen_id] = round($avgPercentage, 2) . '%';
        }

        // Final result format
        $result = [];
        $idCounter = 1;

        foreach ($grouped as $dosen_id => $data) {
            $rows = [];
            $uniqueMahasiswaIds = [];

            foreach ($data['detail'] as $kriteria => $mahasiswaGroup) {
                $row = ['kriteria' => $kriteria];
                $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

                foreach ($mahasiswaGroup as $mahasiswaId => $nilai) {
                    $uniqueMahasiswaIds[$mahasiswaId] = true; // only keep unique mahasiswa per dosen
                    if (isset($counts[$nilai])) {
                        $counts[$nilai]++;
                    }
                }

                $total = array_sum($counts) ?: 1;

                foreach ([5, 4, 3, 2, 1] as $scoreKey) {
                    $count = $counts[$scoreKey];
                    $percentage = round(($count / $total) * 100);
                    $label = $labels[$scoreKey];
                    $row[$label] = "$count ({$percentage}%)";
                }

                $row['total'] = $total;
                $rows[] = $row;
            }

            $totalMahasiswa = count($uniqueMahasiswaIds); // count unique mahasiswa who rated this dosen

            $result[] = [
                'id' => $idCounter++,
                'dosen_id' => $dosen_id,
                'dosen' => $data['dosen'],
                'jumlah' => $totalMahasiswa . ' Mahasiswa',
                'score' => $scoreByDosen[$dosen_id] ?? '0%',
                'detail' => $rows
            ];
        }

        $catatan = Penilaian::whereHas('perkuliahan', function ($query) use ($semesterId) {
            $query->where('semester_id', $semesterId);
        })->whereNotNull('komentar')->get()->unique('komentar');

        $pdf = Pdf::loadView('admin.pages.laporan.pdf', [
            'title' => 'LAPORAN PERSENTASE KEPUASAN MAHASISWA TERHADAP KINERJA DOSEN',
            'data' => $result,
            'semester' => $semester,
            'jumlah' => Helper::getJumlahResponden($penilaian) . ' Mahasiswa',
            'catatan' => $catatan
        ]);

        return $pdf->download('laporan.pdf');
    }
}
