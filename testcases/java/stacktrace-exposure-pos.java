// EXPECTED_RULES:
// codevigilant.java.jenkins.info-exposure.stacktrace-to-string-writer

// Positive repro: exception stack trace captured into an in-memory StringWriter
// whose toString() is returned from a UI getter. Rule must fire.
import java.io.PrintWriter;
import java.io.StringWriter;

public class StackTraceExposurePos {

    private transient Throwable error;

    public String getError() {
        StringWriter message = new StringWriter();
        error.printStackTrace(new PrintWriter(message));
        error = null;
        return message.toString();
    }

    private String alt() {
        StringWriter sw = new StringWriter();
        try {
            risky();
        } catch (Throwable t) {
            t.printStackTrace(new PrintWriter(sw));
            return sw.toString();
        }
        return "";
    }

    private void risky() {}
}