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
                    <div class="col-sm-7 col-12 mb-md-0 mb-2 ">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive table" role="grid" aria-describedby="dataTable_info">
                    <table class="my-0 table" id="dataTable">
                        <thead>
                            <tr>
                                <th data-col="COUNT" width="5%">No.</th>
                                <th data-col="AKTIVITAS">AKTIVITAS</th>
                                <th data-col="CREATED_AT" data-type="DATETIME">WAKTU</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            generateDatatable('dataTable', "{{ route('mahasiswa.log.data') }}");
        });
    </script>
@endpush
