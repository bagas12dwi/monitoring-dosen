@extends('layouts.app')
@section('title', $title)

@section('content')
    <div class="container-fluid">
        <div class="col-12 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-3 col-md-3 col-sm-6">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="fw-bold">FEEDBACK DOSEN</p>
                        <p class="fw-bold">D4 MI</p>
                    </div>
                    @forelse ($dosens as $dosen)
                        <div class="card shadow rounded mb-3">
                            <div class="card-body">
                                <div class="card-title m-0">
                                    {{ $dosen->nama }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title m-0">
                                    Belum Ada Data Dosen
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="col-9 col-md-9 col-sm-6">
                    @forelse ($perkuliahans as $perkuliahan)
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="data-dosen d-flex flex-column">
                                    <div class="nama fw-bold">
                                        {{ $perkuliahan->perkuliahan->dosen->nama }}
                                    </div>
                                    <div class="data-matkul">
                                        {{ $perkuliahan->perkuliahan->day }},
                                        {{ \Carbon\Carbon::parse($perkuliahan->perkuliahan->jam_mulai)->format('h:i A') }}
                                        -
                                        {{ \Carbon\Carbon::parse($perkuliahan->perkuliahan->jam_selesai)->format('h:i A') }}
                                        | {{ $perkuliahan->perkuliahan->ruangan }}
                                    </div>
                                </div>
                                <div class="data-matkul">
                                    {{ $perkuliahan->perkuliahan->matkul->nama }}
                                </div>
                            </div>
                            <div class="card-body">
                                @foreach ($months as $month)
                                    @php
                                        $submitted = $penilaians->first(function ($penilaian) use (
                                            $perkuliahan,
                                            $month,
                                        ) {
                                            return $penilaian->perkuliahan_id === $perkuliahan->perkuliahan->id &&
                                                \Carbon\Carbon::parse($penilaian->created_at)->format('m') ===
                                                    $month['month_number'] &&
                                                \Carbon\Carbon::parse($penilaian->created_at)->format('Y') ==
                                                    $month['year'];
                                        });

                                        $monthEndDate = \Carbon\Carbon::create(
                                            $month['year'],
                                            $month['month_number'],
                                            1,
                                        )->endOfMonth();
                                        $today = now();
                                    @endphp

                                    <div class="card-title d-flex gap-2 align-items-center mb-3">
                                        @if ($submitted)
                                            <div class="card border bg-success text-white py-1 px-3">
                                                Submitted
                                            </div>
                                        @elseif ($today->greaterThan($monthEndDate))
                                            <div class="card border bg-danger text-white py-1 px-3">
                                                Late
                                            </div>
                                        @else
                                            <div class="card border bg-warning text-dark py-1 px-3">
                                                Not Submitted Yet
                                            </div>
                                        @endif
                                        Feedback {{ $month['label'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title text-center">Tidak Ada Perkuliahan</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
