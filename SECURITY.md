# Security notice

This is an archived learning and portfolio project. It is not maintained, has not been professionally audited, and is not suitable for handling real money, identity documents, account credentials, or other sensitive information.

## Known limitations

- Some legacy authentication paths use MD5-based password storage. A maintained version would need a carefully tested migration to PHP's `password_hash()` and `password_verify()` APIs.
- The application contains a broad administration surface and many AJAX endpoints. Authorization, CSRF protection, validation, upload handling, and rate limiting would all require a fresh audit before deployment.
- External market-data and licensing integrations are historical project components and are not guaranteed to remain available.
- The bundled frontend assets and PHP dependencies should be reviewed and updated before reuse.
- No claim is made that the trading calculations or workflows are appropriate for real financial activity.

## Secret handling

Runtime configuration, installation markers, logs, database exports, dependency folders, and uploaded identity documents are excluded by `.gitignore`. Use `core/system/config.example.php` only as a template and keep the generated `core/system/config.php` local.

Credentials that have ever been committed must be considered compromised and rotated at their provider. Removing a value from Git history does not invalidate it.

## Reporting

Because the repository is archived, it does not accept patches. Do not publish suspected credentials or personal data in a public issue. Contact the repository owner through the GitHub profile if a private report is necessary.
