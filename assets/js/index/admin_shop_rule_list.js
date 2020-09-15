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
import * as b_bs from 'datatables.net-buttons-bs4';

require('@fortawesome/fontawesome-free/css/all.min.css');
require('datatables.net-dt/css/jquery.dataTables.min.css');
require('datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css');
require('datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css');
require('datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css');

require('@fortawesome/fontawesome-free/js/all.js');

const $ = require('jquery');
global.$ = global.jQuery = $;
// import 'popper.js';
require('bootstrap');
require('bootstrap-select');


document.addEventListener("DOMContentLoaded", function () {
    console.log("admin shop rule list!");
    const body = $('body');

    body.on('keydown keyup onblur', 'textarea', function () {
        let input = $(this);
        if (input.length) {
            var regexp_clear_space = /\s+/g;
            if (input.val().match(regexp_clear_space)) {
                let clearValue = input.val().replace(regexp_clear_space, ' ');
                input.val(clearValue);
                var regexp = /[^a-z, ¤æøĂéëäöåÉÄÖÅ™®«»©]+/gi;
                if (clearValue.match(regexp)) {
                    input.val(clearValue.replace(regexp, ''));
                }
            }
        }
    });

    body.on('click', '.remove_block .fa-minus-square', function () {
        let current = $(this);
        let block = current.closest('.form-group');
        block.remove();
    });

    body.on('click', 'label', function () {
        let current = $(this);
        $.each(['positive', 'negative'], function (k, v) {
            if (current.hasClass(v)) {
                changeBlockType(current, v);
                return false;
            }
        });
    });

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

    let modalEditShopRules = $('#editShopRules');
    let modalNewShopRules = $('#newShopRules');
    let newShopRulesForm = modalNewShopRules.find('#newShopRulesForm');
    let shopNamesSelect = modalNewShopRules.find('.select-shop');
    let columnsSelect = modalNewShopRules.find('.block-for-select-column');


    $('.add-new-shop').on('click', function () {
        modalNewShopRules.modal('toggle');
    });

    modalNewShopRules.on('show.bs.modal', function (event) {
        var modal = $(this);
        let columnsSelect = modal.find('.block-for-select-column');
        columnsSelect.hide();
    });

    modalNewShopRules.on('hide.bs.modal', function (event) {
        var modal = $(this);
        newShopRulesForm.empty();
        let columnsSelectBlock = modal.find('.block-for-select-column');
        let columnsSelect = columnsSelectBlock.find('.select-column');
        columnsSelect.find('option:first').prop('selected',true);
        columnsSelect.selectpicker('val', '');
        columnsSelect.selectpicker('refresh');

        columnsSelectBlock.hide();

        shopNamesSelect.find('option:first').prop('selected',true);
        shopNamesSelect.selectpicker('val', '');
        shopNamesSelect.selectpicker('refresh');
    });

    shopNamesSelect.on('change', function () {
        let current = $(this);
        let selectedEl = current.find('option').filter(':selected');

        newShopRulesForm.empty();

        let hInput = $('<input type="hidden">');
        hInput.attr('id', 'shopName');
        hInput.attr('name', 'shopName');
        hInput.val(selectedEl.val());
        newShopRulesForm.append(hInput);

        columnsSelect.show();
    });

    modalEditShopRules.on('hide.bs.modal', function (event) {
        var modal = $(this);
        let editShopRules = modal.find('.modal-body #editShopRulesForm');
        editShopRules.empty();
    });

    modalEditShopRules.on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var modal = $(this);

        let shopRuleId = button.data('shopRuleId');
        modal.find('.modal-title').text('Edit ' + $('.srs_' + shopRuleId).text() + ' rules');
        let form = modal.find('#editShopRulesForm');
        let hInput = $('<input type="hidden">');
        hInput.attr('id', 'shopRuleId');
        hInput.attr('name', 'shopRuleId');
        hInput.val(shopRuleId);
        form.append(hInput);
        let pure = $('.pure-result-sr_' + shopRuleId);
        if (pure) {
            let pureData = pure.attr('pure-result');
            if(pureData) {
                let arrayRules = JSON.parse(pureData);
                $.each(arrayRules, function (k, v) {
                    if ($.isArray(v)) {
                        prepareRuleBlockForm(k, v, form);
                    } else {
                        $.each(v, function (subK, subV) {
                            prepareRuleBlockForm(subK, subV, form, k);
                        });
                    }
                })
            }
        }
    })

    $('.shop-rules-edit-select').on('change', function () {
        let current = $(this);
        let form = current.closest('.modal-body').find('form');
        var selectedVal = $(this).children('option:selected').val();
        prepareRuleBlockForm(selectedVal, null, form);
    });

    modalNewShopRules.find('.btn.btn-primary').on('click', function () {
        let selectVal = shopNamesSelect.children('option:selected').val();
        shopNamesSelect.find('[value=' + selectVal + ']').remove();
        shopNamesSelect.selectpicker('refresh');
        const apiPoint = window.Routing
            .generate('app_rest_admin_adminshoprule_createshoprules');
        sendRequest($(this), apiPoint, modalNewShopRules);
    });

    modalEditShopRules.find('.btn.btn-primary').on('click', function () {
        const apiPoint = window.Routing
            .generate('app_rest_admin_adminshoprule_editshoprules');
        sendRequest($(this), apiPoint, modalEditShopRules);
    });

    function sendRequest(current, apiPoint, modalObject) {
        let form = current.closest('.modal-content').find('form');
        let formTextAreas = form.find('textarea');
        if (formTextAreas.length) {
            $.each(formTextAreas, function (k, v) {
                $(v).val($.trim($(v).val()));
            })
        }

        let serialize = form.serialize();

        $.ajax({
            type: "POST",
            url: apiPoint,
            data: serialize,
            error: (result) => {
                console.log(result.responseJSON.status);
            },
            success: (data) => {
                console.log(data);
                modalObject.modal('toggle');
                table.ajax.reload();
            }
        });
    }

    /**
     *
     * @param str
     * @param force
     * @returns {string}
     */
    function ucfirst(str,force){
        str=force ? str.toLowerCase() : str;
        return str.replace(/(\b)([a-zA-Z])/,
            function(firstLetter){
                return   firstLetter.toUpperCase();
            });
    }

    /**
     *
     * @param current
     * @param oldClassName
     * @param newClassName
     */
    function changeBlockType(current, oldClassName, newClassName = 'positive') {
        let iTag = $('<i/>');
        if (oldClassName === 'positive') {
            newClassName = 'negative';
        }
        current.removeClass(oldClassName);
        current.addClass(newClassName);
        current.find('svg').remove();
        if (newClassName === 'positive') {
            iTag.addClass('fas fa-search-plus');
        } else {
            iTag.addClass('fas fa-search-minus');
        }

        current.append(iTag);

        let block = current.closest('.form-group');
        let inputTextArea = block.find('textarea');
        if (inputTextArea) {
            let textAreaName = inputTextArea.attr('name');
            inputTextArea.attr('name', textAreaName.replace(oldClassName, newClassName));
        }
    }

    /**
     *
     * @param k
     * @param v
     * @param form
     * @param parentK
     */
    function prepareRuleBlockForm(k, v, form, parentK) {
        var divTag = $('<div/>', {'class': "form-group"});
        let smallText = 'Fill words';
        let iTag = $('<i/>');
        let negative = false;
        if (~k.indexOf("!")) {
            k = k.replace('!', '');
            iTag.addClass('fas fa-search-minus');
            smallText = smallText + ' for exclude';
            negative = true;
        } else {
            iTag.addClass('fas fa-search-plus');
            smallText = smallText + ' for match'
        }
        let identityData = '';
        if (parentK) {
            identityData = k + '_' + parentK + '_input';
        } else {
            identityData = k + '_input';
        }

        let checkIdentityDataElements = $('.' + identityData);
        let identityDataId = identityData + '_' + checkIdentityDataElements.length;

        let columnName = ucfirst(k, true);

        // prepare label with minus\plus opportunity
        var divTagRow = $('<div/>', {'class': "row"});
        var divTagColLabel = $('<div/>', {'class': "col"});
        var divTagColMinus = $('<div/>', {'class': "col text-right remove_block"});

        divTagColMinus.append('<i>Remove rule </i>').append('<i class="fas fa-minus-square"></i>');
        let label = $("<label>").addClass(negative ? 'negative' : 'positive');

        if (parentK) {
            let span = $("<span>");
            span
                .append(ucfirst(parentK,true))
                .append('<i class="fa fa-arrow-right" aria-hidden="true"></i>')
                .append(columnName);

            smallText = smallText + ' in ' + ucfirst(parentK,true) + ' column in ' + columnName + ' key';
            label.attr({'for': identityDataId}).append(span);
        } else {
            label.attr({'for': identityDataId}).text(columnName);
            smallText = smallText + ' in ' + columnName + ' column';
        }

        label.append(iTag);
        divTagColLabel.append(label);

        divTagRow.append(divTagColLabel).append(divTagColMinus);
        // finish

        //append block with label and minus\plus opportunity
        divTag.append(divTagRow);


        // create input
        let input = $('<textarea>', {
            id: identityDataId,
            'class': 'form-control ' + identityData,
            'aria-describedby': identityDataId + '_kwHelp',
            'rows': '3'
        });
        let inputName = 'positive';
        if (negative) {
            inputName = 'negative'
        }
        let generateInputName = inputName;
        if(parentK) {
            generateInputName = generateInputName + '[' + k + ']' + '[' + parentK + ']';
        } else {
            generateInputName = generateInputName + '[' + k + '][]';
        }

        input.attr({
            'name': generateInputName,
        });

        if (v) {
            input.val(v.join(','));
            input.text(v.join(','));
        }

        divTag.append(input);
        let small = $("<small>", {
            'id': identityDataId + '_kwHelp',
            'class': 'form-text text-muted'
        }).text(smallText);
        divTag.append(small);

        form.append(divTag);
    }
});