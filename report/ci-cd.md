---
title: CI/CD
has_children: false
nav_order: 9
---

# CI/CD

## What is automated?

The project uses four GitHub Actions workflows, so that a change pushed to the repository is checked, packaged and, in the case of the report, published without me doing anything by hand.

1. **Syntax checking**: when PHP files change on `main` or in a pull request against it, every `*.php` file outside `vendor/` is passed through `php -l`. A parse error therefore fails the run instead of reaching a running site.

2. **Dependency validation**: when `composer.json`, `composer.lock`, or any PHP file changes, a Composer workflow runs `composer validate --strict`, restores a cached `vendor` folder, and installs the development dependencies (PHPUnit). This proves the declared dependencies still resolve.

3. **Artifact packaging**: when application files change on `main`, the project is copied into a clean release folder (excluding `.git`, `.github`, `node_modules`, and `vendor`), zipped into `expense-tracker.zip`, and uploaded as a build artifact that can be downloaded and deployed.

4. **Report publishing**: when anything under `report/` changes on `main`, the folder is built with Jekyll and deployed to GitHub Pages, which is how the page you are reading stays in step with the repository.

Each workflow is filtered by path, so a documentation-only commit does not trigger a PHP lint and a code-only commit does not rebuild the site. All four can also be started manually from the Actions tab.

## Why is this automated?

I automated these steps for four reasons. The checks run identically on every change rather than depending on me remembering them. A broken PHP file or an invalid dependency file is reported soon after the push, while the change is still fresh in my head. The deployable package is produced by the pipeline instead of being assembled by hand, which takes out a manual step where mistakes are easy to make and hard to notice. And the published report is rebuilt from the source, so it cannot quietly fall behind the repository.

## GitHub Actions implementation

The workflows are YAML files in `.github/workflows/`.

### PHP syntax check (`main.yml`)

- **Checkout**: `actions/checkout` retrieves the repository.
- **Set up PHP**: `shivammathur/setup-php` provisions PHP 8.2.
- **Lint**: every `*.php` file outside `vendor/` is passed through `php -l`, so any syntax error fails the run.

### PHP Composer (`php.yml`)

- **Checkout**: `actions/checkout` retrieves the repository.
- **Validate**: `composer validate --strict` checks the dependency file.
- **Cache and install**: `actions/cache` restores the `vendor` folder, then `composer install` installs the development dependencies.

### Build artifact (`artifact.yml`)

- **Checkout**: the repository is checked out.
- **Assemble**: the project is copied into a `release/` folder with development-only paths excluded.
- **Package and upload**: the folder is zipped into `expense-tracker.zip` and uploaded with `actions/upload-artifact`, kept for seven days.

### Deploy report site (`report-site.yml`)

- **Checkout and configure Pages**: the repository is checked out and GitHub Pages is configured.
- **Build with Jekyll**: `actions/jekyll-build-pages` builds the site from the `report/` folder.
- **Deploy**: the built site is uploaded and published with `actions/deploy-pages`.

Between them these four cover the parts of the lifecycle that gave the most trouble on a single-developer PHP project: catching errors, proving the dependencies resolve, producing a deployable package, and keeping the report online. The obvious gap is that the PHPUnit suite is not run in the pipeline yet. The step exists in `php.yml` but is still commented out, so a failing test would not currently block a change. Enabling it is the first item in Future work.
