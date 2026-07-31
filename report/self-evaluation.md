---
title: Self-evaluation
has_children: false
nav_order: 12
---

# Self-evaluation

I worked on this project alone and was responsible for all of it: the database design, the application code, the tests, the automation, and this report.

## Strengths

- **It is finished and it works.** The application covers the whole expense-tracking workflow, from creating an account and entering a purchase through to budgeting, recurring payments, dashboards, and reports. Every core feature does what it was specified to do.
- **It goes past basic CRUD.** Categories, per-category monthly budgets with progress indicators, automatic recurring expenses, receipt uploads, multiple currencies, CSV export, and printable reports were all added on top of the record-keeping.
- **The engineering improved as it went.** The newer pages sit on a shared helper layer with prepared statements, output escaping, and CSRF tokens. Comparing them with the original code is the clearest evidence of what I learned during the project.
- **The testing and automation are real.** There is a passing PHPUnit suite of 44 tests, a Playwright end-to-end test that drives the live site, and GitHub Actions workflows for linting, dependency validation, packaging, and report publishing.
- **I understand the whole stack now.** Building every layer myself, rather than one part of a larger system, is what tied database design, PHP, security, testing, and CI/CD together for me.

## Weaknesses

- **Security is inconsistent.** The newer pages use prepared statements and CSRF protection, but parts of the authentication flow still use direct SQL and MD5 password hashing. MD5 is not acceptable for storing passwords, and I would not deploy this publicly in its current state.
- **The tests are uneven.** The helper functions and the main browser flow are well covered; the logic inside individual pages is only tested indirectly, and the edit, delete, and CSV-download paths have no automatic assertions.
- **Schema evolution is informal.** Extending the database from helper code at runtime was convenient while I was the only user, but it leaves no migration history and makes the true state of the schema harder to see.
- **It is still procedural.** The architecture is layered in intent, but the pages are file-based procedural PHP rather than a framework, which limits how strictly the separation of concerns can actually be enforced.
- **Nobody else reviewed it.** Working alone meant the automated checks caught syntax problems and regressions, but no one questioned a design decision or told me a page was confusing to use.

Looking back, the project does what I set out to do and shows a genuine progression from a basic application towards something more maintainable and better tested. The legacy authentication code is the part I am least happy with, and it is where a second iteration should start.
