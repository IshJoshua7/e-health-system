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
