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
    console.log("resource shop list!");
    const body = $('body');
    const app_rest_admin_resourceshops_shopreloading = window.Routing.generate('app_rest_admin_resourceshops_shopreloading');
    var table;
    body.on('click', '.resource_reloading', function () {
        let current = $(this);
        let button = current.find('button');
        let shopName = button.data('shopName');

        reloadingShop(shopName, current);
    });

    const app_rest_admin_resourceshops_resourcelist = window.Routing.generate('app_rest_admin_resourceshops_resourcelist');
    $.ajax({
        type: "POST",
        url: app_rest_admin_resourceshops_resourcelist,
        error: (result) => {
            console.log(result.responseJSON.status);
        },
        success: (data) => {
            const collectionData = window.Routing
                .generate('app_rest_admin_resourceshops_resourceshoplist');


            var common_defs = [];

            $.each(for_prepare_defs, function (key, value) {
                if (value.data === 'ManuallyJobs') {
                    common_defs.push({
                            "targets": key,
                            data: 'ManuallyJobs',
                            render: function (data, type, row, meta) {
                                var divTag = $('<div/>');
                                if ($.isArray(data)) {
                                    let uTag;
                                    let pTag;
                                    $.each(data, function (k, v) {
                                        pTag = $('<p/>');
                                        uTag = $('<ul/>');

                                        let licreatedAt = $('<li/>', {
                                            text: 'createdAt - ' + v.createdAt
                                        });
                                        uTag.append(licreatedAt);
                                        if (v.createdAtAdmin) {
                                            let licreatedAtAdmin = $('<li/>', {
                                                text: 'createdAtAdmin - ' + v.createdAtAdmin.email
                                            });
                                            uTag.append(licreatedAtAdmin);
                                        }

                                        let listatus = $('<li/>', {
                                            text: 'status - ' + v.enumStatusPresent
                                        });
                                        uTag.append(listatus);

                                        pTag.append(uTag);

                                        divTag.append(pTag)
                                    })
                                }

                                return type === 'display' ?
                                    divTag.html() : ''
                            }
                        }
                    );
                }
                if (value.data === 'Action') {
                    common_defs.push({
                        "targets": key,
                        data: 'Action',
                        render: function (data, type, row, meta) {
                            let result = '';
                            // if (row.queue.messageCount === 0) {
                                let actions = data.split(",");
                                var divTag = $('<div/>');
                                $.each(actions, function (k, v) {
                                    let pTag = $('<p/>', {"class": 'resource_reloading rn_' + row.ResourceName});
                                    let button = $('<button/>',
                                        {
                                            "class": 'btn btn-primary',
                                            'type': 'button',
                                            'data-shop-name': row.ShopName,
                                            'name': v,
                                            'text': ucfirst(v, true)
                                        }
                                    );

                                    pTag.append(button);
                                    divTag.append(pTag);
                                });

                                result = divTag.html();
                            // }

                            return type === 'display' ?
                                result : ''
                        }
                    });
                }
            });


            table = $('#empTable').DataTable({
                initComplete: function () {
                    if (data.length) {
                        this.api().columns(0).every(function () {
                            var column = this;
                            var select = $('<select><option value=""></option></select>')
                                .appendTo($(column.footer()).empty())
                                .on('change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );

                                    column
                                        .search(val ? val : '', false, false)
                                        .draw();
                                });
                            $.each(data, function (key, value) {
                                // console.log(key, value);
                                select.append('<option value="' + value + '">' + value.substr(0, 10) + '</option>')
                            });
                        });
                    }
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
        }
    });

    function reloadingShop(shopName, current) {
        $.ajax({
            type: "POST",
            url: app_rest_admin_resourceshops_shopreloading,
            data: {
                shopName: shopName
            },
            error: (result) => {
                console.log(result.responseJSON.status);
            },
            success: (data) => {
                console.log(data);
                if (table) {
                    table.ajax.reload();
                }
            }
        });
    }

    /**
     *
     * @param str
     * @param force
     * @returns {string}
     */
    function ucfirst(str, force) {
        str = force ? str.toLowerCase() : str;
        return str.replace(/(\b)([a-zA-Z])/,
            function (firstLetter) {
                return firstLetter.toUpperCase();
            });
    }
});