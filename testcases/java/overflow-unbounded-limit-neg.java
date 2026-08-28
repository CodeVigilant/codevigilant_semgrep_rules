// Negative repro: safe/literal or overflow-guarded computations. Rule must NOT fire.

// 1. Compile-time constants only (no runtime operands).
public class UnboundedLimitNegativeLiterals {
    private boolean check() {
        long memory = 1024L * 1024L;     // literal * literal — no overflow concern
        return freeBytes() < memory;
    }
}

// 2. Overflow-guarded with Math.multiplyExact.
public class UnboundedLimitNegativeExact {
    private boolean check() {
        long disk = Math.multiplyExact(getDisk(), getUnitOrigin());
        return freeBytes() < disk;
    }
}

// 3. Non-limit variable name (not a resource cap).
public class UnboundedLimitNegativeOtherName {
    private boolean check() {
        long total = getA() * getB();    // name not a limit/cap -> not matched
        return freeBytes() < total;
    }
}

// Helper stubs so the file parses.
class Balance {
    public long getMemory() { return 0; }
    public Unit getMemoryUnit() { return null; }
    public long getDisk() { return 0; }
    public Unit getDiskUnit() { return null; }
    public long getA() { return 0; }
    public long getB() { return 0; }
}
class Unit {
    public long getOrigin() { return 0; }
}