# Report Site

This folder contains a GitHub Pages compatible Jekyll report using the `Just the Docs` theme.

## Publish Steps

1. Push your project to GitHub.
2. Make sure GitHub Pages is enabled with `GitHub Actions`.
3. Commit and push the changes.
4. Wait for GitHub Pages to build the site.

Your report will then be available at:

`https://zagrosbaban1.github.io/Expense-Tracker-System/report/`

## Why GitHub Actions Is Used

GitHub Pages normally serves Jekyll sites from the repository root or from `/docs`. Since this report lives in the `report/` folder and should also be visible at `/report/` in the final URL, the workflow builds the site and publishes it inside a `report` subfolder.

## Screenshots

Put your screenshots in:

`report/assets/images/`

Recommended filenames:

- `register-page.png`
- `dashboard-page.png`
- `add-expense-page.png`
- `manage-expenses-page.png`
- `categories-budgets-page.png`
- `daily-report-page.png`
- `recurring-expenses-page.png`
- `profile-page.png`
