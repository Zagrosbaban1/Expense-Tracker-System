---
title: Home
has_children: false
nav_order: 1
description: Home page for the Expense Tracker System university report.
---

# Expense Tracker System

### Author
- [Zagros Baban](mailto:zagrosbaban28@gmail.com)

## Abstract

The Expense Tracker System is a web application for managing personal finances. After registering, a user can record daily expenses, sort them into categories, attach a receipt, set monthly budgets, schedule payments that repeat, and study the result through a dashboard and a set of report pages. My aim was to take a plain list of expenses and turn it into something a person can actually read and act on.

Each expense is entered through a short form: the date, the item, the amount, the currency, a category, free-text notes, and an optional receipt file. Any of those can be corrected or removed later. The records are then aggregated for the user automatically. The dashboard shows running totals and the largest categories, and the date-wise, month-wise, and year-wise report pages present the same data as tables and charts, with CSV export and printing. Because everything is stored in a relational database instead of a spreadsheet, the summaries stay consistent however many records build up.

The system is meant for one person at a time, and that shaped most of the design decisions. Someone stretching a monthly allowance, someone who spends in more than one currency, or someone who just wants to see where the year went should all be able to add a purchase, compare it against a budget, and see it reflected in the charts within a few clicks.

The application is built with PHP and MySQL and runs on a standard XAMPP stack (Apache with MariaDB/MySQL). As an academic case study it works well because it solves an everyday problem, connects several modules around one shared database, and shows a gradual move away from a legacy procedural codebase towards reusable helper functions, prepared statements, output escaping, and CSRF protection. That migration is not finished, and this report is honest about where it stops.
