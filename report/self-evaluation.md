---
title: Self-evaluation
has_children: false
nav_order: 12
---

# Self-evaluation

I created this project and was responsible for every stage of the process, from the database design and the application code to the testing, the automation, and this report.

## Strengths

- **A complete, working product**: the application delivers the full expense-tracking workflow, from account creation and daily entry through to budgeting, recurring payments, dashboards, and reports. The core features all reach their intended goal.
- **More than basic CRUD**: the project goes well beyond simple record-keeping by adding categories, per-category monthly budgets with progress indicators, recurring-expense automation, receipt uploads, multi-currency support, CSV export, and printable reports.
- **Movement toward good engineering practice**: newer pages are built on a shared helper layer that provides prepared statements, output escaping, and CSRF tokens, which reduced duplication and improved safety compared with the original code.
- **A genuine testing and automation setup**: the project has a passing PHPUnit suite, a browser end-to-end test, and GitHub Actions workflows for linting, dependency validation, packaging, and report publishing.
- **Experience gained**: building the whole system gave me practical experience across database design, PHP development, security concerns, testing, and CI/CD, and strengthened my understanding of how these parts fit together.

## Weaknesses

- **Inconsistent security across the codebase**: the newer pages use prepared statements and CSRF protection, but some older pages — including parts of the authentication flow — still use direct SQL and MD5 password hashing, which is not acceptable for a real deployment.
- **Uneven test depth**: coverage is good for the helper functions and the main browser flow, but individual page logic is only tested indirectly, and edit/delete and CSV-download paths are not yet asserted automatically.
- **Informal schema evolution**: the database is extended at runtime by helper code rather than through a proper migration system, which is convenient locally but makes the schema history harder to track.
- **Still largely procedural**: the architecture is modular and layered in intent, but the pages remain file-based procedural PHP rather than a formal framework, which limits how far the separation of concerns can be enforced.
- **No external code review**: the project would have benefited from a second pair of eyes, since automated checks catch syntax and regressions but not design or usability concerns.

Overall I am satisfied that the project achieves its aims and demonstrates a realistic progression from a basic application toward more maintainable and better-tested software, while being honest about the legacy areas that a further iteration should address first.
