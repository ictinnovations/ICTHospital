<?php
/**
 * ICTHospital - OPD consultation notes.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ConsultTemplate;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\Lab;
use App\Models\LabCategory;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationTest extends TestCase
{
    use RefreshDatabase;

    private $patient;
    private $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::create(['firstname' => 'Dr', 'lastname' => 'Test', 'login' => 'drtest', 'email' => 'dr@example.test',
            'desc' => '', 'group' => 'Admin', 'password' => bcrypt('secret')]));
        $this->patient = Patient::create(['name' => 'Nazish Saba', 'patient_id' => '000050', 'img_url' => '', 'email' => '', 'doctor' => '',
            'address' => '', 'phone' => '', 'sex' => 'female', 'age' => '30', 'bloodgroup' => '', 'ion_user_id' => '', 'how_added' => 'test']);
        $this->doctor = Doctor::create(['name' => 'Dr Saman', 'img_url' => '', 'email' => '', 'address' => '', 'phone' => '',
            'department' => '', 'profile' => '', 'x' => '', 'y' => '', 'ion_user_id' => '']);
    }

    private function visit($status = 'arrived')
    {
        $start = now()->setTime(11, 0);

        return Appointment::create(['patient' => $this->patient->id, 'doctor' => $this->doctor->id, 'date' => $start->toDateString(),
            'time_slot' => '11:00 - 11:15', 's_time' => $start->toDateTimeString(), 'e_time' => $start->copy()->addMinutes(15)->toDateTimeString(),
            's_time_key' => $start->format('YmdHi'), 'status' => $status, 'remarks' => '', 'add_date' => $start->toDateString(),
            'registration_time' => now()->toDateTimeString(), 'user' => '', 'request' => '']);
    }

    public function test_saving_records_the_note_and_marks_the_patient_with_the_doctor(): void
    {
        $v = $this->visit();
        $this->post('/consultations/visit/' . $v->id, [
            'presenting_complaint' => 'Fever for 3 days', 'provisional_diagnosis' => 'Viral fever', 'b_p' => '120/80',
            'exam' => ['chest' => ['status' => 'abnormal', 'note' => 'Crepts right base'], 'cvs' => ['status' => 'normal']],
            'past' => ['diabetes' => 'Since 2019, on metformin'],
        ])->assertSessionHasNoErrors();

        $c = Consultation::first();
        $this->assertSame('Viral fever', $c->provisional_diagnosis);
        $this->assertSame('abnormal', $c->examination['chest']['status']);
        $this->assertSame('120/80', $v->fresh()->b_p);
        $this->assertSame('in consultation', $v->fresh()->status);
    }

    public function test_save_and_discharge_completes_the_visit(): void
    {
        $v = $this->visit();
        $this->post('/consultations/visit/' . $v->id, ['diagnosis' => 'URTI', 'action' => 'complete'])->assertSessionHasNoErrors();

        $this->assertSame('completed', Consultation::first()->status);
        $this->assertSame('completed', $v->fresh()->status);
        $this->assertNotNull($v->fresh()->checked_out_at);
    }

    public function test_lab_tests_ordered_in_the_consult_raise_a_lab_request(): void
    {
        $cbc = LabCategory::create(['category' => 'CBC', 'reference_value' => '']);
        $v = $this->visit();
        $this->post('/consultations/visit/' . $v->id, ['presenting_complaint' => 'Fatigue', 'lab_tests' => [$cbc->id]])->assertSessionHasNoErrors();

        $lab = Lab::with('tests')->first();
        $this->assertNotNull($lab);
        $this->assertSame('CBC', $lab->tests->first()->name);
        $this->assertSame($this->doctor->id, (int) $lab->doctor);
    }

    public function test_allergies_and_past_history_carry_to_the_next_visit(): void
    {
        $v1 = $this->visit('completed');
        $this->post('/consultations/visit/' . $v1->id, ['allergies' => 'Penicillin: rash', 'past' => ['hypertension' => 'On amlodipine']]);
        $v2 = $this->visit();

        $this->get('/consultations/visit/' . $v2->id)->assertOk()->assertSee('Penicillin: rash')->assertSee('On amlodipine');
    }

    public function test_edd_and_gestational_age_are_worked_out_from_the_lmp(): void
    {
        [$edd, $ga] = Consultation::obstetricDates('2026-01-01', '2026-03-12');
        $this->assertSame('2026-10-08', $edd);
        $this->assertSame('10w 0d', $ga);

        $v = $this->visit();
        $this->post('/consultations/visit/' . $v->id, ['gynae' => ['lmp' => now()->subDays(70)->toDateString(), 'g' => '2']]);
        $this->assertSame('10w 0d', Consultation::first()->specialty['gynae']['gestational_age']);
    }

    public function test_the_text_can_be_saved_as_a_template(): void
    {
        $v = $this->visit();
        $this->post('/consultations/visit/' . $v->id, ['plan' => 'Paracetamol, fluids, review in 3 days', 'template_name' => 'Viral fever']);

        $t = ConsultTemplate::first();
        $this->assertSame('Viral fever', $t->name);
        $this->assertSame('Paracetamol, fluids, review in 3 days', $t->fields['plan']);
        $this->get('/consultations/visit/' . $this->visit()->id)->assertSee('Viral fever');
    }

    public function test_screens_render(): void
    {
        $v = $this->visit();
        $this->get('/consultations/visit/' . $v->id)->assertOk()->assertSee('Examination')->assertSee('Gynae and obstetric');
        $this->post('/consultations/visit/' . $v->id, ['diagnosis' => 'Migraine']);
        $c = Consultation::first();
        $this->get('/consultations/' . $c->id . '/print')->assertOk()->assertSee('Migraine');
        $this->get('/consultations')->assertOk()->assertSee('Nazish Saba');
        $this->get('/patients/' . $this->patient->id)->assertOk()->assertSee('Consultations');
    }
}
