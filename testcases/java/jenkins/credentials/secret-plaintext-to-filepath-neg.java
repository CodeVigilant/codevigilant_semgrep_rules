// Negative repro: non-secret content written to a workspace temp file must not fire.
import hudson.FilePath;

class NegNonSecret {
    FilePath writeInventory(FilePath workspace, String content) throws Exception {
        // content is non-secret job/plain config, no Secret source.
        return workspace.createTextTempFile("inventory", ".ini", content, false);
    }
}