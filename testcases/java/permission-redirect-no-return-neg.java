import hudson.model.AbstractProject;
import hudson.model.Item;
import org.kohsuke.stapler.StaplerRequest;
import org.kohsuke.stapler.StaplerResponse;

public class PermissionRedirectNeg {
  private final AbstractProject<?, ?> project;

  public PermissionRedirectNeg(AbstractProject<?, ?> project) {
    this.project = project;
  }

  // fixed: terminal return after the redirect
  public void doBuild(final StaplerRequest request, final StaplerResponse response) {
    if (!project.hasPermission(Item.BUILD)) {
      response.sendRedirect(request.getContextPath() + '/' + project.getUrl());
      return;
    }
    project.scheduleBuild2(0, new Cause.UserIdCause(), new Action[0]);
    response.sendRedirect(request.getContextPath() + '/' + project.getUrl());
  }
}
