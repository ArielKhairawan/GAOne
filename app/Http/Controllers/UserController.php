<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::with('roles')->latest()->paginate(15)]);
    }

    public function create()
    {
        return view('users.form', ['user' => new User(), 'roles' => Role::pluck('name')]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        $user = User::create($data);
        $user->syncRoles($roles);

        return redirect()->route('users.index')->with('status', 'User dibuat.');
    }

    public function edit(User $user)
    {
        return view('users.form', ['user' => $user, 'roles' => Role::pluck('name')]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        $user->update($data);
        $user->syncRoles($roles);

        return redirect()->route('users.index')->with('status', 'User diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->is(auth()->user()), 422, 'Tidak dapat menghapus akun sendiri.');
        $user->delete();

        return back()->with('status', 'User dihapus.');
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        return back()->with('status', 'User diaktifkan.');
    }

    public function deactivate(User $user)
    {
        abort_if($user->is(auth()->user()), 422, 'Tidak dapat menonaktifkan akun sendiri.');
        $user->update(['is_active' => false]);

        return back()->with('status', 'User dinonaktifkan.');
    }

    public function export()
    {
        $rows = User::with('roles')->get()->map(fn (User $user) => [
            $user->name, $user->email, $user->phone, $user->department, $user->position,
            $user->roles->pluck('name')->implode('|'), $user->is_active ? 'Aktif' : 'Nonaktif',
        ]);
        $csv = "Nama,Email,Nomor HP,Departemen,Jabatan,Role,Status\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn ($value) => '"'.str_replace('"', '""', (string) $value).'"', $row))."\n";
        }

        return response($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename=users.csv']);
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $id = $user?->id ?? 'NULL';

        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|max:255|unique:users,email,$id",
            'phone' => 'nullable|string|max:50',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'is_active' => 'boolean',
        ]);
    }
}
