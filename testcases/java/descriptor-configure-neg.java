// Negative A1: configure override with explicit permission check before save()
package org.example.plugin;

import org.kohsuke.stapler.StaplerRequest;
import hudson.model.Descriptor;
import jenkins.model.Jenkins;
import net.sf.json.JSONObject;

public class MyDescriptor extends Descriptor<MyItem> {

    @Override
    public boolean configure(StaplerRequest req, JSONObject json) throws FormException {
        Jenkins.get().checkPermission(Jenkins.ADMINISTER);
        bindParams(req, json);
        save();
        return super.configure(req, json);
    }

    // Negative A2: no-arg property-style accessor is not the configure override
    public boolean configure() {
        return true;
    }
}
