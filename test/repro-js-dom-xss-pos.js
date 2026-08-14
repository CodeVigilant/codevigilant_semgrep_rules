// POSITIVE repro for codevigilant.javascript.wordpress.xss.dom.jquery_html_concat_taint
// Unescaped server-side value concatenated into a jQuery HTML sink.
jQuery(".list_wrapper").each(function () {
    var $list = jQuery(this).find(".list");
    items.forEach(function (item) {
        $list.append(
            "<div class='item'>" +
            "<label for='" + item.id + "'>" +
            item.name +
            "</label></div>"
        );
    });
    jQuery("#status").html("<b>" + response.data.message + "</b>");
    jQuery("#nav").prepend("<li>" + data.label + "</li>");
});
