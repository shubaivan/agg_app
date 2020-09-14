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
    console.log("admin shop rule list!");
    const body = $('body');


    const collectionData = window.Routing
        .generate('app_rest_admin_adminshoprule_postshoprulelist');
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

        if (value.data === 'store') {
            common_defs.push({
                "targets": key,
                data: 'store',
                render: function (data, type, row, meta) {
                    var divTag = $('<div/>');
                    var pTag = $('<p/>', {"class": 'srs_' + row.id});

                    pTag.append(data);
                    divTag.append(pTag);
                    return type === 'display' ?
                        divTag.html() : '';
                }
            });
        }

        if (value.data === 'Action') {
            common_defs.push({
                "targets": key,
                data: 'Action',
                render: function (data, type, row, meta) {
                    let actions = data.split(",");
                    var divTag = $('<div/>');
                    $.each(actions, function (k, v) {
                        let pTag = $('<p/>', {"class": 'sr_' + row.id});
                        let button = $('<button/>',
                            {
                                "class": 'btn btn-primary',
                                'type': 'button',
                                'data-shop-rule-id': row.id,
                                'data-toggle': 'modal',
                                'data-target': '#editShopRules',
                                'name': v,
                                'text': ucfirst(v, true)
                            }
                        );

                        pTag.append(button);
                        divTag.append(pTag);
                    });
                    return type === 'display' ?
                        divTag.html() : ''
                }
            });
        }

        if (value.data === 'columnsKeywords') {
            common_defs.push({
                "targets": key,
                data: 'Action',
                render: function (data, type, row, meta) {
                    let arrayRules = JSON.parse(data);
                    var divTag = $('<div/>');
                    let pTagPureResult = $('<p/>', {
                        "class": 'pure-result-sr_' + row.id,
                        'type': 'hidden',
                        'pure-result': data
                    });
                    divTag.append(pTagPureResult);

                    $.each(arrayRules, function (k, v) {
                        if ($.isArray(v)) {
                            let pTag = $('<p/>', {"class": 'sr_' + row.id});
                            let iTag = $('<i/>');
                            if (~k.indexOf("!")) {
                                k = k.replace('!', '');
                                iTag.addClass('fas fa-search-minus')
                            } else {
                                iTag.addClass('fas fa-search-plus')
                            }
                            pTag.append(iTag);
                            pTag.append(k + ' - ' + v.join(','));

                            divTag.append(pTag)
                        } else {
                            $.each(v, function (subK, subV) {
                                if ($.isArray(subV)) {
                                    let pTag = $('<p/>', {"class": 'sr_' + row.id});
                                    let iTag = $('<i/>');
                                    if (~subK.indexOf("!")) {
                                        subK = subK.replace('!', '');
                                        iTag.addClass('fas fa-search-minus')
                                    } else {
                                        iTag.addClass('fas fa-search-plus')
                                    }
                                    pTag.append(iTag);
                                    pTag.append(k + '<i class="fa fa-arrow-right" aria-hidden="true"></i>' + subK + ' - ' + subV.join(','));

                                    divTag.append(pTag)
                                }
                            });
                        }
                    });

                    return type === 'display' ?
                        divTag.html() : ''
                }
            });
        }
    });

    var table = $('#empTable').DataTable({

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

    $('#editShopRules').on('hide.bs.modal', function (event) {
        var modal = $(this);
        let editShopRules = modal.find('.modal-body #editShopRulesForm');
        editShopRules.empty();
    });

    $('#editShopRules').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var modal = $(this);

        let shopRuleId = button.data('shopRuleId');
        modal.find('.modal-title').text('Edit ' + $('.srs_' + shopRuleId).text() + ' rules');
        let form = modal.find('#editShopRulesForm');
        let hInput = $('<input type="hidden">', {
            id: 'shop_rule_id',
            'name': 'shop_rule_id'
        }).val(shopRuleId);
        form.append(hInput);
        let pure = $('.pure-result-sr_' + shopRuleId);
        if (pure) {
            let pureData = pure.attr('pure-result');
            if(pureData) {
                let arrayRules = JSON.parse(pureData);
                $.each(arrayRules, function (k, v) {
                    if ($.isArray(v)) {
                        var divTag = $('<div/>', {'class': "form-group"});
                        let smallText = 'Fill words';
                        let iTag = $('<i/>');
                        if (~k.indexOf("!")) {
                            k = k.replace('!', '');
                            iTag.addClass('fas fa-search-minus');
                            smallText = smallText + ' for exclude';
                        } else {
                            iTag.addClass('fas fa-search-plus');
                            smallText = smallText + ' for match'
                        }

                        let columnName = ucfirst(k,true);
                        smallText = smallText + ' in ' + columnName + ' column';
                        let label = $("<label>", {'for': k + '_input'}).text(columnName);
                        label.append(iTag);
                        divTag.append(label);

                        let input = $('<input type="text">', {
                            id: k + '_input',
                            'name': k,
                            'class': "form-control",
                            'aria-describedby': k + '_kwHelp'
                        });
                        input.val(v.join(','));
                        input.text(v.join(','));
                        divTag.append(input);
                        let small = $("<small>", {
                            'id': k + '_kwHelp',
                            'class': 'form-text text-muted'
                        }).text(smallText);
                        divTag.append(small);

                        form.append(divTag);
                    } else {
                        $.each(v, function (subK, subV) {
                            var divTag = $('<div/>', {'class': "form-group"});
                            let smallText = 'Fill words';
                            let iTag = $('<i/>');
                            if (~subK.indexOf("!")) {
                                subK = subK.replace('!', '');
                                iTag.addClass('fas fa-search-minus');
                                smallText = smallText + ' for exclude';
                            } else {
                                iTag.addClass('fas fa-search-plus');
                                smallText = smallText + ' for match'
                            }
                            let span = $("<span>");

                            let columnName = ucfirst(subK,true);
                            span
                                .append(ucfirst(k,true))
                                .append('<i class="fa fa-arrow-right" aria-hidden="true"></i>')
                                .append(columnName);
                            smallText = smallText + ' in ' + ucfirst(k,true) + ' column in ' + columnName + ' key';
                            let label = $("<label>", {'for': k + '_' + subK + '_input'}).append(span);
                            label.append(iTag);
                            divTag.append(label);

                            let input = $('<input type="text">', {
                                id: k + '_' + subK + '_input',
                                'name': subK,
                                'class': "form-control",
                                'aria-describedby': subK + '_kwHelp'
                            });
                            input.val(subV.join(','));
                            input.text(subV.join(','));
                            divTag.append(input);
                            let small = $("<small>", {
                                'id': subK + '_kwHelp',
                                'class': 'form-text text-muted'
                            }).text(smallText);
                            divTag.append(small);

                            form.append(divTag);
                        });
                    }
                })
            }
        }
    })

    $('.shop-rules-edit-select').on('change', function () {
        let form = $('#editShopRulesForm');
        var selectedVal = $(this).children('option:selected').val();
        var selectedText = $(this).children('option:selected').text();

    });

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

    function ucfirst(str,force){
        str=force ? str.toLowerCase() : str;
        return str.replace(/(\b)([a-zA-Z])/,
            function(firstLetter){
                return   firstLetter.toUpperCase();
            });
    }
});