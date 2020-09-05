/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../../css/app.scss';
import * as dt_bs4 from 'datatables.net-bs4'
import * as fh_bs from 'datatables.net-fixedheader-bs4';
import * as r_bs from 'datatables.net-responsive-bs4';

require('@fortawesome/fontawesome-free/css/all.min.css');
require('datatables.net-dt/css/jquery.dataTables.min.css');
require('datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css');
require('datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css');

require('@fortawesome/fontawesome-free/js/all.js');

const $  = require( 'jquery' );
global.$ = global.jQuery = $;
// import 'popper.js';
require('bootstrap');


document.addEventListener("DOMContentLoaded", function () {
    console.log( "brand list!" );

    const collectionData = window.Routing
        .generate('app_rest_admin_brand_postbrandlist');
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
});