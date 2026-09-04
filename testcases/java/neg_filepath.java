import hudson.FilePath;

public class Negative {
  // SAFE: literal filename, no traversal possible
  public void ok(FilePath base) throws Exception {
    FilePath t = new FilePath(base, "/fixed/jenkinsfile"); // safe
    String s = t.readToString();
  }
}