---
title: Design
has_children: false
nav_order: 4
---

# Design

## Architecture

The application is organised as a **layered architecture** with three layers: what the user sees, the shared rules, and the stored data. The main practical benefit for me was testability, since the middle layer can be exercised by unit tests without a browser or a live database.

1. *Presentation layer*: the PHP pages that render forms, tables, dashboard cards, and charts using HTML, CSS, Bootstrap, JavaScript, and Chart.js. This layer shows data and collects input, and nothing else.

2. *Logic layer*: the helper files, mainly `includes/expense-helpers.php` and `includes/report-helpers.php`, holding validation, formatting, prepared-statement execution, CSRF handling, budgeting, and recurring-expense logic. Keeping these rules in one place is what stopped the pages from each growing their own copy.

3. *Data layer*: the MySQL database, reached through the shared connection in `includes/dbconnection.php`, responsible for storing and returning the user's records.

```text
+----------------------------+
|        Web browser         |
|     (GUI: pages, charts)   |
+-------------+--------------+
              |
              v
+----------------------------+
|     PHP presentation       |
|  auth · dashboard ·        |
|  expenses · reports        |
+-------------+--------------+
              |
              v
+----------------------------+
|    Shared helper logic     |
|  validation · formatting · |
|  CSRF · budgets · recurring|
+-------------+--------------+
              |
              v
+----------------------------+
|      MySQL database        |
|  tbluser · tblexpense ·    |
|  tblcategories · tblbudgets|
|  tblrecurring · tblitems   |
+----------------------------+
```

## Modelling

The **domain** of the Expense Tracker System is personal finance management. Its **bounded context** is the recording and analysis of one user's expenses, plus the concepts that give those expenses meaning: categories, budgets, and recurring rules. Anything outside that boundary, such as bank integration or shared household accounts, was deliberately left out.

The **domain model** exists so that the application can store, aggregate, and present spending consistently. I kept it to a small number of concepts, because every extra entity would have had to be carried through the forms, the reports, and the database.

```text
User 1 ────────< Expense >──────── 1 Category
  │                                     │
  ├────< Item                           ├────< Budget
  ├────< Category                       └────< RecurringRule
  ├────< Budget
  └────< RecurringRule
```

1. *Entities*. The central entity is the `Expense`. It belongs to one user and carries:
    - `id`, the unique identifier used when editing or deleting;
    - `userId`, the owner, which is what keeps each user's data separate;
    - `expenseDate`, the day the money was spent;
    - `expenseItem`, a short description of what was bought;
    - `expenseCost` and `currency`, the amount and the currency it is expressed in;
    - `categoryId`, the category it belongs to;
    - `notes` and `receiptPath`, optional detail and a link to a stored receipt file.

    Around it sit `Category` (a user-defined grouping), `Budget` (a planned amount for one category, month, and currency), `RecurringRule` (a template that generates expenses on a schedule), and `Item` (a reusable expense name). Each has its own lifecycle and belongs to exactly one user.

2. *Repositories*. The helper functions stand in for repositories, sitting between the pages and the database. `expense_prepare_and_execute`, `expense_fetch_all_assoc`, `expense_get_categories`, and `expense_process_recurring` wrap the SQL needed to read and write the entities. The pages call a named operation rather than writing a query, the read and write logic for an entity stays in one file, and because every page uses the same helpers the behaviour does not drift between them.

3. *Data-layer integration*. The model is persisted in MySQL. `expense_ensure_schema` checks at runtime that the expected columns and tables exist and creates the missing ones, which is how the schema was able to keep evolving while older databases stayed usable. Isolation between users comes from always filtering on the current `userId`.

### Design principles

Three principles guided the design:
- *Separation of concerns*: presentation, shared logic, and persistence live in different places, so a change to how money is formatted does not mean editing a dozen pages.
- *Reuse over repetition*: escaping output, formatting money, and running prepared statements are single functions, not copied fragments.
- *Room to grow*: attributes and entities can be added to the model, and the runtime schema helper extends existing tables without invalidating data that is already there.

### Tactical patterns

The project is not a formal Domain-Driven Design implementation, but several tactical patterns influenced it:

- **Entities**: `Expense`, `Category`, `Budget`, and `RecurringRule` each have their own identity (an `id`) and their own lifecycle.

- **Repositories**: the helper functions play the repository role, hiding the SQL behind named operations so that a page asks for categories rather than writing a `SELECT`.

- **Value objects**: `currency` and `expenseCost` behave like value objects, though they are still two separate columns. Promoting the pair into a single money value object would be the obvious next step, and would put formatting and conversion in one place.

- **Aggregates**: `User` is the natural aggregate root, since every other entity belongs to exactly one user. Every query is scoped by `UserId`, which is what actually enforces the boundary in practice.

## Interaction

The sequence below shows what happens when an expense is added. The browser posts the form, the PHP page calls the helpers to check the CSRF token and validate the values, the helpers run a prepared statement, and the result travels back up so the page can show a confirmation and the refreshed list.

```text
User -> Browser        : fill and submit the add-expense form
Browser -> PHP page    : POST expense data (with CSRF token)
PHP page -> Helpers    : verify CSRF, validate and format input
Helpers -> Database    : prepared INSERT into tblexpense
Database -> Helpers    : insert result
Helpers -> PHP page    : success or error outcome
PHP page -> Browser    : show confirmation and refreshed list
```

The other write workflows follow the same shape, including editing an expense and creating categories, budgets, and recurring rules.

## Behaviour

The state diagram below is the lifecycle of an expense. It starts as *draft input* in the form, becomes *stored* once submitted and accepted, and from there can be *edited* (which returns it to stored) or *deleted*, after which it is gone along with its receipt file.

```text
Draft input --submit--> Validated --save--> Stored
                                              |  ^
                                         edit |  | save
                                              v  |
                                            Edited
                                              |
                                        delete v
                                           Deleted
```

Recurring rules have a related lifecycle. A rule is *created* and becomes *active*; each time its next run date is reached, it *generates* an expense and advances its schedule; the user can move it to an *inactive* state to stop further generation.

```text
Created --> Active --due date--> Generates expense --> Active
              |
       deactivate
              v
           Inactive
```

## Data-related aspects

1. *Data persistence technology*: the system uses a **MySQL/MariaDB** relational database called `detsdb`, which comes with the XAMPP stack. I chose a relational store because the data is already relational: expenses point at categories, budgets point at categories and months, and every row points at its owner. A document store would have made the report aggregations harder rather than easier.

2. *Main tables*: the database has six tables, each scoped to a user through a `UserId` column.
    - `tbluser`: accounts, plus the default currency and default category used to pre-fill forms.
    - `tblexpense`: the expense records themselves, with date, item, cost, currency, category, notes, receipt path, and creation time.
    - `tblcategories`: user-defined categories.
    - `tblitems`: reusable expense item names.
    - `tblbudgets`: monthly budget amounts per category and currency.
    - `tblrecurring`: recurring rules with frequency, next run date, and an active flag.

3. *Integrity and isolation*: every query is filtered by the logged-in user's `UserId`, which is how one account's data is kept away from another's. Newer pages pass their values through prepared statements, and `expense_ensure_schema` keeps the physical schema in line with what the code expects to find.
