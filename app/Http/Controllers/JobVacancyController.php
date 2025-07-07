<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class JobVacancyController extends Controller
{
    public function index()
    {
        $jobs = \App\Models\JobVacancy::with('position')->get();

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
            'message' => 'Job vacancies retrieved successfully',
            'data'    => $formatted,
        ]);
    }

    // POST /api/job-vacancies
    public function store(Request $request)
    {
        $data = $request->validate([
            'position_id'  => 'required|uuid|exists:positions,position_id',
            'description'  => 'required|string',
            'requirements' => 'required|string',
            'deadline'     => 'required|date',
        ]);

        $job = JobVacancy::create([
            'vacancy_id'   => Str::uuid(),
            'position_id'  => $data['position_id'],
            'description'  => $data['description'],
            'requirements' => $data['requirements'],
            'deadline'     => $data['deadline'],
        ]);

        // Muat relasi 'position' agar bisa ditampilkan
        $job->load(['position' => function ($query) {
            $query->select('position_id', 'name', 'salary', 'level', 'created_at', 'updated_at');
        }]);

        return response()->json([
            'vacancy_id'   => $job->vacancy_id,
            'position_id'  => $job->position_id,
            'description'  => $job->description,
            'requirements' => $job->requirements,
            'deadline'     => $job->deadline,
            'created_at'   => $job->created_at,
            'updated_at'   => $job->updated_at,
            'position'     => [
                'name'       => $job->position->name,
                'salary'     => $job->position->salary,
                'level'      => $job->position->level,
                'created_at' => $job->position->created_at,
                'updated_at' => $job->position->updated_at,
            ],
        ], 201);
    }

    // GET /api/job-vacancies/{id}
    public function show($id)
    {
        $job = JobVacancy::with(['position' => function ($q) {
            $q->select('position_id', 'name', 'salary', 'level');
        }])->findOrFail($id);

        return response()->json([
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
        ]);
    }

    // PUT /api/job-vacancies/{id}
    public function update(Request $request, $id)
    {
        $job = JobVacancy::findOrFail($id);

        $data = $request->validate([
            'position_id'   => 'sometimes|uuid|exists:positions,position_id',
            'description'   => 'nullable|string',
            'requirements'  => 'nullable|string',
            'deadline'      => 'nullable|date',
        ]);

        $job->update($data);

        return response()->json($job);
    }

    // DELETE /api/job-vacancies/{id}
    public function destroy($id)
    {
        $job = JobVacancy::findOrFail($id);
        $job->delete();

        return response()->json(['message' => 'Deleted']);
    }
}