// Positive: RSA key material from an embedded Base64 constant used for encryption
import javax.crypto.Cipher;
import java.security.KeyFactory;
import java.security.spec.X509EncodedKeySpec;
import java.util.Base64;

public class PasswordProtector {

    private static final String PUBLIC_KEY =
        "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDCD43scUktBOFoR10dS80DbFJf";

    static String encryptPassword(String password) throws Exception {
        byte[] keyRawData = Base64.getDecoder().decode(PUBLIC_KEY);
        KeyFactory keyFactory = KeyFactory.getInstance("RSA");
        KeySpec ks = new X509EncodedKeySpec(keyRawData);
        RSAPublicKey publicKey = (RSAPublicKey) keyFactory.generatePublic(ks);
        Cipher rsa = Cipher.getInstance("RSA");
        rsa.init(Cipher.ENCRYPT_MODE, publicKey);
        return Base64.getEncoder().encodeToString(rsa.doFinal(password.getBytes("UTF-16LE")));
    }
}
