---
title: Release
has_children: false
nav_order: 7
---

# Release

For this project, the primary release artifact is a **packaged copy of the web application** that can be deployed onto a XAMPP stack. The package is produced automatically by a GitHub Actions workflow, which copies the project (excluding development-only folders such as `.git`, `.github`, and `vendor`), compresses it into `expense-tracker.zip`, and uploads it as a build artifact. This gives a repeatable, ready-to-deploy bundle without any manual copying.

Because the application is server-side rather than a compiled binary, "release" here means a known-good snapshot of the source that a user can drop into `htdocs` and run, together with the database script needed to create the schema.

## Current releases

The project is released through its Git history on GitHub, with the `main` branch always holding the latest working version. Progress was delivered incrementally: early versions established the core expense CRUD and authentication, while later changes added categories and budgets, recurring expenses, receipt uploads, multi-currency support, the dashboard, and the reporting pages. The most recent changes refined the detailed report pages with a category breakdown, an average-per-period metric, a print option, and corrected currency symbols. Each of these was delivered as a focused commit so that the state of the project at any point can be reconstructed from the history.

## Choice of the license

For this project the **MIT License** was chosen, as declared in `composer.json`. The MIT License is one of the most permissive open-source licenses: it allows anyone to use, modify, distribute, and even commercialise the software, provided that the original license and copyright notice are preserved. It offers no warranty, so the author is not liable for issues that may arise from use.

This license was chosen because the project is developed for educational purposes rather than as a commercial product. Anyone who wishes to study, reuse, or extend the code is encouraged to do so, and the permissive terms keep that as simple as possible.

## Choice of the versioning schema

The project follows the **Semantic Versioning (SemVer)** convention of `MAJOR.MINOR.PATCH`, which is widely understood by both developers and tools. Under this schema, patch numbers cover small fixes, minor numbers cover backward-compatible features, and major numbers are reserved for breaking changes. SemVer works well with automation, so tagged versions can be tied cleanly to the packaging workflow and to the published report site.
