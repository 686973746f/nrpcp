<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PatientController;

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

Route::group(['middleware' => ['IsStaff']], function () {
    Route::get('/patient', [PatientController::class, 'index'])->name('patient_index');
    Route::get('/patient/create', [PatientController::class, 'create'])->name('patient_create');
    Route::post('/patient/create', [PatientController::class, 'store'])->name('patient_store');
    Route::get('/patient/{id}/edit', [PatientController::class, 'edit'])->name('patient_edit');
    Route::post('/patient/{id}/edit', [PatientController::class, 'update'])->name('patient_update');
    Route::get('/patient/ajaxList', [PatientController::class, 'ajaxList'])->name('patient_ajaxlist');

    Route::get('/vaccination_site', [AdminController::class, 'vaccinationsite_index'])->name('vaccinationsite_index');
    Route::post('/vaccination_site', [AdminController::class, 'vaccinationsite_store'])->name('vaccinationsite_store');

    Route::get('/encode?id={id}', [AdminController::class, 'encodevaccination_create'])->name('encodevaccination_create');
});

Route::get('/home', function() {
    return view('home');
})->name('home');

Route::get('/', function () {
    if(auth()->check()) {
        return view('home');
    }
    else {
        return view('auth.login');
    }
})->name('main');