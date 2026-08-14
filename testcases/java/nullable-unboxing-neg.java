// Negative A1: unboxing guarded by a null check
package org.example.plugin;

public class Config {

    private boolean useFlag;

    public Config(String a, String b, Boolean useFlag) {
        if (useFlag != null) {
            this.useFlag = useFlag.booleanValue();
        }
    }

    // Negative A2: unboxing a compile-time constant, not a parameter
    public boolean constant() {
        return Boolean.TRUE.booleanValue();
    }
}
