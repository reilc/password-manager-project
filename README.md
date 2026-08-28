# HusKey Manager — Security Hardening Project

A week-by-week security hardening project built on the **HusKey Manager**, an
intentionally vulnerable password management web application originally
created for the University of Washington iSchool's *Information Assurance
and Cybersecurity* course.

The starter app was provided as a functional but deliberately insecure
password manager. Over the course of a quarter, I identified and fixed a new
class of vulnerability each week — attacking the app first to understand the
weakness, then patching it — simulating the kind of incremental security
remediation a real engineer performs on a legacy or newly-inherited codebase.

Each week's work is fully documented in [`/writeups`](./writeups), including
methodology, tools used, and screenshots of both the attack and the fix.

## Attribution

The base application (`HusKey Manager`) was originally created for UW's
Information Assurance and Cybersecurity course, credited to Zach Kornas.
It is provided under the MIT License — see [`LICENSE`](./LICENSE). All
security hardening, writeups, and analysis in this repository are my own
work, built on top of that starter application.

## Weekly Breakdown

| Week | Topic | Summary |
|------|-------|---------|
| [2](./writeups/week2-writeup.md) | Man in the Middle Attack | Performed an ARP spoofing MITM attack against the app's unencrypted HTTP traffic, capturing login credentials in cleartext via Wireshark — motivating the move to HTTPS/TLS |
| [3](./writeups/week3-writeup.md) | Applying Cryptography | Identified weak/missing cryptographic protections in the app and implemented proper encryption for sensitive data at rest and in transit |
| [4](./writeups/week4-writeup.md) | Application Architecture & Logging | Reviewed the app's architecture for security gaps and implemented logging to support monitoring and incident detection |
| [5](./writeups/week5-writeup.md) | Authentication and Authorization | Implemented session-based authentication and access controls to properly restrict user privileges and protect account access |
| [6](./writeups/week6-writeup.md) | Offensive Hacker Tools | Used offensive security tooling to probe the application for exploitable weaknesses, informing later hardening steps |
| [7](./writeups/week7-writeup.md) | Application Security | Identified and remediated core web application vulnerabilities (e.g. XSS/SQLi) using output encoding and parameterized queries |
| [8](./writeups/week8-writeup.md) | Bug Bounty and Hardening Lab | Conducted a self-directed bug bounty-style assessment of the app and applied hardening fixes for discovered issues |
| [9](./writeups/week9-writeup.md) | Remediation and Testing | Final remediation pass and testing to validate that implemented fixes held up against the earlier attack scenarios |

## Tech Stack

`PHP` `MySQL` `Docker` `Nginx` `Wireshark` `bettercap` `pytest`

## What This Project Demonstrates

- Vulnerability identification across multiple classes (network, cryptographic, authentication, web application, architectural)
- Root-cause analysis paired with practical remediation, not just theoretical fixes
- Use of offensive tooling (ARP spoofing, packet analysis) to validate defensive improvements
- Structured documentation of security work, from attack reproduction through fix verification