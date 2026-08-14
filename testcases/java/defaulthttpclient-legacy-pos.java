import org.apache.http.impl.client.DefaultHttpClient;

// Positive: legacy client constructed without TLS/timeout hardening
public class LegacyClientRepro {
    public DefaultHttpClient make() {
        return new DefaultHttpClient();
    }
}
