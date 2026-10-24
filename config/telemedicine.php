<?php
/**
 * ICTHospital - video consultations.
 *
 * Rooms are opened on a Jitsi Meet server. The public meet.jit.si works out of the
 * box; a hospital that wants its calls on its own server sets TELEMED_BASE_URL to a
 * self-hosted Jitsi. Room names are long and random so they cannot be guessed.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

return [
    'base_url' => rtrim(env('TELEMED_BASE_URL', 'https://meet.jit.si'), '/'),
    'room_prefix' => env('TELEMED_ROOM_PREFIX', 'icthospital'),
];
