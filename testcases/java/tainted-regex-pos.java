// Positive B1: String.matches with non-literal (config-derived) pattern
public boolean contains(Job<?, ?> job) {
    return job.getName().matches(getJobPattern());
}

// Positive B2: Pattern.compile with non-literal pattern
Pattern p = Pattern.compile(userProvidedPattern);
Matcher m = p.matcher(jobName);
