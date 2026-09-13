# ICTHospital

Hospital management software with unified communications built in. ICTHospital handles
the day-to-day running of a hospital or clinic, and adds SMS, voice and video
consultation so patients and clinicians can reach each other without leaving the system.

Built and maintained by [ICT Vision](https://ict.vision), part of
[ICT Innovations](https://www.ictinnovations.com).

Product site: **[www.icthospital.com](https://www.icthospital.com)**

---

## Status: the core system works

Every screen a hospital needs day to day is built, tested and documented. If you want to
see it rather than read about it, there is a
**[user guide with screenshots of every screen](docs/USER-GUIDE.md)**.

ICTHospital previously ran on CodeIgniter 2.2.2, which reached end of life in 2017 and
will not run on PHP 8. Rather than patch an unsupported framework carrying patient data,
the application was rebuilt on **Laravel 12**, sharing a foundation with our
[ICTSchool](https://github.com/ictinnovations/ICTSchool) platform.

Where it stands:

- **Ready to try.** Install it, load the demonstration data, and the whole workflow runs
  end to end: register a patient, book them in, admit them, prescribe, order bloods,
  dispense, bill, and read the reports
- **Tested.** 153 feature tests covering the clinical modules, the permission rules and
  the writes that used to race. CI runs them on every push, alongside a dependency
  advisory check
- **Not yet proven in production.** The screens are new and have automated coverage
  rather than years of use behind them. Read the two sections near the end of this file
  before you put real patients in it

**What works today**

- Full hospital data model: 50 tables covering patients, appointments, doctors, nurses,
  beds and wards, prescriptions, lab tests, diagnostic reports, pharmacy, payments,
  expenses and payroll
- Authentication, users, and a hospital role catalogue: 90 permissions across Admin,
  Doctor, Nurse and Accountant, grouped by patients, appointments, admissions, clinical,
  pharmacy, staff, billing and administration
- Hospital dashboard: patient and appointment counts, bed occupancy, clinical activity,
  income against expenses, recent patients and appointments
- Global search across patients and doctors
- Messaging to patients and doctors over [ICTCore](https://github.com/ictinnovations/ictcore),
  with templates, SMS logging and notification types
- Two scheduled jobs, `hospital:payment-reminder` and `hospital:appointment-reminder`,
  which text patients who owe money or who are due in
- Accounting: income, expenses, sectors and reports
- Settings, hospital information, branches, barcode generation and an activity log
- A schema moderniser, described below

**Clinical and front desk**

- Patient registration, search and records. Only a name is required, so a walk-in is
  registerable, and age is derived from the date of birth rather than typed
- Appointment booking, as a day view by doctor and status, refusing to double book a
  doctor while allowing back to back slots
- Beds, wards and admissions. A bed is free when no admission points at it with an open
  discharge time, so the ward board cannot disagree with the records
- Prescriptions, with the drugs as rows rather than a packed string, so "which patients
  are on this drug" is answerable
- Laboratory requests and results, per test, so a request can be part reported while a
  culture is still growing
- Pharmacy dispensing, which takes stock out of the catalogue in the same transaction as
  the sale and refuses to go negative
- Patient invoicing with part payment, where the settlement state is derived from the
  payments rather than typed
- A doctor register, and a billable service and price list. Both feed the selects on the
  clinical screens, so a fresh install can be made usable without touching the database
- Reports over the new tables: outstanding lab work, prescribed against dispensed, aged
  debtors, and bed occupancy with length of stay. Every figure is derived when the report
  is opened, so none of it can drift out of step with the records
- Staff records for nurses, pharmacists, laboratory staff, receptionists and accountants,
  and a department list. Renaming a department carries its doctors with it, because the
  doctor record stores the department as text rather than a foreign key
- Patient medical history: allergies, past operations and chronic conditions, with a scan
  or report attached where you have one
- Payment gateway credentials, stored ready for the online payment work still to come.
  Nothing in this release charges a card, and the screen says so
- A demonstration data seeder, so an evaluator sees a working hospital rather than empty
  tables: `php artisan db:seed --class=DemoDataSeeder`

**What changed in the schema**

The original tables packed repeating data into single columns: every drug on a
prescription in one varchar, every test on a lab request in another, every charge on an
invoice in a third with the prices in a fourth, matched by position. Four child tables
replace those, and each legacy column is still written with a readable summary so
anything reading it keeps working:

`prescription_medicine`, `lab_test`, `pharmacy_sale_item`, and `invoice_item` with
`invoice_payment` alongside it.

Names and prices are copied onto each line rather than only referenced. A prescription,
a lab result and an invoice are records of what was written, measured and charged on a
given day, and they must not change when a catalogue or price list is next edited.

**Permissions**

The 90 permission catalogue in `config/hospital_permissions.php` covers Admin, Doctor,
Nurse and Accountant, and the clinical routes check it. Admin is granted everything by
the seeder; the other roles start with nothing and are granted what they need on the
permissions screen. The menu hides what a role cannot open, so nobody is offered a link
that bounces them.

If you are upgrading an install that came from ICTSchool, its permission table still
holds the old student and class rights and none of the hospital ones. Run this once
after migrating, or every role including Admin will be locked out of every screen:

```bash
php artisan hospital:sync-permissions          # add what is missing, grant it to Admin
php artisan hospital:sync-permissions --prune  # also clear the school leftovers
```

It never re-grants a right that was deliberately taken away, so it is safe to re-run.

**Concurrency**

Every check that guards a write now holds a row lock for the life of the transaction
that does the writing: appointment slots, bed occupancy, pharmacy stock, invoice
balances and patient id allocation. Two people pressing the same button at the same
moment get one success and one refusal rather than two successes.

The test suite runs on sqlite, where row locks are a no-op, so it cannot demonstrate the
races themselves. It covers the transaction boundaries and the arithmetic around them;
the locks are exercised by MySQL and MariaDB.

**What is not finished**

- Patient deposits and operation theatre payments have tables and models but no screens.
  Both are planned
- The doctor and hospital commission percentages on the price list are recorded and
  applied to nothing. They come from the legacy schema, and how a split should actually
  work is a decision rather than a missing screen
- Reporting covers four questions: outstanding lab work, drug usage, debtors and bed
  occupancy. Anything else still means querying the database

**Before you put real patients in it**

The clinical screens are new. They have automated coverage rather than production miles,
and a hospital system is not the place to find out what that difference is worth. If you
are weighing ICTHospital up for live use, please
[get in touch](https://www.icthospital.com) so we can talk about your data and your
migration.

Two practical things to do first. Change the `admin` password, which ships as `123456`.
And if you are upgrading an install that came from ICTSchool, run
`php artisan hospital:sync-permissions` after migrating, or every role including Admin
will be locked out of every screen.

## Requirements

- PHP 8.2 or newer
- MySQL or MariaDB
- Composer

## Installation

```bash
git clone https://github.com/ictinnovations/ICTHospital.git
cd ICTHospital
composer install
cp .env.example .env
php artisan key:generate
php artisan passport:keys
```

Set your database in `.env`, then:

```bash
php artisan migrate --seed
php artisan serve
```

`passport:keys` writes `storage/oauth-private.key` and `storage/oauth-public.key`. They
are secrets, they are in `.gitignore`, and the API guard returns a server error until
they exist.

### Docker

A development environment is included:

```bash
docker compose up -d
docker compose exec app php artisan migrate --seed
```

The application is then on http://localhost:8082.

## Schema modernisation

The schema was carried over from the original application exactly as it was, which means
dates stored as `varchar(100)` and relationships stored as a loose string holding the
parent row's id. Nothing stopped a bad date or an appointment pointing at a patient who
had been deleted.

`hospital:modernise-schema` fixes both, and refuses to do either one blindly:

```bash
php artisan hospital:modernise-schema           # report only, changes nothing
php artisan hospital:modernise-schema --apply   # make the safe changes
```

A date column is converted to `DATE` or `DATETIME` only if every value in it parses. A
relationship becomes a real foreign key only if every value is numeric and matches a row
in the parent table. Anything else is skipped and reported with the reason, so you can
clean the data and run it again. On a clean database it converts 41 date columns and adds
16 foreign keys.

The same work runs from a migration, so `php artisan migrate` upgrades an existing
install. Read the report before applying it to live data, and take a backup.

### The school tables are gone

Thirty-three tables came across with the fork and describe a school rather than a
hospital: students, classes, sections, subjects, exams, marks, GPA rules, timetables,
admissions, library books, dormitories, and the family fee voucher chain. Their
migrations have been removed, so a fresh install never creates them, and a migration
drops them from a database that already has them.

```bash
php artisan hospital:remove-school-tables           # report only
php artisan hospital:remove-school-tables --apply   # drop the empty ones
```

A table is only dropped when it is empty. Anything holding rows is left alone and
reported, so no migration quietly deletes data. Add `--force` if you want them gone
regardless. A clean install is 84 tables, down from 117.

## Communications

SMS and voice run through [ICTCore](https://github.com/ictinnovations/ictcore), our own
open source communications framework, configured in Settings. Video consultation targets
LiveKit. None of these are required to run the core system.

Two scheduled jobs use it:

```bash
php artisan hospital:payment-reminder                  # patients with an outstanding balance
php artisan hospital:appointment-reminder              # patients due in tomorrow
php artisan hospital:appointment-reminder --days=0     # patients due in today
```

Both report why they did nothing rather than failing, so they are safe to put on cron
before ICTCore is configured.

## Contributing

Issues and pull requests are welcome. For bug reports, the PHP version, database version
and the exact error message help more than anything else.

Run the route smoke test after any significant change. It signs in, requests every
route that takes no parameters, and reports the status codes:

```bash
bash smoke-test.sh
```

The current baseline is 33 pages serving, no server errors.

## Related projects

- [ICTSchool](https://github.com/ictinnovations/ICTSchool) - school management
- [ICTCore](https://github.com/ictinnovations/ictcore) - communications framework
- [ICTFax](https://github.com/ictinnovations/ictfax) - fax server on FreeSWITCH
- [ICTDialer](https://github.com/ictinnovations/ictdialer) - auto dialer and campaigns
- [ICTPBX Community Edition](https://github.com/ictinnovations/ictpbx-community-edition) - multi-tenant IP PBX

Full list: [ictinnovations.com/projects](https://ictinnovations.com/projects/)

## Licence

GNU General Public License v3.0 - see [LICENSE](LICENSE).

Copyright (c) ICT Innovations.

---

Developed by Tahir Almas, [ICT Innovations](https://www.ictinnovations.com).
