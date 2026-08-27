# Application Security
**Riel Calilung | February 25, 2026**

## Introduction and Materials
In the last lab, I saw how attackers can leverage tools such as Metasploit and msfvenom in order to attack applications. This lab will provide another layer of exposure to the ways that attackers can exploit vulnerabilities in applications, particularly through XSS and SQL injection attacks. Additionally, learning about how these attacks work will help with strengthening the application’s security.

## Steps to Reproduce
1. First, we’ll install ZAP!, a dynamic application security testing tool. Since I’m using an M1 Macbook, I’ll download the macOS(Apple Silicon - aarch64) Installer.

[](week7-images/img1.png)

2. With ZAP! installed, keep the top most option selected and click “Start”

[](week7-images/img2.png)

3. Afterwards, in the welcome page, choose the “Automated Scan Option”. Be sure to have open docker and your web application beforehand.

[](week7-images/img3.png)

4. Set the URL to attack to be `https://localhost`, then click Attack

[](week7-images/img4.png)

5. As ZAP! does its scan, you can click the “Alerts” tab towards the bottom right to see what types of vulnerabilities have been detected. In particular, we’re interested in Cross Site Scripting and SQL Injections.

[](week7-images/img.png)

### **Attack 1 - Logging in as Admin without using their password**
1. To do this, head to the login page for the password manager.

[](week7-images/imga1-1.png)

2. Set the username to, `‘ OR 1=1; --`, and set the password to be anything.

[](week7-images/imga1-2.png)

3. Upon logging in, we’ll see the following screen.

[](week7-images/imga1-3.png)

    
### **Attack 2 - Find a way to retrieve all VAULT passwords from the database**
1. Signed in as an Admin, navigate to the “Vaults” tab.

[](week7-images/imga2-1.png)

2. In the search bar, type, UNION SELECT username, password FROM vault_passwords-- -, and you should see the following:

[](week7-images/imga2-2.png)

### **Attack 3 - Create a false pop-up asking your victim for confidential information whenever they access a vault**
1. Log out and then request an account like so (be sure to set the first name to `<script>alert(“SEND CREDIT CARD INFO TO 123 456-7890”);</script>`):

[](week7-images/imga3-1.png)

2. Log into the password manager as an Admin, then click the “Users” tab towards the top-right.

[](week7-images/imga3-2.png)

3. Upon clicking it, you should see the alert that we’ve created.

[](week7-images/imga3-3.png)

## Analysis
1. Q: For each attack you executed, which of the CIA pillars were violated?

    A: In attack 1, availability was violated as we were able to log into the password manager without knowing the login. In attack 2, all three of the CIA pillars were violated as we were able to access vaults we shouldn’t have access to, were able to see the information in each vault, and even have the opportunity to manipulate the data in them. In attack 3, integrity was violated as we were able to tamper and inject JavaScript code into the application.

2. Q: Why does our web application allow for such attacks to occur?

    A: Much of the attacks were able to occur because of the lack of sanitizing we do for user input. As a result, SQL queries are able to be manipulated and user given input can even be interpreted as JavaScript code in certain instances.

3. Q: Which of the Security Principles provided by OWASP relate to the vulnerabilities outlined in this lab?

    A: Fail Securely because, in the case of error, data gets leaked. Minimize Attack Surface Area because there are many vulnerabilities that ZAP! found, with a good portion of them being related to XSS or SQL injection vulnerabilities.

## Conclusion
This news [article](https://hackread.com/zero-day-flaws-pdf-platforms-xss-one-click-attacks/) describes how two PDF systems had zero-day vulnerabilities that serve as a way for attackers to take over user accounts or gain access into and run commands on the company’s servers. One of the risks mentioned in the article is how scripts can be hidden in a certain comment, and when a user interacts with it, the script is executed. This is actually something observed during the lab where JavaScript code was written and injected when requesting a new account. While the script only created a small popup, a more elaborate one could be executed to a more dangerous and malicious extent.