<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $employeeId = auth()->user()->employee_id;

            $history = LeaveRequest::where('employee_id', $employeeId)
                ->latest()
                ->get();

            return response()->json([
                'message' => 'Riwayat pengajuan cuti berhasil diambil',
                'data' => $history,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil riwayat pengajuan cuti',
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
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after_or_equal:start_date',
                'reason' => 'required|string|max:255',
            ]);

            $validated['employee_id'] = auth()->user()->employee_id;

            $leaveRequest = LeaveRequest::create($validated);
            return response()->json([
                'message' => 'Pengajuan cuti berhasil dibuat',
                'data' => $leaveRequest,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat pengajuan cuti',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            if (!$leaveRequest) {
                return response()->json(['message' => 'Pengajuan cuti tidak ditemukan.'], 404);
            }

            if ($leaveRequest->employee_id !== auth()->user()->employee_id) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke pengajuan ini.'], 403);
            }

            if ($leaveRequest->status !== 'pending') {
                return response()->json(['message' => 'Pengajuan ini tidak dapat diubah karena sudah diproses.'], 400);
            }

            $validated = $request->validate([
                'name'       => 'required|string|max:100',
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after_or_equal:start_date',
                'reason'     => 'required|string|max:255',
            ]);

            $leaveRequest->update($validated);

            return response()->json([
                'message' => 'Pengajuan cuti berhasil diperbarui.',
                'data' => $leaveRequest
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui pengajuan cuti',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            if (!$leaveRequest) {
                return response()->json(['message' => 'Pengajuan cuti tidak ditemukan.'], 404);
            }

            if ($leaveRequest->employee_id !== auth()->user()->employee_id) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke pengajuan ini.'], 403);
            }

            if ($leaveRequest->status !== 'pending') {
                return response()->json(['message' => 'Pengajuan ini tidak dapat dihapus karena sudah diproses.'], 400);
            }

            $leaveRequest->delete();

            return response()->json(['message' => 'Pengajuan cuti berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus pengajuan cuti',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}