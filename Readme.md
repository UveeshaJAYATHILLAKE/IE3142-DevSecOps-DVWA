## Project Overview

The project uses **Damn Vulnerable Web Application (DVWA)** as the intentionally vulnerable application and applies DevSecOps practices to identify, exploit, remediate, and verify security vulnerabilities.
The application is containerized using **Docker** and **Docker Compose**, with the DVWA web application running in one container and a MariaDB database running in another container.

This README documents the work completed by **Member A** on the member-a branch. The work covered:

- Local Docker and Docker Compose setup for DVWA
- SQL Injection vulnerability remediation
- SQL Injection SAST verification using Semgrep
- Before-fix and after-fix SAST evidence
- Changes required to build and run DVWA locally

## Application Setup

The project uses a containerized DVWA environment consisting of:

- **DVWA web application** – PHP application running on Apache
- **MariaDB database** – MySQL-compatible database used by DVWA
- **Docker** – Container runtime
- **Docker Compose** – Used to configure and run the application and database containers

The application can be built and started locally using Docker Compose.

**Running Application Prerequisites**

The following software is required to run the project locally:
- Docker
- Docker Compose
- Git
Docker Desktop can be used on Windows, macOS, or Linux.

## Member A Project Structure

```text
member-a/
│
├── Dockerfile
│
├── compose.yml
│
├── vulnerabilities/
│   └── sqli/
│       └── source/
│           └── low.php
│
└── evidence/
    └── vulnerability-1-sql-injection/
        └── sast/
            ├── before/
            │   └── semgrep-before.txt
            │
            └── after/
                └── semgrep-after.txt
```
--------------------------------------------------------------------------------------------------------------------------------------------------

# **Member A Work contribution**

## 1. Setup Docker Application

Member A configured the project so that DVWA could be built and executed locally using Docker.

The application consists of two main services:

User / Browser
       |
       v
DVWA Web Container
(Apache + PHP + DVWA)
       |
       v
MariaDB Container
(Database)

Docker Compose is used to manage the communication between the DVWA application container and the MariaDB database container.

**Dockerfile**

The project Dockerfile was modified to allow the DVWA image to build successfully in the local environment.
The resulting Docker image can therefore be built locally using:

docker compose build

**Docker Compose**

The compose.yml file defines the application and database services and provides the Docker networking required for DVWA to communicate with MariaDB.

## 2. SQL Injection Vulnerability
**Vulnerability Location:**

vulnerabilities/sqli/source/low.php

The vulnerable implementation directly incorporated user controlled input into the SQL query.
This creates a SQL Injection risk because specially crafted input can alter the intended SQL statement instead of being treated only as a user supplied value.
SQL Injection can allow an attacker to manipulate database queries and potentially retrieve or modify information that should not be accessible through the application's normal functionality.

## 3. SQL Injection Exploitation

The original DVWA implementation was intentionally vulnerable to SQL Injection.
The vulnerability was tested against the unmodified implementation before applying the secure coding fix.
The purpose of this testing was to demonstrate that the application was vulnerable before remediation and to provide a baseline for comparison with the fixed implementation.

## 4. SQL Injection Fix

**The SQL Injection vulnerability was fixed in:**
vulnerabilities/sqli/source/low.php

The vulnerable SQL construction was replaced with a prepared statement and parameter binding approach.
For the MySQL/MariaDB implementation, the query is prepared before the user-supplied ID is bound as a parameter.
The secure implementation follows the following pattern:
```text
$stmt = mysqli_prepare(
    $GLOBALS["___mysqli_ston"],
    "SELECT first_name, last_name FROM users WHERE user_id = ?;"
);

mysqli_stmt_bind_param( $stmt, "i", $id );
mysqli_stmt_execute( $stmt );
mysqli_stmt_bind_result( $stmt, $first, $last );
```

The Security improvement is that the user input is no longer directly concatenated into the SQL query.
Instead:
- The SQL statement is prepared.
- The input is bound to the parameter.
- The database executes the prepared statement.
- The input is treated as a parameter rather than executable SQL syntax.

## 5. Testing After the Fix

After applying the secure coding changes, the application was rebuilt using Docker:
docker compose build

The application was then restarted:
```docker compose up -d```

The same SQL Injection functionality was tested again after remediation.

The purpose of this second test was to verify that the vulnerable behaviour was no longer present after replacing the unsafe SQL construction with prepared statements.

## 6. SAST Using Semgrep

Semgrep was used as the Static Application Security Testing (SAST) tool for Member A's SQL Injection work. 
The purpose of SAST was to automatically analyse the source code and provide security evidence before and after the remediation.

**The SAST evidence is stored in:**
evidence/vulnerability-1-sql-injection/sast/

The directory contains separate results for the vulnerable and remediated implementations:
```text
sast/
├── before/
│   └── semgrep-before.txt
│
└── after/
    └── semgrep-after.txt
```
## 7. SAST Before the Fix

Before the SQL Injection remediation, the vulnerable implementation was scanned using Semgrep.

**The scan result is stored in:**
evidence/vulnerability-1-sql-injection/sast/before/semgrep-before.txt

This file provides the baseline SAST evidence for the vulnerable version of the source code.

## 8. SAST After the Fix

After the SQL Injection remediation was applied, Semgrep was run again against the updated source code.

**The resulting scan output is stored in:**
evidence/vulnerability-1-sql-injection/sast/after/semgrep-after.txt

The post fix Semgrep scan completed with:

0 findings
0 blocking findings

The scan successfully parsed the target source file and did not report a remaining security finding from the applied Semgrep configuration.
This provides automated evidence that the targeted insecure SQL pattern was removed from the remediated implementation.

## 9. SAST Before & After Verification

The SAST process provides a direct comparison between the vulnerable and remediated versions of the SQL Injection implementation.

```
Before Fix
    |
    v
Vulnerable SQL implementation
    |
    v
Semgrep SAST
    |
    v
Security finding
    |
    v
SQL Injection remediation
    |
    v
Prepared statement + parameter binding
    |
    v
Semgrep SAST
    |
    v
0 findings
```

## 10. Tasks done and Evidence

The following evidence is maintained for Member A's work:

Docker Build
-Running Damn Vulnerable Web Application
-SQL Injection Before Fix Evidence
-SQL Injection After Fix Evidence
-Semgrep Before-Fix Evidence
-Semgrep After-Fix Evidence

## 11. Files Modified by Member A

The main files changed as part of Member A's implementation are:

-Dockerfile
-compose.yml
-vulnerabilities/sqli/source/low.php

The SAST files added are:

evidence/vulnerability-1-sql-injection/sast/before/semgrep-before.txt
evidence/vulnerability-1-sql-injection/sast/after/semgrep-after.txt

## 12. Tools Used

| Component | Technology |
|---|---|
| Web Application | Damn Vulnerable Web Application (DVWA) |
| Programming Language | PHP |
| Web Server | Apache |
| Database | MariaDB |
| Containerisation | Docker |
| Multi-container Management | Docker Compose |
| Source Control | Git / GitHub |
| SAST | Semgrep |


## 13. Member A Contribution Summary

Member A was responsible for the following project activities:

Configuring the DVWA application to build locally using Docker.
Configuring Docker Compose for the DVWA web application and MariaDB database.
Resolving the Docker build issue related to the Debian package repository configuration.
Configuring the local Compose setup to build the modified DVWA source code.
Investigating the SQL Injection vulnerability in vulnerabilities/sqli/source/low.php.
Demonstrating the SQL Injection vulnerability before remediation.
Replacing unsafe SQL query construction with prepared statements and parameter binding.
Updating the corresponding SQLite implementation with parameterized queries.
Rebuilding and testing the application after remediation.
Running Semgrep before and after the SQL Injection fix.
Maintaining the SQL Injection SAST evidence in the repository.

All work documented in this README only belongs to the member-a branch and represents only Member A's contribution.
Member A's work is on branch: member-a
Repository: https://github.com/UveeshaJAYATHILLAKE/IE3142-DevSecOps-DVWA

**Security Warning**

DVWA is intentionally designed to contain vulnerable application code for security testing and education.
Do not expose this application to the public internet or use it as a production application.
Run the application only in a controlled local or isolated testing environment.
