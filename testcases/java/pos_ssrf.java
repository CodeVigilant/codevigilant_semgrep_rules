import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;

public class PosS {   // private repro file
  public void go(HttpClientContext ctx, String url) throws Exception {
    HttpGet httpget = new HttpGet(url);            // SSRF non-literal
    HttpPost httppost = new HttpPost(url);          // SSRF non-literal
    httpclient.execute(httppost, ctx);
  }
}