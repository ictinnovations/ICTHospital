# ICTHospital

Hospital management software with unified communications built in. ICTHospital handles
the day-to-day running of a hospital or clinic — patients, appointments, admissions,
lab, pharmacy and billing — and adds SMS, voice and video consultation so patients and
clinicians can reach each other without leaving the system.

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
- Authentication, users, roles and permissions
- Hospital dashboard: patient and appointment counts, bed occupancy, clinical activity,
  income against expenses, recent patients and appointments
- Global search across patients and doctors
- Settings, messaging, SMS logging, templates, barcode generation, activity logging
- ICTCore integration for SMS and voice

**What is not finished**

- Several modules inherited from the shared foundation still carry school-specific logic
  and are being rewritten for hospital use
- The public-facing website module does not run on a fresh install
- The legacy schema is carried over faithfully and not yet modernised: some dates are
  stored as strings and foreign keys are not yet declared

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
```

Set your database in `.env`, then:

```bash
php artisan migrate --seed
php artisan serve
```

### Docker

A development environment is included:

```bash
docker compose up -d
docker compose exec app php artisan migrate --seed
```

The application is then on http://localhost:8082.

## Communications

SMS and voice run through [ICTCore](https://github.com/ictinnovations/ictcore), our own
open source communications framework, configured in Settings. Video consultation targets
LiveKit. None of these are required to run the core system.

## Contributing

Issues and pull requests are welcome. For bug reports, the PHP version, database version
and the exact error message help more than anything else.

Run the route smoke test after any significant change:

```bash
bash smoke-test.sh
```

## Related projects

- [ICTSchool](https://github.com/ictinnovations/ICTSchool) — school management
- [ICTCore](https://github.com/ictinnovations/ictcore) — communications framework
- [ICTFax](https://github.com/ictinnovations/ictfax) — fax server on FreeSWITCH
- [ICTDialer](https://github.com/ictinnovations/ictdialer) — auto dialer and campaigns
- [ICTPBX Community Edition](https://github.com/ictinnovations/ictpbx-community-edition) — multi-tenant IP PBX

Full list: [ictinnovations.com/projects](https://ictinnovations.com/projects/)

## Licence

GNU General Public License v3.0 — see [LICENSE](LICENSE).

Copyright (c) ICT Innovations.
