<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PositionController extends Controller
{
    public function index()
    {
        return response()->json(Position::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'salary' => 'nullable|numeric',
            'level'  => 'nullable|string|max:50',
        ]);

        $position = Position::create([
            'position_id' => Str::uuid(),
            'name'        => $validated['name'],
            'salary'      => $validated['salary'] ?? null,
            'level'       => $validated['level'] ?? null,
        ]);

        return response()->json($position, 201);
    }

    public function show($id)
    {
        $position = Position::findOrFail($id);
        return response()->json($position);
    }

    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'name'   => 'sometimes|string|max:100',
            'salary' => 'nullable|numeric',
            'level'  => 'nullable|string|max:50',
        ]);

        $position->update($validated);

        return response()->json($position);
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return response()->json(null, 204);
    }
}