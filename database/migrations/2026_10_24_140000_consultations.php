<?php
/**
 * ICTHospital - the OPD consultation note.
 *
 * Until now a visit carried vitals and a free-text remark, and the clinical record
 * lived in prescriptions, lab requests and history notes. A consultation is the
 * doctor's note for one visit: complaint, history, examination by system,
 * diagnosis, plan, follow-up, plus the specialty sections a gynae, dental or eye
 * clinic needs. Structured sections are JSON, because each specialty has its own
 * fields and most visits fill only a few of them.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('appointment_id')->nullable()->unique();
            $table->unsignedInteger('patient_id')->index();
            $table->unsignedInteger('doctor_id')->nullable()->index();
            $table->date('date');
            $table->text('presenting_complaint')->nullable();
            $table->text('history')->nullable();
            $table->text('examination')->nullable(); // JSON: system => [status, note]
            $table->text('provisional_diagnosis')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('plan')->nullable();
            $table->text('treatment')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('follow_up_notes', 255)->nullable();
            $table->text('allergies')->nullable();
            $table->text('past_history')->nullable(); // JSON
            $table->text('social_history')->nullable(); // JSON
            $table->text('specialty')->nullable(); // JSON: gynae, dental, eye
            $table->text('immunizations')->nullable(); // JSON: vaccine => date
            $table->string('status', 12)->default('draft'); // draft, completed
            $table->dateTime('completed_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('consult_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->unsignedInteger('doctor_id')->nullable();
            $table->text('fields'); // JSON of the text sections
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consult_template');
        Schema::dropIfExists('consultation');
    }
};
