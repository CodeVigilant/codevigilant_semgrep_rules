// POSITIVE: markup built by concatenating a non-literal value between tags.
// The value is attacker/build-influenced and NOT XML-escaped -> markup injection.
class Pos {
    String buildTwiML(String message, String projectName) {
        // TwiML (voice) document built via raw string concatenation
        String twiml = "<Response><Say>" + message + "</Say></Response>";
        return twiml;
    }

    String buildHtml(String label) {
        return "<option value=\"" + label + "\">" + label + "</option>";
    }

    String buildNested(String content) {
        return "<div>" + content + "</div>";
    }
}
