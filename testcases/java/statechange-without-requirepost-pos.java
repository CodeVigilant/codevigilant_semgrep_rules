import hudson.model.AbstractProject;
import hudson.model.Action;
import hudson.model.Cause;
import hudson.model.Executor;
import hudson.model.Result;
import jenkins.model.Jenkins;
import org.kohsuke.stapler.StaplerRequest;
import org.kohsuke.stapler.StaplerResponse;

public class StateChangePos implements Action {
  private final AbstractProject<?, ?> project;

  public StateChangePos(AbstractProject<?, ?> project) {
    this.project = project;
  }

  public String getUrlName() {
    return "accelerated";
  }

  // vulnerable: no @RequirePOST -> GET-reachable state changes
  public void doBuild(final StaplerRequest request, final StaplerResponse response) {
    project.scheduleBuild2(0, new Cause.UserIdCause(), new Action[0]);
    Jenkins.getInstance().getQueue().schedule(project, 0, new Cause.UserIdCause());
    Executor executor = getExecutor();
    if (executor != null) {
      executor.interrupt(Result.ABORTED);
    }
    Jenkins.getInstance().getQueue().setSorter(null);
    response.sendRedirect(request.getContextPath() + '/' + project.getUrl());
  }

  // vulnerable: GET-reachable directory wipe, no @RequirePOST
  public hudson.util.FormValidation doClear() {
    try {
      org.apache.commons.io.FileUtils.cleanDirectory(this.activityDir);
    } catch (java.io.IOException e) {
      return hudson.util.FormValidation.error(e, "Unable clear Activity");
    }
    return hudson.util.FormValidation.ok("Done. Please refresh the page.");
  }

  private Executor getExecutor() {
    return null;
  }
}
