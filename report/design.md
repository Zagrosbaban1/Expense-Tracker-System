---
title: Design
has_children: false
nav_order: 4
---

# Design

## Architecture

The application follows a **layered architecture**, which keeps a clear separation of concerns between the parts that display information, the parts that hold the rules and shared logic, and the parts that store data. This separation improves the maintainability and testability of the system. The architecture is made of three main layers:

1. *Presentation layer*: The PHP pages that render forms, tables, dashboard cards, and charts using HTML, CSS, Bootstrap, JavaScript, and Chart.js. This layer is responsible for showing data to the user and capturing input.

2. *Logic layer*: The reusable helper files, mainly `includes/expense-helpers.php` and `includes/report-helpers.php`, which hold validation, formatting, prepared-statement execution, CSRF handling, budgeting, and recurring-expense logic. This layer keeps the individual pages thin and avoids duplicating the same rules.

3. *Data layer*: The MySQL database, reached through a shared connection in `includes/dbconnection.php`. This layer is responsible for persisting and retrieving the user's records.

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
|      MySQL database         |
|  tbluser · tblexpense ·    |
|  tblcategories · tblbudgets|
|  tblrecurring · tblitems   |
+----------------------------+
```

## Modelling

The **domain** on which the Expense Tracker System is focused is personal finance management. Its **bounded context** is the recording and analysis of a single user's expenses, together with the supporting concepts that give those expenses meaning: categories, budgets, and recurring rules.

The **domain model** is designed to capture the core information and rules of expense tracking so that the application can store, aggregate, and present spending consistently. The model is organised around a small number of clear concepts and their relationships.

```text
User 1 ────────< Expense >──────── 1 Category
  │                                     │
  ├────< Item                           ├────< Budget
  ├────< Category                       └────< RecurringRule
  ├────< Budget
  └────< RecurringRule
```

1. *Entities* — The central entity of the domain is the `Expense`. Each expense belongs to one user and is characterised by the following attributes:
    - id — a unique identifier used for editing and deletion;
    - userId — the owner of the record, which keeps every user's data isolated;
    - expenseDate — the day the money was spent;
    - expenseItem — a short description of what was bought;
    - expenseCost — the amount spent;
    - currency — the currency the amount is expressed in;
    - categoryId — the category the expense belongs to;
    - notes and receiptPath — optional extra information and a link to a stored receipt file.

    The supporting entities are `Category` (a user-defined grouping), `Budget` (a monthly planned amount for a category in a given currency), `RecurringRule` (a template that generates expenses on a schedule), and `Item` (a reusable expense name). Each of these has its own lifecycle and belongs to exactly one user.

2. *Repositories* — In this project the role of the repository is played by the shared helper functions that mediate between the pages and the database. Functions such as `expense_prepare_and_execute`, `expense_fetch_all_assoc`, `expense_get_categories`, and `expense_process_recurring` encapsulate the SQL needed to read and write the entities, giving the following benefits:
    - Abstraction: the pages call a named helper instead of writing raw SQL, so they do not need to know the exact query behind an operation.
    - Encapsulation: the create, read, update, and delete logic for an entity is kept in one place.
    - Consistency: the same helpers are reused across pages, so data operations behave the same way everywhere.

3. *Data-layer integration* — The model is persisted in a MySQL database. A helper, `expense_ensure_schema`, checks at runtime that the required columns and tables exist and creates any that are missing, which allows the schema to evolve as new features are added. User isolation is enforced by always filtering queries on the current `userId`.

### Design principles

The design follows several principles:
- *Separation of concerns*: presentation, shared logic, and persistence are kept in distinct places, which makes each part easier to change.
- *Reuse over repetition*: common operations such as escaping output, formatting money, and running prepared statements live in helper functions rather than being copied into each page.
- *Future scalability*: new attributes or entities can be added to the model, and the runtime schema helper makes it straightforward to extend existing tables without breaking older data.

### Tactical patterns

The influence of tactical design patterns can be seen in the following ways:

- **Entities**: `Expense`, `Category`, `Budget`, and `RecurringRule` are modelled as entities, each with a distinct identity (its `id`) and its own lifecycle.

- **Repositories**: the helper functions act as repositories that abstract the details of storing and retrieving entities, so the pages depend on named operations rather than on SQL.

- **Value objects**: attributes such as `currency` and `expenseCost` behave like value objects; in a future iteration the pair of amount and currency could be promoted into an explicit money value object to centralise formatting and conversion.

- **Aggregates**: the `User` acts as the natural aggregate root, since every other entity is owned by, and only accessible through, a single user. Queries are consistently scoped to the owning user, which enforces this boundary.

## Interaction

The sequence below illustrates the process of adding an expense. The user submits the add-expense form in the browser; the PHP page receives the request and calls the helper functions to validate and prepare the values; the helpers execute a prepared statement against the database; the result flows back through the layers, and the page renders a confirmation and the updated list.

```text
User -> Browser        : fill and submit the add-expense form
Browser -> PHP page    : POST expense data (with CSRF token)
PHP page -> Helpers    : verify CSRF, validate and format input
Helpers -> Database    : prepared INSERT into tblexpense
Database -> Helpers    : insert result
Helpers -> PHP page    : success or error outcome
PHP page -> Browser    : show confirmation and refreshed list
```

This sequence is representative of the other write workflows as well, including editing an expense and creating categories, budgets, and recurring rules.

## Behaviour

The state diagram below outlines the lifecycle of an expense within the system. An expense begins as *draft input* in the form. Once submitted and accepted, it becomes a *stored* expense. From the stored state it can be *edited* (returning to stored) or *deleted*, after which it no longer exists.

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

1. *Data persistence technology* — The system uses a **MySQL/MariaDB** relational database named `detsdb`, provided by the XAMPP stack. A relational store is chosen because the domain is naturally tabular and relational: expenses reference categories, budgets reference categories and months, and every record references its owner.

2. *Main tables* — The database is organised into the following tables, each owned per user through a `UserId` column:
    - `tbluser` — accounts and default preferences (default currency and category).
    - `tblexpense` — the individual expense records, including date, item, cost, currency, category, notes, receipt path, and creation time.
    - `tblcategories` — user-defined categories.
    - `tblitems` — reusable expense item names.
    - `tblbudgets` — monthly budget amounts per category and currency.
    - `tblrecurring` — recurring rules with frequency, next run date, and an active flag.

3. *Integrity and isolation* — Every query is scoped to the logged-in user's `UserId`, which keeps each account's data separate. Newer pages use prepared statements to pass values safely, and the `expense_ensure_schema` helper keeps the physical schema aligned with the features the code expects.
