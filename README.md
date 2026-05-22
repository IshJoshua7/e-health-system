e-health-system

This local repository contains a lightweight auth helper for JWT used in the e_health_system workspace. The file `lib/auth.php` includes env-based `JWT_SECRET` lookup and correct base64url encoding/decoding for minimal JWT creation/verification.

How to publish to GitHub:

1. Create a new repository on GitHub named `e-health-system` (do not initialize with README).
2. Add the remote and push:

```bash
git remote add origin <REMOTE_URL>
git push -u origin main
```

Alternatively, apply the bundle `e-health-system.bundle` included in this folder into another repo.

Setup and development
---------------------

Prerequisites:

- PHP 7.4 or newer
- Composer (https://getcomposer.org/)

Install dependencies:

```bash
composer install
```

Environment secret:

Set the `JWT_SECRET` environment variable in your deployment environment or local shell. Example (Linux/macOS):

```bash
export JWT_SECRET="your-very-secret-key"
```

On Windows (PowerShell):

```powershell
$env:JWT_SECRET = 'your-very-secret-key'
```

Run tests locally:

```bash
composer test
```

Notes:

- The project uses `firebase/php-jwt` for JWT handling. If Composer is not installed, `lib/auth.php` contains a minimal fallback for local/dev use; in production you should install Composer and rely on the library.
- Consider storing `JWT_SECRET` in a secret manager (e.g., GitHub Secrets, Azure Key Vault, AWS Secrets Manager) for production deployments.
