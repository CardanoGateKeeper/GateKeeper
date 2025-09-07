---
layout: default
title: Roles & Permissions
nav_order: 2
---

# Roles & Permissions

## Teams

Every registered user automatically gets a **Team** (organization). A team owns
events and membership.

## Roles

| Role         | Scope     | Can Do                                                              | Cannot Do                                    |
|--------------|-----------|---------------------------------------------------------------------|----------------------------------------------|
| **Admin**    | Team-wide | Create events; edit all settings; invite/remove users; assign roles | —                                            |
| **Editor**   | Team-wide | Edit event details/settings for the team                            | Create events; invite/remove users           |
| **Staff**    | Per-event | Check-in attendees                                                  | Change settings; invite users; create events |
| **Attendee** | Per-event | Connect wallet; generate ticket from eligible NFT                   | Change event settings; access admin UI       |

> RBAC is enforced server-side in Laravel. Attendee identity is derived from *
*wallet stake address** at ticket creation time.
