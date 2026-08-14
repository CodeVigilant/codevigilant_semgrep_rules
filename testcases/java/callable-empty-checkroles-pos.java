import hudson.remoting.Callable;
import org.jenkinsci.remoting.RoleChecker;
import java.io.Serializable;

// Positive: Callable whose checkRoles() override is empty -> no role verification
public class UnsafeCallable implements Callable<String, Exception>, Serializable {
    @Override
    public void checkRoles(RoleChecker roleChecker) throws SecurityException {}

    @Override
    public String call() throws Exception {
        return "ran without role check";
    }
}

// Positive: variant without @Override and without throws clause
class UnsafeCallable2 implements Callable<String, Exception>, Serializable {
    public void checkRoles(RoleChecker rc) {}

    public String call() throws Exception {
        return "ran without role check";
    }
}
