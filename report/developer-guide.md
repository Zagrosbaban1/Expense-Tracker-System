---
title: Developer Guide
has_children: false
nav_order: 11
---

# Developer guide

To contribute to or extend the Expense Tracker System, follow these steps to set up a development environment, run the application and its tests, and understand where the important code lives.

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

- `includes/` — the shared connection (`dbconnection.php`), the helper libraries (`expense-helpers.php`, `report-helpers.php`), and the common layout partials (`header.php`, `sidebar.php`, `footer.php`).
- Root `*.php` files — the application pages, such as `dashboard.php`, `add-expense.php`, `manage-expense.php`, `manage-categories.php`, `manage-recurring.php`, the report pages, and the authentication pages.
- `uploads/receipts/` — stored receipt files.
- `css/`, `js/`, `assets/`, `fonts/` — frontend resources.
- `tests/` — the unit tests (`tests/unit/`), the browser end-to-end test (`tests/e2e/`), and the manual acceptance checklist.
- `report/` — the Jekyll source for this report site.

The most important place to look first is `includes/expense-helpers.php`, since it centralises the reusable logic (validation, formatting, prepared statements, CSRF handling, budgeting, and recurring processing) that the newer pages depend on.

## Building and running the application

There is no compile step: once the files are in `htdocs` and the database exists, open `http://localhost/Expense-Tracker-System/` in a browser. Install the development dependencies with Composer so that the test tools are available:

```bash
composer install
```

## Testing

The project includes automated unit tests and an optional browser end-to-end test.

Run the PHPUnit unit and syntax tests:

```bash
composer test
```

Run the Playwright end-to-end test against the local site (with Apache and MySQL running and `detsdb` imported):

```bash
python -m pip install -r requirements-dev.txt
python -m playwright install chromium
python tests/e2e/playwright_smoke.py
```

Developers are encouraged to add tests when changing behaviour, and to keep new database access on prepared statements through the existing helpers.

## Contributing to the project

To contribute:

- Create a branch for your change: `git checkout -b feat/your-change`
- Commit using the Conventional Commits style: `git commit -m "feat: add pagination to expense list"`
- Push the branch and open a pull request: `git push origin feat/your-change`

Please keep changes small and focused, reuse the shared helpers rather than duplicating logic, and make sure `composer test` passes before opening a pull request.
