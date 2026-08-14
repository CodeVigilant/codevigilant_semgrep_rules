// Positive 1: do* handler serving a file with no permission check
import org.kohsuke.stapler.StaplerRequest;
import org.kohsuke.stapler.StaplerResponse;

public class ReportAction {
    public void doDynamic(StaplerRequest req, StaplerResponse rsp) throws IOException {
        String path = req.getRestOfPath();
        File file = new File(basePath, path);
        FileInputStream fis = new FileInputStream(file);
        rsp.serveFile(req, fis, file.lastModified(), 0, file.length(), "application/force-download");
    }
}

// Positive 2: do* handler opening a ZipFile with no permission check
public class ArchiveAction {
    public void doDownload(StaplerRequest req, StaplerResponse rsp) throws Exception {
        ZipFile archive = new ZipFile(archivePath);
        rsp.serveFile(req, archive.getInputStream(archive.getEntry("index.htm")), 0, 0, 0, "index.htm");
    }
}
