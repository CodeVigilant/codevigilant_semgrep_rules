import hudson.FilePath;
import hudson.remoting.VirtualChannel;
import java.net.URL;

// Positive: copyFrom fed a non-literal URL (remote-controlled download link),
// fetched into a path built by string concatenation. Must fire on the copyFrom sink.
public class FilePathTaintedRepro {
    public void saveReport(FilePath workspace, VirtualChannel channel, String reportName, String urlSource) throws Exception {
        URL url = new URL(urlSource);
        FilePath reportFile = new FilePath(channel, workspace.getRemote() + "/" + reportName);
        reportFile.copyFrom(url);
    }
}
