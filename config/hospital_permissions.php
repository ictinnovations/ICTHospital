<?php
/**
 * ICTHospital - the permission catalogue and the roles it applies to.
 *
 * This list used to be duplicated, once in PermissionController and once inside
 * permission.blade.php. The two had to stay in the same order or the checkbox
 * grid read the wrong rows, so it now lives here and both read it from config.
 *
 * The labels replace the school catalogue inherited from ICTSchool, which granted
 * rights over students, classes, exams, marks and school fees.
 *
 * `permission_name` in the database is the label lowercased with spaces turned
 * into underscores, so changing a label changes the stored key. Add to the end of
 * the list rather than reordering it if you already have permissions saved.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

return [

    /*
     * Roles the permission grid has a column for. The key is stored in
     * permission.permission_group and matched against users.group in lower case.
     */
    'groups' => [
        'admin' => 'Admin',
        'doctor' => 'Doctors',
        'nurse' => 'Nurses',
        'accountant' => 'Accountants',
    ],

    'permissions' => [

        // Patients
        'Patient View',
        'Patient Add',
        'Patient Update',
        'Patient Delete',
        'Patient Medical History',
        'Patient Portal Access',
        'Patient Bulk Add',

        // Appointments
        'Appointment View',
        'Appointment Add',
        'Appointment Update',
        'Appointment Cancel',
        'Appointment Calendar',
        'View Appointment Reports',

        // Admissions, beds and wards
        'Admission View',
        'Admission Add',
        'Admission Discharge',
        'Bed View',
        'Bed Allot',
        'Bed Release',
        'Ward View',
        'Ward Add',
        'Ward Update',
        'Ward Delete',

        // Clinical
        'Prescription View',
        'Prescription Add',
        'Prescription Update',
        'Prescription Delete',
        'Lab Test View',
        'Lab Test Add',
        'Lab Test Update',
        'Lab Test Delete',
        'Diagnostic Report View',
        'Diagnostic Report Add',
        'Operation Theatre View',
        'Operation Theatre Schedule',

        // Pharmacy
        'Medicine View',
        'Medicine Add',
        'Medicine Update',
        'Medicine Delete',
        'Pharmacy Sale',
        'Pharmacy Stock View',
        'View Pharmacy Reports',

        // Staff
        'Doctor View',
        'Doctor Add',
        'Doctor Update',
        'Doctor Delete',
        'Doctor Schedule',
        'Doctor Portal Access',
        'Nurse View',
        'Nurse Add',
        'Nurse Update',
        'Nurse Delete',
        'Staff View',
        'Staff Add',
        'Staff Update',
        'Staff Delete',
        'Department View',
        'Department Add',
        'Department Update',
        'Department Delete',

        // Money
        'Payment View',
        'Payment Add',
        'Payment Update',
        'Payment Delete',
        'Patient Deposit',
        'Invoice View',
        'Invoice Print',
        'Expense View',
        'Expense Add',
        'Expense Update',
        'Expense Delete',
        'Payroll View',
        'Payroll Process',
        'View Payment Reports',
        'Accounting',

        // Communication and administration
        'Send Sms/Voice',
        'Send Notification',
        'View Sms/voice log Reports',
        'Service View',
        'Service Add',
        'Service Update',
        'Service Delete',
        'Holidays View',
        'Holidays Add',
        'Holidays Delete',
        'Hospital Information Add',
        'Branch View',
        'Branch Add',
        'Branch Update',
        'Branch Delete',
    ],
];
