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

const $ = require('jquery');
global.$ = global.jQuery = $;
// import 'popper.js';
require('bootstrap');


document.addEventListener("DOMContentLoaded", function () {
    console.log("brand list!");
    const body = $('body');
    body.on('keydown keyup onblur', '#bn', function () {
        let input = $(this);
        if (input.length) {
            var regexp_clear_space = /\s+/g;
            if (input.val().match(regexp_clear_space)) {
                let clearValue = input.val().replace(regexp_clear_space, ' ');
                input.val(clearValue);
                var regexp = /[^a-z, ¤æøĂéëäöåÉÄÖÅ&™®«»©]+/gi;
                if (clearValue.match(regexp)) {
                    input.val(clearValue.replace(regexp, ''));
                }
            }
        }
    });

    const collectionData = window.Routing
        .generate('app_rest_admin_brand_postbrandlist');
    var common_defs = [];
    $.each(for_prepare_defs, function (key, value) {
        if ($.inArray(value.data, img_columns) !== -1) {
            common_defs.push({
                "targets": key,
                "data": value.data,
                "render": function (data, type, row, meta) {
                    return type === 'display' ?
                        ' <img src="' + data + '" class="img-thumbnail">' : '';
                }
            })
        }

        if ($.inArray(value.data, link_columns) !== -1) {
            common_defs.push({
                "targets": key,
                "data": value.data,
                "render": function (data, type, row, meta) {
                    return type === 'display' ?
                        '<a href="' + data + '">Link</a>' : '';
                }
            })
        }

        if ($.inArray(value.data, short_preview_columns) !== -1) {
            common_defs.push({
                "targets": key,
                "data": value.data,
                "render": function (data, type, row, meta) {
                    return type === 'display' && data.length > 10 ?
                        '<span title="' + data + '">' + data.substr(0, 10) + '...</span>' :
                        data;
                }
            })
        }

        if ($.inArray(value.data, convert_to_html_columns) !== -1) {
            common_defs.push({
                "targets": key,
                "data": value.data,
                "render": function (data, type, row, meta) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(data, 'text/html');

                    return type === 'display' ?
                        doc.firstChild.innerHTML :
                        '';
                }
            })
        }
    });

    common_defs.push({
        "targets": 1,
        "data": 'brandName',
        "render": function (data, type, row, meta) {
            let topBrand = row.top;
            var divTag = $('<div/>');
            var pTag = $('<p/>', {"class": 'bn_' + row.id});
            var span = $('<span />').addClass('tb_' + row.id).attr('tb_val', topBrand);

            if (topBrand === true) {
                span.append('<i class="fa fa-check" aria-hidden="true"></i>');
            } else {
                span.append('<i class="fas fa-ban"></i>');
            }
            pTag.append(data).append(span);
            divTag.append(pTag);
            return type === 'display' ?
                divTag.html() : ''
        }
    });

    common_defs.push({
        "targets": 3,
        data: 'Action',
        render: function (data, type, row, meta) {
            return '    <!-- Button trigger modal -->\n' +
                '    <button type="button" class="btn btn-primary" data-brand-id="' + row.id + '" data-toggle="modal" data-target="#exampleModalLong">\n' +
                '        Edit\n' +
                '    </button>';
        }
    });

    var table = $('#empTable').DataTable({
        initComplete: function () {

            this.api().columns(1).every(function () {
                var column = this;

                var divTag = $('<div />').addClass('form-group col-md-4');

                var select = $('<select><option value="all">All</option></select>')
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search(val ? val : '', false, false)
                            .draw();
                    });
                var labelTag = $('<label />').attr('for', 'inputState');
                labelTag.text('Hot');
                divTag.append(labelTag).append(select);

                divTag.appendTo($(column.footer()).empty());

                $.each([1, 0], function (key, value) {
                    // console.log(key, value);
                    select.append('<option value="' + value + '">' + (value === 1 ? 'yes' : 'no') + '</option>')
                });
            });

        },
        'responsive': true,
        'fixedHeader': true,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url': collectionData,
        },
        columns: th_keys,
        "columnDefs": common_defs
    });


    $('#exampleModalLong').on('hide.bs.modal', function (event) {
        var modal = $(this);
        let hotCategory = modal.find('.modal-body #topBrand');
        let bn = modal.find('.modal-body #bn');
        bn.val('');
        bn.text('');
        hotCategory.prop("checked", false);
    });


    $('#exampleModalLong').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var modal = $(this);
        let brandId = button.data('brandId');
        modal.find('.modal-title').text('Edit ' + $('.bn_' + brandId).text() + ' brand');
        let bn_input = modal.find('.modal-body #bn');
        let bn_value = $('.bn_' + brandId);

        $.each(bn_value, function (k, v) {
            let bn_value_data = $(v).text();
            if (bn_value_data) {
                bn_input.text(bn_value_data);
                bn_input.val(bn_value_data);
                return false;
            }
        });

        let brnd_id_input = modal.find('.modal-body #brand_id');
        brnd_id_input.text(brandId);
        brnd_id_input.val(brandId);

        let topBrand = modal.find('.modal-body #topBrand');
        let tb_value = $('.tb_' + brandId);
        $.each(tb_value, function (k, v) {
            let tp_value_data = $(v).attr('tb_val');
            if (tp_value_data) {
                if (tp_value_data === 'true') {
                    topBrand.prop("checked", true);
                }
                return false;
            }
        })
    })

    $('.btn.btn-primary').on('click', function () {
        if ($('#editBrand input').length) {
            $.each($('#editBrand input'), function (k, v) {
                $(v).val($.trim($(v).val()));
            })
        }

        let serialize = $('#editBrand').serialize();

        const app_rest_admin_brand_editbrand = window.Routing
            .generate('app_rest_admin_brand_editbrand');

        $.ajax({
            type: "POST",
            url: app_rest_admin_brand_editbrand,
            data: serialize,
            error: (result) => {
                console.log(result.responseJSON.status);
            },
            success: (data) => {
                console.log(data);
                $('#exampleModalLong').modal('toggle');
                table.ajax.reload();
            }
        });
    })
});