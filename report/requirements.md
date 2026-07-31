---
title: Requirements
has_children: false
nav_order: 3
---

# Requirements

The requirements start from what a person actually needs at each step of tracking their spending. The user stories come first, the functional requirements follow from them, and each requirement is written with acceptance criteria concrete enough to be tested rather than argued about.

## User stories

1. As a budget-minded user, I want to record small daily expenses quickly, so that I can make my money last the whole month.

2. As a new user, I want to create a private account, so that only I can see and manage my financial records.

3. As a budget-conscious user, I want to assign a monthly budget to each category, so that I can compare what I planned with what I actually spent.

4. As a traveller, I want to record expenses in different currencies, so that my spending abroad is stored accurately.

5. As a busy professional, I want recurring bills to be created automatically, so that I do not have to re-enter the same payment every month.

6. As an organised user, I want to attach a receipt to an expense, so that I can keep proof of a purchase together with its record.

7. As an analytical user, I want daily, monthly, and yearly reports with charts, so that I can understand my spending over different time frames.

8. As a user, I want to filter my expenses and export the result, so that I can reuse selected records in a spreadsheet.

## Functional requirements

- The user must be able to register and authenticate.
    * The user can open the sign-up page and create an account with a full name, mobile number, email, and password.
    * The system rejects a registration whose email is already in use.
    * The user can log in with valid credentials and is redirected to the dashboard.
    * The user can log out, after which protected pages require logging in again.

- The user must be able to recover and change a password.
    * The user can request a password reset and set a new password.
    * A logged-in user can change their password from the profile area.

- The user must be able to add an expense.
    * The user can open the add-expense form and provide a date, item, amount, currency, and category, together with optional notes and a receipt.
    * The system validates the input and stores the expense against the current user.
    * The new expense immediately appears in the expense list and in the dashboard totals.

- The user must be able to edit and delete an expense.
    * The user can open an existing expense, change any field, and save the update.
    * The user can delete an expense, after which it no longer appears in any list or summary.
    * Deleting an expense that has a receipt also removes the stored receipt file.

- The user must be able to search, filter, and export expenses.
    * The user can filter the expense list by free text, date range, amount range, category, and currency.
    * The list shows only the records that match the active filters.
    * The user can export the filtered records to a downloadable CSV file.

- The user must be able to manage categories and monthly budgets.
    * The user can create categories that belong only to their account.
    * The user can assign a monthly budget amount and currency to a category.
    * The current spending is shown against the budget as a progress value that is clamped between 0% and 100%.

- The user must be able to schedule recurring expenses.
    * The user can define a recurring rule with an item, amount, currency, category, frequency (weekly or monthly), and start date.
    * The system stores the rule together with its next run date and an active/inactive flag.
    * When a rule is due, the system automatically generates the corresponding expense and advances the next run date.
    * The user can deactivate a rule to stop further generation.

- The user must be able to view a dashboard.
    * The dashboard shows summary figures such as spending for the current period and the total recorded so far.
    * The dashboard shows chart-based insights, including the distribution of spending across categories.

- The user must be able to generate reports.
    * The user can produce date-wise, month-wise, and year-wise reports for a chosen period and currency.
    * Each report shows summary cards, an average per period, a detailed table, and a category breakdown with a chart.
    * The user can export a report to CSV or print it.

## Non-functional requirements

- The application must be usable by non-technical users.
    * Every action (add, edit, filter, report) is reachable from a clearly labelled page in a small number of clicks.
    * The system shows a confirmation or error message after each meaningful action.

- The application must respond quickly for personal-scale data.
    * Dashboard and report pages should load without noticeable delay for a typical single user's dataset.

- User data must be kept private and reasonably secure.
    * Protected pages are accessible only within an authenticated session.
    * User-supplied input is escaped on output, and newer database access uses prepared statements and CSRF tokens. The older authentication pages do not yet meet this criterion, which is recorded in the Self-evaluation.

- The application must be portable.
    * The project runs on a standard XAMPP environment (Apache, MySQL/MariaDB, PHP) without additional server software.

- The codebase must be maintainable.
    * Shared behaviour is centralised in helper files so that pages do not duplicate the same logic.

## Implementation

- The application must be built with PHP and MySQL.
    * Server-side pages are written in PHP and persist data in a MySQL database named `detsdb`.

- The frontend must use standard web technologies.
    * The interface is built with HTML, CSS, Bootstrap, JavaScript, and jQuery, and uses Chart.js for visualisation.

- The project must run under XAMPP.
    * The application is developed and executed with the Apache and MySQL services provided by XAMPP.

- Shared logic must be provided through helper files.
    * `includes/expense-helpers.php` and `includes/report-helpers.php` centralise validation, formatting, prepared-statement execution, CSRF handling, category defaults, budget calculation, and recurring processing.

- The project must include automated checks.
    * PHPUnit unit tests cover the helper functions, and a browser end-to-end test exercises the running site; both are described in the Validation section.

- The source must be stored in a Git repository on GitHub.
    * The repository keeps a clear commit history following the Conventional Commits style, and its automated checks run through GitHub Actions.
