import hudson.model.Run;
import hudson.model.listeners.RunListener;
import hudson.remoting.VirtualChannel;

public class RunListenerBlockingNeg extends RunListener<Run<?, ?>> {
    @Override
    public void onFinalized(Run<?, ?> r) {
        Computer computer = Computer.currentComputer();
        VirtualChannel channel = computer.getChannel();
        // async dispatch - does not pin the executor thread
        channel.callAsync(new ProbeCallable());
        // local work only, no remoting call
        LOGGER.info("run finalized: " + r.getFullDisplayName());
    }
}
