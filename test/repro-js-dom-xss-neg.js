// NEGATIVE repro for codevigilant.javascript.wordpress.xss.dom.jquery_html_concat_taint
// Values are escaped before reaching the HTML sink.
jQuery(".list_wrapper").each(function () {
    var $list = jQuery(this).find(".list");
    items.forEach(function (item) {
        // Escaped via jQuery text() before insertion into the HTML string
        $list.append("<label>" + $("<span>").text(item.name).html() + "</label>");
        // Escaped with esc_html()
        jQuery("#status").html("<b>" + esc_html(response.data.message) + "</b>");
        // DOM node built with .text(), no string concatenation
        var $li = $("<li>").text(item.name);
        jQuery("#nav").append($li);
        // No user data involved
        jQuery("#static").append("<p>Hello</p>");
    });
});
