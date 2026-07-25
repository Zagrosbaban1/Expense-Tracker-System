---
title: Development
has_children: false
nav_order: 5
---

# Development

## Distributed Version Control System (DVCS)

For the version control of this project, **Git** was used together with **GitHub** as the remote host. The development process was kept simple, with work carried out mainly on the `main` branch and supporting branches used for the report site. New features, fixes, and documentation were committed as small, focused changes.

To keep the project history readable, commit messages follow the Conventional Commits format `<type>: <subject>`, where the type is one of `feat`, `fix`, `docs`, `test`, `style`, or `chore`, and the subject is a short description of the change. This makes it easy to scan the history and to see at a glance whether a commit added a feature, fixed a bug, or updated the documentation.

## Implementation details

The project began as a straightforward procedural PHP application in which SQL, HTML, and business logic were mixed together in each page. As the feature set grew, the biggest engineering effort went into gradually pulling shared logic out of the pages and into reusable helpers, without breaking the parts that already worked.

The helper file `includes/expense-helpers.php` became the centre of this refactoring. It now provides the reusable building blocks used across the newer pages:

- currency handling — the list of supported currencies, the mapping to display symbols (for example `€` and `£`), and money formatting;
- output escaping — a single function that escapes user data before it is printed, to guard against HTML injection;
- safe database access — a wrapper that prepares and executes parameterised statements and helpers that fetch rows as associative arrays;
- CSRF protection — token generation and verification for form submissions;
- schema maintenance — `expense_ensure_schema`, which checks for and creates the columns and tables that newer features need;
- domain logic — category defaults, budget-progress calculation, and the recurring-expense processing that turns due rules into real expenses.

One recurring challenge (`expense_process_recurring`) was making the recurring-payment feature behave correctly when the application had not been opened for a while. The logic advances a rule's next run date in a loop, generating one expense for each period that has passed, so that a monthly bill that became due several times still produces the right number of records rather than a single one.

Another consideration was that the project needed to keep running on existing databases that were created before some columns existed. Rather than requiring a manual database rebuild, the runtime schema helper adds any missing columns and tables the first time a relevant page is opened. This kept local upgrades painless, at the cost of not having a formal migration history — a trade-off discussed in the Self-evaluation section.

In the end the application reached its goals: the core expense workflow is complete, and it is surrounded by categories, budgets, recurring payments, receipts, multi-currency support, dashboards, and reports. There is still room to move the remaining legacy pages onto the same helper-based foundation, but that work goes beyond the scope of this project.
