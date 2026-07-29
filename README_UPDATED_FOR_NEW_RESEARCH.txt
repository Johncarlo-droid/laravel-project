UPDATED SYSTEM: Asset + Inventory + Facilities Management with Linear Regression Forecasting

What was changed:
1. Retained old CAPEX/OPEX Laravel features:
   - CAPEX asset records
   - OPEX inventory records
   - Requisition and approval workflow
   - QR-based CAPEX tracking fields
   - Users, roles, departments, suppliers, acquisitions, issuances, reports

2. Added new research-paper features:
   - Facilities Management module
   - Facility reservation form
   - Schedule conflict detection
   - FMO approval/rejection workflow
   - Linear Regression-based OPEX consumption forecasting
   - Automated restocking suggestion
   - Inventory usage history table
   - Asset scan logs
   - Room mismatch detection and reporting
   - Housekeeping role for misplaced asset checks
   - FMO role for facility management

Demo accounts after seeding:
- admin@nuclark.local / admin123        = Asset Management Admin
- requestor@nuclark.local / request123  = Requestor
- dean@nuclark.local / dean12345        = Dean Approver
- exec@nuclark.local / exec12345        = Executive Approver
- fmo@nuclark.local / fmo12345          = Facilities Management Office
- housekeeping@nuclark.local / house123 = Housekeeping Scanner

Run commands:
1. Open terminal inside this folder.
2. Run:
   composer install
   copy .env.example .env
   php artisan key:generate
   type nul > database\database.sqlite
   php artisan config:clear
   php artisan cache:clear
   php artisan migrate:fresh --seed
   php artisan serve

If Windows says database/database.sqlite already exists, skip the "type nul" command.

Important fix for "Nothing to migrate" but tables are missing:
- Delete database/database.sqlite
- Create it again using: type nul > database\database.sqlite
- Then run: php artisan migrate:fresh --seed

Main URLs:
- /dashboard
- /items?type=CAPEX
- /items?type=OPEX
- /requisitions
- /facilities
- /forecasting
- /asset-scans
- /reports

Notes:
- Forecasting uses Linear Regression on inventory_usage_logs monthly usage data.
- Facility reservations check conflicts against pending and approved schedules.
- Asset scan monitoring compares assigned room vs. scanned/current room.
- The web system is updated. The Android/mobile app is still a separate project if you want a native Kotlin app later.

===================================================================
PENDING / DEFERRED WORK (noted per developer request, not yet built)
===================================================================
MOBILE APP — ASSET SCANNING WORKFLOW (deferred, web system prioritized first)
- Requirement: housekeeping/asset custodians should ONLY be able to scan assets
  via the mobile app (not the web). Every scan performed on mobile must appear
  in both the mobile app and the web system's Scans module.
- Additional requirement (not yet designed): when a scanned item is moved to
  its correct/designated room or location, the system should reflect that the
  item has been relocated. The exact workflow for this (e.g. scan source room
  -> scan destination room -> system logs a "relocation" event vs. a plain
  "verification" scan) still needs to be defined before development starts.
- Status: intentionally not started. Web system (roles, nav, registration,
  activity proposals, forecasting, floor-based asset tags) was prioritized
  first per instruction. Revisit mobile app + relocation workflow next.

===================================================================
THIS ROUND: merged your deployed/hosting version as the new base
===================================================================
Started from your uploaded "ETO YUNG NAKA DEPLOY SA HOSTING" zip (the one
with entrypoint.sh, Dockerfile, Procfile, nixpacks.toml) instead of my old
working copy, so your deployment-specific setup is preserved. Confirmed from
your own code (EXTRACT()/TO_CHAR() usage, NOT is_approved fix) that your
actual host runs PostgreSQL -- not MySQL as I'd assumed in an earlier round.

1. DATABASE PORTABILITY (real fix, not just config)
   - Removed a duplicate "add_floor_to_items_table" migration (two files were
     doing the same thing).
   - Built app/Support/DateSql.php: one helper that picks the correct SQL for
     whichever database is connected -- SQLite (local testing), MySQL
     (general portability), or PostgreSQL (your actual host). Replaces your
     hand-written Postgres-only EXTRACT()/TO_CHAR() calls in Dashboard,
     Reports, and Forecasting with driver-aware calls that work on all three
     without manual editing when moving between environments.
   - Verified for real: installed an actual PostgreSQL server in a test
     environment, ran migrate:fresh --seed (16 migrations, including tables
     with 6+ foreign keys), then a full page-by-page regression pass against
     it -- Dashboard, Reports, Forecasting, Activity Proposals, Facilities,
     Users, and the mobile API. All confirmed working, not just assumed.

2. ASSET TYPES -- finished something that was half-built
   Found an asset_types migration + model that existed but was never
   actually wired into anything (the item form was still reading from my old
   hardcoded PHP list). Connected it properly: the dropdown is now database-
   backed, and picking "Other, specify" and typing a new type saves it for
   future reuse (same self-growing pattern as the category field). Seeded
   with the original starter list so it's not empty on first run.

3. EMAIL BUG -- found the actual root cause
   Your .env has MAIL_MAILER=log, which writes "sent" emails to
   storage/logs/laravel.log on the server instead of actually sending them.
   That's why it worked on localhost (you could read the log file directly)
   but never reached anyone once deployed. This needs a real SMTP provider --
   see the new block in .env.example for Brevo setup (free tier, no 2FA/
   App Password hassle like Gmail). Any SMTP provider works the same way
   (Gmail, Outlook, Mailgun, etc.) -- these settings only control who is
   SENDING mail, not who can receive it, so recipients on Gmail, Outlook, or
   anywhere else all work regardless of which provider you pick to send from.

4. PREMIUM VISUAL REDESIGN (web + mobile)
   Full design-token rewrite of resources/views/layouts/admin.blade.php (the
   shared stylesheet loaded by every admin page) plus the login and register
   pages -- refined navy/gold palette built on your existing NU Clark brand
   identity (not replaced), proper Inter+Lexend typography (Inter was
   referenced before but never actually loaded, so it was silently falling
   back to system fonts), consistent shadows/radius/spacing system, refined
   status pill colors, personalized avatar initials instead of a generic
   icon. Same CSS class names throughout, so none of the ~30 view files
   needed touching individually. Verified visually with real screenshots
   (login, dashboard, items, activity proposals, facilities, users, settings,
   reports, forecasting, register, plus a mobile-viewport check) -- not
   guessed blind. Mobile app colors.xml updated to match the same palette
   exactly for brand consistency between web and app.

STILL TO DO
- Actually create the Brevo account and plug in real SMTP credentials (I
  can't do that step for you -- needs your own account signup).
- Android Studio build/emulator verification for the mobile app -- I syntax-
  checked the Kotlin and confirmed all XML is well-formed, but cannot
  compile an APK in this environment (no Android SDK here).
