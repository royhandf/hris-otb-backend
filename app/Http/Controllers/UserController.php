<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;


class UserController extends Controller
{
    public function index()
    {
        $users = User::with('employee:employee_id,name')->latest()->paginate(10);
        return response()->json($users);
    }

    public function createUserForEmployee(Request $request)
    {
        $request->validate([
            'employee_id' => [
                'required',
                'uuid',
                'exists:employees,employee_id',
                Rule::unique('users', 'employee_id')
            ],
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
            'role' => ['required', Rule::in(['admin', 'hr', 'karyawan'])],
        ], [
            // Pesan error kustom
            'employee_id.unique' => 'Karyawan yang dipilih sudah memiliki akun.',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'employee_id' => $request->employee_id,
        ]);

        $user->load('employee:employee_id,name');

        return response()->json([
            'message' => "Akun untuk {$user->employee->name} berhasil dibuat.",
            'data' => $user
        ], 201);
    }

    public function updateRole(Request $request, $userId)
    {
        $request->validate([
            'role' => ['required', Rule::in(['admin', 'hr', 'karyawan'])],
        ]);

        $userToUpdate = User::find($userId);

        if (!$userToUpdate) {
            return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404);
        }

        $userToUpdate->role = $request->role;
        $userToUpdate->save();

        return response()->json([
            'message' => "Role untuk pengguna {$userToUpdate->email} berhasil diubah.",
            'data' => $userToUpdate
        ], 200);
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil direset',
        ]);
    }

    // fungsi untuk mendapatkan data user
    public function showProfile()
    {
        $user = auth()->user();
        $user->load('employee.department', 'employee.position');

        if (!$user->employee) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan untuk pengguna ini.'], 404);
        }

        return response()->json([
            'message' => 'Profil berhasil diambil',
            'data' => $user
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        try {
            $request->validate([
                'email' => 'sometimes|required|email|unique:users,email,' . $user->user_id . ',user_id',
                'current_password' => 'nullable|string|required_with:password',
                'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],

                'name' => 'sometimes|required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:laki-laki,perempuan',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'bank_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:255',
            ]);


            if ($request->hasFile('photo')) {
                if ($employee->photo_path) {
                    Storage::disk('public')->delete($employee->photo_path);
                }
                $path = $request->file('photo')->store('employee_photos', 'public');
                $employeeData['photo_path'] = $path;
            }

            if (!empty($employeeData)) {
                $employee->update($employeeData);
            }

            if ($request->filled('email')) {
                $user->email = $request->email;
            }

            if ($request->filled('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json(['message' => 'Password saat ini yang Anda masukkan salah.'], 422);
                }
                $user->password = Hash::make($request->password);
            }

            if ($user->isDirty()) {
                $user->save();
            }

            $user->refresh()->load('employee.department', 'employee.position');
            return response()->json([
                'message' => 'Profil berhasil diperbarui',
                'data' => $user
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui profil.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getEmployeesWithoutAccount()
    {
        $existingUserEmployeeIds = User::whereNotNull('employee_id')->pluck('employee_id');

        $employees = Employee::whereNotIn('employee_id', $existingUserEmployeeIds)
            ->select('employee_id', 'name')
            ->get();

        return response()->json([
            'message' => 'Daftar karyawan tanpa akun berhasil diambil.',
            'data' => $employees
        ], 200);
    }
}
