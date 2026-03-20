---
title: Design
nav_order: 4
---

# Design

## System Architecture

The project follows a simple modular web architecture:

1. Presentation Layer  
   This includes PHP pages mixed with HTML, CSS, Bootstrap, and JavaScript. It is responsible for forms, tables, navigation, and charts.

2. Business Logic Layer  
   Shared logic is placed in helper files such as `includes/expense-helpers.php` and `includes/report-helpers.php`. These files handle currency formatting, schema checks, CSRF protection, category utilities, recurring processing, and report formatting.

3. Data Access Layer  
   Database communication is handled through MySQLi using the shared connection file `includes/dbconnection.php`. Several modules use prepared statements, especially in newer expense-management pages.

## Main Modules

- Authentication module: registration, login, forgot password, reset password, and password change.
- Dashboard module: overview metrics, charts, latest expense, category analysis, and budget health.
- Expense management module: add, edit, delete, filter, and export expense records.
- Item management module: reusable list of expense item names.
- Category and budget module: user-defined categories and monthly spending limits.
- Recurring expense module: scheduled weekly or monthly expenses.
- Reporting module: day-wise, month-wise, and year-wise summaries with charts and CSV export.
- Profile module: user data and default preferences such as currency and default category.

## Database Design

From the codebase, the project uses the following main tables:

### `tbluser`

- `ID`
- `FullName`
- `MobileNumber`
- `Email`
- `Password`
- `RegDate`
- `DefaultCurrency`
- `DefaultCategoryId`

### `tblexpense`

- `ID`
- `UserId`
- `ExpenseDate`
- `ExpenseItem`
- `ExpenseCost`
- `Currency`
- `CategoryId`
- `Notes`
- `ReceiptPath`

### `tblitems`

- `ID`
- `UserId`
- `ItemName`
- `CreatedAt`

### `tblcategories`

- `ID`
- `UserId`
- `CategoryName`
- `CreatedAt`

### `tblbudgets`

- `ID`
- `UserId`
- `CategoryId`
- `BudgetMonth`
- `Currency`
- `BudgetAmount`
- `CreatedAt`
- `UpdatedAt`

### `tblrecurring`

- `ID`
- `UserId`
- `ExpenseItem`
- `ExpenseCost`
- `Currency`
- `CategoryId`
- `Notes`
- `Frequency`
- `StartDate`
- `NextRunDate`
- `LastRunDate`
- `IsActive`
- `CreatedAt`

## Input Design

The system uses form-based inputs for registration, login, adding expenses, filtering reports, updating profile information, and setting budgets. Input controls include text fields, password fields, date pickers, month selectors, dropdown lists, numeric inputs, file uploads, and action buttons.

## Output Design

The system produces outputs in the form of:

- Dashboard cards
- Tables of expense records
- Chart-based visual summaries
- Budget progress indicators
- CSV exports
- Daily, monthly, and yearly report pages
