---
title: Development
nav_order: 5
---

# Development

## Development Approach

The codebase suggests an incremental development process. Older pages use straightforward procedural PHP mixed with SQL and HTML, while newer pages introduce reusable helpers, prepared statements, runtime schema checks, CSRF tokens, and cleaner page layouts. This shows that the system was extended in stages rather than rewritten all at once.

## Technologies Used

- PHP for server-side processing
- MySQL for data storage
- HTML and CSS for structure and presentation
- Bootstrap for responsive UI components
- JavaScript and jQuery for client-side behavior
- Chart.js for dashboard and report visualization
- XAMPP for local development and execution

## Implementation Highlights

### Authentication

The project includes account registration, login, forgot-password, password reset, logout, and password change functionality.

### Expense Processing

The add and edit expense workflows validate expense date, item, amount, category, and optional receipt uploads. Expenses are associated with categories and currencies to improve reporting quality.

### Budget Tracking

Users can define monthly budgets per category. The dashboard compares spending with the allocated budget and highlights warning or over-budget states.

### Recurring Expenses

Recurring entries are stored in a dedicated table with frequency, next run date, and activation status. When the application is accessed, due recurring entries can be inserted into the main expense table automatically.

### Reporting

The reporting module summarizes expenses by day, month, and year. The available report pages include totals, record counts, chart-based summaries, and CSV export.

### Helper-Based Reuse

The helper file `includes/expense-helpers.php` centralizes reusable logic such as:

- Currency option handling
- Output escaping
- Prepared statement execution
- CSRF token generation and verification
- Schema extension checks
- Category defaults
- Budget calculations
- Recurring-expense processing

## Engineering Observations

The project demonstrates practical software evolution. It already contains useful abstractions and clearer feature separation than a basic student CRUD project, but it also preserves some procedural patterns that can still be improved in future iterations.
