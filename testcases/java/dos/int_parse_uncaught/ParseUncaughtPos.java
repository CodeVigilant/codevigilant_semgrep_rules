// POSITIVE test case — unguarded Integer/Long parse of a non-constant
// (external/streamed) string. Each of these MUST fire the rule
// codevigilant.java.jenkins.dos.int-parse-uncaught.
package hudson.plugins.example;

public class ParseUncaughtPos {
    public int parseInt(String externalInput) {
        return Integer.parseInt(externalInput);
    }

    public long parseLong(String s) {
        return Long.parseLong(s);
    }

    public Integer valueOf(String s) {
        return Integer.valueOf(s);
    }

    public int fromCtor(String s) {
        return new Integer(s);
    }
}