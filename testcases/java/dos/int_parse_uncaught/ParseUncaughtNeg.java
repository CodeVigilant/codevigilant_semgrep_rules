// NEGATIVE test case — parse guarded by try/catch, or literal argument.
// These MUST NOT fire codevigilant.java.jenkins.dos.int-parse-uncaught.
package hudson.plugins.example;

public class ParseUncaughtNeg {
    public int guardedNfe(String externalInput) {
        try {
            return Integer.parseInt(externalInput);
        } catch (NumberFormatException e) {
            return 0;
        }
    }

    public int guardedException(String externalInput) {
        try {
            return Integer.parseInt(externalInput);
        } catch (Exception e) {
            return 0;
        }
    }

    public int literalArg() {
        return Integer.parseInt("123");
    }
}