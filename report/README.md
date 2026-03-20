# Report Site

This folder contains a GitHub Pages compatible Jekyll report using the `Just the Docs` theme.

## Publish Steps

1. Push your project to GitHub.
2. Edit `report/_config.yml` and replace:
   - `YOUR-GITHUB-USERNAME`
   - `YOUR-REPOSITORY-NAME`
3. Commit and push the changes.
4. In GitHub, open `Settings > Pages`.
5. Under `Build and deployment`, choose `GitHub Actions`.
6. Push again if needed so the workflow runs.
7. Wait for GitHub Pages to build the site.

Your report will then be available at:

`https://YOUR-GITHUB-USERNAME.github.io/YOUR-REPOSITORY-NAME/`

## Why GitHub Actions Is Used

GitHub Pages normally serves Jekyll sites from the repository root or from `/docs`. Since this report lives in the `report/` folder, the included workflow builds and publishes that folder correctly.

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
