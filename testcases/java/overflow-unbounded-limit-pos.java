// EXPECTED_RULES:
// codevigilant.java.jenkins.overflow.unbounded-limit-multiplication

// Positive repro: a long resource limit derived by multiplying two runtime
// getter values, then compared with '<' against a live capacity metric to gate
// a load/quota decision. Rule must fire on the multiplication assignment.
public class UnboundedLimitOverflowRepro {
    private boolean canTake(long freeMetric) {
        Balance b = getBalance();
        long memory = b.getMemory() * b.getMemoryUnit().getOrigin();
        long disk = b.getDisk() * b.getDiskUnit().getOrigin();
        if (freeMetric < memory) {
            return false;
        }
        if (freeDiskSpace() < disk) {
            return false;
        }
        return true;
    }
}