import hudson.remoting.Callable;
import org.jenkinsci.remoting.RoleChecker;
import org.jenkinsci.remoting.Roles;
import java.io.Serializable;

// Negative: checkRoles() performs a real role check -> must NOT fire
public class SafeCallable implements Callable<String, Exception>, Serializable {
    @Override
    public void checkRoles(RoleChecker roleChecker) throws SecurityException {
        roleChecker.check(this, Roles.ROLE_MASTER);
    }

    @Override
    public String call() throws Exception {
        return "ok";
    }
}
