<?php
/**
 * ICTHospital - patient types, per-type price lists and fuller registration.
 *
 * A private hospital does not charge everyone the same. A walk-in pays the general
 * price, a patient sent by a panel company is billed at the rate agreed with that
 * company, a charity case may pay nothing, and patients booked through an online
 * platform have their own list. The price also differs between an outpatient visit
 * and the same service on the ward. Until now there was one c_price per service.
 *
 * Every column added to a legacy table is nullable, so existing rows stay valid.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 80)->unique();
            // general, panel, charity, online or staff. Reports group on this.
            $table->string('kind', 20)->default('general');
            $table->string('company', 120)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        DB::table('patient_type')->insert([
            'name' => 'General', 'kind' => 'general', 'active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        Schema::create('service_price', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_category_id');
            $table->unsignedInteger('patient_type_id');
            // Null means "use the service's list price" for that setting.
            $table->decimal('opd_price', 12, 2)->nullable();
            $table->decimal('ipd_price', 12, 2)->nullable();
            $table->timestamps();
            $table->unique(['payment_category_id', 'patient_type_id']);
        });

        Schema::table('patient', function (Blueprint $table) {
            $table->unsignedInteger('patient_type_id')->nullable();
            $table->string('cnic', 20)->nullable();
            $table->string('guardian_relation', 40)->nullable();
            $table->string('guardian_mrn', 20)->nullable();
            $table->boolean('mlc')->default(false);
            $table->string('old_mrn', 40)->nullable();
            $table->string('employee_no', 40)->nullable();
            $table->string('alt_phone', 40)->nullable();
            $table->string('city', 60)->nullable();
        });

        Schema::table('payment', function (Blueprint $table) {
            $table->unsignedInteger('patient_type_id')->nullable();
            // opd or ipd: which column of the price list the lines were priced from.
            $table->string('care_setting', 10)->nullable();
        });

        Schema::table('payment_category', function (Blueprint $table) {
            $table->string('department', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payment_category', fn (Blueprint $t) => $t->dropColumn('department'));
        Schema::table('payment', fn (Blueprint $t) => $t->dropColumn(['patient_type_id', 'care_setting']));
        Schema::table('patient', fn (Blueprint $t) => $t->dropColumn([
            'patient_type_id', 'cnic', 'guardian_relation', 'guardian_mrn', 'mlc', 'old_mrn',
            'employee_no', 'alt_phone', 'city',
        ]));
        Schema::dropIfExists('service_price');
        Schema::dropIfExists('patient_type');
    }
};
