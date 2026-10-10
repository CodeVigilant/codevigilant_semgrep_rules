import hudson.util.Secret;

public class NegGetter {
    private final Secret accessKeySecret;

    public NegGetter(Secret accessKeySecret) {
        this.accessKeySecret = accessKeySecret;
    }

    // Correct Jenkins pattern: getter returns the Secret object, not plaintext.
    public Secret getAccessKeySecret() {
        return accessKeySecret;
    }

    // Non-secret getter, not a credential.
    public String getDisplayName() {
        return "Aliyun OSS Uploader";
    }

    // toString() on a non-credential receiver must not fire.
    public String getGroupId() {
        return groupId.toString();
    }
}

public class NegPlaintextStorage {
    // Stored as plain String — covered by the field rules (PR #92/#140), not this
    // getter rule. This file is only a negative for the getPlainText getter shape.
    private final String accessKeySecret;

    public String getAccessKeySecret() {
        return accessKeySecret;
    }
}