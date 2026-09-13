<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstituteController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ICTCoreController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CronjobController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\LabCategoryController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentCategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DepartmentController;





// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/session', [UsersController::class, 'session']);


Route::group(['middleware' => ['web', 'activity']], function () {
    Route::get('/', [HomeController::class, 'index'])->name("login");
    Route::get('/dashboard/', [DashboardController::class, 'index']);
    Route::post('/users/login', [UsersController::class, 'postSignin']);
    Route::get('/login/{user_id}/{d_id}', [UsersController::class, 'dologin']);
    Route::get('/verification_code', [UsersController::class, 'codeverify']);
    Route::post('/users/code_check', [UsersController::class, 'code_check']);
    Route::get('/branches', [InstituteController::class, 'branches']);
    Route::post('/branch', [InstituteController::class, 'createbranch']);
    Route::get('/verify_code', [UsersController::class, 'verify_code']);
    Route::post('/verified', [UsersController::class, 'verified']);
    Route::get('/users/logout', [UsersController::class, 'getLogout']);
    Route::get('/users', [UsersController::class, 'show']);
    Route::post('/usercreate', [UsersController::class, 'create']);
    Route::get('/useredit/{id}', [UsersController::class, 'edit']);
    Route::post('/userupdate', [UsersController::class, 'update']);
    Route::get('/userdelete/{id}', [UsersController::class, 'delete']);
});

Route::group(['middleware' => ['auth', 'activity']], function () {
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::post('/search', [SearchController::class, 'search'])->name('search.query');
    /**
     * Class Routes
     **/
    // Uncomment the line below to enable the middleware for delete permission
    // ->middleware('checkPermission:class_delete');
});


Route::group(['middleware' => ['web', 'activity']], function () {
});


Route::group(['middleware' => ['auth', 'activity']], function () {

    //level routes



    //Question routes
});


Route::group(['middleware' => ['web', 'activity']], function () {
});

// The ICTSchool student route groups stood here. Every one was empty after the
// rebuild and the student_* permissions they named are no longer in the
// catalogue, so they were removed rather than left as dead scaffolding.


Route::group(['middleware' => ['web', 'activity']], function () {

    // Teacher routes
});


Route::group(['middleware' => ['auth', 'activity']], function () {
});


Route::group(['middleware' => ['web', 'activity']], function () {
});


Route::group(['middleware' => 'auth'], function () {
});


Route::group(['middleware' => ['web', 'activity']], function () {
});


Route::group(['middleware' => ['auth', 'activity']], function () {
});


Route::group(['middleware' => ['web', 'activity']], function () {

    // Student attendance
});


Route::group(['middleware' => ['auth', 'activity']], function () {
    /**
     * Papers route
     **/

    // Exam
});


// Acadamic Year

// GPA Routes


Route::group(['middleware' => 'auth'], function () {

    Route::get('/smslog', [SmsController::class, 'getsmsLog']);
    Route::post('/smslog', [SmsController::class, 'postsmsLog']);
    Route::get('/smslog/delete/{id}', [SmsController::class, 'deleteLog']);
});


// Mark routes


    //Markssheet
Route::group(['middleware' => 'auth'], function () {
});

    //tabulation sheet
Route::group(['middleware' => 'auth'], function () {

    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings', [SettingsController::class, 'save']);
    Route::get('/institute', [InstituteController::class, 'index']);
    Route::post('/institute', [InstituteController::class, 'save']);
    Route::get('/ictcore', [ICTCoreController::class, 'index']);
    Route::post('/ictcore', [ICTCoreController::class, 'create']);
    Route::post('/notification_type', [ICTCoreController::class, 'noti_create']);
    Route::get('/notification_type', [ICTCoreController::class, 'noti_index']);
    Route::get('/ictcore/attendance', [ICTCoreController::class, 'attendance_index']);
    Route::post('/ictcore/attendance', [ICTCoreController::class, 'post_attendance']);
    Route::get('/ictcore/fees', [ICTCoreController::class, 'fee_message_index']);
    Route::post('/ictcore/fees', [ICTCoreController::class, 'post_fees']);

    //promotion
    Route::get('/template/create', [TemplateController::class, 'index']);
    Route::post('/template/create', [TemplateController::class, 'create']);
    Route::get('/template/list', [TemplateController::class, 'show']);
    Route::get('/message/edit/{id}', [TemplateController::class, 'edit']);
    Route::post('/message/update', [TemplateController::class, 'update']);
    Route::get('/message/delete/{id}', [TemplateController::class, 'delete']);
    Route::get('/message', [MessageController::class, 'index']);
    Route::post('/message', [MessageController::class, 'create']);
});


Route::get('/settings', [SettingsController::class, 'index']);
Route::post('/settings', [SettingsController::class, 'save']);
Route::get('/permission', [PermissionController::class, 'index']);
Route::post('/permission/create', [PermissionController::class, 'store']);
Route::get('/schedule', [SettingsController::class, 'get_schedule']);
Route::post('/schedule', [SettingsController::class, 'post_schedule']);

    // Accounting
Route::group(['middleware' => 'auth'], function () {
    Route::get('/accounting', [AccountingController::class, 'index'])->middleware('checkPermission:accounting');
    Route::post('/accounting', [AccountingController::class, 'store'])->middleware('checkPermission:accounting');
    Route::get('/accounting/sectors', [AccountingController::class, 'sectors'])->middleware('checkPermission:accounting');
    Route::post('/accounting/sectorcreate', [AccountingController::class, 'sectorCreate'])->middleware('checkPermission:accounting');
    Route::get('/accounting/sectorlist', [AccountingController::class, 'sectors'])->middleware('checkPermission:accounting');
    Route::get('/accounting/sectoredit/{id}', [AccountingController::class, 'sectorEdit'])->middleware('checkPermission:accounting');
    Route::post('/accounting/sectorupdate', [AccountingController::class, 'sectorUpdate'])->middleware('checkPermission:accounting');
    Route::get('/accounting/sectordelete/{id}', [AccountingController::class, 'sectorDelete'])->middleware('checkPermission:accounting');

    Route::get('/accounting/income', [AccountingController::class, 'income'])->middleware('checkPermission:accounting');
    Route::post('/accounting/incomecreate', [AccountingController::class, 'incomeCreate'])->middleware('checkPermission:accounting');
    Route::get('/accounting/incomelist', [AccountingController::class, 'incomeList'])->middleware('checkPermission:accounting');
    Route::post('/accounting/incomelist', [AccountingController::class, 'incomeListPost'])->middleware('checkPermission:accounting');
    Route::get('/accounting/incomeedit/{id}', [AccountingController::class, 'incomeEdit'])->middleware('checkPermission:accounting');
    Route::post('/accounting/incomeupdate', [AccountingController::class, 'incomeUpdate'])->middleware('checkPermission:accounting');
    Route::get('/accounting/incomedelete/{id}', [AccountingController::class, 'incomeDelete'])->middleware('checkPermission:accounting');

    Route::get('/accounting/expence', [AccountingController::class, 'expence'])->middleware('checkPermission:accounting');
    Route::post('/accounting/expencecreate', [AccountingController::class, 'expenceCreate'])->middleware('checkPermission:accounting');
    Route::get('/accounting/expencelist', [AccountingController::class, 'expenceList'])->middleware('checkPermission:accounting');
    Route::post('/accounting/expencelist', [AccountingController::class, 'expenceListPost'])->middleware('checkPermission:accounting');
    Route::get('/accounting/expenceedit/{id}', [AccountingController::class, 'expenceEdit'])->middleware('checkPermission:accounting');
    Route::post('/accounting/expenceupdate', [AccountingController::class, 'expenceUpdate'])->middleware('checkPermission:accounting');
    Route::get('/accounting/expencedelete/{id}', [AccountingController::class, 'expenceDelete'])->middleware('checkPermission:accounting');
    Route::get('/accounting/report', [AccountingController::class, 'getReport'])->middleware('checkPermission:accounting');
    Route::get('/accounting/reportsum', [AccountingController::class, 'getReportsum'])->middleware('checkPermission:accounting');
    Route::get('/accounting/reportprint/{rtype}/{fdate}/{tdate}', [AccountingController::class, 'printReport'])->middleware('checkPermission:accounting');
    Route::get('/accounting/reportprintsum/{fdate}/{tdate}', [AccountingController::class, 'printReportsum'])->middleware('checkPermission:accounting');
});

    //Fees Related routes
Route::group(['middleware' => 'auth'], function () {

    // Route::get('/fee/vouchar','feesController@getvouchar');
    // Route::post('/fees/classreport','feesController@classreport');

});

    //Admisstion routes
Route::middleware(['auth'])->group(function () {
});

    //library routes
Route::middleware(['auth'])->group(function () {

    //check availabe book
});


//Hostal Routes
Route::middleware(['auth'])->group(function () {
});


    //barcode generate
Route::middleware(['auth'])->group(function () {
    Route::get('/barcode', [BarcodeController::class, 'index']);
    Route::post('/barcode', [BarcodeController::class, 'generate']);

    // Holiday Routes

    // Class Off Routes

    // Website Contents Routes
});


Route::middleware(['super_admin'])->group(function () {
});


Route::get('/cronjob/payment-reminder', [CronjobController::class, 'paymentReminder']);


// The doctor register. Appointments, prescriptions, lab requests and invoices all
// pick a doctor from this table, so on a clean install nothing clinical works
// until there is a row in it. /doctors/create comes before /doctors/{id} so the
// word "create" is never read as an id.
Route::middleware(['auth'])->group(function () {
    Route::get('/doctors', [DoctorController::class, 'index'])->middleware('checkPermission:doctor_view');
    Route::get('/doctors/create', [DoctorController::class, 'create'])->middleware('checkPermission:doctor_add');
    Route::post('/doctors', [DoctorController::class, 'store'])->middleware('checkPermission:doctor_add');
    Route::get('/doctors/{id}', [DoctorController::class, 'show'])->whereNumber('id')->middleware('checkPermission:doctor_view');
    Route::get('/doctors/{id}/edit', [DoctorController::class, 'edit'])->whereNumber('id')->middleware('checkPermission:doctor_update');
    Route::put('/doctors/{id}', [DoctorController::class, 'update'])->whereNumber('id')->middleware('checkPermission:doctor_update');
    Route::delete('/doctors/{id}', [DoctorController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:doctor_delete');
});


// Patient records. The front desk entry point: admissions, prescriptions, lab
// requests and invoices all key on a patient row, so this module lands first.
Route::middleware(['auth'])->group(function () {
    Route::get('/patients', [PatientController::class, 'index'])->middleware('checkPermission:patient_view');
    Route::get('/patients/create', [PatientController::class, 'create'])->middleware('checkPermission:patient_add');
    Route::post('/patients', [PatientController::class, 'store'])->middleware('checkPermission:patient_add');
    Route::get('/patients/{id}', [PatientController::class, 'show'])->whereNumber('id')->middleware('checkPermission:patient_view');
    Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->whereNumber('id')->middleware('checkPermission:patient_update');
    Route::put('/patients/{id}', [PatientController::class, 'update'])->whereNumber('id')->middleware('checkPermission:patient_update');
    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:patient_delete');
});


// Appointment booking. The day view is the front desk screen; the clash check
// lives in the controller because two clerks can book the same slot at once.
Route::middleware(['auth'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index'])->middleware('checkPermission:appointment_view');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->middleware('checkPermission:appointment_add');
    Route::post('/appointments', [AppointmentController::class, 'store'])->middleware('checkPermission:appointment_add');
    Route::get('/appointments/{id}', [AppointmentController::class, 'show'])->whereNumber('id')->middleware('checkPermission:appointment_view');
    Route::get('/appointments/{id}/edit', [AppointmentController::class, 'edit'])->whereNumber('id')->middleware('checkPermission:appointment_update');
    Route::put('/appointments/{id}', [AppointmentController::class, 'update'])->whereNumber('id')->middleware('checkPermission:appointment_update');
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:appointment_cancel');
});


// Beds and admissions. A bed is free when no admission row points at it with a
// null discharge time, so the ward board and the admission form read the same
// source rather than the bed.status column, which is only a copy.
Route::middleware(['auth'])->group(function () {
    Route::get('/beds', [BedController::class, 'index'])->middleware('checkPermission:bed_view');
    Route::get('/beds/create', [BedController::class, 'create'])->middleware('checkPermission:ward_add');
    Route::post('/beds', [BedController::class, 'store'])->middleware('checkPermission:ward_add');
    Route::get('/beds/categories', [BedController::class, 'categories'])->middleware('checkPermission:ward_view');
    Route::post('/beds/categories', [BedController::class, 'storeCategory'])->middleware('checkPermission:ward_add');
    Route::delete('/beds/categories/{id}', [BedController::class, 'destroyCategory'])->whereNumber('id')->middleware('checkPermission:ward_delete');
    Route::get('/beds/{id}/edit', [BedController::class, 'edit'])->whereNumber('id')->middleware('checkPermission:ward_update');
    Route::put('/beds/{id}', [BedController::class, 'update'])->whereNumber('id')->middleware('checkPermission:ward_update');
    Route::delete('/beds/{id}', [BedController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:ward_delete');

    Route::get('/admissions', [AdmissionController::class, 'index'])->middleware('checkPermission:admission_view');
    Route::get('/admissions/create', [AdmissionController::class, 'create'])->middleware('checkPermission:admission_add');
    Route::post('/admissions', [AdmissionController::class, 'store'])->middleware('checkPermission:admission_add');
    Route::get('/admissions/{id}', [AdmissionController::class, 'show'])->whereNumber('id')->middleware('checkPermission:admission_view');
    Route::post('/admissions/{id}/discharge', [AdmissionController::class, 'discharge'])->whereNumber('id')->middleware('checkPermission:admission_discharge');
});


// Medicine catalogue and prescriptions. Prescribed drugs are rows in
// prescription_medicine rather than a packed string in prescription.medicine,
// which is what makes "who is on this drug" answerable at all.
Route::middleware(['auth'])->group(function () {
    Route::get('/medicines', [MedicineController::class, 'index'])->middleware('checkPermission:medicine_view');
    Route::get('/medicines/create', [MedicineController::class, 'create'])->middleware('checkPermission:medicine_add');
    Route::post('/medicines', [MedicineController::class, 'store'])->middleware('checkPermission:medicine_add');
    Route::get('/medicines/categories', [MedicineController::class, 'categories'])->middleware('checkPermission:medicine_view');
    Route::post('/medicines/categories', [MedicineController::class, 'storeCategory'])->middleware('checkPermission:medicine_add');
    Route::delete('/medicines/categories/{id}', [MedicineController::class, 'destroyCategory'])->whereNumber('id')->middleware('checkPermission:medicine_delete');
    Route::get('/medicines/{id}/edit', [MedicineController::class, 'edit'])->whereNumber('id')->middleware('checkPermission:medicine_update');
    Route::put('/medicines/{id}', [MedicineController::class, 'update'])->whereNumber('id')->middleware('checkPermission:medicine_update');
    Route::delete('/medicines/{id}', [MedicineController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:medicine_delete');

    Route::get('/prescriptions', [PrescriptionController::class, 'index'])->middleware('checkPermission:prescription_view');
    Route::get('/prescriptions/create', [PrescriptionController::class, 'create'])->middleware('checkPermission:prescription_add');
    Route::post('/prescriptions', [PrescriptionController::class, 'store'])->middleware('checkPermission:prescription_add');
    Route::get('/prescriptions/{id}', [PrescriptionController::class, 'show'])->whereNumber('id')->middleware('checkPermission:prescription_view');
    Route::get('/prescriptions/{id}/edit', [PrescriptionController::class, 'edit'])->whereNumber('id')->middleware('checkPermission:prescription_update');
    Route::put('/prescriptions/{id}', [PrescriptionController::class, 'update'])->whereNumber('id')->middleware('checkPermission:prescription_update');
    Route::delete('/prescriptions/{id}', [PrescriptionController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:prescription_delete');
});


// Laboratory. Tests are rows in lab_test with their own result and status, which
// is what lets a request be part reported: bloods back, culture still growing.
// The catalogue routes come before /lab/{id} so "catalogue" is never read as an id.
Route::middleware(['auth'])->group(function () {
    Route::get('/lab', [LabController::class, 'index'])->middleware('checkPermission:lab_test_view');
    Route::get('/lab/create', [LabController::class, 'create'])->middleware('checkPermission:lab_test_add');
    Route::post('/lab', [LabController::class, 'store'])->middleware('checkPermission:lab_test_add');
    Route::get('/lab/catalogue', [LabCategoryController::class, 'index'])->middleware('checkPermission:lab_test_view');
    Route::post('/lab/catalogue', [LabCategoryController::class, 'store'])->middleware('checkPermission:lab_test_add');
    Route::put('/lab/catalogue/{id}', [LabCategoryController::class, 'update'])->whereNumber('id')->middleware('checkPermission:lab_test_update');
    Route::delete('/lab/catalogue/{id}', [LabCategoryController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:lab_test_delete');
    Route::get('/lab/{id}', [LabController::class, 'show'])->whereNumber('id')->middleware('checkPermission:lab_test_view');
    Route::post('/lab/{id}/results', [LabController::class, 'saveResults'])->whereNumber('id')->middleware('checkPermission:lab_test_update');
    Route::delete('/lab/{id}', [LabController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:lab_test_delete');
});


// Pharmacy dispensing. Line items are rows, and the stock decrement happens in
// the same transaction as the sale, so medicine.quantity follows what was handed
// over instead of being typed in by hand.
Route::middleware(['auth'])->group(function () {
    Route::get('/pharmacy', [PharmacyController::class, 'index'])->middleware('checkPermission:pharmacy_stock_view');
    Route::get('/pharmacy/create', [PharmacyController::class, 'create'])->middleware('checkPermission:pharmacy_sale');
    Route::post('/pharmacy', [PharmacyController::class, 'store'])->middleware('checkPermission:pharmacy_sale');
    Route::get('/pharmacy/{id}', [PharmacyController::class, 'show'])->whereNumber('id')->middleware('checkPermission:pharmacy_stock_view');
    Route::delete('/pharmacy/{id}', [PharmacyController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:pharmacy_sale');
});


// Patient invoicing. Charges are rows in invoice_item and payments are rows in
// invoice_payment, so a deposit now and the balance later is expressible. The
// settlement state is derived from the payment rows, never typed.
Route::middleware(['auth'])->group(function () {
    Route::get('/invoices', [InvoiceController::class, 'index'])->middleware('checkPermission:invoice_view');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->middleware('checkPermission:payment_add');
    Route::post('/invoices', [InvoiceController::class, 'store'])->middleware('checkPermission:payment_add');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->whereNumber('id')->middleware('checkPermission:invoice_view');
    Route::post('/invoices/{id}/pay', [InvoiceController::class, 'pay'])->whereNumber('id')->middleware('checkPermission:payment_add');
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:payment_delete');
});


// Billable services and the price list. Invoice lines are raised against these,
// so like the doctor register this has to be fillable before invoicing works on
// a clean install. It sits at /services rather than under /invoices so it can
// never collide with /invoices/{id}.
Route::middleware(['auth'])->group(function () {
    Route::get('/services', [PaymentCategoryController::class, 'index'])->middleware('checkPermission:service_view');
    Route::post('/services', [PaymentCategoryController::class, 'store'])->middleware('checkPermission:service_add');
    Route::put('/services/{id}', [PaymentCategoryController::class, 'update'])->whereNumber('id')->middleware('checkPermission:service_update');
    Route::delete('/services/{id}', [PaymentCategoryController::class, 'destroy'])->whereNumber('id')->middleware('checkPermission:service_delete');
});


// Where checkPermission sends a user whose role is missing the right a screen
// needs. It only requires a session, not a permission, or a denied user would be
// redirected to the page that explains the denial and denied again.
Route::middleware(['auth'])->get('/no-permission', function () {
    return view('app.nopermission');
});


// Reports over the child tables the rebuild introduced. Replacing the packed
// varchar columns with rows is what makes any of these answerable; until now
// nothing queried them. Every figure is derived at read time, so there is no
// summary table that can fall out of step.
//
// The index is behind auth alone and hides the cards a role cannot open, since
// gating it on one permission would hide the menu from someone entitled to read
// a different report on it.
Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/lab', [ReportController::class, 'lab'])
        ->middleware('checkPermission:lab_test_view');
    Route::get('/reports/drug-usage', [ReportController::class, 'drugUsage'])
        ->middleware('checkPermission:view_pharmacy_reports');
    Route::get('/reports/debtors', [ReportController::class, 'debtors'])
        ->middleware('checkPermission:view_payment_reports');
    Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])
        ->middleware('checkPermission:bed_view');
});


// Departments. The doctor form offers this list; renaming an entry carries its
// doctors with it, because the doctor record stores the department as text rather
// than a foreign key. /departments/adopt comes before /departments/{id}.
Route::middleware(['auth'])->group(function () {
    Route::get('/departments', [DepartmentController::class, 'index'])
        ->middleware('checkPermission:department_view');
    Route::post('/departments', [DepartmentController::class, 'store'])
        ->middleware('checkPermission:department_add');
    Route::post('/departments/adopt', [DepartmentController::class, 'adopt'])
        ->middleware('checkPermission:department_add');
    Route::put('/departments/{id}', [DepartmentController::class, 'update'])->whereNumber('id')
        ->middleware('checkPermission:department_update');
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->whereNumber('id')
        ->middleware('checkPermission:department_delete');
});


// Nurses, pharmacists, laboratory staff, receptionists and accountants. Five tables
// of the same shape behind one controller, driven by config/hospital_staff.php. The
// permission prefix comes from that config too: the catalogue has a dedicated Nurse
// set and a general Staff set, so the check is resolved per type rather than hard
// coded on the route.
Route::middleware(['auth'])->group(function () {
    Route::get('/staff/{type}', [StaffController::class, 'index']);
    Route::get('/staff/{type}/create', [StaffController::class, 'create']);
    Route::post('/staff/{type}', [StaffController::class, 'store']);
    Route::get('/staff/{type}/{id}/edit', [StaffController::class, 'edit'])->whereNumber('id');
    Route::put('/staff/{type}/{id}', [StaffController::class, 'update'])->whereNumber('id');
    Route::delete('/staff/{type}/{id}', [StaffController::class, 'destroy'])->whereNumber('id');
});
