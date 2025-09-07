---
layout: default
title: Security & Data
nav_order: 8
---

# Security & Data

- **No attendee PII** is stored. Identity = **stake address** controlling the
  NFT.
- Staff accounts use **Laravel auth** with RBAC (Admin/Editor/Staff).
- Wallet signatures are verified server-side before ticket issuance.
- Cache: **Redis**; Database: **MySQL**.
- Recommended: enforce HTTPS, strict CORS to your frontend origin, rotate
  provider keys.

> If you need a formal security review section or audit record, add it here.
