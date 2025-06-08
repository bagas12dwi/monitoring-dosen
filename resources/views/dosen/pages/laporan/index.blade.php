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
                        <div class="d-sm-flex justify-content-sm-end">
                            @include('components.btn-pdf', [
                                'route' => 'dosen.laporan',
                            ])
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-2">
                    @include('components.filter-semester')
                    <div class="card-title m-0">Jumlah Responden : <span id="jumlah">{{ $jumlah }}</span> Mahasiswa
                    </div>
                </div>
                <div class="table-responsive table" role="grid" aria-describedby="dataTable_info">
                    <table class="table" id="dataTable">
                        <thead>
                            <tr>
                                <th style="width: 5%"></th> <!-- for expand button -->
                                <th style="width: 5%">No.</th>
                                <th>Matkul</th>
                                <th>Jumlah Responden</th>
                                <th>Skor Penilaian</th>
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
            const table = $('#dataTable').DataTable({
                serverSide: true,
                processing: true,
                ajax: function(data, callback, settings) {
                    const semesterId = $('#semester').val();
                    const url = "{{ route('dosen.laporan.data') }}" + '?semester=' + semesterId;

                    $.ajax({
                        url: url,
                        data: data, // this includes pagination, sorting, etc.
                        success: callback
                    });
                },
                columns: [{
                        data: null,
                        className: 'dt-control text-center',
                        orderable: false,
                        searchable: false,
                        defaultContent: ''
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'matkul',
                        name: 'matkul'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah'
                    },
                    {
                        data: 'score',
                        name: 'score'
                    }
                ],
                responsive: true,
                autoWidth: false
            });

            // Reload table on semester change
            $('#semester').on('change', function() {
                table.ajax.reload();
                getJmlResponden($(this).val());
            });
            // Toggle child rows
            $('#dataTable tbody').on('click', 'td.dt-control', function() {
                const tr = $(this).closest('tr');
                const row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).html('');
                } else {
                    const detailData = row.data().detail;
                    const html = generateInnerTable(detailData);
                    row.child(html).show();
                    tr.addClass('shown');
                    $(this).html('');
                }
            });

            function generateInnerTable(details) {
                let html = `<table class="table table-sm table-bordered mt-2">
                <thead>
                    <tr>
                        <th>KRITERIA</th>
                        <th>SANGAT SETUJU</th>
                        <th>SETUJU</th>
                        <th>NETRAL</th>
                        <th>TIDAK SETUJU</th>
                        <th>SANGAT TIDAK SETUJU</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>`;

                details.forEach(item => {
                    html += `<tr>
                    <td>${item.kriteria}</td>
                    <td>${item.sangat_setuju}</td>
                    <td>${item.setuju}</td>
                    <td>${item.netral}</td>
                    <td>${item.tidak_setuju}</td>
                    <td>${item.sangat_tidak_setuju}</td>
                    <td>${item.total}</td>
                </tr>`;
                });

                html += '</tbody></table>';
                return html;
            }
        });

        function getJmlResponden(semesterId) {
            $.ajax({
                url: "{{ url('global/jumlah') }}/" + semesterId, // Correct URL
                type: 'GET',
                success: function(response) {
                    $('#jumlah').text(response);
                    // You can handle the response here
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan saat mengambil data.', 'error');
                },
                complete: function() {}
            });
        }
    </script>
@endpush
