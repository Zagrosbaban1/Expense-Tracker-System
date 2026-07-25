---
title: Future Work
has_children: false
nav_order: 13
---

# Known issues and future work

## What is missing

1. *Pagination for large lists*: the expense list loads all matching records at once, which is fine for personal-scale data but would become slow for a very large history. Paginated or lazily loaded results are needed.
2. *Automated tests in CI*: the PHPUnit suite exists and passes locally, but the pipeline does not yet run it, so a failing test would not currently block a change.
3. *PDF report export*: reports can be exported to CSV and printed, but there is no direct PDF export, which some users would prefer for sharing.
4. *A formal migration system*: schema changes are applied at runtime by helper code; a proper, versioned migration mechanism is missing.

## Issues

1. *Legacy password hashing*: older authentication code stores passwords using MD5, which is insecure. This should be replaced with `password_hash()` and `password_verify()`.
2. *Remaining direct SQL*: some older pages still build queries by concatenating input rather than using the prepared-statement helpers, which is a security and consistency risk.
3. *Runtime schema checks*: creating tables and columns on page load simplifies local setup but adds overhead and hides the true schema state; it is a workaround rather than a solution.
4. *Minimal error feedback in places*: some failure cases are handled quietly; clearer messages would help users understand what went wrong, for example during a failed receipt upload.

## Potential future developments to improve or expand the software

1. *Secure account recovery*: replace the current password-recovery flow with a secure, token-based, email-driven reset.
2. *Richer analytics*: add long-term category trends, month-over-month comparisons, and forecasting to the dashboard and reports.
3. *An API or mobile client*: expose the data through a REST API so that a mobile or single-page frontend could be built on top of the same backend.
4. *Localisation and internationalisation*: support multiple interface languages and locale-aware number and date formatting to broaden the audience.
5. *Multi-user roles*: if the system were to grow beyond single-user use, add role-based administration and shared or household accounts.
