import hudson.FilePath;

public class Positive {
  // VULN: non-literal rel from config/workspace file content, no containment
  public void foo(FilePath base, String attackerRel) throws Exception {
    FilePath t = new FilePath(base, attackerRel);          // TRAVERSAL
    String contents = t.readToString();                    // reads ../../etc/passwd
    byte[] enc = java.util.Base64.encode(contents.getBytes());
    System.out.println(enc);
  }
}