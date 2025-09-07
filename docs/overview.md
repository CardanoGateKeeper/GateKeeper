---
layout: default
title: Overview
nav_order: 1
---

# GateKeeper — Turn Cardano NFTs into Event Tickets

**GateKeeper** lets event hosts accept **existing Cardano NFTs** as tickets.
Attendees connect a wallet, pick an eligible NFT, sign once, and receive a valid
ticket. Organizers get a scalable, serverless backend with clear roles for
admins, editors, and staff.

## Who is it for?

- **Organizers / Admins** – create teams, events, invite users, set rules.
- **Editors** – configure event settings for their team.
- **Staff** – check in attendees at the door.
- **Attendees** – connect a wallet and generate tickets from eligible NFTs.

## Key benefits

- **NFTs → tickets in minutes** – no new minting required; reuse existing
  collections.
- **Familiar wallets** – CIP-30 wallets with SignData (e.g., Lace, Eternl,
  VESPR, Begin).
- **Standards-based** – supports **CIP-25** & **CIP-68** NFTs.
- **Scales automatically** – serverless deployment via **Laravel Vapor**.
- **Privacy-first** – no attendee PII; stake addresses only.

## What GateKeeper is *not* (yet)

- **Fungible tokens** and rich FT logic are **not supported** (on roadmap).
- Doesn’t perform on-chain ticket transfers; it **validates ownership +
  signature** to issue event tickets off-chain.
