<?php

namespace App\Helpers;

use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Perkuliahan;
use App\Models\Semester;
use App\Models\User;
use Carbon\Carbon;

class Helper
{
    public static function greet($name)
    {
        return "Hello, " . ucfirst($name) . "!";
    }

    public static function getDataFeedback()
    {
        $mahasiswaId = auth()->user()->id;
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Ambil perkuliahan yang sudah pernah dinilai oleh mahasiswa ini bulan ini
        $perkuliahanYangSudahDinilai = Penilaian::where('mahasiswa_id', $mahasiswaId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->pluck('perkuliahan_id')
            ->toArray();

        // Ambil dosen unik yang mengajar mahasiswa ini, dan belum dinilai bulan ini
        $allDosens = User::with(['perkuliahan' => function ($q) use ($mahasiswaId, $perkuliahanYangSudahDinilai) {
            $q->whereHas('kelasMahasiswa', function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId);
            })->whereNotIn('id', $perkuliahanYangSudahDinilai);
        }])->whereHas('perkuliahan.kelasMahasiswa', function ($q) use ($mahasiswaId) {
            $q->where('mahasiswa_id', $mahasiswaId);
        })->whereHas('perkuliahan', function ($q) use ($perkuliahanYangSudahDinilai) {
            $q->whereNotIn('id', $perkuliahanYangSudahDinilai);
        })->get();

        // Filter dosen unik
        $uniqueDosens = $allDosens->unique('id')->values();

        return $uniqueDosens;
    }

    public static function getCountProgressMahasiswa()
    {
        $mahasiswaId = auth()->user()->id;
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Ambil perkuliahan yang sudah pernah dinilai oleh mahasiswa ini bulan ini
        $perkuliahanYangSudahDinilai = Penilaian::where('mahasiswa_id', $mahasiswaId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->pluck('perkuliahan_id')
            ->toArray();

        // Ambil semua dosen yang mengajar mahasiswa ini
        // $allDosens = User::with(['perkuliahan' => function ($q) use ($mahasiswaId) {
        //     $q->whereHas('kelasMahasiswa', function ($query) use ($mahasiswaId) {
        //         $query->where('mahasiswa_id', $mahasiswaId);
        //     });
        // }])->whereHas('perkuliahan.kelasMahasiswa', function ($q) use ($mahasiswaId) {
        //     $q->where('mahasiswa_id', $mahasiswaId);
        // })->get();

        $allDosens = Perkuliahan::with(['kelasMahasiswa' => function ($q) use ($mahasiswaId) {
            $q->where('mahasiswa_id', $mahasiswaId);
        }])
            ->whereHas('kelasMahasiswa', function ($q) use ($mahasiswaId) {
                $q->where('mahasiswa_id', $mahasiswaId);
            })
            ->get();


        $totalDosen = $allDosens->count();

        // Ambil dosen yang belum dinilai
        $notRatedDosens = User::with(['perkuliahan' => function ($q) use ($mahasiswaId, $perkuliahanYangSudahDinilai) {
            $q->whereHas('kelasMahasiswa', function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId);
            })->whereNotIn('id', $perkuliahanYangSudahDinilai);
        }])->whereHas('perkuliahan.kelasMahasiswa', function ($q) use ($mahasiswaId) {
            $q->where('mahasiswa_id', $mahasiswaId);
        })->whereHas('perkuliahan', function ($q) use ($perkuliahanYangSudahDinilai) {
            $q->whereNotIn('id', $perkuliahanYangSudahDinilai);
        })->get();

        $sisaDosen = $notRatedDosens->count();
        $sudahDinilai = $totalDosen - $sisaDosen;

        $progress = $totalDosen > 0 ? round(($sudahDinilai / $totalDosen) * 100) : 0;
        $color = 'primary';
        $progress_text =  'Uncomplete';
        if ($progress <= 25) {
            $color = 'danger';
        } elseif ($progress <= 50) {
            $color = 'warning';
        } elseif ($progress <= 75) {
            $color = 'primary';
        } else { // $progress > 75
            $color = 'success';
            $progress_text = 'Complete';
        }

        $data = [
            'color' => $color,
            'value' => $progress,
            'progress_text' => $progress_text
        ];

        return $data;
    }

    public static function getCountSatisfactionAdmin()
    {
        $semester = Semester::where('aktif', true)->first();
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

        if ($averageByDosen->count() > 1) {
            $averagePercentage = round($averageByDosen->avg('percentage'));
        } else {
            $averagePercentage = 0;
        }

        $satisfaction = $averagePercentage;

        $color = 'primary';
        if ($satisfaction <= 25) {
            $color = 'danger';
        } elseif ($satisfaction <= 50) {
            $color = 'warning';
        } elseif ($satisfaction <= 75) {
            $color = 'primary';
        } else { // $satisfaction > 75
            $color = 'success';
        }

        $data = [
            'color' => $color,
            'value' => $satisfaction,
        ];

        return $data;
    }

    public static function getCountSatisfactionDosen()
    {
        $semester = Semester::where('aktif', true)->first();
        // $penilaians = Penilaian::with(['pertanyaan.kriteria', 'perkuliahan'])->get();
        $penilaians = Penilaian::with(['pertanyaan.kriteria', 'perkuliahan'])->whereHas('perkuliahan', function ($query) use ($semester) {
            $query->where('semester_id', $semester->id);
        })->get();

        $finalScores = Helper::getDataDashboard($penilaians);
        // dd($finalScores);

        $averageByDosen = $finalScores->groupBy('dosen_id')->map(function ($group, $dosenId) {
            return [
                'nama' => User::where('id', $dosenId)->first()->nama,
                'dosen_id' => $dosenId,
                'percentage' => $group->avg('percentage'),
                'total_perkuliahan' => $group->count(),
                'total_nilai_akhir' => $group->sum('nilai_akhir')
            ];
        })->values();


        // Ambil hanya untuk dosen yang sedang login
        $dataFinal = $averageByDosen->where('dosen_id', auth()->user()->id);

        $averagePercentage = $dataFinal->first()['percentage'] ?? 0;
        // dd($averagePercentage);

        // Hitung rata-rata percentage
        if ($dataFinal->count() > 1) {
            $averagePercentage = round($dataFinal->avg('percentage'));
        } elseif ($dataFinal->count() === 1) {
            $averagePercentage = round($dataFinal->first()['percentage']);
        } else {
            $averagePercentage = 0;
        }

        $satisfaction = $averagePercentage;

        $color = 'primary';
        if ($satisfaction <= 25) {
            $color = 'danger';
        } elseif ($satisfaction <= 50) {
            $color = 'warning';
        } elseif ($satisfaction <= 75) {
            $color = 'primary';
        } else { // $satisfaction > 75
            $color = 'success';
        }

        $data = [
            'color' => $color,
            'value' => $satisfaction,
        ];

        return $data;
    }

    public static function convertIntoMonth($date, $is_string = false)
    {
        $date_convert = \Carbon\Carbon::parse($date);

        if ($is_string) {
            $date_convert = $date_convert->translatedFormat('F');
        } else {
            $date_convert  = $date_convert->format('m');
        }

        return $date_convert;
    }

    public static function getDataDashboard($penilaians)
    {

        // Group by perkuliahan_id and kriteria_id
        // $grouped = $penilaians->groupBy(function ($item) {
        //     return $item->perkuliahan_id . '-' . $item->pertanyaan->kriteria->id;
        // });

        $grouped = $penilaians->groupBy(function ($item) {
            return $item->perkuliahan_id . '-' . $item->pertanyaan->kriteria->id . '-' . Helper::convertIntoMonth($item->created_at, true);
        });


        // Map to result with perkuliahan + kriteria + rata-rata
        $averages = $grouped->map(function ($group) {
            $first = $group->first();
            return [
                'dosen_id' => $first->perkuliahan->dosen_id,
                'perkuliahan_id' => $first->perkuliahan_id,
                'bulan' => \Carbon\Carbon::parse($first->created_at)->translatedFormat('F'),
                'perkuliahan_nama' => $first->perkuliahan->matkul->nama ?? 'N/A',
                'kriteria_id' => $first->pertanyaan->kriteria->id,
                'kriteria_nama' => $first->pertanyaan->kriteria->nama ?? 'N/A',
                'rata_rata' => $group->avg('nilai'),
            ];
        })->values(); // Optional: reset keys

        // foreach ($averages as $item) {
        //     dump("Perkuliahan: {$item['perkuliahan_nama']} (ID: {$item['perkuliahan_id']}), ");
        //     dump("Kriteria: {$item['kriteria_nama']} (ID: {$item['kriteria_id']}), ");
        //     dump("Rata-rata: {$item['rata_rata']}\n");
        // }
        // die;

        // Group the averaged data by kriteria_id
        $maxOfAveragesPerKriteria = $averages->groupBy('kriteria_id')->map(function ($group) {
            $maxItem = $group->sortByDesc('rata_rata')->first();
            return [
                'kriteria_id' => $maxItem['kriteria_id'],
                'kriteria_nama' => $maxItem['kriteria_nama'],
                'nilai_rata_rata_tertinggi' => $maxItem['rata_rata'],
                'perkuliahan_id' => $maxItem['perkuliahan_id'],
                'perkuliahan_nama' => $maxItem['perkuliahan_nama'],
                'bulan' => $maxItem['bulan']
            ];
        })->values();

        // foreach ($maxOfAveragesPerKriteria as $item) {
        //     dump("Kriteria: {$item['kriteria_nama']} (ID: {$item['kriteria_id']}), ");
        //     dump("Perkuliahan Tertinggi: {$item['perkuliahan_nama']} (ID: {$item['perkuliahan_id']}), ");
        //     dump("Rata-rata Tertinggi: {$item['nilai_rata_rata_tertinggi']}\n");
        // }

        // First, make an associative map from kriteria_id => max rata-rata
        $maxMap = $maxOfAveragesPerKriteria->keyBy('kriteria_id');

        // Add normalized value to each item in $averages
        $normalized = $averages->map(function ($item) use ($maxMap) {
            $maxValue = $maxMap[$item['kriteria_id']]['nilai_rata_rata_tertinggi'] ?? 1; // avoid division by zero
            return [
                'dosen_id' => $item['dosen_id'],
                'perkuliahan_id' => $item['perkuliahan_id'],
                'perkuliahan_nama' => $item['perkuliahan_nama'],
                'bulan' => $item['bulan'],
                'kriteria_id' => $item['kriteria_id'],
                'kriteria_nama' => $item['kriteria_nama'],
                'rata_rata' => $item['rata_rata'],
                'nilai_maksimum' => $maxValue,
                'nilai_ternormalisasi' => $item['rata_rata'] / $maxValue,
            ];
        });

        // foreach ($normalized as $item) {
        //     dump("Perkuliahan: {$item['perkuliahan_nama']}, Kriteria: {$item['kriteria_nama']}");
        //     dump("Normalized: {$item['nilai_ternormalisasi']}");
        // }

        $kriteriaMap = Kriteria::pluck('bobot', 'id'); // [kriteria_id => bobot]

        // Add `bobot_kriteria` and `bobot` to each normalized row
        $finalData = $normalized->map(function ($item) use ($kriteriaMap) {
            $bobotKriteria = $kriteriaMap[$item['kriteria_id']] ?? 0;
            return [
                ...$item,
                'bobot_kriteria' => $bobotKriteria,
                'bobot' => $item['nilai_ternormalisasi'] * $bobotKriteria,
            ];
        });

        // foreach ($finalData as $item) {
        //     dump("Perkuliahan: {$item['perkuliahan_nama']}, Kriteria: {$item['kriteria_nama']}");
        //     dump("Normalized: {$item['nilai_ternormalisasi']}, Bobot Kriteria: {$item['bobot_kriteria']}, Bobot: {$item['bobot']}");
        // }

        $finalScores = $finalData->groupBy(function ($item) {
            return $item['perkuliahan_id'] . '-' . $item['bulan'];
        })->map(function ($group) {
            $first = $group->first();
            return [
                'dosen_id' => $first['dosen_id'],
                'perkuliahan_id' => $first['perkuliahan_id'],
                'perkuliahan_nama' => $first['perkuliahan_nama'],
                'bulan' => $first['bulan'],
                'nilai_akhir' => $group->sum('bobot'),
                'percentage' => $group->sum('bobot') * 100,
            ];
        })->values();

        return $finalScores;
    }

    public static function getLabelsLaporan()
    {
        $labels = [
            5 => 'sangat_setuju',
            4 => 'setuju',
            3 => 'netral',
            2 => 'tidak_setuju',
            1 => 'sangat_tidak_setuju'
        ];

        return $labels;
    }

    public static function getDataLaporan($penilaian)
    {
        // Group penilaian
        $grouped = [];

        foreach ($penilaian as $item) {
            $perkuliahan_id = $item->perkuliahan_id;
            $matkul_nama = $item->perkuliahan->matkul->nama ?? 'Tidak Diketahui';
            $kriteriaNama = $item->pertanyaan->kriteria->nama ?? 'Tidak Diketahui';
            $mahasiswaId = $item->mahasiswa_id;
            $nilai = $item->nilai;

            $grouped[$perkuliahan_id]['matkul'] = $matkul_nama;
            $grouped[$perkuliahan_id]['detail'][$kriteriaNama][$mahasiswaId] = $nilai;
        }

        return $grouped;
    }

    public static function getDataFeedbackDosen($penilaians)
    {
        // Ganti $semesterAktif menjadi $selectedSemesterId

        $grouped = $penilaians->groupBy(function ($item) {
            return $item->perkuliahan_id . '-' . $item->pertanyaan->kriteria->id . '-' . Helper::convertIntoMonth($item->created_at, true);
        });


        // Map to result with perkuliahan + kriteria + rata-rata
        $averages = $grouped->map(function ($group) {
            $first = $group->first();
            return [
                'dosen_id' => $first->perkuliahan->dosen_id,
                'perkuliahan_id' => $first->perkuliahan_id,
                'bulan' => \Carbon\Carbon::parse($first->created_at)->translatedFormat('F'),
                'komentar' => $first->komentar,
                'perkuliahan_nama' => $first->perkuliahan->matkul->nama ?? 'N/A',
                'kriteria_id' => $first->pertanyaan->kriteria->id,
                'kriteria_nama' => $first->pertanyaan->kriteria->nama ?? 'N/A',
                'rata_rata' => $group->avg('nilai'),
            ];
        })->values(); // Optional: reset keys

        // foreach ($averages as $item) {
        //     dump("Perkuliahan: {$item['perkuliahan_nama']} (ID: {$item['perkuliahan_id']}), ");
        //     dump("Kriteria: {$item['kriteria_nama']} (ID: {$item['kriteria_id']}), ");
        //     dump("Rata-rata: {$item['rata_rata']}\n");
        // }
        // die;

        // Group the averaged data by kriteria_id
        $maxOfAveragesPerKriteria = $averages->groupBy('kriteria_id')->map(function ($group) {
            $maxItem = $group->sortByDesc('rata_rata')->first();
            return [
                'kriteria_id' => $maxItem['kriteria_id'],
                'kriteria_nama' => $maxItem['kriteria_nama'],
                'nilai_rata_rata_tertinggi' => $maxItem['rata_rata'],
                'perkuliahan_id' => $maxItem['perkuliahan_id'],
                'perkuliahan_nama' => $maxItem['perkuliahan_nama'],
                'bulan' => $maxItem['bulan'],
                'komentar' => $maxItem['komentar']
            ];
        })->values();

        // foreach ($maxOfAveragesPerKriteria as $item) {
        //     dump("Kriteria: {$item['kriteria_nama']} (ID: {$item['kriteria_id']}), ");
        //     dump("Perkuliahan Tertinggi: {$item['perkuliahan_nama']} (ID: {$item['perkuliahan_id']}), ");
        //     dump("Rata-rata Tertinggi: {$item['nilai_rata_rata_tertinggi']}\n");
        // }

        // First, make an associative map from kriteria_id => max rata-rata
        $maxMap = $maxOfAveragesPerKriteria->keyBy('kriteria_id');

        // Add normalized value to each item in $averages
        $normalized = $averages->map(function ($item) use ($maxMap) {
            $maxValue = $maxMap[$item['kriteria_id']]['nilai_rata_rata_tertinggi'] ?? 1; // avoid division by zero
            return [
                'dosen_id' => $item['dosen_id'],
                'perkuliahan_id' => $item['perkuliahan_id'],
                'perkuliahan_nama' => $item['perkuliahan_nama'],
                'bulan' => $item['bulan'],
                'komentar' => $item['komentar'],
                'kriteria_id' => $item['kriteria_id'],
                'kriteria_nama' => $item['kriteria_nama'],
                'rata_rata' => $item['rata_rata'],
                'nilai_maksimum' => $maxValue,
                'nilai_ternormalisasi' => $item['rata_rata'] / $maxValue,
            ];
        });

        // foreach ($normalized as $item) {
        //     dump("Perkuliahan: {$item['perkuliahan_nama']}, Kriteria: {$item['kriteria_nama']}");
        //     dump("Normalized: {$item['nilai_ternormalisasi']}");
        // }

        $kriteriaMap = Kriteria::pluck('bobot', 'id'); // [kriteria_id => bobot]

        // Add `bobot_kriteria` and `bobot` to each normalized row
        $finalData = $normalized->map(function ($item) use ($kriteriaMap) {
            $bobotKriteria = $kriteriaMap[$item['kriteria_id']] ?? 0;
            return [
                ...$item,
                'bobot_kriteria' => $bobotKriteria,
                'bobot' => $item['nilai_ternormalisasi'] * $bobotKriteria,
            ];
        });

        // foreach ($finalData as $item) {
        //     dump("Perkuliahan: {$item['perkuliahan_nama']}, Kriteria: {$item['kriteria_nama']}");
        //     dump("Normalized: {$item['nilai_ternormalisasi']}, Bobot Kriteria: {$item['bobot_kriteria']}, Bobot: {$item['bobot']}");
        // }

        return $finalData;
    }

    public static function getJumlahResponden($penilaian)
    {


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
}
