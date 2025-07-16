<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Cek apakah user HR
        if ($user->role === 'hr') {
            $attendances = Attendance::with('employee')->get();
        }
        // Jika user karyawan, ambil hanya miliknya
        elseif ($user->role === 'karyawan') {
            if (!$user->employee) {
                return response()->json([
                    'message' => 'Data karyawan tidak ditemukan untuk pengguna ini.',
                ], 404);
            }

            $attendances = Attendance::with('employee')
                ->where('employee_id', $user->employee->employee_id)
                ->get();
        }
        else {
            return response()->json([
                'message' => 'Dilarang. Anda tidak memiliki izin untuk mengakses resource ini.',
                'user_role' => $user->role,
            ], 403);
        }

        $formatted = $attendances->map(function ($item) {
            return [
                'attendance_id' => $item->attendance_id,
                'employee_id'   => $item->employee_id,
                'date'          => $item->date,
                'check_in'      => $item->check_in,
                'check_out'     => $item->check_out,
                'status'        => $item->status,
                'created_at'    => $item->created_at,
                'updated_at'    => $item->updated_at,
                'employee'      => [
                    'name'                => $item->employee->name,
                    'phone'               => $item->employee->phone,
                    'address'             => $item->employee->address,
                    'birth_date'          => $item->employee->birth_date,
                    'gender'              => $item->employee->gender,
                    'photo_path'          => $item->employee->photo_path,
                    'bank_name'           => $item->employee->bank_name,
                    'bank_account_number' => $item->employee->bank_account_number,
                    'join_date'           => $item->employee->join_date,
                    'department_id'       => $item->employee->department_id,
                    'position_id'         => $item->employee->position_id,
                    'status'              => $item->employee->status,
                    'created_at'          => $item->employee->created_at,
                    'updated_at'          => $item->employee->updated_at,
                ]
            ];
        });

        return response()->json([
            'message' => 'Absensi kehadiran berhasil dibuat.',
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
            'attendance_id' => Str::uuid(),
            'employee_id'   => $data['employee_id'],
            'date'          => $data['date'],
            'check_in'      => $data['check_in'],
            'check_out'     => $data['check_out'],
            'status'        => $data['status'],
        ]);

        // Load relasi employee, dan pilih field yang diinginkan saja
        $attendance->load(['employee' => function ($query) {
            $query->select(
                'employee_id', // tetap harus di-select untuk relasi
                'name', 'phone', 'address', 'birth_date',
                'gender', 'photo_path', 'bank_name', 'bank_account_number',
                'join_date', 'department_id', 'position_id', 'status',
                'created_at', 'updated_at'
            );
        }]);

        return response()->json([
            'attendance_id' => $attendance->attendance_id,
            'employee_id'   => $attendance->employee_id,
            'date'          => $attendance->date,
            'check_in'      => $attendance->check_in,
            'check_out'     => $attendance->check_out,
            'status'        => $attendance->status,
            'created_at'    => $attendance->created_at,
            'updated_at'    => $attendance->updated_at,
            'employee'      => [
                'name'                => $attendance->employee->name,
                'phone'               => $attendance->employee->phone,
                'address'             => $attendance->employee->address,
                'birth_date'          => $attendance->employee->birth_date,
                'gender'              => $attendance->employee->gender,
                'photo_path'          => $attendance->employee->photo_path,
                'bank_name'           => $attendance->employee->bank_name,
                'bank_account_number' => $attendance->employee->bank_account_number,
                'join_date'           => $attendance->employee->join_date,
                'department_id'       => $attendance->employee->department_id,
                'position_id'         => $attendance->employee->position_id,
                'status'              => $attendance->employee->status,
                'created_at'          => $attendance->employee->created_at,
                'updated_at'          => $attendance->employee->updated_at,
            ],
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
            'check_in' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s',
            'status' => 'nullable|in:hadir,izin,sakit,cuti,alpha',
        ]);

        $attendance->update($data);

        $attendance->load('employee:employee_id,name');

        return response()->json([
            'message' => 'Absensi kehadiran berhasil diperbaiki',
            'data' => $attendance
        ]);
    }

    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return response()->json(['message' => 'Absensi dihapus']);
    }
}
