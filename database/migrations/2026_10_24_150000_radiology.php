<?php
/**
 * ICTHospital - radiology: the exam catalogue, orders and reports.
 *
 * An order moves ordered, scheduled, done, reported (or cancelled). The exam name,
 * modality and the radiologist's findings are written on the order itself, so a
 * report reads the same after the catalogue changes.
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
        Schema::create('radiology_exam', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 120);
            $table->string('modality', 20); // xray, ultrasound, ct, mri, mammography, fluoroscopy, other
            // The service billed for it, so the price list and doctor share apply.
            $table->unsignedInteger('payment_category_id')->nullable();
            $table->text('report_template')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('radiology_order', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('patient_id')->index();
            $table->unsignedInteger('doctor_id')->nullable();
            $table->unsignedInteger('appointment_id')->nullable();
            $table->unsignedInteger('radiology_exam_id')->nullable();
            $table->string('name', 120);
            $table->string('modality', 20);
            $table->boolean('urgent')->default(false);
            $table->string('clinical_notes', 1000)->nullable();
            $table->string('status', 12)->default('ordered');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('performed_at')->nullable();
            $table->text('findings')->nullable();
            $table->text('impression')->nullable();
            $table->unsignedInteger('reported_by')->nullable();
            $table->dateTime('reported_at')->nullable();
            $table->unsignedInteger('payment_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_order');
        Schema::dropIfExists('radiology_exam');
    }
};
