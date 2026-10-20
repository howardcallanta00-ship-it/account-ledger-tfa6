# Verification performed — 20 October 2026

## Local application and MySQL

- Official Laravel 12 project installed with Composer and committed dependency lock metadata; PHP 8.4.26 and Laravel 12.70.0 used for verification.
- After final refinements, all **12 PHPUnit tests passed with 127 assertions against MySQL Community 8.4.10**. The earlier nine-test suite also passed on SQLite with the MySQL-specific schema test explicitly skipped.
- Actual MySQL table definitions were inspected: auto-incrementing INT IDs, specified VARCHAR lengths, nullable customer phone, NOT NULL DATETIME created_at, unique username, no unique email, no updated_at or authentication columns. Both tables contained five seeded rows.
- Repeated seeding, preservation of unrelated records, all specified search fields, bound hostile-looking search input, every sortable column/direction, invalid/array/oversized parameters, deterministic ordering, escaping, empty/no-match states, timestamps, uniqueness, HTTPS forwarded headers, and read-only routes were tested.
- Composer validation passed. Dependency update reported no security vulnerability advisories. Laravel Pint formatting check passed. Blade view compilation passed. Route inspection showed only the four required GET pages and `/up` (no upload/write routes).
- Additional checks verify array-shaped invalid sort values, stable ID order for equal names, updating models without `updated_at`, accessible 422 recovery, preserved search/sort retry URLs, and the `ledger:check` deployment diagnostic.
- Real MySQL outage: server was stopped; `/customers` and `/users` each returned **503**, concise retry content, and no SQLSTATE in the response. `/up` continued returning **200**, demonstrating why database-backed checks are separate. Restarting MySQL restored both pages to 200 with five records each, without reseeding.

## Browser and visual checks

Chrome was automated against the actual Laravel server with MySQL records, not a static mockup.

- **33 browser assertions passed**: four page responses and active navigation, desktop/mobile overflow, submitted search, sorting preserving the query, browser Back, reset, density persistence, actual clipboard contents, rejected clipboard feedback, no missing-phone copy, bounded table scrolling, no-match state, unavailable local storage, JavaScript-disabled search/records, hidden unavailable enhancements, and no JavaScript errors.
- Screenshots of all four pages at **1440 × 900** and **390 × 844** were captured. Representative customer desktop/mobile and Home desktop images were visually inspected, followed by revised customer desktop/mobile images.
- The meaningful visual revision gave ID and contact columns stable widths, preventing email addresses from being squeezed into awkward fragments on mobile. Table scrolling retains all columns. The mobile brand spacing was corrected. An absolutely positioned screen-reader label was contained inside the scroll region to prevent document overflow.
- Final refinements add a visible narrow-screen scrolling hint, a sticky ID gutter, more readable column/copy labels, and per-button “Copied” feedback. Browser and responsive checks passed again after these changes.
- All four pages had no page-level horizontal overflow at **375px, 390px, 720px, and 1440px**. A 720px effective-width reflow check approximated 200% desktop zoom layout; browser UI zoom itself was not directly automated.
- Keyboard skip-link order and transfer of focus to main were verified. Reduced motion was emulated. This is not a full assistive-technology audit.
- Measured palette contrast: primary text/white **14.16:1**, supporting text/background **5.59:1**, supporting text/rail **5.42:1**, white/active green **9.61:1**, green/pale selection **8.22:1**.

## Docker and live deployment: not performed

- Docker was unavailable in this environment. The Dockerfile, Apache configuration, startup script, and Render Blueprint were authored and reviewed, but an image was **not built or run** here.
- **Remote Docker verification passed on GitHub Actions** for commit `7fd268c`: the production image built and started, Apache served `/`, `/about`, `/up` and the stylesheet, security headers were present, and an unavailable database produced the expected 503 page. This is a remote CI Docker result, not a local image run or a live Render deployment.
- CI checks also cover fresh-checkout test discovery and forwarded HTTPS host/port handling independently of the local `APP_URL`. Check the latest workflow result for the final remote MySQL run.
- Render/Clever Cloud account access and connection credentials were not supplied. No services were provisioned, no remote migrations were run, and no live URL or live persistence result is claimed.
- Current official provider documentation was checked for the required plans. Account-specific zero-cost availability/billing and DEV database TLS/CA remain to be confirmed. The README documents the TLS checkpoint and exact account/deployment actions.
- Source/image-context secret exclusions are present. Actual built-image inspection, live response/log inspection, HTTPS verification, Render restart persistence, and final free-plan confirmation remain part of the deployment checklist.

## Evidence files

Representative desktop/mobile captures are included in `docs/screenshots/`. The local deliverable's adjacent `../review/` folder contains all captured page screenshots and machine-readable browser/contrast results. These are local verification evidence, not hosted deployment screenshots.
