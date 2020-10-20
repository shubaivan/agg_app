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

document.addEventListener("DOMContentLoaded", function () {
    console.log("brand list!");
    const body = $('body');
    let exampleModalLong = $('#exampleModalLong');

    const attachment_files = window.Routing
        .generate('app_rest_admin_attachmentfile_postattachmentfile');
    const app_rest_admin_attachmentfile_getattachmentfileslist = window.Routing
        .generate('app_rest_admin_attachmentfile_getattachmentfileslist');
    const app_rest_admin_attachmentfile_getattachmentfilestemplate = window.Routing
        .generate('app_rest_admin_attachmentfile_getattachmentfilestemplate');


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
        "targets": 5,
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

    exampleModalLong.on('hide.bs.modal', function (event) {
        var modal = $(this);

        let form = modal.find("form");
        form.trigger("reset");
        form.find('textarea').val('');
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
                        });
                }
            });
        }
    });

    function renderAttachmentFilesBlock(brandId, form, modal, button, attachmentFiles, uppy_callback_function) {
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
                    let uppy = renderUppy(brandId, form, modal, button);
                    if (attachmentFiles && uppy_callback_function) {
                        uppy_callback_function(uppy, attachmentFiles);
                    }
                }
            }
        });
    }

    function renderUppy(brandId, form, modal, button) {
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

        if (brandId) {
            uppy.setMeta({
                id: brandId, entity: 'App\\Entity\\Brand'
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

    function renderEditForm(brandId, form, modal, button) {
        let bn_value = $('.bn_' + brandId);
        modal.find('.modal-title').text('Edit ' + bn_value.text() + ' brand');
        setDataInForm(modal.find('.modal-body #bn'),
            '.bn_' + brandId);

        let brand_id_input = $('<input>').attr({
            type: 'hidden',
            id: 'brand_id',
            name: 'brand_id',
            class: 'brand_exist_id'
        });

        brand_id_input.val(brandId);
        form.append(brand_id_input);

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
        });
        renderSimditor(brandId, form, modal, button);
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
                '.' + $(v).attr('id') + '_' + categoryId
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
                table.ajax.reload(null, false);
            }
        });
    })
});