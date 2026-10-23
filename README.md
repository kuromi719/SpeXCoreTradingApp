# SpeXCore Trading App

SpeXCore Trading App is a personal PHP/MySQL project I built while learning how a larger web application fits together. It started as a small trading dashboard and gradually grew into a multi-page platform with user accounts, simulated trading flows, wallet screens, an administration area, background jobs, and external market-data integrations.

This repository is an **archived portfolio snapshot**. It is useful as a record of the architecture and features I implemented, but it is not a production exchange and must not be used to hold funds or process real financial transactions.

## What I built

- A small PHP router and page-based application structure
- User registration, login, session handling, profile settings, and email flows
- Google Authenticator-compatible two-factor authentication
- Spot, futures, bot, wallet, presale, and portfolio interfaces
- JSON/AJAX endpoints for account and trading operations
- An administration panel for users, coins, themes, reports, KYC workflows, and system settings
- MySQL data access using both prepared statements and reusable database helpers
- CoinGecko and Binance market-data integrations
- Cron-oriented background jobs, logging, caching, and database maintenance utilities
- A guided browser installer for database and administrator setup

## Technology

- PHP 8
- MySQL / MariaDB
- JavaScript, HTML, and CSS
- Bootstrap-based interfaces
- Composer
- PHPMailer
- PragmaRX Google2FA
- BaconQrCode

## Repository layout

```text
.
|-- index.php              Application entry point and router
|-- core/
|   |-- ajax/              JSON and form endpoints
|   |-- api/               Market-data and HTTP helpers
|   |-- crons/             Background tasks
|   |-- pages/             User-facing pages and assets
|   |-- system/            Bootstrap, configuration, and shared functions
|   |-- utils/             Reusable application services
|   `-- z-admin/           Administration interface
`-- install/               Browser-based installation flow
```

## Local setup

The project is archived, so the following steps describe the original development setup rather than a supported deployment path.

1. Use PHP 8, MySQL or MariaDB, and Apache/Nginx.
2. Install PHP dependencies:

   ```bash
   cd core
   composer install
   ```

3. Copy `core/system/config.example.php` to `core/system/config.php`, then provide local database values or environment variables.
4. Import a database schema compatible with the application or use the installer flow.
5. Keep the application behind HTTPS and never use real financial data.

No database dump, user upload, installation marker, access token, API key, or production configuration is included in this archive.

## Security status

This code has not received a professional security audit. It contains legacy design decisions that I would change before any new deployment, including older password-hashing code that must be migrated to `password_hash()` and `password_verify()`.

During the portfolio cleanup I removed generated database backups, uploaded identity documents, runtime logs, local configuration files, embedded credentials, an outbound notification routine, and legacy remote update/command components. I also replaced an `eval`-based configuration load with a scoped `require`.

See [SECURITY.md](SECURITY.md) for the remaining limitations.

## Project status

Development has ended and the repository is read-only. I keep it public to demonstrate the scope of the PHP application, the backend organization, and the problems I explored while building it.

