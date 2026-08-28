// Negative repro: stack traces that go to a log (safe audience) or to stderr,
// or writer content that is only logged, must NOT fire.
import java.io.PrintWriter;
import java.io.StringWriter;
import java.util.logging.Logger;

public class StackTraceExposureNeg {
    private static final Logger LOGGER = Logger.getLogger("x");

    // 1. printStackTrace() default (writes to System.err) - safe.
    private void a() {
        try { risky(); } catch (Throwable t) { t.printStackTrace(); }
    }

    // 2. Captured to a writer but only the trace is logged server-side, not returned/stored.
    private String b() {
        StringWriter sw = new StringWriter();
        try { risky(); } catch (Throwable t) {
            t.printStackTrace(new PrintWriter(sw));
            LOGGER.warning(sw.toString());
            return "handled";
        }
        return "";
    }

    // 3. Writer content returned but never fed by a stack trace.
    private String c() {
        StringWriter sw = new StringWriter();
        sw.write("some message, no stack trace");
        return sw.toString();
    }

    private void risky() {}
}