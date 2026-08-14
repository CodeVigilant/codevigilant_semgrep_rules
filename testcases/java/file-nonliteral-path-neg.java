import java.io.File;
import java.io.FileInputStream;
import org.apache.http.entity.mime.content.FileBody;

// Negative: literal paths only -> must NOT fire
public class FileLiteralRepro {
    public void upload() throws Exception {
        FileBody body = new FileBody(new File("/var/lib/jenkins/workspace/app.apk"));
        FileInputStream fis = new FileInputStream("/etc/hostname");
        String s = fis.toString();
    }
}
