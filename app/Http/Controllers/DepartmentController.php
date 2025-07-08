<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $departments = Department::all();

            return response()->json([
                'message' => 'Daftar departemen berhasil diambil',
                'data' => $departments,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil daftar departemen',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:departments,name',
                'description' => 'nullable|string',
            ]);

            $department = Department::create($validated);

            return response()->json([
                'message' => 'Departemen berhasil dibuat',
                'data' => $department,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat departemen',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $departments = Department::find($id);

        if (!$departments) {
            return response()->json([
                'message' => 'Departemen tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Departemen berhasil ditemukan',
            'data' => $departments,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                return response()->json([
                    'message' => 'Departemen tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:departments,name,' . $id . ',department_id',
                'description' => 'nullable|string',
            ]);

            $department->update($validated);

            return response()->json([
                'message' => 'Departemen berhasil diperbarui',
                'data' => $department,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui departemen',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'message' => 'Departemen tidak ditemukan',
            ], 404);
        }

        try {
            $department->delete();

            return response()->json([
                'message' => 'Departemen berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus departemen',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}