---
title: Validation
nav_order: 6
---

# Validation

## Testing Approach

The project does not include automated unit or integration tests in the repository, so validation is mainly manual. Manual testing can still be structured around core workflows.

## Sample Test Cases

| Test Case | Input | Expected Result |
| --- | --- | --- |
| User registration | Valid name, email, mobile, password | User account is created successfully |
| Login | Correct email and password | User is redirected to dashboard |
| Add expense | Valid date, item, amount, category | New expense is saved and listed |
| Edit expense | Update amount or category | Existing expense is updated |
| Delete expense | Valid selected record | Expense is removed from list |
| Add category | New category name | Category is stored successfully |
| Save budget | Category, month, amount | Budget is available in category view and dashboard |
| Add recurring expense | Weekly or monthly schedule | Future expense is auto-generated on due date |
| Generate report | Valid date or month range | Report table and charts are displayed |
| Export CSV | Click export | Data downloads in CSV format |

## Validation Results

From inspection of the code, the project supports the complete main workflow successfully:

- User authentication is available.
- Expense CRUD operations are implemented.
- Filters and CSV export are implemented.
- Budget and category features are integrated with the dashboard.
- Recurring expense automation exists.
- Reporting is implemented in three time scopes.

## Quality Observations

The project shows practical functionality, but validation also reveals areas for improvement:

- Some pages use prepared statements, while older pages still use direct SQL queries.
- Passwords are stored using MD5, which is not secure by modern standards.
- Schema migration logic is triggered at runtime from helper functions instead of using dedicated migration scripts.
- The project has no automated test suite.
