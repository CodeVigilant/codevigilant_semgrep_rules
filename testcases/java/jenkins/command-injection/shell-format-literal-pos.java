public class Repro {
    // shell body string with a format placeholder -> interpolated then executed
    String scriptTemplate =
            "echo hi\n" +
            "curl %s/jnlp/agent.jar -o agent.jar\n" +
            "nohup %s &> /tmp/log1.txt & \n";

    String p2 = "sh -c 'curl %s && start %s'";
}