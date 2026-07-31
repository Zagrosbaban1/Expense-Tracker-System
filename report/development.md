---
title: Development
has_children: false
nav_order: 5
---

# Development

## Distributed Version Control System (DVCS)

I used **Git** for version control, with **GitHub** as the remote. The workflow was deliberately simple: most work happened on `main`, with supporting branches used for the report site. Features, fixes, and documentation went in as small commits rather than large ones, which made it much easier to go back and find where something broke.

Commit messages follow the Conventional Commits format `<type>: <subject>`, where the type is one of `feat`, `fix`, `docs`, `test`, `style`, or `chore`. Reading `git log --oneline` then tells you at a glance whether a commit added a feature, fixed a bug, or only touched the documentation.

## Implementation details

The project started as a plain procedural PHP application, with SQL, HTML, and business logic all mixed together in the same file. That was quick to write and became difficult to change. As the feature set grew, most of the engineering effort went into pulling the shared logic out of the pages and into reusable helpers, without breaking what already worked.

`includes/expense-helpers.php` is where that refactoring ended up. It now holds the building blocks the newer pages are written against:

- currency handling: the list of supported currencies, the mapping to display symbols such as `€` and `£`, and money formatting;
- output escaping: one function that escapes user data before it is printed, which closes off HTML injection;
- safe database access: a wrapper that prepares and executes parameterised statements, plus helpers that return rows as associative arrays;
- CSRF protection: token generation and verification for form submissions;
- schema maintenance: `expense_ensure_schema`, which creates the columns and tables that newer features expect;
- domain logic: category defaults, budget-progress calculation, and the recurring-expense processing.

The hardest part to get right was `expense_process_recurring`, and specifically what should happen when the application has not been opened for a while. Comparing the next run date against today and inserting one expense is not enough: a monthly bill that fell due three times would produce a single record. The working version advances the rule's next run date in a loop and generates one expense for every period that has passed, so nothing is silently lost.

The second problem was that new columns kept being added while databases created under the older schema were still in use. Rather than requiring a manual rebuild, the schema helper adds any missing columns and tables the first time a relevant page is opened. That made local upgrades painless, at the cost of leaving the project with no migration history, and it is not a choice I would repeat on a system with more than one deployment. The trade-off comes up again in the Self-evaluation.

By the end, the core expense workflow was complete and surrounded by categories, budgets, recurring payments, receipts, multi-currency support, dashboards, and reports. What is still outstanding is moving the remaining legacy pages, mostly the authentication ones, onto the same helper-based foundation.
