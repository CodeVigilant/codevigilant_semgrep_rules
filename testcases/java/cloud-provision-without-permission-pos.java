package com.example;

// POSITIVE: registers a cloud agent template with NO permission check (CWE-862)
public class CloudProvisionWithoutPermissionPos {
    private final CloudProvisioner clouds = new CloudProvisioner();
    public void create() {
        Object template = buildTemplate();
        clouds.addDynamicTemplate(template);   // missing checkPermission
    }
}