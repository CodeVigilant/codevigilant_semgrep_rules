public class ReproNegative {
    public String guarded(String s) {
        int to = s.indexOf("END");
        if (to < 0) {
            return s;
        }
        return s.substring(1, to);
    }

    public String literal() {
        return "fix".substring(0, 3);
    }

    public String safeSeparate(String s, String sep) {
        int start = s.indexOf(sep);
        if (start >= 0) {
            return s.substring(start);
        }
        return s;
    }
}