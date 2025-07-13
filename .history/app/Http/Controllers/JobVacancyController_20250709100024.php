<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;

class JobVacancyController extends Controller
{
    public function index()
    {
        try {
            $jobs = JobVacancy::with('position')->get();

            $formatted = $jobs->map(function ($job) {
                return [
                    'vacancy_id'    => $job->vacancy_id,
                    'position_id'   => $job->position_id,
                    'position_name' => $job->position->name ?? null,
                    'salary'        => $job->position->salary ?? null,
                    'level'         => $job->position->level ?? null,
                    'description'   => $job->description,
                    'requirements'  => $job->requirements,
                    'deadline'      => $job->deadline,
                    'created_at'    => $job->created_at,
                    'updated_at'    => $job->updated_at,
                ];
            });

            return response()->json([
                'message' => 'Lowongan pekerjaan berhasil diambil',
                'data'    => $formatted,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil daftar lowongan pekerjaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'position_id'  => 'required|uuid|exists:positions,position_id',
                'description'  => 'required|string',
                'requirements' => 'required|string',
                'deadline'     => 'required|date',
            ]);

            $job = JobVacancy::create($validated);
            $job->load('position:position_id,name,salary,level');

            return response()->json([
                'message' => 'Lowongan pekerjaan berhasil dibuat',
                'data' => $job,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat lowongan pekerjaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/job-vacancies/{id}
    public function show($id)
    {
        try {
            $job = JobVacancy::with('position:position_id,name,salary,level')->findOrFail($id);

            if (!$job) {
                return response()->json([
                    'message' => 'Lowongan pekerjaan tidak ditemukan',
                ], 404);
            }
            return response()->json([
                'message' => 'Lowongan pekerjaan berhasil ditemukan',
                'data' => $job,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil lowongan pekerjaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // PUT /api/job-vacancies/{id}
    public function update(Request $request, $id)
    {
        try {
            $job = JobVacancy::find($id);

            if (!$job) {
                return response()->json([
                    'message' => 'Lowongan pekerjaan tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'position_id'  => 'sometimes|required|uuid|exists:positions,position_id',
                'description'  => 'sometimes|required|string',
                'requirements' => 'sometimes|required|string',
                'deadline'     => 'sometimes|required|date',
            ]);

            $job->update($validated);
            $job->load('position:position_id,name,salary,level');

            return response()->json([
                'message' => 'Lowongan pekerjaan berhasil diperbarui',
                'data' => $job,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui lowongan pekerjaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // DELETE /api/job-vacancies/{id}
    public function destroy($id)
    {
        try {
            $job = JobVacancy::find($id);

            if (!$job) {
                return response()->json([
                    'message' => 'Lowongan pekerjaan tidak ditemukan',
                ], 404);
            }

            $job->delete();

            return response()->json([
                'message' => 'Lowongan pekerjaan berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus lowongan pekerjaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}