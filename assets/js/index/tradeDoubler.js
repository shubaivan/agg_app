document.addEventListener("DOMContentLoaded", function () {
    console.log( "sort!" );

    const product_collection_awin_dictDeclineError = window.Routing.generate('product_collection_awin_dictDeclineError');
    $.ajax({
        type: "POST",
        url: product_collection_awin_dictDeclineError,
        data: {
            collectionName: 'TradeDoublerProduct'
        },
        error: (result) => {
            console.log(result.responseJSON.status);
        },
        success: (data) => {
            const collectionData = window.Routing
                .generate('app_rest_tradedoublercollection_postproducts');
            var common_defs = [];
            var decline_reason_key = [];
            $.each( for_prepare_defs, function( key, value ) {
                if ($.inArray(value.data, img_columns ) !== -1) {
                    common_defs.push({
                        "targets": key,
                        "data": value.data,
                        "render": function ( data, type, row, meta ) {
                            return type === 'display' ?
                                ' <img src="'+data+'" class="img-thumbnail">' : '';
                        }
                    })
                }

                if ($.inArray(value.data, link_columns ) !== -1) {
                    common_defs.push({
                        "targets": key,
                        "data": value.data,
                        "render": function ( data, type, row, meta ) {
                            return type === 'display' ?
                                '<a href="'+data+'">Link</a>' : '';
                        }
                    })
                }

                if ($.inArray(value.data, short_preview_columns ) !== -1) {
                    common_defs.push({
                        "targets": key,
                        "data": value.data,
                        "render": function ( data, type, row, meta ) {
                            return type === 'display' && data.length > 10 ?
                                '<span title="'+data+'">'+data.substr( 0, 10 )+'...</span>' :
                                data;
                        }
                    })
                }

                if ($.inArray(value.data, convert_to_html_columns ) !== -1) {
                    common_defs.push({
                        "targets": key,
                        "data": value.data,
                        "render": function ( data, type, row, meta ) {
                            var parser = new DOMParser();
                            var doc = parser.parseFromString(data, 'text/html');

                            return type === 'display' ?
                                doc.firstChild.innerHTML :
                                '';
                        }
                    })
                }

                if ($.inArray(value.data, decline_reason ) !== -1) {
                    decline_reason_key.push(key);
                    common_defs.push({
                        "targets": key,
                        "data": value.data,
                        "render": function ( data, type, row, meta ) {
                            return type === 'display' && data.length > 10 ?
                                '<span title="'+data+'">'+data.substr( 0, 10 )+'...</span>' :
                                data;
                        }
                    })
                }
            });

// Setup - add a text input to each footer cell
            var separate_filter_column_keys = [];
            $('#empTable tfoot th').each( function (k, v) {
                var title = $(this).text();
                if ($.inArray(title, separate_filter_column ) !== -1) {
                    separate_filter_column_keys.push(k);
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            var table = $('#empTable').DataTable({
                initComplete: function () {
                    // Apply the search
                    this.api().columns(separate_filter_column_keys).every( function () {
                        var that = this;

                        $( 'input', this.footer() ).on( 'keyup change clear', function () {
                            if ( that.search() !== this.value ) {
                                that
                                    .search( this.value )
                                    .draw();
                            }
                        } );
                    } );

                    this.api().columns(decline_reason_key).every( function () {
                        var column = this;
                        var select = $('<select><option value=""></option></select>')
                            .appendTo( $(column.footer()).empty() )
                            .on( 'change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );

                                column
                                    .search( val ? val : '', false, false )
                                    .draw();
                            } );
                        $.each(data, function( key, value ) {
                            // console.log(key, value);
                            select.append( '<option value="'+value+'">'+value.substr( 0, 10 )+'</option>' )
                        });
                    } );
                },
                'responsive': true,
                'fixedHeader': true,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url':collectionData,
                },
                columns: th_keys,
                "columnDefs": common_defs
            });
        }
    });
});