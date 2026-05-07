# SOC Lab Management System

A role-based task management and performance tracking system designed for structured departmental operations and training environments.

---

## 📌 Overview

The SOC Lab Management System is a PHP + MySQL web application built to simulate structured task delegation and performance monitoring across multiple departments.

The system implements:

* Role-Based Access Control (RBAC)
* Departmental Isolation
* Many-to-Many Task Assignments
* Proof-of-Work Submission
* Performance Rating
* Skill Documentation
* Activity Logging
* System-wide Administrative Oversight

---

## 👥 User Roles

### 🔹 Super Admin

* Manage users and departments
* View all system-wide tasks
* Monitor cross-department performance
* Access activity logs

### 🔹 Department Admin

* Create tasks
* Assign tasks to multiple members
* Review submitted reports
* Rate completed work
* View department member skills
* Monitor department activity logs

### 🔹 Member

* View assigned tasks
* Upload task completion reports (.pdf)
* Update reports before review
* View ratings
* Document acquired skills
* Update profile credentials

---

## 🏗 System Architecture

The system uses:

* PHP (Server-side logic)
* PDO (Secure database interaction)
* MySQL (Relational database)
* XAMPP (Local development environment)
* Structured folder organization
* Session-based authentication

---

## 🗄 Database Design

Key relational concepts implemented:

* One-to-Many (Department → Users)
* One-to-Many (Role → Users)
* Many-to-Many (Tasks ↔ Users via task_assignments)
* Foreign Key Constraints
* Cascading Deletes & Controlled Nullification

Core Tables:

* `user`
* `role`
* `department`
* `task`
* `task_assignments`
* `user_skill`
* `activity_log`

---

## 📂 Project Structure

```
soc_lab/
│
├── auth/
│   ├── auth.php
│   ├── authorize.php
│   ├── logger.php
│   ├── login_process.php
│   ├── logout.php
│   └── role_router.php

│
├── config/
│   └── db.php
│
├── dashboards/
│   ├── super_admin/
│   ├── department_admin/
│   └── member/
│
├── includes/
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
│
├── uploads/
│   └── task_reports/
│
├── css/
│   ├── styles.css
│   └── dashboard.css
|
├── public/
│   ├── create_user.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── process_profile_update.php
│   ├── profile.php
│   ├── unauthorized.php
|
├── db_schema.sql
├── password.php
└── README.md
```

---

## 🔐 Security Features

* Prepared statements (PDO)
* Role authorization middleware
* Department-level isolation
* File upload restrictions (.pdf only)
* Session-based authentication
* Foreign key enforcement
* Restricted file access logic
* Activity logging for traceability

---

## 🚀 Core Features

### Task Lifecycle

1. Department Admin creates task
2. Task assigned to one or more members
3. Member uploads PDF proof
4. Admin reviews and rates (1–5 stars)
5. Assignment locked after review

### Skill Tracking

Members document acquired skills which can be viewed by department administrators.

### System Oversight

Super Admin can monitor all task assignments across departments.

---

## 🧪 Development Environment

* PHP 8+
* MySQL 8+
* XAMPP
* Git for version control
* HTML
* CSS

  
## 📈 Future Improvements

* Task analytics dashboard (charts)
* Department performance ranking
* Leaderboard system
* Overdue task detection
* Endorsement system for skills
* Export to PDF / CSV
* API layer for integration


This project is for educational and internal training purposes.
