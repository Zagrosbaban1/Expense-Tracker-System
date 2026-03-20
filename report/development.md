---
title: Development
nav_order: 5
---

# Development

## Development Approach

Based on the code structure, the project appears to have been developed incrementally. The older pages use straightforward PHP and SQL mixed inside view files, while the newer pages introduce helper functions, prepared statements, CSRF handling, better validation, modernized layout styling, and more modular logic. This suggests progressive enhancement rather than a full rewrite.

## Tools and Technologies Used

- PHP for server-side programming
- MySQL for persistent data storage
- HTML and CSS for page structure and styling
- Bootstrap for responsive UI components
- JavaScript and jQuery for frontend behavior
- Chart.js for visual analytics
- XAMPP for local server execution

## Implementation Highlights

### Authentication

Users can create accounts, log in, reset forgotten passwords, and change passwords after logging in.

### Expense Processing

The add and edit expense workflows include validation for date, item, amount, category, and optional receipt uploads. Expenses are connected to categories and currencies, which improves reporting accuracy.

### Budget Tracking

Users can set budgets by category and month. The dashboard compares actual expenses with defined budgets and highlights warning and over-budget states.

### Recurring Expenses

Recurring entries are stored in a dedicated table. On page access, due entries are automatically inserted into the main expense table based on weekly or monthly schedules.

### Reporting

The reporting module summarizes expense totals by day, month, and year. Each report includes total amount, highest period, record count, visual charts, and export to CSV.
