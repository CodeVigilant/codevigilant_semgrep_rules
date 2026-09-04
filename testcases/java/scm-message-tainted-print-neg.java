import hudson.model.BuildListener;
import hudson.scm.ChangeLogSet;

// Negative: sanitized before print / literal only -> must NOT fire
public class LogSafeRepro {
    public void log(BuildListener listener, ChangeLogSet.Entry entry) {
        String clean = entry.getMsg().replaceAll("[\\x00-\\x1F\\x7F]", "");
        listener.getLogger().println("change: " + clean);
        listener.getLogger().println("change: fixed literal message");
    }
}
