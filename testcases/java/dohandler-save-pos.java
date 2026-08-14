// Positive A1: do* handler persisting config without permission check (the vulnerable shape)
package jenkins.advancedqueue;

public class PriorityConfiguration extends GlobalConfiguration implements RootAction {

    private List<JobGroup> jobGroups;

    @RequirePOST
    public void doPriorityConfigSubmit(StaplerRequest2 req, StaplerResponse2 rsp) throws IOException, ServletException {
        if (!checkActive()) {
            FormApply.success("..").generateResponse(req, rsp, this);
            return;
        }
        jobGroups = new LinkedList<JobGroup>();
        String parameter = req.getParameter("json");
        JSONObject jobGroupsObject = JSONObject.fromObject(parameter);
        jobGroups.add(req.bindJSON(JobGroup.class, jobGroupsObject));
        save();
        FormApply.success("..").generateResponse(req, rsp, this);
    }
}
