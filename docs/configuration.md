---
layout: default
title: Configuration
nav_order: 5
---

# Configuration

## Server (.env)

Common Laravel/Vapor settings plus chain providers.

```dotenv
# App
APP_NAME=GateKeeper
APP_ENV=local
APP_KEY=base64:TODO_GENERATE
APP_DEBUG=true
APP_URL=http://localhost

# DB
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=gatekeeper
DB_USERNAME=sail
DB_PASSWORD=password

# Cache/Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=file
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=null

# Cardano network
CARDANO_NETWORK=preprod   # preprod | mainnet
# Choose one provider (or both) and set keys/urls
KOIOS_URL=https://preprod.koios.rest/api/v1
KOIOS_API_KEY=           # optional if your endpoint requires it
BLOCKFROST_PROJECT_ID=   # e.g., preprod: your_preprod_key

# CORS / Frontend URL
FRONTEND_URL=http://localhost:5173
```

## Frontend (Vite env)

Create .env or .env.local under the frontend root if separate:

```dotenv
VITE_APP_API_BASE_URL=http://localhost        # your Laravel base URL
VITE_CARDANO_NETWORK=preprod                  # preprod | mainnet
VITE_BLOCKFROST_PROJECT_ID=                   # if using Blockfrost from FE
```

> **Note:** use preprod keys/urls for development. For mainnet, switch
> `CARDANO_NETWORK` and corresponding provider keys/urls.