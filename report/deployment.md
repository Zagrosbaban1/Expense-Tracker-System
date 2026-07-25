---
title: Deployment
has_children: false
nav_order: 8
---

# Deployment

Since the application is a PHP and MySQL project, deployment means placing the source onto a machine that runs Apache, PHP, and MySQL. The simplest environment for this is **XAMPP**, which bundles all three. The steps below take a fresh copy of the project to a running site:

1. Install XAMPP — download and install XAMPP on the target computer, then start the **Apache** and **MySQL** services from its control panel.
2. Place the project — copy the project folder into XAMPP's `htdocs` directory (for example `C:\xampp\htdocs\Expense-Tracker-System`), or unzip the release artifact there.
3. Create the database — using phpMyAdmin, create a database named `detsdb` and import `database.sql` to build the schema. Optionally import one of the `seed-*.sql` files to load demonstration data.
4. Check the connection — confirm that the credentials in `includes/dbconnection.php` match the local MySQL settings (by default, user `root` with an empty password on `localhost`).
5. Run the application — open `http://localhost/Expense-Tracker-System/` in a browser, then register an account and start using the system.

Requirements:
- Server stack — Apache, PHP 7.4 or higher, and MySQL/MariaDB, all provided by XAMPP.
- File uploads — the environment must allow file uploads, because receipts are stored inside the project's `uploads/receipts/` folder, which is created automatically when the first receipt is saved.
- Schema maintenance — several tables and columns are created automatically by the helper functions the first time a relevant page is opened, so a partially set-up database will complete itself on first use.

The report site itself is deployed separately: a GitHub Actions workflow builds the `report/` folder with Jekyll and publishes it to GitHub Pages, independently of the application deployment described above.
