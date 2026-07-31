---
title: Future Work
has_children: false
nav_order: 13
---

# Known issues and future work

## What is missing

1. *Automated tests in the pipeline*. The PHPUnit suite exists and passes locally, but the step that would run it in CI is still commented out in `php.yml`. Until it is enabled, a failing test does not block a change, which makes the rest of the pipeline less valuable than it looks.
2. *Pagination for large lists*. The expense list loads every matching record at once. That is fine for a personal history and will not stay fine, so paginated or lazily loaded results are needed.
3. *PDF report export*. Reports can be exported to CSV and printed from the browser, but there is no direct PDF export, which is what most people would want for sharing a report.
4. *A real migration system*. Schema changes are applied at runtime by helper code. A versioned migration mechanism is missing.

## Issues

1. *Legacy password hashing*. The older authentication code stores passwords with MD5, which is broken for this purpose. It needs to be replaced with `password_hash()` and `password_verify()`, with a path for rehashing existing accounts on next login.
2. *Remaining direct SQL*. A few older pages still build queries by concatenating input instead of going through the prepared-statement helpers. This is both a security risk and an inconsistency.
3. *Runtime schema checks*. Creating tables and columns on page load made local setup easy, but it costs a check on every request and hides the real state of the schema. It is a workaround, not a design.
4. *Quiet failures*. Some error cases are handled without telling the user much. A rejected receipt upload is the clearest example: the expense is saved, but the reason the file did not attach is not always obvious.

## Potential future developments to improve or expand the software

1. *Secure account recovery*: replace the current password-recovery flow with a token-based reset sent by email.
2. *Richer analytics*: category trends over time, month-over-month comparison, and simple forecasting on the dashboard and in the reports.
3. *An API or mobile client*: exposing the data through a REST API would let a mobile app or a single-page frontend be built on the same backend.
4. *Localisation*: multiple interface languages, with number and date formatting that follows the user's locale.
5. *Multi-user roles*: if the system ever grew past single-user use, role-based administration and shared household accounts would be the first things needed.
