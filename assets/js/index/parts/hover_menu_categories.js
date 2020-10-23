export function generateCategoryView(state) {
    if (state.element ) {
        let currentOption = $(state.element);
        if (currentOption.length) {
            if (state.slug === undefined
                && currentOption.data('slug')
            ) {
                state.slug = currentOption.data('slug');
            }
            if (state.hotCategory === undefined
                && currentOption.data('hot-category')
            ) {
                state.hotCategory = currentOption.data('hot-category');
            }
            if (state.section_relation_id === undefined
                && currentOption.data('section-relation')
            ) {
                state.section_relation_id = currentOption.data('section-relation');
            }
            if (state.disableForParsing === undefined
                && currentOption.data('disable-for-parsing')
            ) {
                state.disableForParsing = currentOption.data('disable-for-parsing');
            }
        }
    }
    let hotCategory = state.hotCategory || state.HotCategory;
    let disableForParsing = state.disableForParsing || state.DisableForParsing;
    var commonSpan = $('<span/>');
    var pTag = $('<p/>', {
        "class": 'cn_' + state.id
    });
    if (state.section_relation_id) {
        pTag.attr('data-section-id', state.section_relation_id);
    }
    var span = $('<span />').addClass('hc_' + state.id).attr('hc_val', hotCategory);
    var span_dfp = $('<span />').addClass('dfp_' + state.id).attr('dfp_val', disableForParsing);

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
    if (state.CategoryName) {
        pTag.append(state.CategoryName);
    }
    if (state.text) {
        pTag.append(state.text);
    }
    pTag.append(span).append(span_dfp);
    commonSpan.append(pPathTag).append(pTag);

    return commonSpan;
}