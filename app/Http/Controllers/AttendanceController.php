<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('employee')->get();

        $formatted = $attendances->map(function ($item) {
            return [
                'attendance_id' => $item->attendance_id,
                'date'          => $item->date,
                'check_in'      => $item->check_in,
                'check_out'     => $item->check_out,
                'status'        => $item->status,
                'created_at'    => $item->created_at,
                'updated_at'    => $item->updated_at,
                'employee'      => [
                    'employee_id'        => $item->employee->employee_id,
                    'name'                => $item->employee->name,
                ]
            ];
        });

        return response()->json([
            'message' => 'All attendance records retrieved',
            'data'    => $formatted,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|uuid|exists:employees,employee_id',
            'date'        => 'required|date',
            'check_in'    => 'required|date_format:Y-m-d H:i:s',
            'check_out'   => 'nullable|date_format:Y-m-d H:i:s|after:check_in',
            'status'      => 'required|in:hadir,izin,sakit,cuti,alpha',
        ]);

        $attendance = Attendance::create([
            'attendance_id' => \Str::uuid(),
            'employee_id'   => $data['employee_id'],
            'date'          => $data['date'],
            'check_in'      => $data['check_in'],
            'check_out'     => $data['check_out'],
            'status'        => $data['status'],
        ]);

        // Load relasi employee, dan pilih field yang diinginkan saja
        $attendance->load('employee:employee_id,name');

        return response()->json([
            'message'       => 'Attendance created successfully',
            'data'          => [
                'attendance_id' => $attendance->attendance_id,
                'date'          => $attendance->date,
                'check_in'      => $attendance->check_in,
                'check_out'     => $attendance->check_out,
                'status'        => $attendance->status,
                'created_at'    => $attendance->created_at,
                'updated_at'    => $attendance->updated_at,
                'employee'      => [
                    'employee_id' => $attendance->employee->employee_id,
                    'name'        => $attendance->employee->name,
                ]
            ]
        ], 201);
    }
    public function show($id)
    {
        $attendance = Attendance::with('employee')->findOrFail($id);
        return response()->json($attendance);
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $data = $request->validate([
            'check_in'  => 'nullable|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s',
            'status'    => 'nullable|in:hadir,izin,sakit,cuti,alpha',
        ]);

        $attendance->update($data);

        $attendance->load('employee:employee_id,name');

        return response()->json([
            'message' => 'Attendance updated successfully',
            'data'    => [
                'attendance_id' => $attendance->attendance_id,
                'employee_id'   => $attendance->employee_id,
                'date'          => $attendance->date,
                'check_in'      => $attendance->check_in,
                'check_out'     => $attendance->check_out,
                'status'        => $attendance->status,
                'created_at'    => $attendance->created_at,
                'updated_at'    => $attendance->updated_at,
                'employee'      => [
                    'employee_id' => $attendance->employee->employee_id,
                    'name'        => $attendance->employee->name,
                ]
            ]
        ]);
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json(['message' => 'Attendance deleted successfully']);
    }
}
