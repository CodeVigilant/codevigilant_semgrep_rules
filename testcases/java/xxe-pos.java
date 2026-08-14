import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import java.io.File;

// POSITIVE: unhardened factories parsing untrusted XML (both rules must fire)
public class XxePos {
  public void parseChangelog(File changelogFile) throws Exception {
    DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
    DocumentBuilder builder = factory.newDocumentBuilder();
    org.w3c.dom.Document doc = builder.parse(changelogFile); // XXE

    TransformerFactory tf = TransformerFactory.newInstance();
    Transformer t = tf.newTransformer(); // XXE
  }
}
