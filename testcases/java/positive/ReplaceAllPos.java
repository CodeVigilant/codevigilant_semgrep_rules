import java.util.Map;
import java.util.regex.Pattern;
import java.util.regex.Matcher;

public class ReplaceAllPos {
    public String expand(String cmdln, Map<String, String> envMap) {
        final Pattern expr = Pattern.compile("\\$\\{([A-Za-z_][A-Za-z0-9_]*)\\}");
        Matcher matcher = expr.matcher(cmdln);
        while (matcher.find()) {
            String envValue = envMap.get(matcher.group(1).toUpperCase());
            if (envValue == null) {
                envValue = "";
            }
            Matcher subexpr = Pattern.compile(Pattern.quote(matcher.group(0))).matcher(cmdln);
            cmdln = subexpr.replaceAll(envValue);
        }
        return cmdln;
    }
}
