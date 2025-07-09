<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\InterviewController;


// Route untuk tes koneksi API
Route::get('/ping', fn() => response()->json(['message' => 'pong']));


Route::prefix('v1')->group(function () {
    // Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // ROUTE YANG PERLU LOGIN
    Route::middleware('auth:sanctum')->group(function () {
        // ROUTE KHUSUS HRD
        Route::middleware('role:hr')->group(function () {
            Route::apiResource('employees', EmployeeController::class);
            Route::apiResource('departments', DepartmentController::class);
            Route::apiResource('positions', PositionController::class);
        });

        // ROUTE KHUSUS MANAJER
        Route::middleware('role:manager')->group(function () {
            
        });

        // ROUTE KHUSUS ADMIN
        Route::middleware('role:admin')->group(function () {
            
        });

        // ROUTE KHUSUS KARYAWAN
        Route::middleware('role:employee')->group(function () {
            
        });

        // Interviews
        Route::apiResource('interviews', InterviewController::class);
        Route::apiResource('attendances', AttendanceController::class);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // 📌 Job Vacancies (menggunakan resource route)
    Route::apiResource('job-vacancies', JobVacancyController::class);

    // 📌 Applicants (menggunakan resource route)
    Route::apiResource('applicants', ApplicantController::class);

    // 📌 Tambahan: Import CSV/XLSX untuk Applicants
    Route::post('/applicants/import', [ApplicantController::class, 'import']);
});