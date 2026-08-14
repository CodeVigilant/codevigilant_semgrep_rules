// Negative 1: RSA key loaded from a KeyStore (secure source)
import javax.crypto.Cipher;
import java.security.KeyStore;
import java.security.cert.Certificate;

public class KeyStoreProtector {
    static String encryptWithStoredKey(String password) throws Exception {
        KeyStore ks = KeyStore.getInstance("JKS");
        ks.load(stream, storePass);
        Certificate cert = ks.getCertificate("alias");
        Cipher rsa = Cipher.getInstance("RSA");
        rsa.init(Cipher.ENCRYPT_MODE, cert.getPublicKey());
        return Base64.getEncoder().encodeToString(rsa.doFinal(password.getBytes()));
    }
}

// Negative 2: direct literal key spec without RSA cipher usage
public class Other {
    void load() throws Exception {
        KeySpec ks = new X509EncodedKeySpec("MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQ==".getBytes());
    }
}
