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
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\LabCategoryController;





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

// Students Routes
Route::middleware(['auth', 'activity'])->group(function() {

    Route::middleware('checkPermission:student_add')->group(function() {
    });

    Route::middleware('checkPermission:student_view')->group(function() {
    });

    Route::middleware('checkPermission:student_info')->group(function() {
    });

    Route::middleware('checkPermission:student_student_portal_access')->group(function() {
    });

    Route::middleware('checkPermission:student_update')->group(function() {
    });

    Route::middleware('checkPermission:student_delete')->group(function() {
    });

    Route::middleware('checkPermission:student_student_bulk_add')->group(function() {
    });

});


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


// Patient records. The front desk entry point: admissions, prescriptions, lab
// requests and invoices all key on a patient row, so this module lands first.
Route::middleware(['auth'])->group(function () {
    Route::get('/patients', [PatientController::class, 'index']);
    Route::get('/patients/create', [PatientController::class, 'create']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::get('/patients/{id}', [PatientController::class, 'show'])->whereNumber('id');
    Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->whereNumber('id');
    Route::put('/patients/{id}', [PatientController::class, 'update'])->whereNumber('id');
    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->whereNumber('id');
});


// Appointment booking. The day view is the front desk screen; the clash check
// lives in the controller because two clerks can book the same slot at once.
Route::middleware(['auth'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/create', [AppointmentController::class, 'create']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show'])->whereNumber('id');
    Route::get('/appointments/{id}/edit', [AppointmentController::class, 'edit'])->whereNumber('id');
    Route::put('/appointments/{id}', [AppointmentController::class, 'update'])->whereNumber('id');
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->whereNumber('id');
});


// Beds and admissions. A bed is free when no admission row points at it with a
// null discharge time, so the ward board and the admission form read the same
// source rather than the bed.status column, which is only a copy.
Route::middleware(['auth'])->group(function () {
    Route::get('/beds', [BedController::class, 'index']);
    Route::get('/beds/create', [BedController::class, 'create']);
    Route::post('/beds', [BedController::class, 'store']);
    Route::get('/beds/categories', [BedController::class, 'categories']);
    Route::post('/beds/categories', [BedController::class, 'storeCategory']);
    Route::delete('/beds/categories/{id}', [BedController::class, 'destroyCategory'])->whereNumber('id');
    Route::get('/beds/{id}/edit', [BedController::class, 'edit'])->whereNumber('id');
    Route::put('/beds/{id}', [BedController::class, 'update'])->whereNumber('id');
    Route::delete('/beds/{id}', [BedController::class, 'destroy'])->whereNumber('id');

    Route::get('/admissions', [AdmissionController::class, 'index']);
    Route::get('/admissions/create', [AdmissionController::class, 'create']);
    Route::post('/admissions', [AdmissionController::class, 'store']);
    Route::get('/admissions/{id}', [AdmissionController::class, 'show'])->whereNumber('id');
    Route::post('/admissions/{id}/discharge', [AdmissionController::class, 'discharge'])->whereNumber('id');
});


// Medicine catalogue and prescriptions. Prescribed drugs are rows in
// prescription_medicine rather than a packed string in prescription.medicine,
// which is what makes "who is on this drug" answerable at all.
Route::middleware(['auth'])->group(function () {
    Route::get('/medicines', [MedicineController::class, 'index']);
    Route::get('/medicines/create', [MedicineController::class, 'create']);
    Route::post('/medicines', [MedicineController::class, 'store']);
    Route::get('/medicines/categories', [MedicineController::class, 'categories']);
    Route::post('/medicines/categories', [MedicineController::class, 'storeCategory']);
    Route::delete('/medicines/categories/{id}', [MedicineController::class, 'destroyCategory'])->whereNumber('id');
    Route::get('/medicines/{id}/edit', [MedicineController::class, 'edit'])->whereNumber('id');
    Route::put('/medicines/{id}', [MedicineController::class, 'update'])->whereNumber('id');
    Route::delete('/medicines/{id}', [MedicineController::class, 'destroy'])->whereNumber('id');

    Route::get('/prescriptions', [PrescriptionController::class, 'index']);
    Route::get('/prescriptions/create', [PrescriptionController::class, 'create']);
    Route::post('/prescriptions', [PrescriptionController::class, 'store']);
    Route::get('/prescriptions/{id}', [PrescriptionController::class, 'show'])->whereNumber('id');
    Route::get('/prescriptions/{id}/edit', [PrescriptionController::class, 'edit'])->whereNumber('id');
    Route::put('/prescriptions/{id}', [PrescriptionController::class, 'update'])->whereNumber('id');
    Route::delete('/prescriptions/{id}', [PrescriptionController::class, 'destroy'])->whereNumber('id');
});


// Laboratory. Tests are rows in lab_test with their own result and status, which
// is what lets a request be part reported: bloods back, culture still growing.
// The catalogue routes come before /lab/{id} so "catalogue" is never read as an id.
Route::middleware(['auth'])->group(function () {
    Route::get('/lab', [LabController::class, 'index']);
    Route::get('/lab/create', [LabController::class, 'create']);
    Route::post('/lab', [LabController::class, 'store']);
    Route::get('/lab/catalogue', [LabCategoryController::class, 'index']);
    Route::post('/lab/catalogue', [LabCategoryController::class, 'store']);
    Route::put('/lab/catalogue/{id}', [LabCategoryController::class, 'update'])->whereNumber('id');
    Route::delete('/lab/catalogue/{id}', [LabCategoryController::class, 'destroy'])->whereNumber('id');
    Route::get('/lab/{id}', [LabController::class, 'show'])->whereNumber('id');
    Route::post('/lab/{id}/results', [LabController::class, 'saveResults'])->whereNumber('id');
    Route::delete('/lab/{id}', [LabController::class, 'destroy'])->whereNumber('id');
});
