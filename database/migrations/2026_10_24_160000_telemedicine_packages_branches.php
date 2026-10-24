<?php
/**
 * ICTHospital - video visits, service kinds and packages, and hospital branches.
 *
 * Video visits: an appointment can be a video consultation with its own meeting
 * room, opened by the doctor from the queue and sent to the patient as a link.
 *
 * Service kinds: dialysis sessions, operation theatre charges, packages and
 * donations are billed like any service, but reports need to tell them apart.
 * A package is a service whose price covers a list of component services.
 *
 * Branches: one installation can run several sites. Invoices, visits and shift
 * closes are stamped with the branch they happened at, users can be tied to one
 * branch, and the busy screens show the current branch only. The existing
 * "branches" table belongs to the ICTSchool remote-branch feature, so this is a
 * new table.
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
        Schema::table('appointment', function (Blueprint $table) {
            $table->string('mode', 12)->nullable(); // null or in_person, video
            $table->string('video_room', 80)->nullable();
            $table->unsignedInteger('branch_id')->nullable()->index();
        });

        Schema::table('payment_category', function (Blueprint $table) {
            $table->string('service_kind', 20)->nullable();
        });

        Schema::create('service_package_item', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('package_id')->index(); // a payment_category of kind package
            $table->unsignedInteger('component_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('hospital_branch', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('address', 255)->nullable();
            $table->string('phone', 60)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable();
        });
        Schema::table('payment', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable()->index();
        });
        Schema::table('shift_close', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('shift_close', fn (Blueprint $t) => $t->dropColumn('branch_id'));
        Schema::table('payment', fn (Blueprint $t) => $t->dropColumn('branch_id'));
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('branch_id'));
        Schema::dropIfExists('hospital_branch');
        Schema::dropIfExists('service_package_item');
        Schema::table('payment_category', fn (Blueprint $t) => $t->dropColumn('service_kind'));
        Schema::table('appointment', fn (Blueprint $t) => $t->dropColumn(['mode', 'video_room', 'branch_id']));
    }
};
