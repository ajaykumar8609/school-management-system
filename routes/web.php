<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AcademicController;
use App\Http\Controllers\FeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/setup-admin', [SetupController::class, 'admin']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/sections-by-class/{classId}', [StudentController::class, 'sectionsByClass'])->name('sections.by-class');
    Route::resource('students', StudentController::class);
    Route::get('/students/{student}/academic', [StudentController::class, 'academic'])->name('students.academic');
    Route::get('/students/{student}/fee-report', [StudentController::class, 'feeReport'])->name('students.fee-report');
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/submit', [AttendanceController::class, 'submit'])->name('submit');
    });
    Route::prefix('academic')->name('academic.')->group(function () {
        Route::get('/', [AcademicController::class, 'index'])->name('index');
        Route::post('/save-marks', [AcademicController::class, 'saveMarks'])->name('save-marks');
    });
    Route::prefix('fee')->name('fee.')->group(function () {
        Route::get('/', [FeeController::class, 'index'])->name('index');
        Route::post('/add-payment', [FeeController::class, 'addPayment'])->name('add-payment');
        Route::delete('/payment/{payment}', [FeeController::class, 'deletePayment'])->name('delete-payment');
    });
});
