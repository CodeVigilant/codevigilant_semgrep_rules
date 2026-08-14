import hudson.FilePath;
import hudson.remoting.VirtualChannel;
import java.net.URL;

// Negative: literal URLs, FilePath-to-FilePath copies and child()-derived paths -> must NOT fire
public class FilePathLiteralRepro {
    public void safe(FilePath workspace, VirtualChannel channel, FilePath other) throws Exception {
        FilePath literal = new FilePath(channel, "/var/lib/jenkins/workspace");
        FilePath child = workspace.child("report.html");
        literal.copyFrom(new URL("https://acunetix.example/reports/1.html"));
        child.copyFrom(other); // FilePath-to-FilePath copy, not a URL fetch
    }
}
