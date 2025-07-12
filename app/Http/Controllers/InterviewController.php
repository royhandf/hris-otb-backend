<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InterviewController extends Controller
{
    public function index()
    {
        $interviews = \App\Models\Interview::with(['applicant', 'interviewer'])->get();

        $formatted = $interviews->map(function ($interview) {
            return [
                'interview_id'      => $interview->interview_id,
                'applicant_id'      => $interview->applicant_id,
                'applicant_name'    => optional($interview->applicant)->name,
                'interviewer_id'    => $interview->interviewer_id,
                'interviewer_name'  => optional($interview->interviewer)->name,
                'schedule'          => $interview->schedule,
                'result'            => $interview->result,
                'notes'             => $interview->notes,
                'created_at'        => $interview->created_at,
                'updated_at'        => $interview->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Interviews retrieved successfully',
            'data'    => $formatted,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'applicant_id' => 'required|uuid|exists:applicants,applicant_id',
            'interviewer_id' => 'required|uuid|exists:employees,employee_id',
            'schedule' => 'required|date',
            'result' => 'nullable|in:lolos,tidak lolos,cadangan',
            'notes' => 'nullable|string',
        ]);

        $data['interview_id'] = Str::uuid();

        $interview = Interview::create($data);
        return response()->json($interview, 201);
    }

    public function show($id)
    {
        return Interview::with(['applicant', 'interviewer'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        $data = $request->validate([
            'schedule' => 'date',
            'result' => 'nullable|in:lolos,tidak lolos,cadangan',
            'notes' => 'nullable|string',
        ]);

        $interview->update($data);
        return response()->json($interview);
    }

    public function destroy($id)
    {
        Interview::findOrFail($id)->delete();
        return response()->json(['message' => 'Interview deleted']);
    }
}