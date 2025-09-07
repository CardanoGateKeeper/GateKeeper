---
layout: default
title: Quickstart (Local)
nav_order: 4
---

# Quickstart (Local Dev)

## Prerequisites

- **Docker** & **Docker Compose**
- **Node 18+** and **pnpm** or **npm**

## 1) Clone & env

```bash
git clone https://github.com/CardanoGateKeeper/GateKeeper.git
cd GateKeeper
cp .env.example .env
```

Update the values in .env (see Configuration for details), then generate the app
key:

```bash
composer install
php artisan key:generate
```

If using Laravel Sail:

```bash
# Build and boot containers
./vendor/bin/sail up -d

# Run migrations (and optionally seed)
./vendor/bin/sail artisan migrate
# ./vendor/bin/sail artisan db:seed   # TODO: add seeders if/when available
```

## 2) Frontend dev

Install deps and start Vite:

```bash
# inside the container:
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

# or on host (if you prefer):
npm install
npm run dev
```

Open the app at:

Frontend: http://localhost:5173
(default Vite)

Backend API: http://localhost
(or the Sail service URL)

## 3) Health checks

- Visit the app homepage; confirm it loads.
- Create an admin account.
- Create a test Team and Event.
- Connect a wallet in preprod and verify NFTs load via your provider settings.

> Tip: If wallet read fails, verify your **Blockfrost/Koios** keys and
> **CARDANO_NETWORK** values in `.env` and in your **Vite** env (e.g.,
> `VITE_CARDANO_NETWORK`).