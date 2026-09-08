<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->string('logo', 1000);
            $table->string('address', 500);
            $table->string('phone', 100);
            $table->string('emergency', 100);
            $table->string('support', 100);
            $table->string('email', 100);
            $table->string('currency', 100);
            $table->string('block_1_text_under_title', 500);
            $table->string('service_block__text_under_title', 500);
            $table->string('doctor_block__text_under_title', 500);
            $table->string('facebook_id', 100);
            $table->string('twitter_id', 100);
            $table->string('google_id', 100);
            $table->string('youtube_id', 100);
            $table->string('skype_id', 100);
            $table->string('x', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
