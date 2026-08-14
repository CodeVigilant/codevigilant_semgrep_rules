// Positive A1: Descriptor.configure override persisting config without a permission check
package org.example.plugin;

import org.kohsuke.stapler.StaplerRequest;
import hudson.model.Descriptor;
import net.sf.json.JSONObject;

public class MyDescriptor extends Descriptor<MyItem> {

    @Override
    public boolean configure(StaplerRequest req, JSONObject json) throws FormException {
        bindParams(req, json);
        save();
        return super.configure(req, json);
    }
}
