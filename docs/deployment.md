---
title: Deployment
nav_order: 8
---

# Deployment

## Deployment Environment

The project is intended for deployment on a local Apache and MySQL stack such as XAMPP.

## Deployment Steps

1. Install XAMPP.
2. Place the project folder inside `htdocs`.
3. Start Apache and MySQL from the XAMPP control panel.
4. Create the MySQL database used by the project.
5. Configure database connection settings in `includes/dbconnection.php`.
6. Import the base database structure if available.
7. Open the project in the browser through `http://localhost/Expense-Tracker-System/`.

## Deployment Notes

Some additional tables and columns are created automatically by helper functions when certain pages are loaded. This helps the system adapt to newer features, although a dedicated migration process would be better in a professional environment.
