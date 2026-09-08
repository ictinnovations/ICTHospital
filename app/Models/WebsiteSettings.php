<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSettings extends Model
{
    use HasFactory;

    protected $table = 'website_settings';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'logo',
        'address',
        'phone',
        'emergency',
        'support',
        'email',
        'currency',
        'block_1_text_under_title',
        'service_block__text_under_title',
        'doctor_block__text_under_title',
        'facebook_id',
        'twitter_id',
        'google_id',
        'youtube_id',
        'skype_id',
        'x',
    ];
}
