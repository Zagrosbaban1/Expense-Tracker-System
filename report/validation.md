---
title: Validation
has_children: false
nav_order: 6
---

# Validation

## Testing

No single kind of test would have covered this project. Its behaviour is spread across reusable functions, individual pages, and workflows that only make sense in a browser, so I used four approaches together:

- *Unit tests* on the helper functions, written with **PHPUnit**.
- *Syntax smoke tests* that run PHP's built-in linter (`php -l`) over every page and helper, which catches a parse error in a file I have not opened in the browser yet.
- *End-to-end browser tests* that drive the running site the way a user would, using **Playwright** from Python against Chromium.
- *Manual acceptance testing* of the main workflows against a written checklist.

The PHPUnit tests live in `tests/unit/`, the Playwright script in `tests/e2e/`, and the manual checklist in `tests/acceptance-checklist.md`.

The unit and syntax tests are run with a single command through Composer:

```powershell
composer test
```

### Success rate

The PHPUnit suite currently reports:

```text
OK (44 tests, 57 assertions)
```

Everything passes. The suite covers the currency options and symbols, money formatting, HTML escaping, month-key generation, currency validation, budget-progress clamping, CSRF token verification, receipt-upload validation, receipt deletion, the report helpers, and the syntax validity of every PHP page. The Playwright script completes its run against a local XAMPP instance and reports a pass.

### Coverage

Coverage is strongest at the two ends of the system, the helper functions and the full browser workflows, and thinnest in the middle, where the logic inside an individual page is only reached indirectly.

- Unit tests: currency handling, formatting and escaping, budgeting maths, CSRF verification, and receipt-file validation and deletion.
- Syntax smoke test: every PHP file in the project root and in `includes/`.
- End-to-end test: login and invalid-login handling, registration, the redirect that protects a page from a signed-out visitor, creation of items, categories, budgets and expenses, expense filtering, recurring-expense creation, and navigation across the dashboard, expense, report, profile, and password pages.

### Future testing plans

The clearest gaps are the edit and delete flows, a real assertion on the contents of a downloaded CSV, and security-focused cases around authentication and file uploads. Setting up a dedicated test database would help most of all, because the page logic could then be tested directly instead of only through a browser.

### Comments with respect to the requirements' acceptance criteria

Both the automated and the manual tests were written from the functional requirements and their acceptance criteria, not the other way round. Every requirement group has a matching entry in the acceptance checklist, so a passing run maps directly onto the behaviour promised in the Requirements chapter.

## Acceptance test

Acceptance testing was done manually, to check the application from the user's point of view rather than the code's. Each scenario comes from a user story or a functional requirement.

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

Every acceptance test confirmed that the application meets the user requirements set out earlier in this report. The tests also showed up weaker areas, above all the legacy authentication code, and those are written down in the Self-evaluation and Future work chapters rather than left out.
