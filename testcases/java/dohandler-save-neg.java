// Negative A1: do* handler with explicit checkPermission before save()
package jenkins.advancedqueue;

public class GoodConfiguration extends GlobalConfiguration implements RootAction {

    private List<JobGroup> jobGroups;

    @RequirePOST
    public void doPriorityConfigSubmit(StaplerRequest2 req, StaplerResponse2 rsp) throws IOException, ServletException {
        Jenkins.get().checkPermission(Jenkins.ADMINISTER);
        jobGroups = new LinkedList<JobGroup>();
        save();
        FormApply.success("..").generateResponse(req, rsp, this);
    }

    // Negative A2: save() inside a plain setter (DataBoundSetter) is not a do* handler
    public void setJobGroups(List<JobGroup> jobGroups) {
        this.jobGroups = jobGroups;
        save();
    }

    // Negative A3: do* validation method that does not persist anything
    public FormValidation doCheckJobPattern(@QueryParameter String value) {
        Pattern.compile(value);
        return FormValidation.ok();
    }
}
