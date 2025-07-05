<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobVacancyController extends Controller
{
    // GET /api/job-vacancies
    public function index()
    {
        return JobVacancy::with('position')->get();
    }

    // POST /api/job-vacancies
    public function store(Request $request)
    {
        $data = $request->validate([
            'position_title' => 'required|string',
            'title'          => 'required|string|max:150',
            'description'    => 'required|string',
            'requirements'   => 'required|string',
            'deadline'       => 'required|date',
        ]);

        $position = Position::where('name', $data['position_title'])->first();

        if (!$position) {
            return response()->json([
                'message' => 'Position not found with the given title.'
            ], 404);
        }

        $job = JobVacancy::create([
            'vacancy_id'  => Str::uuid(),
            'position_id' => $position->position_id,
            'title'       => $data['title'],
            'description' => $data['description'],
            'requirements'=> $data['requirements'],
            'deadline'    => $data['deadline'],
        ]);

        return response()->json($job, 201);
    }

    // GET /api/job-vacancies/{id}
    public function show($id)
    {
        return JobVacancy::with('position')->findOrFail($id);
    }

    // PUT /api/job-vacancies/{id}
    public function update(Request $request, $id)
    {
        $job = JobVacancy::findOrFail($id);

        $data = $request->validate([
            'position_title' => 'sometimes|string',
            'title'          => 'nullable|string|max:150',
            'description'    => 'nullable|string',
            'requirements'   => 'nullable|string',
            'deadline'       => 'nullable|date',
        ]);

        // Jika position_title disertakan, cari position_id-nya
        if (isset($data['position_title'])) {
            $position = Position::where('name', $data['position_title'])->first();

            if (!$position) {
                return response()->json([
                    'message' => 'Position not found with the given title.'
                ], 404);
            }

            $data['position_id'] = $position->position_id;
            unset($data['position_title']);
        }

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