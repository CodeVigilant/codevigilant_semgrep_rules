// Positive A1: unboxing a data-bound Boolean parameter without a null guard
package org.example.plugin;

public class Config {

    private boolean useFlag;

    public Config(String a, String b, Boolean useFlag) {
        this.useFlag = useFlag.booleanValue();
    }
}
