package com.example;

// NEGATIVE: cloud provisioning guarded by an explicit permission check
public class CloudProvisionWithoutPermissionNeg {
    private final CloudProvisioner clouds = new CloudProvisioner();
    public void create() {
        Jenkins.get().checkPermission(Jenkins.ADMINISTER);
        Object template = buildTemplate();
        clouds.addDynamicTemplate(template);
    }
    public void create2() {
        Jenkins.get().checkPermission(Jenkins.ADMINISTER);
        clouds.provision(buildTemplate());
    }
}