---
layout: default
title: System Architecture
nav_order: 3
---

# System Architecture

## High-level components

- **Frontend:** Vue 3 + Vuetify (Vite build)
    - Wallet connect UX (CIP-30 / SignData)
- Reads wallet contents & NFT metadata via provider APIs
- Calls backend controllers for auth and ticket issuance
- **Backend:** Laravel 10 (PHP 8.1)
    - REST controllers, RBAC, validation, ticket lifecycle
- Integrations: **Koios**/**Blockfrost** for chain data
- Caching via **Redis**; database **MySQL**
- **Infrastructure:**
- **Local dev:** Docker (**Laravel Sail**)
- **CI/CD & Staging:** Dockerized pipeline
- **Production:** **Laravel Vapor** (serverless, autoscaling)

## Blockchain specifics

- **Wallets:** CIP-30 compatible wallets with **SignData**
- **Standards:** **CIP-25** & **CIP-68** NFTs
- **Networks:** preprod & mainnet tested
- **Libraries:** CSL (serialization/deserialization), CMS (Cardano Message
  Signing)

## Authentication model

- **Staff users:** Laravel username/password + team RBAC
- **Attendees:** wallet connection (no account); on ticket creation they **sign
  a message** with the staking key; backend verifies signature + token location.

## Data model (conceptual)

- **Team** → has many **Users** (with roles)
- **Event** (belongs to Team) → has many **Tickets**
- **Ticket** references: event_id, stake_address, policy_id + asset_name,
  signature, status (issued/redeemed), timestamps

> Only staff emails are stored. Attendee identity is the **stake address** that
> controls the NFT (public on-chain).
