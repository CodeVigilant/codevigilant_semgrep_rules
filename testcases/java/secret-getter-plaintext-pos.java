import hudson.util.Secret;

public class PosGetter {
    private final Secret accessKeySecret;

    public PosGetter(Secret accessKeySecret) {
        this.accessKeySecret = accessKeySecret;
    }

    public String getAccessKeySecret() {
        return accessKeySecret.getPlainText();
    }

    String getApiToken() {
        return apiToken.getPlainText();
    }

    // Secret.toString() decrypts; same exposure as getPlainText().
    public String getStagingPW() {
        return helper.stagingPW.toString();
    }
}