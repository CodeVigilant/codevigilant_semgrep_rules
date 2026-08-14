// Negative B1: String.matches with a string literal pattern
public boolean contains(Job<?, ?> job) {
    return job.getName().matches(".*");
}

// Negative B2: Pattern.compile with a string literal pattern
Pattern p = Pattern.compile("^[a-z]+$");
Matcher m = p.matcher(jobName);
