<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;

// Route untuk tes koneksi API
Route::get('/ping', fn() => response()->json(['message' => 'pong']));

// Kelompokkan ke dalam versi API v1 (opsional, bisa dihilangkan jika tidak ingin versi)
Route::prefix('v1')->group(function () {
    // 📌 Departments
    Route::apiResource('departments', DepartmentController::class);

    // 📌 Positions
    Route::get('/positions', [PositionController::class, 'index']);
    Route::post('/positions', [PositionController::class, 'store']);
    Route::get('/positions/{id}', [PositionController::class, 'show']);
    Route::put('/positions/{id}', [PositionController::class, 'update']);
    Route::delete('/positions/{id}', [PositionController::class, 'destroy']);

    // 📌 Job Vacancies (menggunakan resource route)
    Route::apiResource('job-vacancies', JobVacancyController::class);

    // 📌 Applicants (menggunakan resource route)
    Route::apiResource('applicants', ApplicantController::class);

    // 📌 Tambahan: Import CSV/XLSX untuk Applicants
    Route::post('/applicants/import', [ApplicantController::class, 'import']);

    // 📌 Login/Authentication nanti (opsional, jika butuh auth)
    // Route::post('/login', [AuthController::class, 'login']);
});
