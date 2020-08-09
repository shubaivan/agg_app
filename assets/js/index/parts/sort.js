export default function list() {
    const awinproductcollection = window.Routing
        .generate('app_rest_awinproductcollection_postproducts');
    var common_defs = [];
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
    });

    $('#empTable').DataTable({
        'responsive': true,
        'fixedHeader': true,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':awinproductcollection,
        },
        columns: th_keys,
        "columnDefs": common_defs
    });


    $('.desc').before($('<span style="color: green">').text("DESC"));
    $('.asc').before($('<span style="color: brown">').text("ASC"));
}
