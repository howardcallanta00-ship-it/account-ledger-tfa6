# Deploy this repository: Clever Cloud + Render Free

Repository: https://github.com/howardcallanta00-ship-it/account-ledger-tfa6

Use **Clever Cloud for MySQL only** and **Render for the Docker web service**. Work through these steps in order. Do not create any paid resources or temporary paid trials.

## Step 1 — Create your Clever Cloud database

1. Open https://console.clever-cloud.com/ and sign in.
2. Choose **Create → an add-on** (the label may appear as **New add-on**).
3. Choose **MySQL**. Choose **DEV**, with a displayed price of **free / zero**. Name it `account-ledger-tfa6` and finish creation.
4. Open the add-on and its **Information / connection details** panel. You need the external connection values below. Never use a private/internal hostname from Render.

| Clever Cloud value | Laravel / Render variable |
| --- | --- |
| `MYSQL_ADDON_HOST` | `DB_HOST` |
| `MYSQL_ADDON_PORT` | `DB_PORT` |
| `MYSQL_ADDON_DB` | `DB_DATABASE` |
| `MYSQL_ADDON_USER` | `DB_USERNAME` |
| `MYSQL_ADDON_PASSWORD` | `DB_PASSWORD` |

Use values shown in your console if its labels differ. Keep passwords in your password manager, local environment file, and Render settings; do not paste them into the repository, screenshots, or chat.

**Check transport before continuing:** confirm the external MySQL endpoint supports TLS with server identity verification and obtain its trusted CA/procedure from Clever Cloud. Its public MySQL documentation currently does not provide those details for DEV. The web-domain HTTPS certificate page is unrelated. If verified database TLS is unavailable on DEV, stop and report that exact blocker; do not switch to paid or disable verification. Detailed CA configuration is in the README.

If the account does not offer a zero-cost DEV plan, stop here and report the displayed message. No paid alternative is authorized.

## Step 2 — Initialize the remote database from your computer

Render Free does not provide shell access or one-off jobs, so initialize MySQL from a trusted local environment. This is a one-time setup, not a container-start command.

Open a terminal in the repository folder. You need PHP 8.4 with `pdo_mysql` and Composer 2 (or use the Docker alternative in README).

```powershell
composer install
Copy-Item .env.example .env.remote
```

Edit `.env.remote` locally. Set the five Clever Cloud DB values, `APP_ENV=production`, `APP_DEBUG=false`, and the absolute trusted CA path in `MYSQL_ATTR_SSL_CA`. `APP_URL` can be set to the actual Render URL later; it does not affect migrations. Generate one persistent key:

```powershell
php artisan key:generate --env=remote
```

This writes the generated key into your ignored local file. Save that `APP_KEY` securely; you will enter the same value into Render. Do not generate a different key every restart.

Check that shell-level `DB_*` variables do not point at another database. Then run:

```powershell
php artisan config:clear
php artisan migrate --env=remote --force
php artisan db:seed --env=remote --force
php artisan ledger:check --env=remote --require-tls
```

Expected: MySQL reachable, columns match, **Customers: 5**, **Users: 5**, and **TLS active**. If initialization fails, resolve the connection/schema issue before creating the Render service. Never reset an existing database. Repeating `db:seed` preserves existing seeded rows and unrelated rows.

## Step 3 — Create the Render application

1. Open https://dashboard.render.com/ and sign in.
2. Choose **New → Web Service** and connect GitHub. Authorize access to `howardcallanta00-ship-it/account-ledger-tfa6` if requested.
3. Choose this repository and branch **main**.
4. Set the following fields:

| Render field | Value |
| --- | --- |
| Name | `account-ledger-tfa6` (or another available name) |
| Language / Runtime | **Docker** |
| Branch | `main` |
| Root Directory | **Leave blank** |
| Dockerfile Path | `./Dockerfile` |
| Docker Command | **Leave blank** |
| Instance Type | **Free** |
| Health Check Path | `/up` |

Do not choose Static Site. Do not enter `php artisan serve`. Do not add a paid disk, worker, database, cron, Redis, or pre-deploy command. The Dockerfile installs locked dependencies and includes finished assets.

Alternatively, use **New → Blueprint** with this repository; `render.yaml` already specifies Docker, Free, and `/up`. Manual Web Service creation is easier for seeing every setting the first time.

## Step 4 — Enter Render environment settings

Enter these in the service's **Environment** panel. Use actual values without shell-style surrounding quotes in the Render UI.

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://YOUR-ACTUAL-SERVICE.onrender.com
APP_KEY=YOUR-SAVED-PERSISTENT-KEY
DB_CONNECTION=mysql
DB_HOST=YOUR-CLEVER-EXTERNAL-HOST
DB_PORT=YOUR-CLEVER-EXTERNAL-PORT
DB_DATABASE=YOUR-CLEVER-DATABASE
DB_USERNAME=YOUR-CLEVER-USER
DB_PASSWORD=YOUR-CLEVER-PASSWORD
LOG_CHANNEL=stderr
LOG_LEVEL=warning
SESSION_DRIVER=array
CACHE_STORE=array
QUEUE_CONNECTION=sync
```

For provider-supported TLS, add its trusted CA under **Secret Files** with filename `mysql-ca.pem`. Set:

```dotenv
MYSQL_ATTR_SSL_CA=/etc/secrets/mysql-ca.pem
```

Use your actual secret-file path if Render shows a different one. Do not upload `.env` as a repository file. Leave `DB_URL` unset and let Render assign `PORT`. Update `APP_URL` to the URL Render assigns if it differs from your requested name, then redeploy.

Confirm the selected service still says **Free** before clicking **Deploy Web Service**. Review spend limits and do not enable paid overages.

## Step 5 — Confirm the deployment

1. Open the assigned `https://…onrender.com/up`; expect a successful response.
2. Open Home, About, Customer Accounts, and User Accounts. Each directory should show five fictional records. `/up` alone does not prove MySQL works.
3. Search `Angela`, `maria.santos@`, `09171234567`, `cashier01`, and `Miguel` in the appropriate directories. Sort headings, reset, copy a value, and change row density.
4. On a phone, scroll the table sideways; the ID gutter stays visible. Every column must be reachable without page-level horizontal overflow.
5. Use Render **Manual Deploy → Deploy latest commit**, then check the same rows remain. Do not reseed to make them reappear. Persistent records live in Clever Cloud.
6. Check the GitHub **Actions → Verify application** run. It runs MySQL tests and builds/starts the production Docker image. A green local test result is not a substitute for a completed Docker job or live checks.
7. Confirm Clever Cloud still says DEV/free and Render still says Free. No live success should be claimed until these checks finish.

## If something goes wrong

| Symptom | Check |
| --- | --- |
| Build fails | Read the first failed build step; PHP 8.4, lockfile and Dockerfile must be at repository root. |
| Startup fails immediately | `APP_KEY` is present and persistent. Do not replace the Docker start command. |
| `/up` works, accounts show 503 | External DB values, TLS CA/path, and migrations. Check `ledger:check` locally with the same connection. |
| Service takes a minute to open | Render Free sleeps after 15 idle minutes. Do not create a keep-alive service. |
| Invalid search options | Use the page's reset action. |
| Copy unavailable | Select and copy the displayed value manually. |
| Data seems missing | Verify the database name and account plan; don't reset or automatically reseed. |

Share the service URL and a redacted error message if you need help. Never share passwords, keys, environment-file contents, or certificates containing private keys.

Provider references: [Render Free](https://render.com/docs/free), [Render Docker](https://render.com/docs/docker), [Clever Cloud MySQL](https://www.clever.cloud/developers/doc/deploy/databases/mysql). Free service limits and account-specific plan availability still apply; DEV is a demonstration database, not production hosting.
