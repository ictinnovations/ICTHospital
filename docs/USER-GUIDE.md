# ICTHospital User Guide

This walks you through the system the way you'd actually use it: register a patient,
book them in, admit them, prescribe, order bloods, dispense, bill, and read the reports
at the end of the week. Every screen shown here is a real screenshot of a working
install with demonstration data loaded.

Developed by Tahir Almas, [ICT Innovations](https://www.ictinnovations.com).

---

## Before you start

Sign in with the account created when the system was installed. The default is `admin`
with the password `123456`, and you should change it on day one.

![Signing in and getting around](screenshots/navigation.png)

The menu on the left is grouped by what you're doing rather than by which table the data
lives in. Clinical work is at the top, money in the middle, administration at the bottom.
You'll only see the entries your role is allowed to open, so a receptionist's menu is
shorter than an administrator's.

### Load the demonstration data

An empty system is hard to judge. If you're evaluating ICTHospital, load a small set of
believable records first:

```bash
php artisan db:seed --class=DemoDataSeeder
```

That gives you four departments, four doctors, five patients, a ward with someone in it,
a part reported lab request and an invoice that's half paid. It's safe to run twice and
it never overwrites anything, but keep it off a live system.

---

## The dashboard

![Dashboard](screenshots/dashboard.png)

Counts across the top, money in the middle, and the most recent patients and
appointments underneath. Nothing here is stored as a running total. Every figure is
worked out when you open the page, so it can't drift away from the records it's counting.

---

## Patients

The front desk starts here. Admissions, prescriptions, lab requests and invoices all hang
off a patient record, so this is the first thing you'll create.

![Patient list](screenshots/patients.png)

One search box covers name, patient ID, phone and email, because the person on the phone
gives you whatever they can remember.

![Registering a patient](screenshots/patient-register.png)

Only the name is required. Someone walking in at two in the morning with no paperwork
still has to be registerable, and a form that refuses that record just gets worked around
with junk data.

Two things worth knowing. Age is worked out from the date of birth rather than typed, so
it can't go stale the way it did in the system this replaces. And the patient ID comes
from the highest one already issued, so deleting a record never hands the next patient a
number someone else is using.

---

## Medical history

![Medical history](screenshots/medical-history.png)

Allergies, past operations, chronic conditions. This is the first thing worth reading
before a consultation, so it has its own screen rather than being buried in the patient
record. You can attach a scan or a photo of a report to any entry.

The patient's name, address and phone are copied onto each entry when you write it. That
looks like duplication and it's deliberate: a note written two years ago should still read
correctly after the patient moves house.

---

## Doctors and departments

![Doctors](screenshots/doctors.png)

Every clinical screen picks a doctor from this list, so add at least one before you try to
book anything. The list shows how many upcoming appointments each doctor has, which
answers the question you actually have when you're looking at it.

A doctor with appointments on file can't be deleted. The appointment record points at the
doctor by id, so removing them would leave every past appointment pointing at nothing.

![Departments](screenshots/departments.png)

Renaming a department here moves every doctor in it to the new name. If you're upgrading
from an older install, the departments your doctors already use will show up as unlisted,
and one button adopts them onto the list.

---

## Staff

![Nurses](screenshots/staff-nurses.png)

Nurses, pharmacists, laboratory staff, receptionists and accountants. They share one
screen because they share one shape: a name, contact details and a photo. Nurses have
their own permission so you can let ward staff manage the nursing list without giving them
the accounts list.

---

## Appointments

![Appointments](screenshots/appointments.png)

A day view, filtered by doctor and by status. The system won't let you double book a
doctor, but it will let you book back to back, because that's how a clinic actually runs.
Cancelling or marking a no-show frees the slot again straight away.

If two clerks try for the same slot at the same moment, one of them gets it and the other
gets told. That's a database lock rather than a polite check, so it holds under real
pressure.

---

## Beds and admissions

![Ward board](screenshots/beds.png)

A bed is free when no admission points at it with an open discharge time. There's a status
column on the bed record and it's kept in step, but the admission is the truth. In the
CodeIgniter system this replaced, a clerk set both by hand and they drifted apart within
weeks.

![Admissions](screenshots/admissions.png)

Two rules the old system never enforced: one patient per bed, and one bed per patient.

---

## Prescriptions and medicines

![Prescriptions](screenshots/prescriptions.png)

Each drug on a prescription is its own row. That's what makes "which patients are on this
drug" a question you can answer, which mattered enough to be the main reason the schema
changed.

![Medicine catalogue](screenshots/medicines.png)

You can prescribe something that isn't in the catalogue. A doctor shouldn't be blocked
because the pharmacy hasn't added a line yet.

---

## Laboratory

![Lab requests](screenshots/laboratory.png)

Each test on a request carries its own result and status, so a request can be part
reported. The bloods come back, the culture is still growing, and the screen shows exactly
that instead of pretending the whole request is either done or not.

![Test catalogue](screenshots/lab-catalogue.png)

Fill in the reference range. It's copied onto the test when it's requested, so a result
from last year is still readable against the range that applied when it was measured.

---

## Pharmacy

![Pharmacy](screenshots/pharmacy.png)

Stock comes out of the catalogue in the same transaction as the sale, so the quantity on
the shelf follows what was actually handed over. A sale that fails leaves the stock
untouched, and reversing a sale puts it back. Two people dispensing the last few units at
the same time can't both succeed.

---

## Invoices, services and prices

![Services and prices](screenshots/services-prices.png)

Invoice lines are raised against this list, so fill it in before you bill anyone. The
price here is the current one. It's copied onto the invoice line when the charge is
raised, so putting a price up next month doesn't rewrite what somebody was billed last
month.

![Invoices](screenshots/invoices.png)

Payments are rows, so a deposit now and the balance later is something the system can
actually express. Whether an invoice is unpaid, part paid or settled is worked out from
those payments. Nobody types it, so it can't be wrong.

You can't overpay an invoice, and a refused payment isn't recorded at all.

---

## Reports

![Reports](screenshots/reports.png)

Four questions a hospital asks most weeks. Every figure is worked out when you open the
report.

![Outstanding lab work](screenshots/report-lab.png)

Tests requested and not yet reported, oldest first, with anything past three days marked.

![Drug usage](screenshots/report-drug-usage.png)

What was prescribed next to what was actually dispensed. The gap between the two columns
is the part worth reading: a drug prescribed and never dispensed here is usually a stock
problem, or a script being filled somewhere else.

![Debtors](screenshots/report-debtors.png)

Who owes what, aged into the usual buckets. The age comes from the patient's oldest
unsettled invoice rather than their most recent one, so a long standing balance doesn't
look fresh just because something new was added to it.

![Bed occupancy](screenshots/report-occupancy.png)

Occupancy per day, the busiest day, discharges and average length of stay, counted from
admissions rather than from the bed status column.

---

## Users and permissions

![Users](screenshots/users.png)

![Permissions](screenshots/permissions.png)

Ninety permissions across Admin, Doctor, Nurse and Accountant. Admin is granted everything
when the system is installed. Everyone else starts with nothing and is given what they
need on this grid. The menu hides what a role can't open, so nobody is offered a link that
bounces them.

**If you're upgrading from ICTSchool**, run this once after migrating or every role,
including Admin, will be locked out of every screen:

```bash
php artisan hospital:sync-permissions
```

It only adds what's missing and never hands back a right you deliberately took away, so
it's safe to run again.

---

## Payment gateways

![Payment gateways](screenshots/gateways.png)

Be clear on what this is. Nothing in this release charges a card. The settings are stored
ready for the online payment work still to come, so you can put your credentials in now
and they'll be there when it lands.

Credentials are write only. They're never shown back to you, because a settings page that
prints an API password puts it in every browser cache and every screenshot. Leave a field
blank when you're editing and whatever is stored stays.

---

## Questions people ask

**Can I register a patient without a date of birth or a phone number?**
Yes. Only the name is required.

**What happens if two people book the same appointment slot?**
One booking succeeds, the other is refused with a message naming the clash. The same
applies to admitting two patients to one bed, dispensing the last of a medicine, and
paying off the same invoice twice.

**Can I delete a doctor, a medicine or a service?**
Only if nothing refers to it. Anything already used stays, so old records keep reading
correctly. Rename it instead.

**If I change a price, does it change old invoices?**
No. The price is copied onto the line when the charge is raised. The same goes for lab
reference ranges and prescribed drug names.

**Why does the menu look different for my colleague?**
The menu only shows what your role can open. Ask an administrator to grant the permission
on the permissions grid.

**How do I get rid of the demonstration data?**
Delete the records from the screens, or reinstall with `php artisan migrate:fresh --seed`
and skip the demo seeder. Don't run `migrate:fresh` on anything real.

---

## Getting help

ICTHospital is open source under the GPL v3. Issues and contributions are welcome at
[github.com/ictinnovations/ICTHospital](https://github.com/ictinnovations/ICTHospital).

If you're weighing it up for a live deployment, get in touch through
[icthospital.com](https://www.icthospital.com) and we can talk about your data and your
migration.

Developed by Tahir Almas, [ICT Innovations](https://www.ictinnovations.com).
