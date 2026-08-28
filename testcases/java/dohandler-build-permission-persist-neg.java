// Negative: do* handler persisting config WITH an Item.CONFIGURE check (safe),
// and a non-do method that saves config.
package com.example;

import hudson.model.Item;
import hudson.model.Job;
import org.kohsuke.stapler.interceptor.RequirePOST;
import org.kohsuke.stapler.QueryParameter;

public class GoodDescriptorImpl {
    @RequirePOST
    public String doSetDefaultValue(Job<?, ?> job, @QueryParameter String name,
        @QueryParameter String value) throws java.io.IOException {
        job.checkPermission(Item.CONFIGURE);
        job.save();
        return "ok";
    }

    // Non-do method: not a Stapler-routed handler.
    public void updateJob(Job<?, ?> job) {
        job.checkPermission(Item.BUILD);
        job.save();
    }
}