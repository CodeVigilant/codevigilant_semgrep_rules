import org.apache.http.client.methods.HttpGet;

public class NegS {
  public void ok() throws Exception {
    HttpGet httpget = new HttpGet("http://engine.internal/v2/images"); // literal
    httpclient.execute(httpget);
  }
}