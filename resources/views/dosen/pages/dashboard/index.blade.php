@extends('layouts.app')
@section('title', $title)

@section('content')
    <div class="container-fluid">
        <div class="col-12 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-3 col-md-3 col-sm-6">
                    <div class="card bg-secondary mb-3">
                        <div class="card-body text-white">
                            <div class="d-flex align-items-center gap-3">
                                <img class="rounded-circle img-profile border"
                                    style="object-fit: cover; height: 3em; width: 3em" alt="Avatar"
                                    src="{{ URL::asset('assets/img/default.jpg') }}">
                                <div class="info-feedback">
                                    <div class="card-title fw-bold m-0" style="font-size: 20pt;">
                                        {{ $totalFeedback }}
                                    </div>
                                    <span>Total Feedback</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body ">
                            <div class="d-flex align-items-center gap-3">
                                <div class="card-title fw-bold m-0" style="font-size: 16pt;">
                                    {{ $skorTertinggi['rata_rata'] ?? 0 }} / 5
                                </div>
                                <div class="info-feedback">
                                    <div class="card-title fw-bold m-0" style="font-size: 14pt;">
                                        Skor Terbaik
                                    </div>
                                    <span>{{ $skorTertinggi['kriteria'] ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body ">
                            <div class="d-flex align-items-center gap-3">
                                <div class="card-title fw-bold m-0" style="font-size: 16pt;">
                                    {{ $skorTerendah['rata_rata'] ?? 0 }} / 5
                                </div>
                                <div class="info-feedback">
                                    <div class="card-title fw-bold m-0" style="font-size: 14pt;">
                                        Skor Terendah
                                    </div>
                                    <span>{{ $skorTerendah['kriteria'] ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body ">
                            <div class="d-flex align-items-center gap-3">
                                <img class="rounded-circle img-profile border"
                                    style="object-fit: cover; height: 3em; width: 3em" alt="Avatar"
                                    src="{{ URL::asset('assets/img/default.jpg') }}">
                                <div class="info-feedback">
                                    <div class="card-title fw-bold m-0" style="font-size: 20pt;">
                                        {{ $totalCatatan }}
                                    </div>
                                    <span>Total Catatan Tambahan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-9 col-md-9 col-sm-6">
                    <style>
                        .carousel-inner {
                            padding: 20px;
                        }

                        canvas {
                            max-height: 300px;
                        }
                    </style>

                    <h5 class="fw-bold">Periode Semester : {{ $semester->tahun_ajaran }} {{ $semester->semester }}</h5>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div id="chartCarousel" class="carousel slide" data-bs-interval="false">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <h5>Grafik Tingkat Kepuasan Dosen Tiap Matkul (%)</h5>
                                        <canvas id="chart1" height="100"></canvas>
                                    </div>
                                    <div class="carousel-item">
                                        <h5>Grafik Tingkat Kepuasan Dosen Per Bulan (%)</h5>
                                        <canvas id="chart2" height="100"></canvas>
                                    </div>
                                </div>

                                {{-- Pindahkan tombol ke luar carousel-inner --}}
                            </div>

                            {{-- Tombol Navigasi di luar carousel --}}
                            <div class="d-flex justify-content-center mt-3">
                                <button class="btn btn-primary me-2" type="button" data-bs-target="#chartCarousel"
                                    data-bs-slide="prev">
                                    ‹ Prev
                                </button>
                                <button class="btn btn-primary" type="button" data-bs-target="#chartCarousel"
                                    data-bs-slide="next">
                                    Next ›
                                </button>
                            </div>
                        </div>
                    </div>
                    <h5 class="fw-bold">Note : </h5>
                    @forelse ($catatan as $item)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-grip-vertical"></i>
                                    <div class="komentar">
                                        <div class="card-title fw-semibold">Catatan dari mahasiswa</div>
                                        <p class="m-0" style="font-size: 10pt">{{ $item->komentar }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <div class="komentar ">
                                        <div class="card-title fw-semibold">Tidak Ada Catatan Dari Mahasiswa
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const chart1Data = @json($chart1Data);
        const labels = chart1Data.map(item => item.nama);
        const nilaiAkhir = chart1Data.map(item => item.total_nilai_akhir);
        const percentage = chart1Data.map(item => item.percentage.toFixed(2));

        const chart1 = new Chart(document.getElementById('chart1').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Persentase (%)',
                    data: percentage,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const chart2Data = @json($chart2Data);
        const monthLabels = @json($monthLabels);

        // Step 1: Group chart2Data by dosen_id
        const groupedByDosen = {};
        chart2Data.forEach(item => {
            if (!groupedByDosen[item.dosen_id]) {
                groupedByDosen[item.dosen_id] = {
                    nama: item.nama,
                    data: {}
                };
            }
            groupedByDosen[item.dosen_id].data[item.bulan] = item.percentage;
        });

        // Step 2: Build datasets
        const chart2Datasets = Object.values(groupedByDosen).map(dosen => {
            const nilaiPerBulan = monthLabels.map(month => {
                return dosen.data[month] ?? 0;
            });

            const r = Math.floor(Math.random() * 255);
            const g = Math.floor(Math.random() * 255);
            const b = Math.floor(Math.random() * 255);

            return {
                label: dosen.nama,
                data: nilaiPerBulan,
                borderColor: `rgba(${r}, ${g}, ${b}, 1)`,
                backgroundColor: `rgba(${r}, ${g}, ${b}, 0)`,
                borderWidth: 2,
                tension: 0.4,
                fill: true
            };
        });

        // Step 3: Create Chart.js chart
        const chart2 = new Chart(document.getElementById('chart2').getContext('2d'), {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: chart2Datasets
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    </script>
@endpush
