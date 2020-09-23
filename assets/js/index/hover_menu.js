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

// const $  = require( 'jquery' );
// global.$ = global.jQuery = $;
// import 'popper.js';
require('bootstrap');

document.addEventListener("DOMContentLoaded", function () {

    const app_rest_hovermenumanagment_listthhovermenu = window.Routing
        .generate('app_rest_hovermenumanagment_listthhovermenu');
    const body = $('body');
    body.on('keydown keyup onblur', '#pkw, #nkw, #category_name', function () {
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

    $.ajax({
        type: "GET",
        url: app_rest_hovermenumanagment_listthhovermenu,
        error: (result) => {
            console.log(result.responseJSON.status);
        },
        success: (data) => {
            var columns = [];
            $.each(data, function (i, v) {
                columns.push({
                    'data': v
                })
            })
            console.log(columns)
            generateDataTables(columns);
        }
    });

    function generateDataTables(columns) {
        const app_rest_hovermenumanagment_listhovermenu = window.Routing
            .generate('app_rest_hovermenumanagment_listhovermenu');

        var table = $('#empTable').DataTable({
            initComplete: function () {
                this.api().columns(0).every(function () {
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
            'serverMethod': 'get',
            'ajax': {
                'url': app_rest_hovermenumanagment_listhovermenu,
            },
            columns: columns,
            "columnDefs": [
                {

                    "targets": 0,
                    "data": 'CategoryName',
                    "render": function (data, type, row, meta) {
                        let hotCategory = row.HotCategory;
                        var divTag = $('<div/>');
                        var pTag = $('<p/>', {"class": 'cn_' + row.id});
                        var span = $('<span />').addClass('hc_' + row.id).attr('hc_val', hotCategory);

                        if (hotCategory === true) {
                            span.append('<i class="fa fa-check" aria-hidden="true"></i>');
                        } else {
                            span.append('<i class="fas fa-ban"></i>');
                        }
                        pTag.append(data).append(span);
                        divTag.append(pTag);
                        return type === 'display' ?
                            divTag.html() : ''
                    }
                },

                {

                    "targets": 1,
                    "data": 'PositiveKeyWords',
                    "render": function (data, type, row, meta) {
                        return type === 'display' && data ?
                            '<p class="pkw_' + row.id + '">' + data + '</p>' : ''
                    }
                },
                {

                    "targets": 2,
                    "data": 'NegativeKeyWords',
                    "render": function (data, type, row, meta) {
                        return type === 'display' && data ?
                            '<p class="nkw_' + row.id + '">' + data + '</p>' : ''
                    }
                },
                {

                    "targets": 3,
                    data: 'CategoryPosition',
                    render: function (data, type, row, meta) {
                        return type === 'display' ?
                            '<p class="position_' + row.id + '">' + data + '</p>' : ''                    }
                },
                {

                    "targets": 4,
                    data: 'Action',
                    render: function (data, type, row, meta) {
                        return '    <!-- Button trigger modal -->\n' +
                            '    <button type="button" class="btn btn-primary" data-category-id="' + row.id + '" data-toggle="modal" data-target="#exampleModalLong">\n' +
                            '        Edit\n' +
                            '    </button>';
                    }
                }
            ],
        });

        $('#exampleModalLong').on('hide.bs.modal', function (event) {
            var modal = $(this);
            let hotCategory = modal.find('.modal-body #hotCatgory');
            hotCategory.prop("checked", false);

            let category_position = modal.find('.modal-body #category_position');
            category_position.val('');
            category_position.text('');

            let category_name_input = modal.find('.modal-body #category_name');
            category_name_input.val('');
            category_name_input.val('');

            let nkw = modal.find('.modal-body #nkw');
            nkw.val('');
            nkw.text('');

            let pkw = modal.find('.modal-body #pkw');
            pkw.val('');
            pkw.text('');
        });


        $('#exampleModalLong').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var modal = $(this);
            let categoryId = button.data('categoryId');
            modal.find('.modal-title').text('Edit ' + $('.cn_' + categoryId).text() + ' category');

            let category_name_input = modal.find('.modal-body #category_name');
            let cn_value = $('.cn_' + categoryId);

            $.each(cn_value, function (k, v) {
                let cn_value_data = $(v).text();
                if (cn_value_data) {
                    category_name_input.text(cn_value_data);
                    category_name_input.val(cn_value_data);
                    return false;
                }
            });

            let pkw_input = modal.find('.modal-body #pkw');
            let pkw_value = $('.pkw_' + categoryId);

            $.each(pkw_value, function (k, v) {
                let pkw_value_data = $(v).text();
                if (pkw_value_data) {
                    pkw_input.text(pkw_value_data);
                    pkw_input.val(pkw_value_data);
                    return false;
                }
            });

            let category_position_input = modal.find('.modal-body #category_position');
            let category_position_value = $('.position_' + categoryId);

            $.each(category_position_value, function (k, v) {
                let category_position_value_data = $(v).text();
                if (category_position_value_data) {
                    category_position_input.text(category_position_value_data);
                    category_position_input.val(category_position_value_data);
                    return false;
                }
            });

            let nkw_input = modal.find('.modal-body #nkw');
            let nkw_value = $('.nkw_' + categoryId);
            $.each(nkw_value, function (k, v) {
                let nkw_value_data = $(v).text();
                if (nkw_value_data) {
                    nkw_input.text(nkw_value_data);
                    nkw_input.val(nkw_value_data);
                    return false;
                }
            });

            let category_id_input = modal.find('.modal-body #category_id');
            category_id_input.text(categoryId);
            category_id_input.val(categoryId);

            let hotCategory = modal.find('.modal-body #hotCatgory');
            let hc_value = $('.hc_' + categoryId);
            $.each(hc_value, function (k, v) {
                let hc_value_data = $(v).attr('hc_val');
                if (hc_value_data) {
                    if (hc_value_data === 'true') {
                        hotCategory.prop("checked", true);
                    }
                    return false;
                }
            })
        })

        $('.btn.btn-primary').on('click', function () {
            if ($('#editCateory textarea').length) {
                $.each($('#editCateory textarea'), function (k, v) {
                    $(v).val($.trim($(v).val()));
                })
            }

            let serialize = $('#editCateory').serialize();

            const app_rest_hovermenumanagment_edithovermenu = window.Routing
                .generate('app_rest_hovermenumanagment_edithovermenu');

            $.ajax({
                type: "POST",
                url: app_rest_hovermenumanagment_edithovermenu,
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
    }
});
