# ICTHospital

Hospital management software with unified communications built in. ICTHospital handles
the day-to-day running of a hospital or clinic, and adds SMS, voice and video
consultation so patients and clinicians can reach each other without leaving the system.

Built and maintained by [ICT Vision](https://ict.vision), part of
[ICT Innovations](https://www.ictinnovations.com).

Product site: **[www.icthospital.com](https://www.icthospital.com)**

---

## Status: active rebuild

This repository holds a **rebuild in progress**, not a finished release.

ICTHospital previously ran on CodeIgniter 2.2.2, which reached end of life in 2017 and
will not run on PHP 8. Rather than patch an unsupported framework carrying patient data,
the application is being rebuilt on **Laravel 11**, sharing a foundation with our
[ICTSchool](https://github.com/ictinnovations/ICTSchool) platform.

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

**What is not finished**

The administrative and communication half of the system runs. The clinical and front
desk screens do not exist yet: patient registration, appointment booking, admissions and
bed allocation, prescriptions, laboratory, pharmacy dispensing and invoicing. The tables
are there, the migrations are there, and the Eloquent models are there waiting for them.
Writing those controllers and views is the next body of work.

If you are evaluating ICTHospital for production use today, please
[get in touch](https://www.icthospital.com) rather than deploying from this branch.

---

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
