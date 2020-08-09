export default function list() {
    const awinproductcollection = window.Routing
        .generate('app_rest_awinproductcollection_postproducts');
    var img_defs = [];
    $.each( for_prepare_defs, function( key, value ) {

        if ($.inArray(value.data, img_columns ) !== -1) {
            img_defs.push({
                "targets": key,
                "data": value.data,
                "render": function ( data, type, row, meta ) {
                    return type === 'display' ?
                        ' <img src="'+data+'" class="img-thumbnail">' : '';
                }
            })
        }
    });

    var common_defs = [
        {
            "targets": 0,
            "data": "id",
            "render": function ( data, type, row, meta ) {
                return type === 'display' ?
                    '<span data-toggle="tooltip" title="'+data+'">#</span>' : '';
            }
        },
        {
            "targets": 3,
            "data": "aw_deep_link",
            "render": function ( data, type, row, meta ) {
                return '<a href="'+data+'">Link</a>';
            }
        },
        {
            "targets": 8,
            "data": "description",
            "render": function ( data, type, row, meta ) {
                return type === 'display' && data.length > 40 ?
                    '<span title="'+data+'">'+data.substr( 0, 38 )+'...</span>' :
                    data;
            }
        }
    ];

    var use_defs = $.merge( common_defs, img_defs );

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
        "columnDefs": use_defs
    });


    $('.desc').before($('<span style="color: green">').text("DESC"));
    $('.asc').before($('<span style="color: brown">').text("ASC"));
}
