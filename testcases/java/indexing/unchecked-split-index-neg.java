public class UncheckedSplitIndexNeg {
    // guarded: length checked before indexing, index 0 only
    public String guarded(String csvResults) {
        String[] lines = csvResults.split("[\\r\\n]+");
        if (lines.length < 2) {
            return "";
        }
        String[] fields = lines[1].split(",");
        if (fields.length >= 4) {
            return fields[0].trim();
        }
        return "";
    }
}