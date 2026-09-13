<?php
/**
 * ICTHospital - a small set of believable records for evaluating the system.
 *
 * Every screen looks the same when it is empty, and an empty system is the hardest
 * thing to judge. This fills one day of a small hospital: a few departments and
 * doctors, a ward with patients in it, prescriptions, lab work part reported, a
 * pharmacy sale and an invoice that is part paid. The reports then have something to
 * report on.
 *
 * Safe to run more than once. It skips anything already there and never touches an
 * existing record, so it cannot overwrite real data. Even so, it is meant for a
 * demonstration or a test install, not a live one.
 *
 *   php artisan db:seed --class=DemoDataSeeder
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Database\Seeders;

use App\Models\AllotedBed;
use App\Models\Bed;
use App\Models\BedCategory;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Lab;
use App\Models\LabCategory;
use App\Models\LabTest;
use App\Models\MedicalHistory;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Nurse;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PaymentCategory;
use App\Models\PharmacyPayment;
use App\Models\PharmacySaleItem;
use App\Models\Prescription;
use App\Models\PrescriptionMedicine;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->warn('Loading demonstration data. Do not run this on a live system.');

        $departments = $this->departments();
        $doctors = $this->doctors();
        $this->nurses();
        $services = $this->services();
        $patients = $this->patients($doctors);
        $this->history($patients);
        $this->wards($patients);
        $medicines = $this->medicines();
        $this->prescriptions($patients, $doctors, $medicines);
        $this->laboratory($patients, $doctors);
        $this->pharmacy($medicines);
        $this->invoices($patients, $doctors, $services);

        $this->command->info(sprintf(
            'Demo data ready: %d departments, %d doctors, %d patients.',
            count($departments), count($doctors), count($patients)
        ));
    }

    private function departments(): array
    {
        $names = [
            'Cardiology' => 'Heart and circulation.',
            'Orthopaedics' => 'Bones, joints and fractures.',
            'Paediatrics' => 'Care for children.',
            'General Medicine' => 'Outpatient and general care.',
        ];

        $made = [];
        foreach ($names as $name => $description) {
            $made[] = Department::firstOrCreate(
                ['name' => $name],
                ['description' => $description, 'x' => '', 'y' => '']
            );
        }

        return $made;
    }

    private function doctors(): array
    {
        $rows = [
            ['Dr Sana Riaz', 'Cardiology', 'MBBS, FCPS Cardiology', '03001234501'],
            ['Dr Imran Bashir', 'Orthopaedics', 'MBBS, MS Orthopaedics', '03001234502'],
            ['Dr Ayesha Khan', 'Paediatrics', 'MBBS, DCH', '03001234503'],
            ['Dr Rehan Ali', 'General Medicine', 'MBBS', '03001234504'],
        ];

        $made = [];
        foreach ($rows as [$name, $department, $profile, $phone]) {
            $made[] = Doctor::firstOrCreate(['name' => $name], [
                'department' => $department, 'profile' => $profile, 'phone' => $phone,
                'email' => '', 'address' => '', 'img_url' => '', 'x' => '', 'y' => '',
                'ion_user_id' => '',
            ]);
        }

        return $made;
    }

    private function nurses(): void
    {
        foreach ([['Nadia Parveen', '03011234501'], ['Saima Iqbal', '03011234502']] as [$name, $phone]) {
            Nurse::firstOrCreate(['name' => $name], [
                'phone' => $phone, 'email' => '', 'address' => '', 'img_url' => '',
                'x' => '', 'y' => '', 'z' => '', 'ion_user_id' => '',
            ]);
        }
    }

    private function services(): array
    {
        $rows = [
            ['Consultation, general', 'Consultation', '1500.00'],
            ['Consultation, specialist', 'Consultation', '3000.00'],
            ['Dressing', 'Procedure', '800.00'],
            ['Ward bed, per day', 'Ward', '4000.00'],
            ['Private room, per day', 'Ward', '9000.00'],
            ['ECG', 'Diagnostics', '1200.00'],
        ];

        $made = [];
        foreach ($rows as [$name, $type, $price]) {
            $made[] = PaymentCategory::firstOrCreate(['category' => $name], [
                'type' => $type, 'c_price' => $price, 'description' => '',
            ]);
        }

        return $made;
    }

    private function patients(array $doctors): array
    {
        $rows = [
            ['Bilal Ahmed', 'male', '1979-04-12', 'O+', '03211234501', '14 Jinnah Road'],
            ['Hina Malik', 'female', '1992-11-03', 'A+', '03211234502', '5 Model Town'],
            ['Imran Shah', 'male', '1965-02-20', 'B+', '03211234503', '88 Canal View'],
            ['Zara Yousaf', 'female', '2018-07-09', 'O-', '03211234504', '22 Gulberg'],
            ['Farooq Nadeem', 'male', '1988-09-30', 'AB+', '03211234505', '9 Cantt'],
        ];

        $made = [];
        foreach ($rows as $i => [$name, $sex, $birth, $blood, $phone, $address]) {
            $made[] = Patient::firstOrCreate(['name' => $name], [
                'patient_id' => str_pad((string) ($i + 1), 6, '0', STR_PAD_LEFT),
                'sex' => $sex, 'birthdate' => $birth, 'bloodgroup' => $blood,
                'age' => (string) \Carbon\Carbon::parse($birth)->age,
                'phone' => $phone, 'address' => $address, 'email' => '',
                'doctor' => $doctors[$i % count($doctors)]->name,
                'img_url' => '', 'ion_user_id' => '', 'how_added' => 'demo data',
                'add_date' => now()->subDays(30 - $i * 4)->toDateString(),
                'registration_time' => now()->subDays(30 - $i * 4),
            ]);
        }

        return $made;
    }

    private function history(array $patients): void
    {
        $rows = [
            [0, 'Penicillin allergy', 'Rash and swelling after amoxicillin in 2024. Avoid all penicillins.'],
            [2, 'Type 2 diabetes', 'Diagnosed 2019. Diet controlled, metformin added 2023.'],
            [2, 'Appendectomy 2011', 'Open appendectomy, no complications.'],
            [4, 'Smoker', 'Around 10 a day for 15 years. Counselled on cessation.'],
        ];

        foreach ($rows as [$index, $title, $description]) {
            $patient = $patients[$index];
            MedicalHistory::firstOrCreate(
                ['patient_id' => $patient->id, 'title' => $title],
                [
                    'description' => $description,
                    'patient_name' => $patient->name,
                    'patient_address' => $patient->address,
                    'patient_phone' => $patient->phone,
                    'img_url' => '',
                    'date' => now()->subDays(20)->toDateString(),
                    'registration_time' => now()->subDays(20),
                ]
            );
        }
    }

    private function wards(array $patients): void
    {
        $general = BedCategory::firstOrCreate(['category' => 'General ward'], ['description' => 'Shared ward.']);
        $private = BedCategory::firstOrCreate(['category' => 'Private room'], ['description' => 'Single occupancy.']);

        $beds = [];
        foreach ([['G1', $general], ['G2', $general], ['G3', $general], ['P1', $private], ['P2', $private]] as [$number, $category]) {
            $beds[] = Bed::firstOrCreate(['number' => $number], [
                'category' => $category->id, 'description' => '', 'status' => 'available',
                'last_a_time' => null, 'last_d_time' => null, 'bed_id' => '',
            ]);
        }

        // One patient currently admitted, one discharged last week, so occupancy has
        // both an open stay and a completed one to measure.
        if (! AllotedBed::where('patient', $patients[2]->id)->exists()) {
            AllotedBed::create([
                'patient' => $patients[2]->id, 'bed_id' => $beds[0]->id,
                'number' => $beds[0]->number, 'category' => $beds[0]->category,
                'a_time' => now()->subDays(3), 'd_time' => null, 'status' => '', 'x' => '',
            ]);
            $beds[0]->update(['status' => 'alloted']);
        }

        if (! AllotedBed::where('patient', $patients[0]->id)->exists()) {
            AllotedBed::create([
                'patient' => $patients[0]->id, 'bed_id' => $beds[3]->id,
                'number' => $beds[3]->number, 'category' => $beds[3]->category,
                'a_time' => now()->subDays(11), 'd_time' => now()->subDays(7),
                'status' => '', 'x' => '',
            ]);
        }
    }

    private function medicines(): array
    {
        $tablets = MedicineCategory::firstOrCreate(['category' => 'Tablets'], ['description' => '']);
        $syrups = MedicineCategory::firstOrCreate(['category' => 'Syrups'], ['description' => '']);

        // The legacy columns are price (what it cost) and s_price (what it sells
        // for). There is no b_price or description on this table.
        $rows = [
            ['Amoxicillin 500mg', $tablets, 120, '12.00', '8.00', 'Amoxicillin'],
            ['Paracetamol 500mg', $tablets, 400, '5.00', '3.00', 'Paracetamol'],
            ['Metformin 850mg', $tablets, 60, '9.00', '6.00', 'Metformin'],
            ['Ibuprofen 400mg', $tablets, 25, '7.00', '4.50', 'Ibuprofen'],
            ['Paracetamol syrup 120ml', $syrups, 40, '85.00', '60.00', 'Paracetamol'],
        ];

        $made = [];
        foreach ($rows as [$name, $category, $quantity, $sell, $buy, $generic]) {
            $made[] = Medicine::firstOrCreate(['name' => $name], [
                'category' => $category->id, 'quantity' => $quantity,
                's_price' => $sell, 'price' => $buy, 'generic' => $generic,
                'box' => '', 'company' => '', 'effects' => '',
                'e_date' => now()->addYear()->toDateString(),
                'add_date' => now()->toDateString(),
            ]);
        }

        return $made;
    }

    private function prescriptions(array $patients, array $doctors, array $medicines): void
    {
        if (Prescription::count() > 0) {
            return;
        }

        $one = Prescription::create([
            'patient' => $patients[0]->id, 'doctor' => $doctors[3]->id,
            'date' => now()->subDays(2)->toDateString(),
            'symptom' => 'Sore throat, fever three days',
            'advice' => 'Rest and fluids. Return if the fever passes 39C.',
            'medicine' => '', 'note' => '', 'state' => '', 'dd' => '', 'validity' => '',
        ]);
        PrescriptionMedicine::create([
            'prescription_id' => $one->id, 'medicine_id' => $medicines[1]->id,
            'name' => 'Paracetamol 500mg', 'dosage' => '1 tablet', 'duration' => '5 days',
            'instructions' => 'Three times a day after food',
        ]);
        $one->update(['medicine' => 'Paracetamol 500mg (1 tablet, 5 days)']);

        $two = Prescription::create([
            'patient' => $patients[2]->id, 'doctor' => $doctors[0]->id,
            'date' => now()->subDays(1)->toDateString(),
            'symptom' => 'Routine diabetic review',
            'advice' => 'Continue current dose. Review in three months.',
            'medicine' => '', 'note' => '', 'state' => '', 'dd' => '', 'validity' => '',
        ]);
        PrescriptionMedicine::create([
            'prescription_id' => $two->id, 'medicine_id' => $medicines[2]->id,
            'name' => 'Metformin 850mg', 'dosage' => '1 tablet', 'duration' => '90 days',
            'instructions' => 'Twice a day with meals',
        ]);
        $two->update(['medicine' => 'Metformin 850mg (1 tablet, 90 days)']);
    }

    private function laboratory(array $patients, array $doctors): void
    {
        if (Lab::count() > 0) {
            return;
        }

        $hb = LabCategory::firstOrCreate(['category' => 'Haemoglobin'], [
            'reference_value' => '13.5 to 17.5 g/dL', 'description' => '', 'procedure_id' => null,
        ]);
        $culture = LabCategory::firstOrCreate(['category' => 'Blood culture'], [
            'reference_value' => 'No growth at 48 hours', 'description' => '', 'procedure_id' => null,
        ]);
        LabCategory::firstOrCreate(['category' => 'Fasting glucose'], [
            'reference_value' => '70 to 100 mg/dL', 'description' => '', 'procedure_id' => null,
        ]);

        // Part reported on purpose: the bloods are back, the culture is still growing.
        // That is the case the per test table exists to express.
        $lab = Lab::create([
            'patient' => $patients[2]->id, 'doctor' => $doctors[0]->id,
            'date' => now()->subDays(4)->toDateString(), 'status' => 'requested',
            'patient_name' => $patients[2]->name, 'patient_phone' => $patients[2]->phone,
            'patient_address' => $patients[2]->address, 'doctor_name' => $doctors[0]->name,
            'category_name' => '', 'report' => '', 'user' => 'demo', 'date_string' => '',
            'cnic' => '', 'token_no' => '', 'invoice_id' => null, 'report_date' => null,
        ]);

        LabTest::create([
            'lab_id' => $lab->id, 'lab_category_id' => $hb->id, 'name' => 'Haemoglobin',
            'reference_value' => '13.5 to 17.5 g/dL', 'result' => '12.8',
            'status' => 'reported', 'reported_at' => now()->subDays(3),
        ]);
        LabTest::create([
            'lab_id' => $lab->id, 'lab_category_id' => $culture->id, 'name' => 'Blood culture',
            'reference_value' => 'No growth at 48 hours', 'result' => null,
            'status' => 'requested', 'reported_at' => null,
        ]);
    }

    private function pharmacy(array $medicines): void
    {
        if (PharmacyPayment::count() > 0) {
            return;
        }

        $sale = PharmacyPayment::create([
            'date' => now()->subDays(1)->toDateString(),
            'amount' => '60.00', 'discount' => '0.00', 'vat' => '0.00',
            'gross_total' => '60.00', 'amount_received' => '60.00', 'status' => 'paid',
            'patient' => null, 'doctor' => null, 'category' => null, 'category_name' => null,
            'category_amount' => null, 'x_ray' => null, 'flat_vat' => null,
            'flat_discount' => null, 'hospital_amount' => null, 'doctor_amount' => null,
        ]);

        PharmacySaleItem::create([
            'pharmacy_payment_id' => $sale->id, 'medicine_id' => $medicines[1]->id,
            'name' => 'Paracetamol 500mg', 'quantity' => 12,
            'unit_price' => '5.00', 'line_total' => '60.00',
        ]);

        $medicines[1]->decrement('quantity', 12);
    }

    private function invoices(array $patients, array $doctors, array $services): void
    {
        if (Payment::count() > 0) {
            return;
        }

        // Part paid, so the debtor report and the settlement state both have
        // something real to show.
        $invoice = Payment::create([
            'patient' => $patients[2]->id, 'doctor' => $doctors[0]->id,
            'date' => now()->subDays(12)->toDateString(),
            'amount' => '15000.00', 'discount' => '0.00', 'vat' => '0.00',
            'gross_total' => '15000.00', 'amount_received' => '5000.00',
            'status' => 'part paid', 'patient_name' => $patients[2]->name,
            'doctor_name' => $doctors[0]->name, 'category' => null, 'category_name' => null,
            'category_amount' => null, 'x_ray' => null, 'flat_vat' => null,
            'flat_discount' => null, 'hospital_amount' => null, 'doctor_amount' => null,
            'deposit_type' => null, 'remarks' => 'Admission, three nights.', 'user' => 'demo',
            'date_string' => '',
        ]);

        InvoiceItem::create([
            'payment_id' => $invoice->id, 'payment_category_id' => $services[1]->id,
            'name' => 'Consultation, specialist', 'quantity' => 1,
            'unit_price' => '3000.00', 'line_total' => '3000.00',
        ]);
        InvoiceItem::create([
            'payment_id' => $invoice->id, 'payment_category_id' => $services[3]->id,
            'name' => 'Ward bed, per day', 'quantity' => 3,
            'unit_price' => '4000.00', 'line_total' => '12000.00',
        ]);
        InvoicePayment::create([
            'payment_id' => $invoice->id, 'amount' => '5000.00',
            'method' => 'cash', 'reference' => 'Deposit on admission',
            'paid_at' => now()->subDays(12),
        ]);
    }
}
