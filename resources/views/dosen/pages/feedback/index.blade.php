@extends('layouts.app')
@section('title', $title)

@section('content')
    <div class="container-fluid">
        <h3 class="text-dark mb-4">{{ $title }}</h3>
        <div class="card shadow">
            <div class="card-header py-3">
                <div class="row text-sm-start text-center">
                    <div class="col-sm-5 col-12 mb-md-0 mb-3">
                        <p class="text-primary fw-bold m-0 mt-2">Daftar {{ $title }}
                        </p>
                    </div>
                    <div class="col-sm-7 col-12 mb-md-0 mb-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    {{-- Dropdown semester di dalam form --}}
                    <form id="semesterForm" method="GET" action="{{ route('dosen.feedback.index') }}">
                        <div class="d-flex gap-2 align-items-center">
                            <label for="semester" class="form-label m-0">Semester</label>
                            <select class="form-select form-select" name="semester" id="semester">
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}"
                                        {{ request('semester', $semesterAktif) == $semester->id ? 'selected' : '' }}>
                                        {{ $semester->tahun_ajaran }} {{ $semester->semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    {{-- jQuery untuk auto submit --}}
                    <script>
                        $(document).ready(function() {
                            $('#semester').on('change', function() {
                                $('#semesterForm').submit();
                            });
                        });
                    </script>

                </div>
                <div class="accordion" id="accordionExample">
                    @forelse ($feedback as $index => $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }} fw-bold"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-controls="collapse{{ $index }}">
                                    {{ $item['perkuliahan_nama'] }}
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}"
                                class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <h6 class="fw-bold">Detail Kriteria</h6>
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Kriteria</th>
                                                        <th>Bobot</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($item['kriteria'] as $k)
                                                        <tr>
                                                            <td>{{ $k['kriteria_nama'] }}</td>
                                                            <td>{{ $k['bobot'] }} %</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td class="fw-bold">Jumlah</td>
                                                        <td class="fw-bold">
                                                            {{ number_format($item['total_bobot'], 2) }}
                                                            %
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <h6 class="fw-bold">Grafik</h6>
                                            <canvas id="chart{{ $index }}" height="100"></canvas>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mt-3">Catatan</h6>
                                    @forelse ($item['komentar'] as $komentar)
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title">
                                                    {{ $komentar['komentar'] ?? 'Tidak Ada Catatan Tambahan' }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title">Tidak Ada Catatan Tambahan</div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title text-center m-0">Tidak Ada Data</div>
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
        $(document).ready(function() {

            $('#semester').on('change', function() {
                $('#semesterForm').submit();
            });
            @foreach ($feedback as $index => $item)
                var ctx = document.getElementById("chart{{ $index }}").getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($item['chart']->pluck('labels')) !!},
                        datasets: [{
                            label: 'Bobot (%)',
                            data: {!! json_encode($item['chart']->pluck('bobot')) !!},
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Persentase (%)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' %';
                                    }
                                }
                            }
                        }
                    }
                });
            @endforeach
        });
    </script>
@endpush
