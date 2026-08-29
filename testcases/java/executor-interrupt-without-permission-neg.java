import hudson.model.Executor;
import hudson.model.Item;
import hudson.model.Result;

public class ExecutorInterruptNeg {
  private final Object project;

  public ExecutorInterruptNeg(Object project) {
    this.project = project;
  }

  // fixed: permission check on the affected build's project before aborting
  public void doKillRunningBuild() {
    if (!((Item) project).hasPermission(Item.CANCEL)) {
      return;
    }
    Executor executor = getExecutorOfLastBuild();
    if (executor != null) {
      executor.interrupt(Result.ABORTED);
    }
  }

  private Executor getExecutorOfLastBuild() {
    return null;
  }
}
