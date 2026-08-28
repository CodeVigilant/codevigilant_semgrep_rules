// Positive: do* handler persisting job config while checking only Item.BUILD
// (no Item.CONFIGURE check) -- the vulnerable shape.
package com.example;

import hudson.model.Item;
import hudson.model.Job;
import org.kohsuke.stapler.interceptor.RequirePOST;
import org.kohsuke.stapler.QueryParameter;

public class ParameterDescriptorImpl {
    @RequirePOST
    public String doSetDefaultValue(Job<?, ?> job, @QueryParameter String name,
        @QueryParameter String value) throws java.io.IOException {
        job.checkPermission(Item.BUILD);
        // ... mutate config ...
        job.save();
        return "ok";
    }
}