---
title: CI/CD
has_children: false
nav_order: 9
---

# CI/CD

## What is automated?

The project uses a set of GitHub Actions workflows so that every change pushed to the repository is automatically checked, packaged, and — for the report — published, without any manual steps.

1. **Syntax checking** — On every push and pull request to `main`, the project's PHP files are linted with `php -l` to catch parse errors before they can reach a running site.

2. **Dependency validation** — A Composer workflow validates `composer.json`, restores a cached `vendor` folder, and installs the development dependencies (PHPUnit), confirming that the project's declared dependencies resolve cleanly.

3. **Artifact packaging** — On every push to `main`, the application is copied into a clean release folder (excluding `.git`, `.github`, `node_modules`, and `vendor`), compressed into `expense-tracker.zip`, and uploaded as a build artifact that can be downloaded and deployed.

4. **Report publishing** — On every push to `main`, the `report/` folder is built with Jekyll and deployed to GitHub Pages, so that the documentation the reader is viewing stays in step with the repository.

## Why is this automated?

Automation was introduced for several reasons:
- *Consistency*: the same checks run the same way on every change, rather than depending on the author remembering to run them.
- *Early feedback*: a broken PHP file or an invalid dependency file is reported immediately on push, while the change is still fresh.
- *Repeatable releases*: the deployable package is produced by the pipeline rather than assembled by hand, which removes a source of mistakes.
- *Always-current documentation*: the report site is rebuilt automatically, so the published version never drifts far from the source.

## GitHub Actions implementation

The workflows are defined as YAML files in `.github/workflows/`. The main ones are described below.

### PHP syntax check (`main.yml`)

- **Checkout**: `actions/checkout` retrieves the repository.
- **Set up PHP**: `shivammathur/setup-php` provisions PHP 8.2.
- **Lint**: every `*.php` file outside `vendor/` is passed through `php -l`, so any syntax error fails the run.

### PHP Composer (`php.yml`)

- **Checkout**: `actions/checkout` retrieves the repository.
- **Validate**: `composer validate --strict` checks the project's dependency file.
- **Cache and install**: `actions/cache` restores the `vendor` folder, and `composer install` installs the development dependencies.

### Build artifact (`artifact.yml`)

- **Checkout**: the repository is checked out.
- **Assemble**: the project is copied into a `release/` folder with development-only paths excluded.
- **Package and upload**: the folder is zipped into `expense-tracker.zip` and uploaded with `actions/upload-artifact`, retained for a fixed number of days.

### Deploy report site (`report-site.yml`)

- **Checkout and configure Pages**: the repository is checked out and GitHub Pages is configured.
- **Build with Jekyll**: `actions/jekyll-build-pages` builds the site from the `report/` folder.
- **Deploy**: the built site is uploaded and published to GitHub Pages with `actions/deploy-pages`.

Together these workflows cover the parts of the lifecycle that benefit most from automation on a single-developer PHP project: catching errors, proving the dependencies resolve, producing a deployable package, and keeping the report online. The natural next step, noted in Future work, is to run the PHPUnit suite inside the pipeline so that the automated tests gate every change as well.
