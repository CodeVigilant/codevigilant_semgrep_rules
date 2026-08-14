import hudson.Launcher;

public class NegativeRepro {
    public void safe(Launcher launcher) throws Exception {
        Launcher.ProcStarter ps = launcher.new ProcStarter();
        ps.cmdAsSingleString("/opt/analyzer -b arm --version-file /tmp/v.txt");
        launcher.launch(ps);
    }
}
