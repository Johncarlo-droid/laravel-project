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
