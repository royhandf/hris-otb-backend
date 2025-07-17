<?php

use App\Http\Controllers\ContractController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\UserController;

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
            Route::apiResource('interviews', InterviewController::class);
            Route::apiResource('job-vacancies', JobVacancyController::class);
            Route::apiResource('contracts', ContractController::class);
        });

        // ROUTE KHUSUS MANAJER
        Route::middleware('role:manager')->group(function () {
            Route::get('/interviews', [InterviewController::class, 'index']);
        });

        // ROUTE KHUSUS ADMIN
        Route::middleware('role:admin')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'createUserForEmployee']);
            Route::post('/users/{userId}/update-role', [UserController::class, 'updateRole']);
            Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword']);
            Route::get('/employees-without-account', [UserController::class, 'getEmployeesWithoutAccount']);
        });

        // ROUTE KHUSUS KARYAWAN
        Route::middleware('role:karyawan')->group(function () {
            Route::apiResource('leave-requests', LeaveRequestController::class);
            Route::post('/attendances', [AttendanceController::class, 'store']);
        });

        Route::get('/profile', [UserController::class, 'showProfile']);
        Route::post('/profile', [UserController::class, 'updateProfile']);

        // Interviews
        Route::apiResource('interviews', InterviewController::class);

        Route::post('attendance', [AttendanceController::class, 'store']);
        Route::get('/attendances', [AttendanceController::class, 'index']);

        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // 📌 Job Vacancies (menggunakan resource route)
    Route::get('/job-vacancies', [JobVacancyController::class, 'index']);

    // 📌 Applicants (menggunakan resource route)
    Route::apiResource('applicants', ApplicantController::class);

    // 📌 Tambahan: Import CSV/XLSX untuk Applicants
    Route::post('/applicants/import', [ApplicantController::class, 'import']);
});
