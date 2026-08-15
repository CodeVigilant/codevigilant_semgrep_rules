import hudson.model.Run;
import hudson.model.listeners.RunListener;
import hudson.remoting.VirtualChannel;

public class RunListenerBlockingPos extends RunListener<Run<?, ?>> {
    @Override
    public void onFinalized(Run<?, ?> r) {
        Computer computer = Computer.currentComputer();
        VirtualChannel channel = computer.getChannel();
        // blocked synchronously on the executor thread, no timeout
        channel.call(new ProbeCallable());
    }
}
