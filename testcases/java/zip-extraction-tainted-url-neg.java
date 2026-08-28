import hudson.tools.ZipExtractionInstaller;

// Negative: literal, hard-coded download URL. Rule must NOT fire.
public class ZipExtractionInstallerLiteralRepro {
    public void install(ToolInstallation tool, Node node, TaskListener log) throws Exception {
        ZipExtractionInstaller installer =
            new ZipExtractionInstaller(null, "https://cdn.example/jdk-17.zip", null);
        FilePath installation = installer.performInstallation(tool, node, log);
    }
}