public class Repro {
    // format string without shell operators: not a shell body
    String n1 = String.format("Hello %s, welcome to %s", "a", "b");

    // shell command string but no format placeholder: not a format-body
    String n2 = "nohup java -jar agent.jar &> /tmp/log1.txt & ";

    // format placeholder with no shell operator
    String n3 = String.format("url=%s", baseUrl);
}