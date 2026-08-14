import hudson.EnvVars;
import java.io.File;
import java.io.FileInputStream;
import org.apache.http.entity.mime.content.FileBody;

// Positive: file sink built from an env-expanded (config/parameter-influenced) path
public class FileUploadRepro {
    public void upload(EnvVars vars, String configuredPath) throws Exception {
        String resolved = vars.expand(configuredPath);
        FileBody body = new FileBody(new File(resolved));
        // ... entity.addPart("apk_file", body)
    }

    public String read(EnvVars vars, String configuredPath) throws Exception {
        String resolved = vars.expand(configuredPath);
        FileInputStream fis = new FileInputStream(resolved);
        return fis.toString();
    }
}
