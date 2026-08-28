import hudson.tools.ZipExtractionInstaller;

// Positive: ZipExtractionInstaller fed a non-literal download URL resolved from
// remote metadata at runtime. Rule must fire on the construction sink.
public class ZipExtractionInstallerTaintedRepro {
    public void install(ToolInstallation tool, Node node, TaskListener log,
                        String binaryLink, Object toolInstaller) throws Exception {
        ZipExtractionInstaller installer = new ZipExtractionInstaller(null, binaryLink, null);
        FilePath installation = installer.performInstallation(tool, node, log);
    }
}