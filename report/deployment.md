---
title: Deployment
has_children: false
nav_order: 8
---

# Deployment

Deploying a PHP and MySQL project means putting the source onto a machine that runs Apache, PHP, and MySQL. The simplest way to get all three is **XAMPP**, which is what I developed against. These five steps take a fresh copy of the project to a running site.

1. **Install XAMPP.** Download and install it on the target computer, then start the **Apache** and **MySQL** services from the control panel.
2. **Place the project.** Copy the project folder into XAMPP's `htdocs` directory, for example `C:\xampp\htdocs\Expense-Tracker-System`, or unzip the release artifact there.
3. **Create the database.** In phpMyAdmin, create a database named `detsdb` and import `database.sql` to build the schema. Importing one of the `seed-*.sql` files as well will fill it with demonstration data, which is useful for seeing the charts populated straight away.
4. **Check the connection.** Make sure the credentials in `includes/dbconnection.php` match the local MySQL settings. The defaults are user `root` with an empty password on `localhost`, which is what a stock XAMPP install provides.
5. **Run it.** Open `http://localhost/Expense-Tracker-System/` in a browser, register an account, and start using the system.

Three things the environment has to provide:

- **Server stack.** Apache, PHP 7.4 or higher, and MySQL or MariaDB. XAMPP supplies all of them; the syntax checks in CI run on PHP 8.2, so anything between those versions is fine.
- **File uploads.** Uploads must be enabled, because receipts are written into the project's `uploads/receipts/` folder. The folder does not need to be created first, as it is made when the first receipt is saved.
- **A tolerant database.** Several tables and columns are added automatically by the helper functions the first time a relevant page is opened, so a database that is missing newer columns will finish setting itself up on first use.

The report site is deployed on its own path. A separate GitHub Actions workflow builds the `report/` folder with Jekyll and publishes it to GitHub Pages, and it has nothing to do with deploying the application.
