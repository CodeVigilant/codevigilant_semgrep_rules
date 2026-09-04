// NEGATIVE: concatenated values are either static literals or explicitly
// XML-escaped before embedding -> no injection.
class Neg {
    String safeLiteral() {
        return "<b>" + "static text" + "</b>";
    }

    String safeEscaped(String message) {
        return "<Response><Say>" + message.replace("<", "&lt;") + "</Say></Response>";
    }

    String safeEscapedAll(String message) {
        return "<div>" + message.replaceAll("[<>&]", "") + "</div>";
    }

    String safeBuilder(String message) {
        // proper escaping at insertion time
        StringBuilder sb = new StringBuilder("<Response><Say>");
        sb.append(message.replace("<", "&lt;"));
        sb.append("</Say></Response>");
        return sb.toString();
    }

    String notMarkup(String url) {
        // string does not start with '<' or end with '>' as a tag fragment
        return "http://example.com/?q=" + url;
    }
}
