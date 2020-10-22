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
import {createErrorImgPlaceHolder, delay, getBlobFromImageUri} from "./parts/photos_config";
import {renderAttachmentFilesBlock, addInitPhotoToUppy} from "./parts/uppy_attachment_files";

require('@fortawesome/fontawesome-free/css/all.min.css');
require('datatables.net-dt/css/jquery.dataTables.min.css');
require('datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css');
require('datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css');

require('@fortawesome/fontawesome-free/js/all.js');

const $ = require('jquery');
global.$ = global.jQuery = $;
// import 'popper.js';
require('bootstrap');

import 'select2';                       // globally assign select2 fn to $ element

document.addEventListener("DOMContentLoaded", function () {
    console.log("resource shop list!");

    const body = $('body');
    let exampleModalLong = $('#exampleModalLong');

    const app_rest_admin_resourceshops_shopreloading = window.Routing
        .generate('app_rest_admin_resourceshops_shopreloading');
    const app_rest_admin_attachmentfile_getattachmentfileslist = window.Routing
        .generate('app_rest_admin_attachmentfile_getattachmentfileslist');
    const collectionData = window.Routing
        .generate('app_rest_admin_resourceshops_resourceshoplist');
    const app_rest_hovermenumanagment_listhovermenuselect2 = window.Routing
        .generate('app_rest_hovermenumanagment_listhovermenuselect2');

    var table;
    body.on('click', '.resource_reloading button.trigger_reloading', function () {
        let current = $(this);

        let shopName = current.data('shopName');
        reloadingShop(shopName);
    });

    body.on('click', 'button.trigger_edit', function () {
        let button = $(this);
        exampleModalLong.modal('show', button);
    });

    const app_rest_admin_resourceshops_resourcelist = window.Routing.generate('app_rest_admin_resourceshops_resourcelist');
    $.ajax({
        type: "POST",
        url: app_rest_admin_resourceshops_resourcelist,
        error: (result) => {
            console.log(result.responseJSON.status);
        },
        success: (data) => {
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
                                    button.addClass('trigger_' + v.toLowerCase().replace(' ', '_'));
                                    if (row.shop_from_db && row.shop_from_db.id) {
                                        button.attr('data-shop-id', row.shop_from_db.id);
                                    }
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
                if ($.inArray(value.data, img_columns ) !== -1) {
                    common_defs.push({
                        "targets": key,
                        "data": value.data,
                        "render": function ( data, type, row, meta ) {
                            let divTagBuffer = $('<div/>');
                            if ($.isArray(data)) {
                                $.each(data, function (k, v) {
                                    var img = $('<img />', {
                                        src: v,
                                        alt: 'open',
                                        target: "_blank"
                                    });
                                    img.addClass('img-thumbnail');
                                    divTagBuffer.append(img);
                                })
                            }
                            let result = divTagBuffer.html();
                            return type === 'display' ?
                                result : '';
                        }
                    })
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

    exampleModalLong.on('hide.bs.modal', function (event) {
        var modal = $(this);

        let form = modal.find("form");
        form.trigger("reset");
        form.find('textarea').val('');
        form.find('.attachment_files_to_categories').remove();
        form.find('.hover_menu_categories').remove();
        form.find('.select2-container').remove();

        form.find('input[type=hidden]').remove();
    });

    exampleModalLong.on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var modal = $(this);
        let shopId = button.data('shopId');
        let form = modal.find("form");

        if (shopId) {
            $.ajax({
                type: "POST",
                url: app_rest_admin_attachmentfile_getattachmentfileslist,
                data: {
                    id: shopId, entity: 'App\\Entity\\Shop'
                },
                error: (result) => {
                    console.log(result.responseJSON.status);
                },
                success: (data) => {
                    renderEditForm(shopId, form, modal, button);
                    renderAttachmentFilesBlock(shopId, form, modal, button, data,
                        function (uppy, data) {
                            (async function (arr) {
                                //Promise.all не подходит, т.к. он отвалится если хоть одна фотка не загрузится
                                for (let item of arr) {

                                    try {
                                        let blob = await getBlobFromImageUri(item.path);
                                        await delay(1000);//минимальное время задержки для корректной работы добавления фоток в Dashborad Uppy
                                        addInitPhotoToUppy(uppy, blob, false, item);

                                    } catch (e) {

                                        let blob = await createErrorImgPlaceHolder();
                                        await delay(1000);
                                        addInitPhotoToUppy(uppy, blob, true, item);
                                        continue;
                                    } finally {
                                        uppy.getFiles().forEach(file => {
                                            uppy.setFileState(file.id, {
                                                progress: {uploadComplete: true, uploadStarted: true}
                                            })
                                        })
                                    }

                                }
                            })(data);
                        }, 'App\\Entity\\Shop');
                }
            });
        }
    });

    function customFormatSelection(node) {
        let level = 0;
        if (node.slug) {
            let split = node.slug.split('-own-');
            if ($.isArray(split)) {
                level = split.length;
            }
        }
        var commonSpan = $('<span/>').append('Level - ' + level);
        let pathTag = $('<span style="padding-left:' + (30 * level) + 'px;">' + node.text + '</span>');
        commonSpan.append(pathTag);

        return commonSpan;
    }

    function formatState (state) {
        let hotCategory = state.hotCategory;
        let disableForParsing = state.disableForParsing;
        var commonSpan = $('<span/>');
        var pTag = $('<p/>', {
            "class": 'cn_' + state.id
        });
        var span = $('<span />');
        var span_dfp = $('<span />');

        if (disableForParsing) {
            span_dfp.append('<i class="fas fa-bell-slash"></i>');
        } else {
            span_dfp.append('<i class="fas fa-bell"></i>');
        }

        if (hotCategory === true) {
            span.append('<i class="fa fa-check" aria-hidden="true"></i>');
        } else {
            span.append('<i class="fas fa-ban"></i>');
        }
        var spanPath = $('<span />');
        spanPath.append('<i class="fas fa-road"></i>').append('<i>' +state.slug + '</i>');
        var pPathTag = $('<p/>').append(spanPath);
        pTag.append(state.text).append(span).append(span_dfp);
        commonSpan.append(pPathTag).append(pTag);

        return commonSpan;
    }

    function renderEditForm(modelId, form, modal, button) {
        var hover_menu_categories = $('<select>').addClass('hover_menu_categories');
        let model_id_input = $('<input>').attr({
            type: 'hidden',
            id: 'shop_id',
            name: 'shop_id',
            class: 'shop_exist_id'
        });

        model_id_input.val(modelId);
        form.prepend(hover_menu_categories);
        form.append(model_id_input);
        applySelect2(hover_menu_categories);
    }

    function applySelect2(select) {
        select.select2({
            placeholder: {
                id: '-1', // the value of the option
                text: 'Select hover menu categories'
            },
            dropdownAutoWidth: true,
            width: '100%',
            multiple: true,
            minimumInputLength: 2,
            allowClear: true,
            templateSelection: formatState,
            templateResult: customFormatSelection,
            ajax: {
                type: 'post',
                url: app_rest_hovermenumanagment_listhovermenuselect2,
                data: function (params) {
                    console.log(params);
                    let query = {
                        search: params.term,
                        page: params.page || 1,
                        type: 'public'
                    };

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                }
            }
        });
    }

    function reloadingShop(shopName) {
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
                    table.ajax.reload(null, false);
                }
            }
        });
    }

    $('.btn.btn-primary').on('click', function () {
        let editShop = $('#editShop input');
        if (editShop.length) {
            $.each(editShop, function (k, v) {
                $(v).val($.trim($(v).val()));
            })
        }

        let serialize = $('#editShop').serialize();

        const app_rest_admin_resourceshops_editshop = window.Routing
            .generate('app_rest_admin_resourceshops_editshop');

        $.ajax({
            type: "POST",
            url: app_rest_admin_resourceshops_editshop,
            data: serialize,
            error: (result) => {
                console.log(result.responseJSON.status);
            },
            success: (data) => {
                console.log(data);
                exampleModalLong.modal('toggle');
                table.ajax.reload(null, false);
            }
        });
    });

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