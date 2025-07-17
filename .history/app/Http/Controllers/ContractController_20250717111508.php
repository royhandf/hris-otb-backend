<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ContractController extends Controller
{
    public function index()
    {
        $data = Contract::all();
        return response()->json([
            'status' => true,
            'message' => 'Data kontrak berhasil diambil.',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|string',
                'type'        => 'required|in:pkwt,pkwtt,magang',
                'start_date'  => 'required|date',
                'end_date'    => 'required|date|after_or_equal:start_date',
                'status'      => 'required|in:aktif,berakhir,diperpanjang',
                'description' => 'nullable|string',
            ]);

            $contract = Contract::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Kontrak berhasil ditambahkan.',
                'data' => $contract
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan kontrak.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $contract = Contract::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Data kontrak berhasil ditemukan.',
                'data' => $contract
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data kontrak tidak ditemukan.'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $contract = Contract::findOrFail($id);

            $validated = $request->validate([
                'employee_id' => 'sometimes|required|string',
                'type'        => 'sometimes|required|in:pkwt,pkwtt,magang',
                'start_date'  => 'sometimes|required|date',
                'end_date'    => 'sometimes|required|date|after_or_equal:start_date',
                'status'      => 'sometimes|required|in:aktif,berakhir,diperpanjang',
                'description' => 'nullable|string',
            ]);

            $contract->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Kontrak berhasil diperbarui.',
                'data' => $contract
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data kontrak tidak ditemukan.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui kontrak.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $contract = Contract::findOrFail($id);
            $contract->delete();

            return response()->json([
                'status' => true,
                'message' => 'Kontrak berhasil dihapus.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data kontrak tidak ditemukan.'
            ], 404);
        }
    }
}
