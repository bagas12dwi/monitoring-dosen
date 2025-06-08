<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-icon-split" data-bs-toggle="modal" data-bs-target="#modalMatkul">
    <span class="icon text-white-50">
        <i class="fas fa-plus"></i>
    </span>
    <span class="text">Tambah</span>
</button>

<!-- Modal -->
<div class="modal fade" id="modalMatkul" tabindex="-1" aria-labelledby="modalMatkulLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Add modal-lg here -->
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalMatkulLabel">Pilih Matakuliah Semester Ini</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive table" role="grid" aria-describedby="dataTableMatkul_info">
                    <table class="my-0 table" id="dataTableMatkul">
                        <thead>
                            <tr>
                                <th data-col="COUNT" width="5%">No.</th>
                                <th data-col="DOSEN.NAMA">NAMA DOSEN</th>
                                <th data-col="MATKUL.NAMA">MATA KULIAH</th>
                                <th data-col="SEMESTER.TAHUN_AJARAN+SEMESTER.SEMESTER">SEMESTER</th>
                                <th data-col="KELAS.KODE">KELAS</th>
                                <th data-type="AKSI_SUBMIT" data-col="ID">AKSI</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            generateDatatable('dataTableMatkul', "{{ route('global.matkul') }}");
        });
        $(document).on('click', '.btn_aksi_submit', function() {
            const button = $(this);
            button.prop('disabled', true); // 🔒 Disable the button

            const table = $('#dataTableMatkul').DataTable();
            const row = table.row(button.closest('tr')).data();


            $.ajax({
                url: "{{ route('mahasiswa.matkul.store') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    perkuliahan_id: row.id
                },
                success: function(response) {

                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalMatkul'));
                    modal.hide();

                    Swal.fire('Sukses', response.message, 'success').then(() => {
                        window.location.href = response.redirect;
                    });
                },
                error: function(xhr) {

                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error');
                },
                complete: function() {
                    button.prop('disabled', false); // 🔓 Enable again after request complete
                }
            });
        });
    </script>
@endpush
