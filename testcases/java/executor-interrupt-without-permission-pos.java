import hudson.model.Executor;
import hudson.model.Result;

public class ExecutorInterruptPos {
  // vulnerable: aborts whatever build is running, no ACL check anywhere
  public void doKillRunningBuild() {
    Executor executor = getExecutorOfLastBuild();
    if (executor != null) {
      executor.interrupt(Result.ABORTED);
    }
  }

  private Executor getExecutorOfLastBuild() {
    return null;
  }
}
