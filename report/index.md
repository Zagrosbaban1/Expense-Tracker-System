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
The Expense Tracker System is a web application for personal finance management. Once registered, a user can record daily expenses, organise them into categories, attach a receipt, define monthly budgets, schedule recurring payments, and study their spending through a dashboard and a set of report pages. The aim of the system is to turn a plain list of expenses into information that is easy to read and act on, so that a user always knows where their money is going 💸.

Each expense is captured through a short form containing fields for the date, the item, the amount, the currency, an optional category, free-text notes, and an optional receipt file, all of which can later be edited or deleted. These records are then aggregated automatically: the dashboard shows the running totals and the largest categories, while the date-wise, month-wise, and year-wise report pages present the same data as tables and charts that can be exported to CSV or printed. Because the information is stored in a relational database rather than in a spreadsheet, the summaries stay consistent no matter how many records exist.

By being simple to use, the Expense Tracker System suits many kinds of user, from someone trying to make a monthly allowance last, to an employee splitting spending between currencies, to anyone who simply wants a clear picture of their habits at the end of the year. With a few clicks a user can add a purchase, compare it against a budget, and see it reflected immediately in the charts.

The application is built with PHP and MySQL and runs on a standard XAMPP stack (Apache and MariaDB/MySQL). From a software engineering perspective it is a suitable academic case study because it addresses a concrete everyday problem, connects several modules around a shared database, and demonstrates a gradual move from a legacy procedural codebase toward reusable helper functions, prepared statements, output escaping, and CSRF protection.
