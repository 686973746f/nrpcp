<?php

use Carbon\Carbon;
use App\Models\VaccinationSite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\VaccinationController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\WalkInRegistrationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

Route::group(['middleware' => ['auth', 'verified', 'IsStaff']], function () {
    Route::get('/patient', [PatientController::class, 'index'])->name('patient_index');
    Route::get('/patient/create', [PatientController::class, 'create'])->name('patient_create');
    Route::post('/patient/create', [PatientController::class, 'store'])->name('patient_store');
    Route::get('/patient/{id}/edit', [PatientController::class, 'edit'])->name('patient_edit');
    Route::post('/patient/{id}/edit', [PatientController::class, 'update'])->name('patient_update');
    Route::get('/patient/ajaxList', [PatientController::class, 'ajaxList'])->name('patient_ajaxlist');
    
    Route::get('/patient/bakuna_records/{id}', [PatientController::class, 'patient_viewbakunarecords'])->name('patient_viewbakunarecords');
    Route::post('/patient/quickscan', [VaccinationController::class, 'qr_quicksearch'])->name('qr_quicksearch');

    Route::get('/vaccination_site', [AdminController::class, 'vaccinationsite_index'])->name('vaccinationsite_index');
    Route::post('/vaccination_site', [AdminController::class, 'vaccinationsite_store'])->name('vaccinationsite_store');

    Route::get('/vaccine_brand', [AdminController::class, 'vaccinebrand_index'])->name('vaccinebrand_index');
    Route::post('/vaccine_brand', [AdminController::class, 'vaccinebrand_store'])->name('vaccinebrand_store');

    Route::post('/encode_search', [VaccinationController::class, 'search_init'])->name('search_init');
    Route::get('/encode/existing/{id}', [VaccinationController::class, 'encode_existing'])->name('encode_existing');
    
    Route::get('/encode/new/{id}', [VaccinationController::class, 'create_new'])->name('encode_create_new');
    Route::post('/encode/new/{id}', [VaccinationController::class, 'create_store'])->name('encode_store');

    Route::get('/encode/edit/{br_id}', [VaccinationController::class, 'encode_edit'])->name('encode_edit');
    Route::post('/encode/edit/{br_id}', [VaccinationController::class, 'encode_update'])->name('encode_update');

    Route::get('/encode/edit/{br_id}/override_schedule', [VaccinationController::class, 'override_schedule'])->name('override_schedule');
    Route::post('/encode/edit/{br_id}/override_schedule', [VaccinationController::class, 'override_schedule_process'])->name('override_schedule_process');

    Route::get('/encode/process_vaccination/{br_id}/{dose}', [VaccinationController::class, 'encode_process'])->name('encode_process');

    Route::get('/encode/rebakuna/{patient_id}', [VaccinationController::class, 'bakuna_again'])->name('bakuna_again');

    Route::get('/report/linelist', [ReportController::class, 'linelist_index'])->name('report_linelist_index');
    Route::post('/report/export1', [ReportController::class, 'export1'])->name('report_export1');

    Route::post('/settings/save', [UserSettingsController::class, 'save_settings'])->name('save_settings');
});

Route::group(['middleware' => ['guest']], function() {
    Route::get('/walkin', [WalkInRegistrationController::class, 'walkin_part1'])->name('walkin_part1');
    Route::get('/wakin/register', [WalkInRegistrationController::class, 'walkin_part2'])->name('walkin_part2');
    Route::post('/wakin/register', [WalkInRegistrationController::class, 'walkin_part3'])->name('walkin_part3');
});

Route::group(['middleware' => ['auth', 'verified', 'IsStaff']], function () {
    Route::get('/home', function() {
        $vslist = VaccinationSite::where('enabled', 1)->orderBy('id', 'ASC')->get();
        return view('home', [
            'vslist' => $vslist,
        ]);
    })->name('home');
});

Route::get('/', function () {
    if(auth()->check()) {
        $vslist = VaccinationSite::where('enabled', 1)->orderBy('id', 'ASC')->get();
        return view('home', [
            'vslist' => $vslist,
        ]);
    }
    else {
        return view('auth.login');
    }
})->name('main');