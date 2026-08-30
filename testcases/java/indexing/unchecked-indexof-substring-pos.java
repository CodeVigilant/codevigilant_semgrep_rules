public class ReproPositive {
    public String extractInline(String line) {
        return line.substring(line.indexOf(":") + 1, line.indexOf("END"));
    }

    public String extractInlineFrom(String s) {
        return s.substring(1, s.indexOf('>', 2));
    }
}