---
title: Developer Guide
has_children: false
nav_order: 11
---

# Developer guide

This chapter is for someone who wants to extend the Expense Tracker System or contribute to it. It covers setting up a development environment, running the application and its tests, and finding the code that matters.

## Setting up the development environment

Prerequisites:
- XAMPP (Apache, PHP 7.4 or higher, and MySQL/MariaDB)
- Composer (for the PHP development dependencies)
- Git (for version control)
- Python with Playwright (optional, only for the browser end-to-end test)

Clone the project into XAMPP's web root:

```bash
git clone https://github.com/Zagrosbaban1/Expense-Tracker-System.git
cd Expense-Tracker-System
```

Place the folder inside `htdocs` if it is not already there, then start Apache and MySQL from the XAMPP control panel.

## Configuring the database

The application uses a MySQL database named `detsdb`:

1. Open phpMyAdmin from the XAMPP control panel.
2. Create a database called `detsdb`.
3. Import `database.sql` to create the schema. Optionally import a `seed-*.sql` file to load demonstration data.
4. Confirm that the connection settings in `includes/dbconnection.php` match your local MySQL configuration (by default, user `root` with an empty password on `localhost`).

Note that several tables and columns are also created automatically at runtime by `expense_ensure_schema` in `includes/expense-helpers.php`, so a database that is missing newer columns will complete itself the first time a relevant page is opened.

## Project structure

- `includes/` holds the shared connection (`dbconnection.php`), the helper libraries (`expense-helpers.php`, `report-helpers.php`), and the layout partials (`header.php`, `sidebar.php`, `footer.php`).
- The root `*.php` files are the application pages: `dashboard.php`, `add-expense.php`, `manage-expense.php`, `manage-categories.php`, `manage-recurring.php`, the report pages, and the authentication pages.
- `uploads/receipts/` stores uploaded receipt files.
- `css/`, `js/`, `assets/`, and `fonts/` hold the frontend resources.
- `tests/` contains the unit tests (`tests/unit/`), the browser end-to-end test (`tests/e2e/`), and the manual acceptance checklist.
- `report/` is the Jekyll source for this report site.

Start with `includes/expense-helpers.php`. Almost everything reusable is in there (validation, formatting, prepared statements, CSRF handling, budgeting, and recurring processing), and the newer pages are written around it, so reading it first makes them much easier to follow.

## Building and running the application

There is no build step. Once the files are in `htdocs` and the database exists, open `http://localhost/Expense-Tracker-System/` in a browser. Install the development dependencies with Composer so the test tools are available:

```bash
composer install
```

## Testing

The project has automated unit tests and an optional browser end-to-end test.

Run the PHPUnit unit and syntax tests, which should report `OK (44 tests, 57 assertions)`:

```bash
composer test
```

Run the Playwright end-to-end test against the local site (with Apache and MySQL running and `detsdb` imported):

```bash
python -m pip install -r requirements-dev.txt
python -m playwright install chromium
python tests/e2e/playwright_smoke.py
```

If you change behaviour, add a test for it, and route any new database access through the existing prepared-statement helpers rather than writing a query inline.

## Contributing to the project

To contribute:

- Create a branch for your change: `git checkout -b feat/your-change`
- Commit using the Conventional Commits style: `git commit -m "feat: add pagination to expense list"`
- Push the branch and open a pull request: `git push origin feat/your-change`

Keep changes small and focused, reuse the shared helpers instead of duplicating logic, and check that `composer test` passes before opening the pull request.
