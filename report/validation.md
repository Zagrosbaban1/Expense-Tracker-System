---
title: Validation
has_children: false
nav_order: 6
---

# Validation

## Testing

For the validation of the Expense Tracker System, a combination of testing approaches was used, because the project is a PHP and MySQL web application whose behaviour spans reusable functions, individual pages, and full browser workflows.

- Type of tests conducted:
    - *Unit tests* on the reusable helper functions, using **PHPUnit**.
    - *Syntax smoke tests* that run PHP's built-in linter (`php -l`) against every application page and helper.
    - *End-to-end browser tests* that drive the running site as a real user, using **Playwright** through Python and a Chromium browser.
    - *Manual acceptance testing* of the main workflows against a checklist.
- Testing framework: PHPUnit for the unit and syntax tests, and Playwright for the browser end-to-end test. The test files live under `tests/unit/` and `tests/e2e/`, and the manual checklist is stored in `tests/acceptance-checklist.md`.

The unit and syntax tests are run with a single command through Composer:

```powershell
composer test
```

### Success rate

The automated PHPUnit suite currently reports:

```text
OK (44 tests, 57 assertions)
```

All tests pass. The suite exercises the currency options and symbols, money formatting, HTML escaping, month-key generation, currency validation, budget-progress clamping, CSRF token verification, receipt-upload validation, receipt deletion, the report helpers, and the syntax validity of every PHP page. The Playwright end-to-end script completes its full run against a local XAMPP instance and prints a passing result.

### Coverage

Coverage is strongest at the two ends of the system — the reusable helper functions and the full browser workflows — and lighter in the middle, where individual page logic is still only exercised indirectly.

- Areas covered by unit tests: currency handling, formatting and escaping, budgeting maths, CSRF verification, and receipt-file validation and deletion.
- Areas covered by the syntax smoke test: every PHP file in the project root and in `includes/`.
- Areas covered by the end-to-end test: login and invalid-login handling, registration, the protected-page redirect, item, category, budget, and expense creation, expense filtering, recurring-expense creation, and navigation across the dashboard, expense, report, profile, and password pages.

### Future testing plans

To raise coverage further, additional tests should be added for the edit and delete flows, for a real CSV-download assertion, and for security-focused cases around authentication and file uploads. Introducing a dedicated test database would also allow the page logic to be tested directly rather than only through the browser.

### Comments with respect to the requirements' acceptance criteria

The automated and manual tests were derived from the functional requirements and their acceptance criteria. Each requirement group has a matching entry in the acceptance checklist, so that a passing run corresponds directly to the behaviour promised in the Requirements section.

## Acceptance test

Acceptance testing was carried out manually to confirm that the application meets its requirements from the user's point of view. The scenarios were taken directly from the user stories and functional requirements.

### Test cases

1. User registration
    - Description: Verify that a new user can register with valid details.
    - Outcome: Passed. The account was created and could then be used to log in; a duplicate email was correctly rejected.

2. Login and logout
    - Description: Verify that a registered user can log in and log out.
    - Outcome: Passed. Valid credentials reached the dashboard, invalid credentials were refused, and logging out required signing in again for protected pages.

3. Add expense
    - Description: Verify that an expense can be created with valid details.
    - Outcome: Passed. The expense was stored and appeared immediately in the list and the dashboard totals.

4. Edit and delete expense
    - Description: Verify that an existing expense can be updated and removed.
    - Outcome: Passed. Edited values were saved correctly, and a deleted expense (along with its receipt) no longer appeared.

5. Search, filter, and export
    - Description: Verify that filtering narrows the list and that the result can be exported.
    - Outcome: Passed. Only matching records were shown, and the CSV export contained the same filtered data.

6. Categories and budgets
    - Description: Verify that categories and monthly budgets can be created and are reflected elsewhere.
    - Outcome: Passed. New categories became available in the expense forms, and budget status appeared in the category and dashboard views.

7. Recurring expenses
    - Description: Verify that a recurring rule generates due expenses automatically.
    - Outcome: Passed. Weekly and monthly rules were stored, and due expenses were inserted into the expense table when the application was next opened.

8. Reports
    - Description: Verify that date-wise, month-wise, and year-wise reports display correct summaries and charts.
    - Outcome: Passed. Report totals, averages, detailed tables, and the category breakdown matched the selected period, and CSV export and printing worked.

### Comments with respect to the requirements' acceptance criteria

All acceptance tests confirmed that the application fulfils the key user requirements defined in the project documentation. Where the tests revealed weaker areas — notably in legacy authentication code — these are recorded honestly in the Self-evaluation and Future work sections rather than hidden.
