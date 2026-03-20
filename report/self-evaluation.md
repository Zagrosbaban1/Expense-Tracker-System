---
title: Self-evaluation
nav_order: 13
---

# Self-evaluation

## Strengths

- The project solves a clear real-world problem.
- It supports much more than simple CRUD by including budgets, recurring expenses, receipt uploads, multi-currency handling, and visual reports.
- The dashboard offers meaningful analysis rather than only raw data entry.
- The helper-based structure in newer modules improves code reuse and maintainability.
- CSV export adds practical usefulness for users.

## Weaknesses

- Security is inconsistent across the codebase.
- Password hashing uses MD5 instead of stronger modern hashing algorithms.
- Some modules still use direct SQL queries, which increase security risk.
- There is no automated test suite.
- There is no formal migration system for schema evolution.
- The architecture is modular but not fully layered in an enterprise sense.

## Lessons Learned

This project demonstrates that software engineering is not only about making features work, but also about structuring the code, validating inputs, designing database relationships, and planning for maintainability. It also shows how a system can evolve over time from a simple procedural design into a more modular structure.
