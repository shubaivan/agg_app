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

import {
    getBlobFromImageUri,
    createErrorImgPlaceHolder,
    delay
} from './parts/photos_config.js';

import {renderAttachmentFilesBlock, addInitPhotoToUppy} from "./parts/uppy_attachment_files";

import 'select2';

document.addEventListener("DOMContentLoaded", function () {
    console.log("brand list!");
    const body = $('body');
    let table;
    let resource_shop_slug;
    let exampleModalLong = $('#exampleModalLong');

    const attachment_files = window.Routing
        .generate('app_rest_admin_attachmentfile_postattachmentfile');
    const app_rest_admin_attachmentfile_getattachmentfileslist = window.Routing
        .generate('app_rest_admin_attachmentfile_getattachmentfileslist');
    const app_rest_admin_attachmentfile_getattachmentfilestemplate = window.Routing
        .generate('app_rest_admin_attachmentfile_getattachmentfilestemplate');
    const app_rest_admin_strategies_getstrategieslist = window.Routing
        .generate('app_rest_admin_strategies_getstrategieslist');
    const app_rest_admin_strategies_postapplystrategybyslug = window.Routing
        .generate('app_rest_admin_strategies_postapplystrategybyslug');


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

        if ($.inArray(value.data, seo_columns) !== -1) {
            common_defs.push({
                "targets": key,
                data: value.data,
                render: function (data, type, row, meta) {
                    let divTagBuffer = $('<div/>');
                    let divTagContent = $('<div/>').addClass( value.data + '_' + row.id).addClass('data_model_seo');

                    let parseHTML = $.parseHTML(data);
                    divTagContent.append(parseHTML);
                    divTagBuffer.append(divTagContent);
                    let result = divTagBuffer.html();

                    return type === 'display' && data ?
                        result : ''
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
        "targets": 0,
        "data": 'brandName',
        "render": function (data, type, row, meta) {
            var divTag = $('<div/>');
            if (row.id) {
                let pTagPureResult = $('<p/>', {
                    "class": 'pure-result-brand_' + row.id,
                    'type': 'hidden',
                    'data-pure-result': JSON.stringify(row)
                });
                var pTagShops = $('<p/>')
                    .append('Shops: ')
                    .append('<i>'+row.shop_names+'</i>');
                divTag.append(pTagPureResult).append(pTagShops);
            }
            let topBrand = row.top;

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
        "targets": 4,
        data: 'Action',
        render: function (data, type, row, meta) {
            return '    <!-- Button trigger modal -->\n' +
                '    <button type="button" class="btn btn-primary" data-brand-id="' + row.id + '" data-toggle="modal" data-target="#exampleModalLong">\n' +
                '        Edit\n' +
                '    </button>';
        }
    });

    table = $('#empTable').DataTable({
        initComplete: function () {
            initiateShopsSelect();
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
        'serverMethod': 'post',
        'ajax': {
            'url': collectionData,
            "data": function ( d ) {
                if (resource_shop_slug) {
                    d.resource_shop_slug = resource_shop_slug;
                }
            }
        },
        columns: th_keys,
        "columnDefs": common_defs
    });

    exampleModalLong.on('hide.bs.modal', function (event) {
        var modal = $(this);

        let form = modal.find("form");
        form.trigger("reset");
        form.find('textarea').val('');
        form.find('.strategies_select').remove();

        modal.find('.render_play_ground').remove();
        form.find('.select2-container').remove();

        form.find('.attachment_files_to_categories').remove();
        form.find('input[type=hidden]').remove();
    });

    exampleModalLong.on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var modal = $(this);
        let brandId = button.data('brandId');
        let form = modal.find("form");

        if (brandId) {
            $.ajax({
                type: "POST",
                url: app_rest_admin_attachmentfile_getattachmentfileslist,
                data: {
                    id: brandId, entity: 'App\\Entity\\Brand'
                },
                error: (result) => {
                    console.log(result.responseJSON.status);
                },
                success: (data) => {
                    renderEditForm(brandId, form, modal, button);
                    renderAttachmentFilesBlock(brandId, form, modal, button, data,
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
                        }, 'App\\Entity\\Brand');
                }
            });
        }
    });

    function renderEditForm(modelId, form, modal, button) {
        let pure_result = $('.pure-result-brand_' + modelId);
        if (pure_result && pure_result.length) {
            let pureResultData = pure_result.data('pureResult');

            modal.find('.modal-title').text('Edit ' + pureResultData.brandName + ' brand');
            let brandNameInput = modal.find('.modal-body #bn');
            brandNameInput.val(pureResultData.brandName);
            brandNameInput.text(pureResultData.brandName);
        } else {
            setDataInForm(modal.find('.modal-body #bn'),
                '.bn_' + modelId);
        }

        let brand_id_input = $('<input>').attr({
            type: 'hidden',
            id: 'brand_id',
            name: 'brand_id',
            class: 'brand_exist_id'
        });

        brand_id_input.val(modelId);
        form.append(brand_id_input);


        var strategies = $('<select>').addClass('strategies_select');
        strategies.attr('name', 'strategy');
        form.prepend(strategies);
        applySelect2(strategies, modelId);

        let topBrand = modal.find('.modal-body #topBrand');
        let tb_value = $('.tb_' + modelId);
        $.each(tb_value, function (k, v) {
            let tp_value_data = $(v).attr('tb_val');
            if (tp_value_data) {
                if (tp_value_data === 'true') {
                    topBrand.prop("checked", true);
                }
                return false;
            }
        });
        renderSimditor(modelId, form, modal, button);
    }

    /**
     *
     * @param select
     * @param modelId
     */
    function applySelect2(select, modelId = null) {
        select.select2({
            placeholder: {
                id: '-1', // the value of the option
                text: 'Select strategy'
            },
            dropdownAutoWidth: true,
            width: '100%',
            multiple: false,
            allowClear: true,
            templateResult: formatOption,
            ajax: {
                type: 'post',
                url: app_rest_admin_strategies_getstrategieslist,
                data: function (params) {

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
        let pure_result = $('.pure-result-brand_' + modelId);

        if (pure_result && pure_result.length) {
            select.on('change', function (e) {
                let currentOption = e.currentTarget.selectedOptions;
                renderStrategyPlayGround($(currentOption).attr('value'), select, pureResultData.slug);
            });

            let pureResultData = pure_result.data('pureResult');
            renderExistStrategyInPlayGround(pureResultData.slug, select);
        }
    }

    function renderExistStrategyInPlayGround(modelSlug, select)
    {
        const app_rest_admin_brand_getbrandbyslug = window.Routing
            .generate('app_rest_admin_brand_getbrandbyslug', {'slug': modelSlug});

        $.ajax({
            type: "GET",
            url: app_rest_admin_brand_getbrandbyslug,
            error: (result) => {
                console.log(result.responseJSON.status);
            },
            success: (data) => {
                if (data.brandStrategies && data.brandStrategies.strategy) {
                    let strategy = data.brandStrategies.strategy;

                    // Set the value, creating a new option if necessary
                    if (select.find("option[value='" + strategy.slug + "']").length) {
                        select.val(data.slug).trigger('change');
                    } else {
                        // Create a DOM Option and pre-select by default
                        var newOption = new Option(strategy.strategyName, strategy.slug, true, true);

                        $(newOption).attr('data-description', strategy.description);

                        // Append it to the select
                        select.append(newOption).trigger('change');
                    }
                }
            }
        });
    }

    function formatOption (option) {
        return  $(
            '<div><strong>' + option.text + '</strong></div>' +
            '<div>' + option.description + '</div>'
        );
    };

    function renderStrategyPlayGround(strategySlug, select, modelSlug) {
        const app_rest_admin_brandstrategy_getbrandstrategy = window.Routing
            .generate('app_rest_admin_brandstrategy_getbrandstrategy');

        $.ajax({
            type: "POST",
            url: app_rest_admin_brandstrategy_getbrandstrategy,
            data: {
                brand_slug: modelSlug,
                strategy_slug: strategySlug
            },
            error: (result) => {
                console.log(result.responseJSON.status);
            },
            success: (data) => {
                if (Object.keys(data).length) {
                    let strategy = data.strategy;
                    $('.render_play_ground').remove();
                    let divTagFormRow = $('<div />').addClass('form-row render_play_ground');
                    renderExecuteButton(strategy, select, divTagFormRow);
                    renderRequiredInputsPlayGround(strategy, select, divTagFormRow);
                    renderRequiredArgsPlayGround(strategy, select, divTagFormRow, data.requiredArgs);
                } else {
                    const app_rest_admin_strategies_getstrategybyslug = window.Routing
                        .generate('app_rest_admin_strategies_getstrategybyslug',
                            {'slug': strategySlug});

                    $.ajax({
                        type: "GET",
                        url: app_rest_admin_strategies_getstrategybyslug,
                        error: (result) => {
                            console.log(result.responseJSON.status);
                        },
                        success: (data) => {
                            $('.render_play_ground').remove();
                            let divTagFormRow = $('<div />').addClass('form-row render_play_ground');
                            renderExecuteButton(data, select, divTagFormRow);
                            renderRequiredInputsPlayGround(data, select, divTagFormRow);
                            renderRequiredArgsPlayGround(data, select, divTagFormRow);
                        }
                    });
                }
            }
        });
    }

    function renderExecuteButton(strategy, select, divTagFormRow)
    {
        var execute = $('<button/>',
            {
                type: "button",
                class: 'btn btn-primary execute_strategy',
                text: 'Execute Strategy'
            });

        execute.attr('data-strategy-slug', strategy.slug);
        execute.on('click', preApplyStrategyCoreAnalysis);
        divTagFormRow.append(execute);
    }

    function renderRequiredArgsPlayGround(strategy, select, divTagFormRow, existArgs = []) {
        let editBrandForm = select.closest('#editBrand');

        if ($.isArray(strategy.requiredArgs)) {
            let lengthRequiredArgs = strategy.requiredArgs.length;

            $.each(strategy.requiredArgs, function (k, render_required_args) {
                var divTag = $('<div />').addClass('form-group col-md-'+ 12/lengthRequiredArgs);
                var labelTag = $('<label />').attr('for', render_required_args);
                labelTag.text(ucfirst(render_required_args));

                let required_arg = $('<input>').attr({
                    name: render_required_args,
                    class: 'required_args form-control',
                    type: 'text'
                });

                if (existArgs && existArgs.hasOwnProperty(render_required_args)) {
                    required_arg.val(existArgs[render_required_args]);
                    required_arg.text(existArgs[render_required_args]);
                }

                required_arg.prop('required',true);
                divTag.append(labelTag).append(required_arg);
                divTagFormRow.prepend(divTag);
            });
            divTagFormRow.insertBefore(editBrandForm);
        }
    }

    function renderRequiredInputsPlayGround(strategy, select, divTagFormRow) {
        let editBrandForm = select.closest('#editBrand');

        if ($.isArray(strategy.requiredInputs)) {
            let lengthRequiredInputs = strategy.requiredInputs.length;

            $.each(strategy.requiredInputs, function (k, render_required_input) {
                var divTag = $('<div />').addClass('form-group col-md-'+ 12/lengthRequiredInputs);
                var labelTag = $('<label />').attr('for', render_required_input);
                labelTag.text(ucfirst(render_required_input));

                let required_input = $('<input>').attr({
                    name: render_required_input,
                    class: 'required_input form-control',
                    type: 'text'
                });
                required_input.prop('required',true);
                divTag.append(labelTag).append(required_input);
                divTagFormRow.prepend(divTag);
            });
            divTagFormRow.insertBefore(editBrandForm);
        }
    }

    function preApplyStrategyCoreAnalysis(e) {
        let button = $(e.currentTarget);

        // remove valiation blocks
        $('.render_play_ground .invalid-feedback').remove();

        // remove result executed core analysis blocks
        $('.core_analysis_result').remove();

        let submitInputs = $('.render_play_ground input');
        let virtualForm = $('<form />');
        if (submitInputs.length) {
            $.each(submitInputs, function (k, v) {
                let currentInput = $(v);
                if (!currentInput.val()) {
                    var divTag = $('<div />').addClass('invalid-feedback');
                    divTag.text('Please provide a valid ' + currentInput.attr('name'));
                    divTag.insertAfter(currentInput);
                } else {
                    let clone = currentInput.clone();
                    virtualForm.append(clone);
                }
            })
        }
        let invalidFeedback = $('.render_play_ground .invalid-feedback');
        if (invalidFeedback.length) {
            invalidFeedback.show();
        } else {
            let strategy_slug_input = $('<input>').attr({
                type: 'hidden',
                name: 'strategy_slug',
            });

            strategy_slug_input.val(button.attr('data-strategy-slug'));
            virtualForm.append(strategy_slug_input);

            applyStrategyCoreAnalysis(button, virtualForm);
        }
    }

    function applyStrategyCoreAnalysis(button, virtualForm) {
        let serialize = virtualForm.serialize();
        $.ajax({
            type: "POST",
            url: app_rest_admin_strategies_postapplystrategybyslug,
            data: serialize,
            error: (result) => {
                console.log(result.responseJSON.status);
            },
            success: (data) => {
                var pDiv = $('<div/>', {'class': 'col-4 core_analysis_result'});
                var pTag = $('<p/>', {'class': 'd-inline'});
                var span = $('<span />');

                if (data.result) {
                    span.append('<i class="fa fa-check" aria-hidden="true"></i>');
                } else {
                    span.append('<i class="fas fa-ban"></i>');
                }
                pTag.append(data.result).append(span);
                pDiv.append($('<p/>', {'text': 'Result: ', 'class': 'd-inline'})).append(pTag);
                pDiv.insertAfter(button);
            }
        });
    }

    function renderSimditor(modelId, form, modal, button) {
        $.each(form.find('.data_model_seo textarea'), function (k, v) {
            Simditor.locale = 'en-US';
            var editor = new Simditor({
                textarea: v,
                upload: false,
                toolbar: [
                    'title',
                    'bold',
                    'italic',
                    'underline',
                    'strikethrough',
                    'fontScale',
                    'color',
                    'ol',
                    'ul',
                    'blockquote',
                    'code',
                    'table',
                    'link',
                    'hr',
                    'indent',
                    'outdent',
                    'alignment'
                ],
                codeLanguages: [
                    {name: 'HTML,XML', value: 'html'}
                ],
            });
            // console.log($(v).attr('id'));
            let dataInForm = setDataInForm(modal.find('.modal-body #' + $(v).attr('id')),
                '.' + $(v).attr('id') + '_' + modelId
            );

            editor.setValue(dataInForm)
        });
    }

    /**
     *
     * @param formInput
     * @param classForValue
     * @returns {string}
     */
    function setDataInForm(formInput, classForValue) {
        let nkw_value = $(classForValue);
        let nkw_value_data = '';
        $.each(nkw_value, function (k, v) {
            if ($(v).hasClass('data_model_seo')) {
                nkw_value_data = $(v).html();
            } else {
                nkw_value_data = $(v).text();
            }

            if (nkw_value_data) {
                formInput.text(nkw_value_data);
                formInput.val(nkw_value_data);
                return false;
            }
        });

        return nkw_value_data;
    }

    $('.btn.btn-primary').on('click', function () {
        let editBrand = $('#editBrand');
        let inputColumns = editBrand.find('input');
        if (inputColumns.length) {
            $.each(inputColumns, function (k, v) {
                $(v).val($.trim($(v).val()));
            })
        }

        let requiredArgs = $('.required_args');
        if (requiredArgs.length) {
            $.each(requiredArgs, function (k, v) {
                let currentInput = $(v).clone();
                currentInput.attr('name', 'required_args['+currentInput.attr('name')+']');
                currentInput.attr('type', 'hidden');
                editBrand.append(currentInput)
            })
        }

        let serialize = editBrand.serialize();

        const app_rest_admin_brand_editbrand = window.Routing
            .generate('app_rest_admin_brand_editbrand');

        $.ajax({
            type: "POST",
            url: app_rest_admin_brand_editbrand,
            data: serialize,
            error: (result) => {
                editBrand.find('.required_args').remove();
                console.log(result.responseJSON.status);
            },
            success: (data) => {
                $('#exampleModalLong').modal('toggle');
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

    function initiateShopsSelect() {
        var shops_select = $('<select>').addClass('shops_select');
        shops_select.attr('name', 'shops');
        shops_select.insertBefore($('#empTable'));
        applySelect2ToShopsSelect(shops_select);
        applyOnChangeToResourceShopSelect(shops_select);
    }

    function applySelect2ToShopsSelect(select) {
        const app_rest_admin_resourceshops_resourceshops = window.Routing
            .generate('app_rest_admin_resourceshops_resourceshops');
        select.select2({
            placeholder: {
                id: '-1', // the value of the option
                text: 'Select resource shop'
            },
            dropdownAutoWidth: true,
            width: '20%',
            multiple: false,
            allowClear: true,
            templateResult: formatShopOption,
            ajax: {
                type: 'post',
                url: app_rest_admin_resourceshops_resourceshops,
                data: function (params) {

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

    function formatShopOption (option) {
        return  $(
            '<div><strong>' + option.text + '</strong></div>' +
            '<div>' + option.resource_relation + '</div>'
        );
    };

    function applyOnChangeToResourceShopSelect(shops_select) {
        shops_select.on('change', function (e) {
            if (table) {
                resource_shop_slug = $(e.currentTarget.selectedOptions).attr('value');
                table.draw();
            }
        })
    }
});