// Positive repro: StringCredentials vault password written to a workspace temp file.
import hudson.FilePath;
import hudson.model.TaskListener;
import org.jenkinsci.plugins.plaincredentials.StringCredentials;

class PosSecretTextToTemp {
    FilePath writeVaultPassword(FilePath tmpPath, StringCredentials secretText) throws Exception {
        tmpPath.mkdirs();
        FilePath key = tmpPath.createTextTempFile("vault", ".password", secretText.getSecret().getPlainText(), true);
        key.chmod(0400);
        return key;
    }
}

// Positive repro: SSH passphrase unwrapped via Secret.toString into an askpass script.
class PosSshAskPass {
    FilePath writeAskPass(FilePath tmpPath, Object creds, hudson.util.Secret passphrase) throws Exception {
        tmpPath.mkdirs();
        StringBuilder sb = new StringBuilder();
        sb.append("#! /bin/sh\n").append("/bin/echo \"" + hudson.util.Secret.toString(passphrase) + "\"");
        FilePath script = tmpPath.createTextTempFile("ssh", ".sh", sb.toString(), true);
        script.chmod(0700);
        return script;
    }
}