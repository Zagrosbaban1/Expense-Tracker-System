---
title: Concept
has_children: false
nav_order: 2
---

# Concept

The product developed in this project is a web application, accessed through a browser, that presents a Graphical User Interface (GUI) for managing personal expenses. The application allows an authenticated user to interact with their own set of financial records, where they can add, view, edit, categorise, and analyse the money they spend.

**Key characteristics** of the product:
- *Platform*: Server-side web application, built with PHP and served by Apache on a XAMPP stack, using a MySQL database for persistence.
- *User interface*: A browser-based GUI that combines form pages (for entering expenses, categories, budgets, and recurring rules) with a dashboard and report pages that display tables and charts.
- *Functionality*: The application provides the core expense functions of creating, updating, and deleting records, and extends them with categories, monthly budgets, recurring payments, receipt attachments, multi-currency support, CSV export, and reporting by day, month, and year.

The primary goal is to give a single user a private and organised place to keep their spending, and to turn those raw entries into readable summaries that support everyday financial decisions.

## Use case collection

**1. Create an account**
- Actor: New user
- Goal: Register a personal account so that expense data is private and persistent.
- Preconditions: The user has reached the application in a browser and does not yet have an account.
- Basic flow:
    - The user opens the application and selects "Sign up".
    - The system presents a registration form requesting a full name, mobile number, email, and password.
    - The user fills in the fields and submits the form.
    - The system checks that the email is not already registered and stores the new account.
    - The system confirms the registration and invites the user to log in.
- Postconditions: A new account exists and can be used to log in.
- Alternative flows:
    - If the email is already registered, the system shows a message and does not create a duplicate account.

**2. Log in and log out**
- Actor: Registered user
- Goal: Start and end an authenticated session.
- Preconditions: The user has a registered account.
- Basic flow:
    - The user opens the login page and enters their email and password.
    - The system validates the credentials and starts a session.
    - The system redirects the user to the dashboard.
    - When finished, the user clicks "Log out" and the session ends.
- Postconditions: Protected pages are available only while the session is active.
- Alternative flows:
    - If the credentials are invalid, the system shows an error and keeps the user on the login page.

**3. Add an expense**
- Actor: User
- Goal: Record a new expense.
- Preconditions: The user is logged in.
- Basic flow:
    - The user opens the "Add expense" page.
    - A form appears with fields for the date, item, amount, currency, category, notes, and an optional receipt.
    - The user fills in the details and submits the form.
    - The system validates the input, stores the expense, and updates the expense list and dashboard totals.
- Postconditions: The expense is saved and visible in the expense list and summaries.
- Alternative flows:
    - If a required field is missing or invalid, the system highlights the problem and does not save the record.
    - If a receipt of an unsupported type is attached, the system rejects the upload with a message.

**4. Edit or delete an expense**
- Actor: User
- Goal: Correct or remove an existing expense.
- Preconditions: The user has previously recorded at least one expense.
- Basic flow:
    - The user opens the "Manage expenses" page and locates a record.
    - To edit, the user opens the edit page, changes any field, and saves.
    - To delete, the user selects the delete action and confirms.
    - The system updates or removes the record and refreshes the list.
- Postconditions: The record reflects the change, or no longer exists.
- Alternative flows:
    - If the user cancels, the original record is kept unchanged.

**5. Search, filter, and export expenses**
- Actor: User
- Goal: Find a subset of expenses and reuse it outside the application.
- Preconditions: The user has recorded expenses.
- Basic flow:
    - The user opens the "Manage expenses" page.
    - The user applies filters such as free text, date range, amount range, category, or currency.
    - The system displays only the matching records.
    - The user clicks "Export CSV" and the system downloads the filtered records as a file.
- Postconditions: The user obtains a filtered view and, optionally, a CSV file of that view.
- Alternative flows:
    - If no records match the filters, the system shows an empty result with an appropriate message.

**6. Manage categories and budgets**
- Actor: User
- Goal: Organise expenses by category and compare spending against a monthly plan.
- Preconditions: The user is logged in.
- Basic flow:
    - The user opens the "Categories and budgets" page.
    - The user creates a category, or assigns a monthly budget amount and currency to an existing one.
    - The system stores the category and budget.
    - The system shows the current spending against the budget as a progress indicator on the category and dashboard views.
- Postconditions: Categories and budget status are available across the application.
- Alternative flows:
    - If a budget already exists for the same category, month, and currency, the system updates the existing value instead of creating a duplicate.

**7. Schedule a recurring expense**
- Actor: User
- Goal: Avoid re-entering predictable payments.
- Preconditions: The user is logged in and has at least one category.
- Basic flow:
    - The user opens the "Recurring expenses" page.
    - The user defines an item, amount, currency, category, frequency (weekly or monthly), and a start date.
    - The system stores the recurring rule and calculates its next run date.
    - When the application is next used on or after a due date, the system automatically inserts the due expense into the main expense list and advances the next run date.
- Postconditions: Due expenses are generated automatically according to the schedule.
- Alternative flows:
    - The user can deactivate a rule so that no further expenses are generated from it.

**8. Generate a report**
- Actor: User
- Goal: Analyse spending over a chosen period.
- Preconditions: The user has recorded expenses.
- Basic flow:
    - The user opens a report page and selects a period (a date range, a range of months, or a range of years) and a currency.
    - The system aggregates the matching expenses.
    - The system displays summary cards, a detailed table, and charts, including a breakdown by category.
    - The user can export the report to CSV or print it.
- Postconditions: The user can read and export a summary of their spending for the selected period.
- Alternative flows:
    - If there are no expenses in the selected period, the system reports that no data is available.
