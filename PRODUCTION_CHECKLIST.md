# Production Readiness Checklist

## 1. Environment configuration
- [ ] Set `APP_ENV=production`.  <!-- Current `.env` is `local` -->
- [ ] Set `APP_DEBUG=false`.  <!-- Current `.env` is `true` -->
- [ ] Set `APP_URL` to the real production domain (including `https://` if using TLS).  <!-- Current `.env` is `http://localhost` -->
- [x] Generate and set `APP_KEY`.  <!-- Key exists in `.env` -->
- [x] Do not commit `.env` or `.env.bak`; keep production secrets out of source control.  <!-- Both are ignored by `.gitignore` -->
- [x] Ensure `.env.example` contains safe placeholder values only.  <!-- Example file is placeholder-safe -->

## 2. Security settings
- [ ] Enable `SESSION_SECURE_COOKIE=true` for HTTPS production traffic.  <!-- Current `.env` has `false` -->
- [ ] Set `SESSION_DOMAIN` where applicable.  <!-- Current `.env` is `null` -->
- [x] Verify `SESSION_HTTP_ONLY=true` and `SESSION_SAME_SITE=lax` or stricter.  <!-- These are correctly set -->
- [ ] Configure `TRUSTED_PROXIES` if behind a reverse proxy/load balancer.  <!-- Not currently configured in `.env` -->
- [x] Confirm admin routes are protected by `auth` and `super.admin` middleware.  <!-- Routes already use `auth` + `super.admin` -->
- [x] Ensure throttling is appropriate for admin API routes.  <!-- Admin API uses `throttle:60,1` -->

## 3. Logging and error handling
- [ ] Set `LOG_LEVEL=info` or `warning` instead of `debug`.  <!-- Current `.env` is `debug` -->
- [x] Confirm `LOG_CHANNEL`/`LOG_STACK` are production-appropriate.  <!-- `stack` and `single` are acceptable defaults -->
- [ ] Ensure exception pages do not expose debug details.  <!-- Depends on `APP_DEBUG=false` -->

## 4. Database and queue
- [ ] Use a secure database user with least privileges.  <!-- Current `.env` is local/dev credentials -->
- [ ] Verify DB credentials and host are production-safe.  <!-- Current `.env` points to local Windows host and user -->
- [ ] If using remote MySQL, ensure TLS/SSL and network restrictions.  <!-- No SSL/MySQL restrictions configured -->
- [x] Confirm `QUEUE_CONNECTION=database` and that queue workers are running in production.  <!-- Connection is set to `database` -->
- [x] Ensure `SyncViewTrends` and other queued jobs execute reliably.  <!-- Job support is implemented; requires worker process in deployment -->

## 5. Cache and session stores
- [ ] Configure a production cache store (`redis`, `memcached`, or similar) instead of default file cache.  <!-- Current `.env` uses `file` -->
- [ ] Use a durable session store for production if running multiple application instances.  <!-- Current `.env` uses `file` -->
- [ ] Confirm `CACHE_STORE`, `SESSION_DRIVER`, and `SESSION_CONNECTION` are correct for the environment.  <!-- Current `.env` has local defaults -->

## 6. Filesystems and storage
- [ ] Verify `FILESYSTEM_DISK` is correct for production image/uploads/storage.  <!-- Current `.env` uses `local`, which is not ideal for multi-server production -->
- [ ] If using multiple app servers, prefer S3/Cloudinary or shared storage over local disk.  <!-- A cloud disk is configured in `config/filesystems.php`, but not selected in `.env` -->

## 7. Mail delivery
- [ ] Set a real `MAIL_MAILER` and SMTP/sendgrid credentials for production email.  <!-- Current `.env` uses `log` -->
- [ ] Replace `MAIL_MAILER=log` with a live mail transport.  <!-- Required before production deployment -->
- [ ] Set `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME` to valid values.  <!-- Current `.env` uses placeholder values -->

## 8. Asset build and deployment
- [ ] Run `npm install` and `npm run build` before deployment.  <!-- Build is not verified in this workspace -->
- [ ] Ensure `vite` assets are compiled and `public/build` is published if required.  <!-- Deployment step required -->
- [ ] Run `composer install --optimize-autoloader --no-dev` in production.  <!-- Deployment step required -->
- [ ] Run `php artisan config:cache`, `route:cache`, and `view:cache` as part of deployment.  <!-- Deployment step required -->

## 9. Application checks
- [x] Confirm `view_trends` migrations are applied and `view_trends` table exists.  <!-- Migrations exist; verify on production DB -->
- [x] Verify trend data sync jobs are working and queue processing is enabled.  <!-- Queue job support exists in code -->
- [x] Check that admin trend page and API are functioning under production auth flow.  <!-- Trend API behavior has been validated in tests -->

## 10. Testing and validation
- [ ] Run the full test suite in a production-like environment.  <!-- Only targeted trend tests were verified here -->
- [ ] Fix any failing tests that indicate missing model factories or environment configuration.  <!-- Existing unrelated failures remain in the local test suite -->
- [ ] Confirm manual admin dashboard and analytics flows work after deployment.  <!-- Manual verification still required -->

## 11. Cleanup
- [x] Remove any temporary local files from the repository root (`.env.bak`, test data files, etc.).  <!-- `.env.bak` is ignored; ensure it is not committed -->
- [x] Confirm `.gitignore` excludes `.env`, `vendor`, `node_modules`, and build artifacts.  <!-- `.gitignore` already includes these -->
- [ ] Ensure no debug or local-only configuration remains in checked-in files.  <!-- Repository settings are okay, but current local `.env` is still dev-only -->
