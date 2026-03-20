---
title: Developer guide
nav_order: 11
---

# Developer Guide

## Folder Overview

- `includes/`: shared database connection, layout includes, and helper functions
- `css/`, `js/`, `assets/`: frontend resources and styling
- Root PHP files: application pages for login, dashboard, expenses, reports, and user features
- `uploads/receipts/`: uploaded receipt files

## Important Files

- `includes/dbconnection.php`: creates the MySQL connection
- `includes/expense-helpers.php`: helper functions for schema updates, prepared statements, category and budget logic, recurring processing, and utility formatting
- `includes/report-helpers.php`: reporting utilities such as currency formatting and sanitization
- `dashboard.php`: main analytics and dashboard page
- `manage-expense.php`: expense listing, filters, delete operation, and CSV export
- `manage-categories.php`: category and budget management
- `manage-recurring.php`: recurring expense scheduling

## Developer Notes

- Newer pages use prepared statements and reusable helpers.
- Older pages still contain inline SQL and can be refactored.
- The system is file-based rather than MVC, so changes should be made carefully to avoid duplication.
- Shared logic should continue to be moved into helper files to improve maintainability.
