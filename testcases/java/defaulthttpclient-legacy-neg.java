import org.apache.http.impl.client.HttpClientBuilder;
import org.apache.http.impl.client.HttpClients;

// Negative: modern builder-based clients -> must NOT fire
public class ModernClientRepro {
    public org.apache.http.client.HttpClient make() {
        return HttpClientBuilder.create()
            .setConnectionTimeToLive(java.util.concurrent.TimeUnit.SECONDS.toMillis(30), java.util.concurrent.TimeUnit.SECONDS)
            .build();
    }
}
