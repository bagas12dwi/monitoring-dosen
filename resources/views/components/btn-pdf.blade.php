<a href="{{ route($route . '.cetak', ['semester' => request('semester')]) }}" id="btnCetakPdf"
    class="btn btn-danger btn-icon-split">
    <span class="icon text-white-50">
        <i class="far fa-file-pdf"></i>
    </span>
    <span class="text">Cetak PDF</span>
</a>

@push('scripts')
    <script>
        const baseRoute = "{{ route($route . '.cetak') }}";

        $('#semester').on('change', function() {
            const semesterId = $(this).val();
            $('#btnCetakPdf').attr('href', baseRoute + '?semester=' + semesterId);
        });

        // trigger once on load
        $('#semester').trigger('change');
    </script>
@endpush
