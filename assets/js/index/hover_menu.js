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
require('bootstrap-select');


import {
    getBlobFromImageUri,
    createErrorImgPlaceHolder,
    delay
} from './parts/photos_config.js';

document.addEventListener("DOMContentLoaded", function () {
    var sub_category_ids = {};
    var table;
    var global_level;
    var sections_select;

    let exampleModalLong = $('#exampleModalLong');

    const app_rest_hovermenumanagment_listthhovermenu = window.Routing
        .generate('app_rest_hovermenumanagment_listthhovermenu');
    const app_rest_hovermenumanagment_getsubcategories = window.Routing
        .generate('app_rest_hovermenumanagment_getsubcategories');
    const app_rest_hovermenumanagment_listhovermenu = window.Routing
        .generate('app_rest_hovermenumanagment_listhovermenu');

    const attachment_files = window.Routing
        .generate('app_rest_admin_attachmentfile_postattachmentfile');
    const app_rest_admin_attachmentfile_getattachmentfileslist = window.Routing
        .generate('app_rest_admin_attachmentfile_getattachmentfileslist');
    const app_rest_admin_attachmentfile_getattachmentfilestemplate = window.Routing
        .generate('app_rest_admin_attachmentfile_getattachmentfilestemplate');


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

    body.on('click', '.radio-level input[type=radio][name=change-category-level-filter]', function () {
        let input = $(this);

        global_level = input.val();

        if (global_level === '1') {
            var span = $('<span/>', {
                class: 'category-add float-right mb-3 mr-2'
            });

            var newButton = $('<button/>',
                {
                    text: 'Create new Category 1th level',
                }).addClass('new_category_1_level')
                .addClass('trigger_new_sub')
                .addClass('btn btn-success');
            span.append(newButton);
            $('.radio-level').append(span);
        } else {
            $('.category-add').remove();
        }

        if (global_level === 'all') {
            sub_category_ids = {};
        }
        let pathPresent = $('#path_present h2');
        pathPresent.empty();

        let select = $('#filter-by-top');
        select.find('option:first').prop('selected', true);
        select
            .val('all');

        if (table) {
            table
                .search('')
                .columns(0)
                .search('all')
                .draw();
        }
    });

    body.on('click', 'button.trigger_new_sub, button.trigger_edit', function () {
        let current = $(this);

        const app_rest_hovermenumanagment_getsectionlist = window.Routing
            .generate('app_rest_hovermenumanagment_getsectionlist');

        $.ajax({
            type: "GET",
            url: app_rest_hovermenumanagment_getsectionlist,
            error: (result) => {
                console.log(result);
            },
            success: (data) => {
                console.log(data);
                var divTag = $('<div />').addClass('form-group').attr('id', 'sections_select_container');
                var select = $('<select><option value="" selected disabled>Please select section</option></select>');
                select.attr({
                    'id': 'sections_list',
                    'name': 'sections_list',
                    'data-live-search': 'true'
                });
                var labelTag = $('<label />').attr('for', 'sections_list');

                labelTag.text('Sections');
                divTag.append(labelTag).append(select);

                $.each(data, function (key, value) {
                    select.append('<option value="' + value.id + '">' + value.sectionName + '</option>')
                });
                sections_select = divTag;
                exampleModalLong.modal('show', current);
            }
        });
    });

    body.on('click', 'a.trigger_sub_categories', function () {
        let aCurrentTag = $(this);
        $('.category-add').remove();
        aCurrentTag.nextAll().remove();
        let categoryId = aCurrentTag.data('categoryId');
        let categoryName = aCurrentTag.data('categoryName');
        aCurrentTag.remove();
        applySubCategories(categoryId, categoryName);
    });

    body.on('click', 'button.trigger_sub_categories', function () {
        let button = $(this);
        $('.category-add').remove();
        let categoryId = button.data('categoryId');
        let categoryName = button.data('categoryName');
        applySubCategories(categoryId, categoryName);
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
            });
            generateDataTables(columns);
        }
    });

    function generateDataTables(columns) {
        table = $('#empTable').DataTable({
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
                    select.attr({
                        'id': 'filter-by-top',
                        'name': 'filter-by-top'
                    });
                    var labelTag = $('<label />').attr('for', 'filter-by-top');
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
            'serverMethod': 'POST',
            'ajax': {
                "url": app_rest_hovermenumanagment_listhovermenu,
                "data": function (d) {
                    d.sub_categories_id = sub_category_ids;
                    if (global_level) {
                        d.level = global_level;
                    }
                }
            },
            columns: columns,
            "columnDefs": [
                {

                    "targets": 0,
                    "data": 'CategoryName',
                    "render": function (data, type, row, meta) {
                        let hotCategory = row.HotCategory;
                        let disableForParsing = row.DisableForParsing;
                        var divTag = $('<div/>');
                        var pTag = $('<p/>', {
                            "class": 'cn_' + row.id,
                            'data-section-id': row.section_relation_id
                        });
                        var span = $('<span />').addClass('hc_' + row.id).attr('hc_val', hotCategory);
                        var span_dfp = $('<span />').addClass('dfp_' + row.id).attr('dfp_val', disableForParsing);

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
                        spanPath.append('<i class="fas fa-road"></i>').append('<i>' +row.slug + '</i>');
                        pTag.append(spanPath).append('<br>').append(data).append(span).append(span_dfp);
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
                        return type === 'display' && data ?
                            '<p class="position_' + row.id + '">' + data + '</p>' : ''
                    }
                },
                {

                    "targets": 4,
                    data: 'SeoTitle',
                    render: function (data, type, row, meta) {
                        let divTagBuffer = $('<div/>');
                        let divTagContent = $('<div/>').addClass('seo_title_' + row.id).addClass('data_model_seo');

                        let parseHTML = $.parseHTML(data);
                        divTagContent.append(parseHTML);
                        divTagBuffer.append(divTagContent);
                        let result = divTagBuffer.html();

                        return type === 'display' && data ?
                            result : ''
                    }
                },
                {

                    "targets": 5,
                    data: 'SeoDescription',
                    render: function (data, type, row, meta) {
                        let divTagBuffer = $('<div/>');
                        let divTagContent = $('<div/>').addClass('seo_description_' + row.id).addClass('data_model_seo');

                        let parseHTML = $.parseHTML(data);
                        divTagContent.append(parseHTML);
                        divTagBuffer.append(divTagContent);
                        let result = divTagBuffer.html();

                        return type === 'display' && data ?
                            result : ''
                    }
                },
                {

                    "targets": 6,
                    data: 'SeoText1',
                    render: function (data, type, row, meta) {
                        let divTagBuffer = $('<div/>');
                        let divTagContent = $('<div/>').addClass('seo_text1_' + row.id).addClass('data_model_seo');

                        let parseHTML = $.parseHTML(data);
                        divTagContent.append(parseHTML);
                        divTagBuffer.append(divTagContent);
                        let result = divTagBuffer.html();

                        return type === 'display' && data ?
                            result : ''
                    }
                },
                {

                    "targets": 7,
                    data: 'SeoText2',
                    render: function (data, type, row, meta) {
                        let divTagBuffer = $('<div/>');
                        let divTagContent = $('<div/>').addClass('seo_text2_' + row.id).addClass('data_model_seo');

                        let parseHTML = $.parseHTML(data);
                        divTagContent.append(parseHTML);
                        divTagBuffer.append(divTagContent);
                        let result = divTagBuffer.html();

                        return type === 'display' && data ?
                            result : ''
                    }
                },
                {
                    "targets": 8,
                    data: 'Action',
                    render: function (data, type, row, meta) {
                        let result = '';
                        let actions = data.split(",");
                        var divTag = $('<div/>');
                        var divBtnGroup = $('<div/>').addClass('btn-toolbar');
                        $.each(actions, function (k, v) {
                            if (v === 'Sub Categories' && !row.sub_count) {
                                return;
                            }
                            let attrObj = {};
                            if (k === 0) {
                                attrObj = {
                                    "class": 'btn btn-primary m-1'
                                }
                            } else {
                                attrObj = {
                                    "class": 'btn btn-info m-1'
                                }
                            }
                            attrObj = $.extend(attrObj, {
                                'data-category-id': row.id,
                                'data-category-name': row.CategoryName,
                                'type': 'button',
                                'name': v,
                                'text': v === 'Sub Categories' ? v + ' (' + row.sub_count + ')' : v
                            });

                            let button = $('<button/>',
                                attrObj
                            );
                            button.addClass('trigger_' + v.toLowerCase().replace(' ', '_'));
                            divBtnGroup.append(button);
                        });
                        divTag.append(divBtnGroup);
                        result = divTag.html();


                        return type === 'display' ?
                            result : ''
                    }
                }
            ],
        });
    }

    function applySubCategories(categoryId, categoryName) {
        $.ajax({
            type: "POST",
            url: app_rest_hovermenumanagment_getsubcategories,
            data: {
                category_id: categoryId
            },
            error: (result) => {
                console.log(result.responseJSON.status);
            },
            success: (data) => {
                sub_category_ids = data;
                let aTag = $('<a>', {
                    'text': categoryName,
                    'title': categoryName,
                    'data-category-id': categoryId,
                    'data-category-name': categoryName,
                }).addClass('trigger_sub_categories');

                let pathPresent = $('#path_present h2');
                pathPresent
                    .append(' ');
                if (pathPresent.find('a').length > 0
                    && pathPresent.children().last().prop("tagName") !== 'svg'
                ) {
                    pathPresent
                        .append('<i class="fas fa-angle-double-right"></i>')
                }
                pathPresent
                    .append(' ')
                    .append(aTag);

                $('.radio-level input[value=all]')
                    .attr('checked', 'checked');
                global_level = 'all';

                let select = $('#filter-by-top');
                select.find('option:first').prop('selected', true);
                select
                    .val('all');

                if (table) {
                    table
                        .search('')
                        .columns(0)
                        .search('all')
                        .draw();
                }
            }
        });
    }

    exampleModalLong.on('hide.bs.modal', function (event) {
        var modal = $(this);

        let form = modal.find("form");
        form.trigger("reset");
        form.find('textarea').val('');
        form.find('.attachment_files_to_categories').remove();
        form.find('input[type=hidden]').remove();

        let sections_select_container = modal.find('.modal-body #sections_select_container');
        if (sections_select_container.length) {
            sections_select_container.remove();
        }
    });

    exampleModalLong.on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);// Button that triggered the modal
        var modal = $(this);
        let form = modal.find("form");
        let categoryId = button.data('categoryId');

        if (categoryId && button.hasClass('trigger_new_sub') === false) {
            $.ajax({
                type: "POST",
                url: app_rest_admin_attachmentfile_getattachmentfileslist,
                data: {
                    id: categoryId, entity: 'App\\Entity\\Category'
                },
                error: (result) => {
                    console.log(result.responseJSON.status);
                },
                success: (data) => {
                    renderCategoryForm(categoryId, form, modal, button);
                    renderAttachmentFilesBlock(categoryId, form, modal, button, data,
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
                        });
                }
            });
        } else {
            renderCategoryForm(categoryId, form, modal, button);
            renderAttachmentFilesBlock(categoryId, form, modal, button);
        }

    });

    function renderAttachmentFilesBlock(categoryId, form, modal, button, attachmentFiles, uppy_callback_function) {
        $.ajax({
            type: "GET",
            url: app_rest_admin_attachmentfile_getattachmentfilestemplate,
            error: (result) => {
                if (result.responseJSON.message) {
                    alert(result.responseJSON.message);
                }
            },
            success: (data) => {
                console.log(data);
                let template = data.template;
                if (template) {
                    let parseHTML = $.parseHTML(template);
                    let attachment_files_template = form.find('#attachment_files_template');
                    attachment_files_template.empty();
                    attachment_files_template.append(parseHTML);
                    let uppy = renderUppy(categoryId, form, modal, button);
                    if (attachmentFiles && uppy_callback_function) {
                        uppy_callback_function(uppy, attachmentFiles);
                    }
                }
            }
        });
    }

    function renderUppy(categoryId, form, modal, button) {
        // Import the plugins
        const Uppy = require('@uppy/core')
        const XHRUpload = require('@uppy/xhr-upload')
        const Dashboard = require('@uppy/dashboard')
        let timeOfLastAttach = new Date();
        const uppy = Uppy({
            debug: true,
            autoProceed: false,
            restrictions: {
                maxFileSize: 100097152,//100Mb
            },
            onBeforeFileAdded: (currentFile, files) => {
                //чтоб мог добавить только один файл за раз
                let currentTime = new Date();
                if ((currentTime - timeOfLastAttach) < 700) {
                    return false;
                }
                timeOfLastAttach = new Date();


                let isSameFile = files && Object.values(files).some(item => {
                    return currentFile.name === item.data.name;
                });
                if (isSameFile) {
                    alert('File already added.');
                    return false;
                }

                return currentFile;
            },
            onBeforeUpload: (currentFiles) => {

            }
        });

        uppy.use(Dashboard, {
            trigger: '.UppyModalOpenerBtn',
            inline: true,
            target: '.DashboardContainer',
            replaceTargetContent: true,
            showProgressDetails: true,
            showRemoveButtonAfterComplete: true,
            note: 'add Images',
            height: 470,
            metaFields: [
                {id: 'name', name: 'name', placeholder: 'file name'},
                {id: 'caption', name: 'caption', placeholder: 'describe what the image is about'}
            ],
            browserBackButtonClose: true
        });

        if (categoryId && button.hasClass('trigger_new_sub') === false) {
            uppy.setMeta({
                id: categoryId, entity: 'App\\Entity\\Category'
            });
        }

        uppy.use(XHRUpload, {
            endpoint: attachment_files,
            formData: true,
            fieldName: 'files[]',
            // bundle: true,
            metaFields: null,
            getResponseData(responseText, response) {
                return {
                    url: responseText
                }
            }
        });

        uppy.on('file-added', (file) => {

        });

        uppy.on('file-editor:complete', (updatedFile) => {
            // console.log(updatedFile)
        });

        uppy.on('upload-success', (file, body) => {
            let files = JSON.parse(body.body.url);
            $.each(files, function (k, v) {
                let input = $('<input>').attr({
                    type: 'hidden',
                    id: 'file_id_' + v.id,
                    name: 'file_ids[]',
                    class: 'attachment_files_to_categories'
                });
                input.val(v.id);
                form.append(input);
                uppy.setFileMeta(file.id, {m_file_id: v.id})
            })
        });

        uppy.on('error', (error) => {
            console.error(error.stack)
        });

        uppy.on('file-removed', (file, reason) => {
            if (reason === 'removed-by-user' && file.meta.m_file_id) {
                var app_rest_admin_attachmentfile_deleteattachmentfile = Routing.generate('app_rest_admin_attachmentfile_deleteattachmentfile', {'id': file.meta.m_file_id});
                $.ajax({
                    url: app_rest_admin_attachmentfile_deleteattachmentfile,
                    type: 'DELETE',
                    success: function (result) {
                        console.log(result);
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            }
        });

        uppy.on('upload-error', (file, error, response) => {
            uppy.removeFile(file.id);
            let parse = JSON.parse(response.body.url);

            uppy.info(parse.message, 'error', 5000);

            console.log('error with file:', file.id);
            console.log('error message:', error);
        });

        uppy.on('complete', result => {
            console.log('successful files:', result.successful)
            console.log('failed files:', result.failed)
        });

        return uppy;
    }

    function renderCategoryForm(categoryId, form, modal, button) {
        let cn_value = $('.cn_' + categoryId);
        modal.find('.modal-body #editCateory').append(sections_select);
        let sections_list = modal.find('.modal-body #editCateory #sections_list');
        if (sections_select) {
            let sectionId = cn_value.data('sectionId');
            if (sectionId) {
                sections_list.val(sectionId).change();
            }
            sections_list.selectpicker();
        }

        if (button.hasClass('trigger_new_sub')) {
            let input_main_category_id = $('<input>').attr({
                type: 'hidden',
                id: 'main_category_id',
                name: 'main_category_id',
                class: 'categories_exist_id'
            });
            if (button.data('categoryName')) {
                modal.find('.modal-title').text('New sub category for ' + button.data('categoryName'));
            } else {
                modal.find('.modal-title').text('New Category first level');
            }
            input_main_category_id.val(categoryId);
            form.append(input_main_category_id);
            renderSimditor(categoryId, form, modal, button);
            return true;
        }

        modal.find('.modal-title').text('Edit ' + cn_value.text() + ' category');
        setDataInForm(modal.find('.modal-body #category_name'),
            '.cn_' + categoryId);
        setDataInForm(modal.find('.modal-body #pkw'),
            '.pkw_' + categoryId);
        setDataInForm(modal.find('.modal-body #category_position'),
            '.position_' + categoryId);
        setDataInForm(modal.find('.modal-body #nkw'),
            '.nkw_' + categoryId);

        let input_category_id = $('<input>').attr({
            type: 'hidden',
            id: 'category_id',
            name: 'category_id',
            class: 'categories_exist_id'
        });

        input_category_id.val(categoryId);
        form.append(input_category_id);

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
        });

        let disableForParsing = modal.find('.modal-body #disableForParsing');
        let dfp_value = $('.dfp_' + categoryId);
        $.each(dfp_value, function (k, v) {
            let dfp_value_data = $(v).attr('dfp_val');
            if (dfp_value_data) {
                if (dfp_value_data === 'true') {
                    disableForParsing.prop("checked", true);
                }
                return false;
            }
        });

        renderSimditor(categoryId, form, modal, button);
    }

    function renderSimditor(categoryId, form, modal, button) {
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
                $(v).attr('id').replace('category_', '.') + '_' + categoryId
            );

            editor.setValue(dataInForm)
        });
    }

    function addInitPhotoToUppy(uppy, blob, isErrorPhoto = false, item) {
        let configObj = {
            name: item.originalName, // override in onBeforeFileAdded event
            type: 'image/jpeg',
            data: blob,
            source: isErrorPhoto ? 'canvasPlaceholderError' : '',
        };

        let file_id = uppy.addFile(configObj);
        uppy.setFileMeta(file_id, {m_file_id: item.id})
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
        let editCateory = $('#editCateory textarea');
        if (editCateory.length) {
            $.each(editCateory, function (k, v) {
                $(v).val($.trim($(v).val()));
            })
        }

        let serialize = $('#editCateory').serialize();
        console.log(serialize);
        const app_rest_hovermenumanagment_edithovermenu = window.Routing
            .generate('app_rest_hovermenumanagment_edithovermenu');

        $.ajax({
            type: "POST",
            url: app_rest_hovermenumanagment_edithovermenu,
            data: serialize,
            error: (result) => {
                if (result.responseJSON.message) {
                    alert(result.responseJSON.message);
                }
            },
            success: (data) => {
                console.log(data);
                $('#exampleModalLong').modal('toggle');
                table.ajax.reload(null, false);
            }
        });
    });
});
