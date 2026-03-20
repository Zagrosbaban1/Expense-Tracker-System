---
title: Requirements
nav_order: 3
---

# Requirements

## Functional Requirements

1. The system shall allow users to register with name, email, mobile number, and password.
2. The system shall allow registered users to log in and log out.
3. The system shall support password reset and password change features.
4. The system shall allow users to manage a personal profile.
5. The system shall allow users to create and manage expense items.
6. The system shall allow users to add expenses with date, item, amount, category, notes, currency, and optional receipt.
7. The system shall allow users to edit and delete expense records.
8. The system shall allow users to search and filter expenses by text, category, date range, amount range, and currency.
9. The system shall allow users to export filtered expense data as CSV.
10. The system shall allow users to manage categories and assign monthly budgets.
11. The system shall allow users to create recurring expenses with weekly or monthly frequency.
12. The system shall automatically post due recurring expenses into the main expense table.
13. The system shall provide dashboard summaries such as today, weekly, monthly, yearly, and total spending.
14. The system shall show visual charts for spending trends and top categories.
15. The system shall generate daily, monthly, and yearly expense reports with chart-based summaries and CSV export.

## Non-Functional Requirements

- Usability: The interface should be simple enough for ordinary users without technical knowledge.
- Performance: The system should load dashboard and reports within acceptable time for normal personal datasets.
- Maintainability: Common logic should be reused through helper functions and modular PHP files.
- Reliability: The system should correctly store and retrieve expense data without data loss during normal usage.
- Portability: The project should run on a typical XAMPP environment with PHP and MySQL.
- Security: Sessions should protect authenticated pages, inputs should be validated, and sensitive operations should be restricted to logged-in users.

## Software Requirements

- Operating System: Windows
- Server Stack: XAMPP
- Language: PHP
- Database: MySQL
- Frontend: HTML, CSS, JavaScript, Bootstrap
- Charts: Chart.js
- Browser: Chrome, Edge, or Firefox

## Hardware Requirements

- Standard personal computer or laptop
- Minimum 4 GB RAM
- Stable local development environment
