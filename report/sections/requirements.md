---
title: Requirements
nav_order: 3
---

# Requirements

## Functional Requirements

1. The system shall allow users to register with full name, mobile number, email, and password.
2. The system shall allow registered users to log in and log out.
3. The system shall support forgot-password, reset-password, and change-password workflows.
4. The system shall allow users to update their profile and default settings.
5. The system shall allow users to add and manage expense items.
6. The system shall allow users to add expenses with date, item, amount, currency, category, notes, and optional receipt.
7. The system shall allow users to edit and delete existing expense records.
8. The system shall allow users to search and filter expenses by text, date range, amount range, category, and currency.
9. The system shall allow users to export filtered expense data as CSV.
10. The system shall allow users to manage categories and assign monthly budgets.
11. The system shall allow users to define recurring expenses with weekly or monthly frequency.
12. The system shall automatically create due recurring expenses in the main expense table.
13. The system shall display dashboard summaries such as today, weekly, monthly, yearly, and total expense.
14. The system shall present chart-based insights for monthly spending and top categories.
15. The system shall generate daily, monthly, and yearly expense reports.

## Non-Functional Requirements

- Usability: The interface should be simple enough for ordinary users without technical knowledge.
- Performance: Common dashboard and report pages should load in acceptable time for personal-scale datasets.
- Maintainability: Shared logic should be reused through helper files and modular PHP pages.
- Reliability: The system should store and retrieve user expense data consistently during normal operation.
- Portability: The project should run in a standard XAMPP environment with PHP and MySQL.
- Security: Sessions should protect internal pages and user input should be validated before processing.

## Software Requirements

- Operating system: Windows
- Web server stack: XAMPP
- Server-side language: PHP
- Database: MySQL
- Frontend technologies: HTML, CSS, JavaScript, Bootstrap
- Chart library: Chart.js
- Browser: Chrome, Edge, or Firefox

## Hardware Requirements

- Personal computer or laptop
- Minimum 4 GB RAM
- Enough disk space for project files, database, and uploaded receipts
