// Negative 1: do* handler with explicit checkPermission before serving
import org.kohsuke.stapler.StaplerRequest;
import org.kohsuke.stapler.StaplerResponse;

public class GoodReportAction {
    public void doDynamic(StaplerRequest req, StaplerResponse rsp) throws IOException {
        build.checkPermission(Run.READ);
        File file = new File(basePath, req.getRestOfPath());
        FileInputStream fis = new FileInputStream(file);
        rsp.serveFile(req, fis, file.lastModified(), 0, file.length(), "application/force-download");
    }
}

// Negative 2: FileInputStream in a helper method (not a do* handler)
public class Helper {
    private FileInputStream open(String name) throws IOException {
        return new FileInputStream(name);
    }
}
