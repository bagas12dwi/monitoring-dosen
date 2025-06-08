<!-- Button trigger modal -->
<button type="button" class="btn btn-success btn-icon-split" data-bs-toggle="modal" data-bs-target="#importModal">
    <span class="icon text-white-50">
        <i class="far fa-file-excel"></i>
    </span>
    <span class="text text-white">
        Import Excel
    </span>
</button>

<!-- Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="importModalLabel">Import Data {{ $title }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ $route }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import{{ $title }}" class="form-label">Import Data
                            {{ $title }}</label>
                        <input type="file" class="form-control" name="import{{ $title }}"
                            id="import{{ $title }}" placeholder="Import Data {{ $title }}"
                            aria-describedby="fileHelpId" />
                        <div id="fileHelpId" class="form-text">Import data menggunakan format (.xlsx, .xls)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <a href="{{ URL::asset('assets/template/template_data_warga.xlsx') }}" class="btn btn-warning"
                        download>Download Template</a> --}}
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
