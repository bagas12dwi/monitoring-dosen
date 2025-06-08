<script>
    function generateDatatable(selector, ajaxUrl) {
        const table = $('#' + selector);

        let columns = [];
        table.find('thead th').each(function() {
            let col = $(this).data('col');
            let type = $(this).data('type');
            let satuan = $(this).data('satuan');
            let routeName = $(this).data('route');
            let route = '';
            if (type === 'PDF' && routeName) {
                route = "{{ url('/') }}/" + routeName.replaceAll('.', '/').replace(/\/:id$/, '') + "/:id";
            }


            if (!col) return;

            let colDef = {};
            let concatCols = col.split('+').map(c => c.trim().toLowerCase());

            if (concatCols.length > 1) {
                // If concatenation needed
                colDef.data = null; // must be null when using render
                colDef.render = function(data, type, row) {
                    return concatCols.map(c => {
                        return c.split('.').reduce((o, key) => (o && o[key]) ? o[key] : '', row);
                    }).join(' ');
                };
            } else {
                col = concatCols[0];
                if (col === 'count') {
                    col = 'DT_RowIndex';
                }
                colDef.data = col;
            }
            if (type === 'DATE' || type === 'DATETIME') {
                colDef.render = function(data) {
                    if (!data) return '';
                    let date = new Date(data);
                    let options = {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        ...(type === 'DATETIME' && {
                            hour: '2-digit',
                            minute: '2-digit'
                        })
                    };
                    return new Intl.DateTimeFormat('id-ID', options).format(date);
                };
            } else if (type == 'AKSI_SUBMIT') {
                colDef.render = function() {
                    return '<button type="button" class="btn btn-outline-primary btn_aksi_submit btn-sm rounded-pill" id="btn_aksi_submit"><i class="fa fa-check"></i></button>';
                };
                colDef.orderable = false;
                colDef.searchable = false;
            } else if (type == 'PDF') {
                colDef.render = function(data, type, row) {
                    if (!data) return '';
                    let url = route.replace(':id', row.id); // Replace route param with actual ID
                    return `<a href="${url}" class="btn btn-danger"><i class="fa fa-file-pdf"></i></a>`;
                };
                colDef.orderable = false;
                colDef.searchable = false;
            } else if (type == 'SATUAN') {
                colDef.render = function(data) {
                    if (!data) return '';
                    return data + ' ' + satuan.toLowerCase();
                }
            } else if (type == 'FLAG') {
                colDef.render = function(data) {
                    let color = 'primary';
                    if (data == 'admin') {
                        color = 'danger';
                    } else if (data == 'ortu') {
                        color = 'warning';
                    }
                    return `<span class="badge rounded-pill text-bg-${color} px-3 py-2">${data.toUpperCase()}</span>`;
                }
            } else if (type == 'PERCENTAGE') {
                colDef.render = function(data) {
                    if (data === null || data === undefined) return '';
                    return parseFloat(data).toFixed(2) * 100 + '%';
                }
            } else if (type == 'AKTIF_FLAG') {
                colDef.render = function(data) {
                    if (data === null || data === undefined) return '';
                    return `<span class="badge rounded-pill text-bg-${data ? 'success' : 'secondary'} px-3 py-2">${data ? '<i class="fas fa-check text-white"></i>' : '<i class="fas fa-times text-white"></i>'}</span>`;
                }
            }



            // Disable ordering/searching for certain special columns
            if (col === 'DT_RowIndex' || col === 'action' || col === 'delete') {
                colDef.orderable = false;
                colDef.searchable = false;
            }

            columns.push(colDef);
        });

        return table.DataTable({
            serverSide: true,
            processing: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: ajaxUrl
            },
            columns: columns
        });
    }
</script>
