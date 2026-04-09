# CWE Reference

All CWE (Common Weakness Enumeration) identifiers used across the semgrep rules in this repository.

| CWE ID | Name | Description |
|--------|------|-------------|
| CWE-20 | Improper Input Validation | Software does not validate or incorrectly validates input that can affect control flow or data flow. |
| CWE-22 | Path Traversal | User input controls file paths without proper validation, allowing access to files outside intended directories. |
| CWE-73 | External Control of File Name or Path | Allowing user input to control file paths can lead to unauthorized file access or inclusion. |
| CWE-77 | Command Injection | User input is used to construct system commands without proper neutralization, enabling arbitrary command execution. |
| CWE-78 | OS Command Injection | User-supplied data is used to construct OS commands without proper neutralization, enabling arbitrary command execution. |
| CWE-79 | Cross-site Scripting (XSS) | User-controllable input is included in web output without proper encoding, allowing script injection in browsers. |
| CWE-89 | SQL Injection | User input is inserted into SQL queries without proper sanitization, enabling unauthorized database operations. |
| CWE-94 | Code Injection | User input is incorporated into dynamically generated code that is then executed by the application. |
| CWE-95 | Eval Injection | User input is passed to an eval()-style function, allowing execution of arbitrary code. |
| CWE-98 | PHP Remote File Inclusion | User input controls a filename in a PHP include/require statement, enabling remote code execution. |
| CWE-113 | HTTP Header Injection | User input is used in HTTP headers without neutralizing CRLF sequences, enabling response splitting attacks. |
| CWE-117 | Log Injection | User input is written to log files without sanitization, allowing forged log entries via newline injection. |
| CWE-129 | Improper Validation of Array Index | An array index from untrusted input is not properly validated, potentially causing out-of-bounds access. |
| CWE-200 | Exposure of Sensitive Information | The application unintentionally reveals sensitive data to actors not authorized to access it. |
| CWE-209 | Error Message Information Disclosure | Error messages contain sensitive information like file paths, stack traces, or database details. |
| CWE-250 | Execution with Unnecessary Privileges | The software operates with more privileges than required, increasing the impact of a compromise. |
| CWE-276 | Incorrect Default Permissions | Resources are created with overly permissive default access rights. |
| CWE-312 | Cleartext Storage of Sensitive Information | Sensitive data is stored without encryption, making it readable to anyone with access to the storage. |
| CWE-327 | Weak Cryptographic Algorithm | The application uses broken or risky cryptographic algorithms like MD5 or SHA1 for security operations. |
| CWE-330 | Weak Randomness | The application uses insufficiently random values for security-sensitive operations like tokens or nonces. |
| CWE-352 | Cross-Site Request Forgery (CSRF) | The application does not verify that requests originated from the legitimate user's intended action. |
| CWE-362 | Race Condition | Concurrent execution of code accesses shared resources in a way that produces unexpected behavior. |
| CWE-384 | Session Fixation | The application does not invalidate or regenerate session identifiers, allowing attackers to hijack sessions. |
| CWE-400 | Uncontrolled Resource Consumption | The application does not properly limit resource usage, enabling denial of service through resource exhaustion. |
| CWE-434 | Unrestricted File Upload | The application allows uploading files without validating type or content, enabling execution of malicious files. |
| CWE-470 | Unsafe Reflection | User input selects classes or functions to invoke dynamically, enabling arbitrary code execution. |
| CWE-473 | PHP Special Character Injection | PHP-specific special characters in user input are not properly handled, leading to unexpected behavior. |
| CWE-477 | Use of Obsolete Function | The code uses deprecated or obsolete functions that may have known security weaknesses or lack of support. |
| CWE-502 | Deserialization of Untrusted Data | Untrusted data is deserialized without validation, potentially allowing object injection and code execution. |
| CWE-601 | Open Redirect | User input is used to construct a redirect URL without validation, enabling phishing via trusted domains. |
| CWE-611 | XML External Entity (XXE) | XML input is parsed without disabling external entities, allowing file disclosure or SSRF attacks. |
| CWE-614 | Insecure Cookie (Missing Secure Flag) | Cookies are set without the Secure attribute, allowing transmission over unencrypted connections. |
| CWE-668 | Exposure of Resource to Wrong Sphere | A resource is accessible to actors outside of its intended access scope. |
| CWE-732 | Incorrect Permission Assignment for Critical Resource | Critical resources are assigned permissions that allow unintended access or modification. |
| CWE-754 | Improper Check for Unusual or Exceptional Conditions | The software does not properly handle exceptional conditions, leading to undefined or insecure behavior. |
| CWE-798 | Hard-coded Credentials | Passwords, API keys, or tokens are embedded directly in source code instead of using secure configuration. |
| CWE-862 | Missing Authorization | The application does not perform authorization checks, allowing unauthorized users to access restricted functionality. |
| CWE-863 | Incorrect Authorization | The application performs authorization checks incorrectly, allowing unauthorized access to restricted resources. |
| CWE-918 | Server-Side Request Forgery (SSRF) | User input controls a server-side URL request, enabling access to internal resources or services. |
| CWE-1004 | Insecure Cookie (Missing HttpOnly Flag) | Cookies are set without the HttpOnly attribute, making them accessible to JavaScript and vulnerable to XSS theft. |
| CWE-1021 | Clickjacking | The application does not restrict UI rendering in frames, allowing attackers to trick users into unintended clicks. |
