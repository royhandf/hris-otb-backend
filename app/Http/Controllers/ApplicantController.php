<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicantController extends Controller
{
    public function index()
    {
        return response()->json(
            Applicant::with('jobVacancy')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:applicants,email',
            'phone' => 'required|string|max:20',
            'applied_position_id' => 'required|uuid|exists:job_vacancies,vacancy_id',
            'cv_file' => 'nullable|string',
            'status' => 'nullable|in:applied,interview,hired,rejected',
        ]);

        $applicant = Applicant::create([
            'applicant_id' => Str::uuid(),
            ...$validated,
        ]);

        return response()->json($applicant, 201);
    }

    public function show($id)
    {
        $applicant = Applicant::with('jobVacancy')->findOrFail($id);
        return response()->json($applicant);
    }

    public function update(Request $request, $id)
    {
        $applicant = Applicant::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:applicants,email,' . $id . ',applicant_id',
            'phone' => 'sometimes|string|max:20',
            'applied_position_id' => 'sometimes|uuid|exists:job_vacancies,vacancy_id',
            'cv_file' => 'nullable|string',
            'status' => 'sometimes|in:applied,interview,hired,rejected',
        ]);

        $applicant->update($validated);

        return response()->json($applicant);
    }

    public function destroy($id)
    {
        $applicant = Applicant::findOrFail($id);
        $applicant->delete();

        return response()->json(null, 204);
    }
}