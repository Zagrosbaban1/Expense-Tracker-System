---
title: Release
has_children: false
nav_order: 7
---

# Release

The release artifact for this project is a **packaged copy of the web application** ready to be dropped onto a XAMPP stack. A GitHub Actions workflow produces it: the project is copied out (leaving behind development-only folders such as `.git`, `.github`, and `vendor`), compressed into `expense-tracker.zip`, and uploaded as a build artifact. Nothing is copied by hand, so the package is the same every time.

Since the application is server-side PHP rather than a compiled program, a "release" here means a known-good snapshot of the source that can be unzipped into `htdocs` and run, together with the SQL script that creates the schema.

## Current releases

The project is released through its Git history on GitHub, with `main` always holding the latest working version. It was built up in stages: the first versions established authentication and the core expense CRUD, then came categories and budgets, recurring expenses, receipt uploads, multi-currency support, the dashboard, and the reporting pages. The most recent work went into the detailed report pages, adding a category breakdown, an average-per-period figure, a print option, and corrected currency symbols. Each of those arrived as its own commit, so the state of the project at any point can be reconstructed from the history.

## Choice of the license

I chose the **MIT License**, declared in `composer.json` and in the `LICENSE` file. MIT is among the most permissive open-source licenses: anyone may use, modify, distribute, or even sell the software, as long as the original license and copyright notice travel with it. It carries no warranty, so I am not liable for what someone else's copy does.

The reason for choosing it is that this is an educational project, not a commercial one. If another student wants to read the code, borrow from it, or extend it, the license should not be the thing standing in the way.

## Choice of the versioning schema

The project follows **Semantic Versioning**, `MAJOR.MINOR.PATCH`. Patch numbers are for small fixes, minor numbers for backward-compatible features, and major numbers for breaking changes. I picked it because it is the convention most developers and tools already expect, which means a tagged version can be tied straight to the packaging workflow and to the published report site without inventing anything new.
