---
layout: default
title: NFT → Ticket Flow
nav_order: 7
---

# NFT → Ticket Flow

## 1) Organizer setup

1. Admin creates a **Team** and an **Event**.
2. In the event settings, specify **eligibility** (e.g., policy IDs, CIP-25/68
   metadata rules).
3. Share the event URL with attendees.

## 2) Attendee steps

1. **Connect wallet** (CIP-30).
2. App reads **NFTs** from the connected wallet using provider APIs.
3. Attendee selects an **eligible NFT**.
4. App prompts a **SignData** request with the staking key.
5. App sends **signature + token reference** to the backend.

## 3) Backend verification

1. Look up the **token location** (policy_id + asset_name, UTxO).
2. Verify **signature** belongs to the stake address that controls the NFT.
3. If valid, **issue a ticket** (off-chain record) and return ticket details/QR.

## 4) Check-in

- Staff scans the ticket (or searches), and the system marks it **redeemed** if
  valid.

> Tickets are off-chain records derived from on-chain ownership at the time of
> issuance.