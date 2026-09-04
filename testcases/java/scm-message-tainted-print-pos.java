import hudson.model.BuildListener;
import hudson.scm.ChangeLogSet;

// Positive: commit message/author printed to the build log without sanitization
public class LogInjectionRepro {
    public void log(BuildListener listener, ChangeLogSet.Entry entry) {
        listener.getLogger().println("change: " + entry.getMsg() + " by " + entry.getAuthor());
    }
}
