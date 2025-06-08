<script>
    function initSelect2(selector, options = {}) {
        // Set default options
        const defaultOptions = {
            placeholder: "Pilih...",
            allowClear: true,
            width: '100%'
        };

        // Merge with custom options
        const finalOptions = Object.assign({}, defaultOptions, options);

        // Initialize Select2
        $(selector).select2(finalOptions);
    }
</script>
