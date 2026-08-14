import java.util.regex.Pattern;
import java.util.regex.Matcher;

public class ReplaceAllNeg {
    public String sanitize(String cmdln) {
        Matcher m = Pattern.compile("\\s+").matcher(cmdln);
        return m.replaceAll(" ");
    }
}
