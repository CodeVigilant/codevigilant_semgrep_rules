import hudson.Launcher;

public class PositiveRepro {
    public void vulnerable(Launcher launcher, String attackerControlled) throws Exception {
        Launcher.ProcStarter ps = launcher.new ProcStarter();
        // attackerControlled: SCM content, build param, env var, remote response
        String cmd = "/opt/analyzer -b " + attackerControlled + " --version-file /tmp/v.txt";
        ps.cmdAsSingleString(cmd);
        launcher.launch(ps);
    }
}
