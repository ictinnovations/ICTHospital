<?php
/**
 * ICTHospital - the staff types that share one screen.
 *
 * nurse, pharmacist, laboratorist, receptionist and accountant are five tables with
 * the same shape: a name, contact details, a photo and the legacy ion_user_id. Five
 * near-identical controllers and ten near-identical views would drift apart the first
 * time a column was added to one of them, so one controller reads this map instead.
 *
 * Doctors are deliberately not here. They carry a department, a qualification and an
 * ICTCore procedure id, and other modules select from them, so they have their own
 * screen.
 *
 * `permission` is the prefix in config/hospital_permissions.php. The catalogue has a
 * dedicated Nurse set and a general Staff set, which is why nurse differs.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

return [

    'nurse' => [
        'model' => \App\Models\Nurse::class,
        'label' => 'Nurses',
        'singular' => 'nurse',
        'permission' => 'nurse',
        'icon' => 'glyphicon-heart',
    ],

    'pharmacist' => [
        'model' => \App\Models\Pharmacist::class,
        'label' => 'Pharmacists',
        'singular' => 'pharmacist',
        'permission' => 'staff',
        'icon' => 'glyphicon-shopping-cart',
    ],

    'laboratorist' => [
        'model' => \App\Models\Laboratorist::class,
        'label' => 'Laboratory staff',
        'singular' => 'laboratory technician',
        'permission' => 'staff',
        'icon' => 'glyphicon-tint',
    ],

    'receptionist' => [
        'model' => \App\Models\Receptionist::class,
        'label' => 'Receptionists',
        'singular' => 'receptionist',
        'permission' => 'staff',
        'icon' => 'glyphicon-bell',
    ],

    'accountant' => [
        'model' => \App\Models\Accountant::class,
        'label' => 'Accountants',
        'singular' => 'accountant',
        'permission' => 'staff',
        'icon' => 'glyphicon-usd',
    ],

];
