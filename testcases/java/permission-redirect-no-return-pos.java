import hudson.model.AbstractProject;
import hudson.model.Item;
import org.kohsuke.stapler.StaplerRequest;
import org.kohsuke.stapler.StaplerResponse;

public class PermissionRedirectPos {
  private final AbstractProject<?, ?> project;

  public PermissionRedirectPos(AbstractProject<?, ?> project) {
    this.project = project;
  }

  // vulnerable: redirect without return -> code below runs for un-authorized users
  public void doBuild(final StaplerRequest request, final StaplerResponse response) {
    if (!project.hasPermission(Item.BUILD)) {
      response.sendRedirect(request.getContextPath() + '/' + project.getUrl());
    }
    project.scheduleBuild2(0, new Cause.UserIdCause(), new Action[0]);
    response.sendRedirect(request.getContextPath() + '/' + project.getUrl());
  }
}
