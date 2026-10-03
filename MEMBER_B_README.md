# IE3142 DevSecOps – Securing DVWA

A university DevSecOps project focused on identifying, exploiting, fixing, and detecting web application vulnerabilities using secure coding practices and automated security checks.

## 1. Project Overview

This project uses **Damn Vulnerable Web Application (DVWA)** to demonstrate how security can be integrated into the software development lifecycle.

The project focuses on:

- Understanding application architecture and security risks.
- Identifying and demonstrating web application vulnerabilities.
- Applying secure coding fixes.
- Performing Static Application Security Testing (SAST).
- Scanning dependencies, secrets, and container images.
- Integrating automated security checks into a CI/CD pipeline.
- Applying STRIDE threat modelling and risk assessment.

**Technology stack**

- PHP
- MariaDB
- Docker and Docker Compose
- Git and GitHub
- GitHub Actions
- Semgrep
- Composer Audit
- Gitleaks and Trivy, where configured

## 2. Application Architecture

The project uses a containerised web application architecture.

```text
             User Browser
                  |
                  v
           DVWA Web App
           (PHP / Apache)
                  |
                  v
              MariaDB
             Database

        Docker Compose Environment

                  |
                  v
          GitHub Repository
                  |
                  v
          GitHub Actions
                  |
         Security Checks
```

The browser sends requests to the DVWA web application. The PHP application processes requests and communicates with MariaDB when database operations are required.

Docker Compose runs the web application and database as separate services. The GitHub Actions workflow is used for automated development and security checks.

## 3. Application Setup

### Prerequisites

Install the following:

- Docker Desktop
- Git
- A web browser

### Clone the repository

```bash
git clone https://github.com/UveeshaJAYATHILLAKE/IE3142-DevSecOps-DVWA.git
cd IE3142-DevSecOps-DVWA
```

### Start the application

```bash
docker compose up -d --build
```

### Check running containers

```bash
docker compose ps
```

### Access DVWA

Open the following address in your browser:

http://127.0.0.1:4280

Complete the DVWA database setup if required by the application, then log in using the credentials configured for the local environment.

**Note:** The repository's Compose configuration has used demonstration database credentials. Do not reuse these credentials in production or expose real secrets in source control.

### Stop the application

```bash
docker compose down
```

To remove the database volume as well (this deletes stored local database data):

```bash
docker compose down -v
```

## 4. Project Structure

```text
IE3142-DevSecOps-DVWA/
|
|-- vulnerabilities/
|   |-- xss_r/
|   |   |-- source/
|   |       |-- low.php
|   |
|   |-- [other vulnerability modules]
|
|-- semgrep-rules/
|   |-- xss-reflected.yml
|
|-- evidence/
|   |-- vulnerability-2-xss/
|       |-- exploit/
|       |   |-- before-xss.png
|       |   |-- after-xss.png
|       |
|       |-- sast/
|           |-- before/
|           |-- after/
|
|-- .github/
|   |-- workflows/
|
|-- compose.yml
|-- Dockerfile
|-- README.md
```

The structure above highlights key project files and the Member B evidence. Other vulnerability modules, workflow files, and evidence directories should be checked against the actual repository.

## 5. Vulnerabilities Demonstrated

The project investigates four main security threats.

| ID | Vulnerability | Description | Security control |
|---|---|---|---|
| T1 | SQL Injection | User input can interfere with database queries. | Prepared statements and parameter binding |
| T2 | Reflected XSS | Untrusted input can be reflected into HTML and executed by the browser. | HTML output encoding and custom Semgrep rule |
| T3 | Command Injection | Unsafe input can cause unauthorised operating-system command execution. | Input validation and shell argument escaping |
| T4 | Dependency vulnerability | Vulnerable third-party components can expose the application to security risks. | Dependency scanning and remediation |

The project also applies STRIDE threat modelling and risk assessment to evaluate these threats and connect them to appropriate controls.

## 6. Member B Contribution

Member B's work covers the Reflected XSS vulnerability and the integrated security analysis.

### 6.1 Reflected XSS

The vulnerable PHP code inserted the `name` GET parameter directly into the HTML response without output encoding.

Example attack payload:

```html
<script>alert('XSS-BEFORE')</script>
```

Before remediation, the browser executed the payload and displayed an alert.

### 6.2 Secure Coding Fix

The vulnerability was fixed by encoding the user-controlled value with `htmlspecialchars()`.

```php
$name = htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8');
$html .= '<pre>Hello ' . $name . '</pre>';
```

After the fix, the same payload appeared as text instead of executing as JavaScript.

### 6.3 SAST Verification

Standard Semgrep returned zero findings for the vulnerable file using the selected configuration. A custom Semgrep rule was created to detect the demonstrated unsafe output pattern.

| Test | Result |
|---|---|
| Standard Semgrep before fix | 0 findings |
| Custom Semgrep before fix | 1 blocking finding |
| Custom Semgrep after fix | 0 findings |
| Browser retest | Payload displayed as text |

### 6.4 Threat Modelling and Risk Analysis

Member B also prepared the integrated security analysis, including:

- STRIDE threat classification.
- Likelihood and impact assessment using a 3×3 risk matrix.
- Threat-to-control mapping.
- Security evidence and control verification.
- Industry case study and security trends.

### 6.5 Evidence

The XSS evidence is stored in:

```text
evidence/vulnerability-2-xss/
```

It includes before-and-after browser screenshots and SAST output files.

The custom detection rule is located at:

```text
semgrep-rules/xss-reflected.yml
```

## 7. CI/CD Pipeline and Security Automation

The project uses GitHub Actions to support automated security checks.

The security gates required by the project are:

| Security gate | Purpose | Example tool |
|---|---|---|
| SAST | Detect insecure source-code patterns | Semgrep |
| SCA | Identify known dependency vulnerabilities | Composer Audit |
| Secret scanning | Detect accidentally committed credentials | Gitleaks |
| Container scanning | Detect known container image vulnerabilities | Trivy |

The pipeline is intended to run security checks automatically and block progression when configured security thresholds are exceeded.

Check the workflow files in `.github/workflows/` for the exact jobs, trigger conditions, thresholds, and current implementation status.

## 8. Security Testing and Evidence

The project uses different testing methods to evaluate security controls.

- **Manual exploitation:** Demonstrates whether a vulnerability can be exploited.
- **Secure coding verification:** Tests whether the implemented fix prevents the demonstrated attack.
- **SAST:** Checks source code for insecure patterns.
- **Dependency scanning:** Checks third-party packages for known security advisories.
- **Secret scanning:** Checks tracked files for exposed credentials.
- **Container scanning:** Checks container images for known vulnerabilities.

Security results should be interpreted alongside the test configuration and supporting evidence. A clean scan does not prove that the entire application is secure.

## 9. Git and Branch Workflow

The project uses Git and GitHub for source control and collaboration.

Member B's working branch:

```bash
git checkout member-b
```

Check the current branch and working tree:

```bash
git status
git branch
```

Stage, commit, and push changes:

```bash
git add .
git commit -m "Describe the changes"
git push origin member-b
```

Changes should follow the group's agreed review and integration process. Avoid pushing directly to `main` unless the group explicitly authorises it.

## 10. Learning Outcomes

This project demonstrates practical DevSecOps concepts:

- Identifying common web application vulnerabilities.
- Applying secure coding techniques.
- Using static analysis to detect insecure patterns.
- Evaluating security risks using STRIDE.
- Mapping threats to security controls.
- Using automated security tools within a software delivery workflow.
- Understanding the role of security evidence and verification.

## 11. References

- [Damn Vulnerable Web Application (DVWA)](https://github.com/digininja/DVWA)
- [OWASP Cross Site Scripting Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [OWASP STRIDE](https://owasp.org/)
- [Semgrep Documentation](https://semgrep.dev/docs/)
- [Docker Documentation](https://docs.docker.com/)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)

---

**Project:** IE3142 DevSecOps – Building and Securing a DevSecOps Pipeline  
**Application:** DVWA  
**Repository:** https://github.com/UveeshaJAYATHILLAKE/IE3142-DevSecOps-DVWA
