<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $employees = Employee::with([
                'department',
                'position',
            ])->latest()->paginate(10);

            return response()->json([
                'message' => 'Daftar karyawan berhasil diambil',
                'data' => $employees,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil daftar karyawan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:laki-laki,perempuan',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'bank_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:255',
                'join_date' => 'required|date',
                'department_id' => 'nullable|uuid|exists:departments,department_id',
                'position_id' => 'nullable|uuid|exists:positions,position_id',
                'status' => 'nullable|in:tetap,kontrak,probation,freelance,magang',
            ]);

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('employee_photos', 'public');
                unset($validated['photo']);
                $validated['photo_path'] = $path;
            }

            $employee = Employee::create($validated);

            return response()->json([
                'message' => 'Karyawan berhasil dibuat',
                'data' => $employee,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat data karyawan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with([
            'department',
            'position',
        ])->find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Data karyawan berhasil ditemukan',
            'data' => $employee,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $employee = Employee::find($id);

            if (!$employee) {
                return response()->json([
                    'message' => 'Data karyawan tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:laki-laki,perempuan',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'bank_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:255',
                'join_date' => 'sometimes|required|date',
                'department_id' => 'nullable|uuid|exists:departments,department_id',
                'position_id' => 'nullable|uuid|exists:positions,position_id',
                'status' => 'nullable|in:tetap,kontrak,probation,freelance,magang',
            ]);

            if ($request->hasFile('photo')) {
                if ($employee->photo_path) {
                    Storage::disk('public')->delete($employee->photo_path);
                }
                $path = $request->file('photo')->store('employee_photos', 'public');
                unset($validated['photo']);
                $validated['photo_path'] = $path;
            }

            $employee->update($validated);

            $employee->load([
                'department',
                'position',
            ]);

            return response()->json([
                'message' => 'Data karyawan berhasil diperbarui',
                'data' => $employee,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui data karyawan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }

        try {
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }

            if ($employee->user) {
                $employee->user()->delete();
            }

            $employee->delete();

            return response()->json([
                'message' => 'Berhasil menghapus data karyawan',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data karyawan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}