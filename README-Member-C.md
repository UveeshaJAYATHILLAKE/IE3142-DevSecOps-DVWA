# Member C -- Secure Coding and CI/CD Security

## Project Overview

The project uses **Damn Vulnerable Web Application (DVWA)** as the
intentionally vulnerable application and applies DevSecOps practices to
identify, exploit, remediate, and verify security vulnerabilities.

The application is containerized using **Docker** and **Docker
Compose**, with the DVWA web application running in one container and a
MariaDB database running in another container.

This README documents the work completed by **Member C** on the
`member-c` branch. The work covered:

-   Command Injection vulnerability demonstration and remediation
-   Secure coding using IP validation and shell argument escaping
-   SAST verification using Semgrep
-   Creation of a custom Command Injection Semgrep rule
-   GitHub Actions CI/CD security scanning
-   A security gate that genuinely fails when vulnerable code is
    detected
-   Dependency / Software Composition Analysis (SCA) using Composer
    Audit

## Application Setup

The project uses a containerized DVWA environment consisting of:

-   **DVWA web application** -- PHP application running on Apache
-   **MariaDB database** -- MySQL-compatible database used by DVWA
-   **Docker** -- Container runtime
-   **Docker Compose** -- Used to configure and run the application and
    database containers
-   **GitHub Actions** -- Used for automated CI/CD security checks

The group project provides application build and automated testing on
every push. Member C's `Security Scanning` workflow complements the
group pipeline with SAST, an enforced Command Injection security gate,
and dependency/SCA scanning.

**Running Application Prerequisites**

The following software is required to run the project locally:

-   Docker
-   Docker Compose
-   Git

Docker Desktop can be used on Windows, macOS, or Linux.

## Member C Project Structure

``` text
member-c/
│
├── vulnerabilities/
│   └── exec/
│       └── source/
│           └── low.php
│
├── .semgrep/
│   └── command-injection.yml
│
└── .github/
    └── workflows/
        └── semgrep-sast.yml
```

------------------------------------------------------------------------

# **Member C Work Contribution**

## 1. Command Injection Vulnerability

**Vulnerability Location:**

``` text
vulnerabilities/exec/source/low.php
```

The original DVWA Low-security implementation accepted a user-supplied
IP address and used the value when constructing an operating-system
`ping` command executed through `shell_exec()`.

This created a Command Injection vulnerability because malicious shell
syntax could be added to the expected IP input and interpreted by the
operating-system shell.

Command Injection can allow an attacker to execute unintended
operating-system commands in the context of the web-server process.

## 2. Command Injection Exploitation

The original implementation was tested before applying the secure coding
fix.

The payload used was:

``` text
127.0.0.1; whoami
```

The value `127.0.0.1` is a legitimate localhost IP address, while `;`
separates shell commands and `whoami` executes a second operating-system
command.

Before remediation, the application returned the normal ping output
followed by:

``` text
www-data
```

This demonstrated that the injected command executed successfully in the
web-server context.

## 3. Command Injection Fix

**The Command Injection vulnerability was fixed in:**

``` text
vulnerabilities/exec/source/low.php
```

The remediation introduced two security controls:

``` text
filter_var($target, FILTER_VALIDATE_IP)
```

This validates that the supplied input is a legitimate IP address.

``` text
escapeshellarg($target)
```

This safely escapes the validated value before it is passed as an
argument to the shell command.

The secure flow is:

``` text
User Input
    |
    v
Validate IP Address
    |
    v
Reject Invalid Input
    |
    v
Escape Valid Shell Argument
    |
    v
shell_exec()
```

The security improvement is that arbitrary user-controlled input can no
longer flow directly into the shell command.

## 4. Testing After the Fix

After applying and deploying the secure coding changes, the exact same
payload was tested again:

``` text
127.0.0.1; whoami
```

The application returned:

``` text
Invalid IP address
```

The injected `whoami` command no longer executed.

A legitimate IP address was also tested after remediation to confirm
that the normal ping functionality still worked.

This provides direct before-and-after evidence that the demonstrated
Command Injection vulnerability was blocked while legitimate
functionality remained available.

## 5. SAST Using Semgrep

Semgrep was used as the **Static Application Security Testing (SAST)**
tool for Member C's work.

Two forms of SAST were used:

-   Repository-wide Semgrep scanning using `--config=auto`
-   A project-specific Command Injection rule

The custom rule is stored in:

``` text
.semgrep/command-injection.yml
```

The custom rule was created to detect the targeted insecure Command
Injection pattern involving unsafe input reaching `shell_exec()`.

## 6. CI/CD Security Workflow

Member C implemented the GitHub Actions security workflow:

``` text
.github/workflows/semgrep-sast.yml
```

The workflow name is:

``` text
Security Scanning
```

It is configured for relevant pushes and pull requests and contains
automated security checks.

The Member C security workflow includes:

``` text
Push / Pull Request
        |
        v
GitHub Actions
        |
        +-----------------------------+
        |                             |
        v                             v
Semgrep SAST                 Composer Dependency Scan
        |
        v
Command Injection
Security Gate
        |
        v
Pass / Fail
```

The Semgrep and dependency-scan jobs are separate jobs and may execute
independently.

## 7. Command Injection Security Gate

The project-specific Semgrep rule was configured as a real CI/CD
security gate using Semgrep's `--error` behaviour.

To demonstrate that the gate genuinely blocks vulnerable code, the
vulnerable Command Injection implementation was temporarily restored for
a controlled CI run.

The result was:

``` text
Vulnerable Code
      |
      v
Custom Semgrep Rule
      |
      v
2 Blocking Findings
      |
      v
Exit Code 1
      |
      v
Pipeline FAIL
```

After the secure fix was restored, the same gate was executed again:

``` text
Secure Code
    |
    v
Custom Semgrep Rule
    |
    v
0 Findings
    |
    v
Pipeline PASS
```

This proves that the security gate does not only produce a warning. It
can genuinely fail the CI workflow when the targeted vulnerable pattern
is detected.

## 8. Dependency / SCA Scanning

Member C also integrated dependency scanning using **Composer Audit**.

The scan runs against the PHP dependencies under:

``` text
vulnerabilities/api
```

The command used is:

``` text
composer audit --locked
```

Composer Audit performs dependency / Software Composition Analysis (SCA)
by checking the locked PHP dependencies against known security
advisories.

The recorded scan completed successfully with:

``` text
No security vulnerability advisories found.
```

This result means that no known security advisories were reported for
the dependency set checked during that scan.

## 9. SAST Before & After Verification

The security verification process can be summarized as:

``` text
Before Fix
    |
    v
Vulnerable Command Injection Code
    |
    v
Custom Semgrep Security Gate
    |
    v
2 Blocking Findings + Exit Code 1
    |
    v
Command Injection Remediation
    |
    v
FILTER_VALIDATE_IP + escapeshellarg()
    |
    v
Same Exploit Retested
    |
    v
Attack Blocked
    |
    v
Custom Semgrep Security Gate
    |
    v
0 Findings + PASS
```

## 10. Tasks Done and Evidence

The following evidence was collected for Member C's work:

-   Normal Command Injection functionality before remediation
-   Successful Command Injection exploit before the fix
-   Vulnerable source-code evidence
-   Secure source-code evidence
-   Exact exploit retest after remediation
-   Legitimate ping functionality after remediation
-   Semgrep SAST execution evidence
-   Deliberately failed Command Injection security gate
-   After-fix Command Injection security gate pass
-   Composer Audit dependency/SCA evidence
-   GitHub Actions workflow success/failure history
-   Git contribution and clean working-tree evidence

## 11. Files Modified / Added by Member C

The main application file modified by Member C is:

``` text
vulnerabilities/exec/source/low.php
```

The security files added/maintained by Member C are:

``` text
.semgrep/command-injection.yml
.github/workflows/semgrep-sast.yml
```

These files represent the secure coding remediation, custom SAST rule,
and CI/CD security automation completed as part of Member C's
contribution.

## 12. Tools Used

  Component                    Technology
  ---------------------------- ----------------------------------------
  Web Application              Damn Vulnerable Web Application (DVWA)
  Programming Language         PHP
  Web Server                   Apache
  Database                     MariaDB
  Containerisation             Docker
  Multi-container Management   Docker Compose
  Source Control               Git / GitHub
  CI/CD                        GitHub Actions
  SAST                         Semgrep
  Dependency / SCA Scan        Composer Audit

## 13. Member C Contribution Summary

Member C was responsible for the following project activities:

-   Investigating the Command Injection vulnerability in
    `vulnerabilities/exec/source/low.php`
-   Demonstrating Command Injection using `127.0.0.1; whoami`
-   Confirming operating-system command execution through the `www-data`
    output
-   Remediating the vulnerability using `FILTER_VALIDATE_IP`
-   Applying `escapeshellarg()` before shell execution
-   Retesting the exact same exploit after remediation
-   Confirming legitimate ping functionality remained available
-   Integrating Semgrep SAST into GitHub Actions
-   Creating the custom `.semgrep/command-injection.yml` security rule
-   Implementing the `.github/workflows/semgrep-sast.yml` security
    workflow
-   Demonstrating a genuine CI security-gate failure with 2 blocking
    findings and exit code 1
-   Restoring the secure implementation and confirming 0 findings and a
    successful gate
-   Integrating Composer Audit for dependency/SCA scanning
-   Collecting before-fix, after-fix, SAST, dependency-scan, and CI/CD
    evidence

All work documented in this README belongs to the `member-c`
contribution and represents Member C's assigned work.

Member C's work is on branch: `member-c`

Repository:
`https://github.com/UveeshaJAYATHILLAKE/IE3142-DevSecOps-DVWA`

## Security Warning

DVWA is intentionally designed to contain vulnerable application code
for security testing and education.

Do not expose this application to the public internet or use it as a
production application. Run the application only in a controlled local
or isolated testing environment.
