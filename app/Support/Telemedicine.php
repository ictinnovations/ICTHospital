<?php
/**
 * ICTHospital - video consultation rooms.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

class Telemedicine
{
    /** A long random room name: anyone with the link can join, nobody can guess it. */
    public static function newRoom()
    {
        return config('telemedicine.room_prefix', 'icthospital') . '-' . bin2hex(random_bytes(12));
    }

    public static function url($appointment)
    {
        return config('telemedicine.base_url', 'https://meet.jit.si') . '/' . rawurlencode((string) $appointment->video_room);
    }
}
