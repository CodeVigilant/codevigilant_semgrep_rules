private Object resolveProject(String name) {
    Object project = Util.getInstance().getItem(name, Util.getInstance(), AbstractProject.class);
    if (project == null) {
        project = Util.getInstance().getItem(name, Util.getInstance(), WorkflowJob.class);
    }
    return project;
}

// SAFE: 4-arg form with an explicit permission (must NOT be matched)
private Object resolveProjectSafe(String name, Class<?> type) {
    Object project = Jenkins.get().getItem(name, Jenkins.get(), type, Jenkins.READ);
    return project;
}