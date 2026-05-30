<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'division'])->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $divisions = Division::all();
        $roles = Role::all();
        return view('users.create', compact('divisions', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'nik'         => 'required|string|max:50|unique:users',
            'password'    => 'required|string|min:6',
            'division_id' => 'required|exists:divisions,id',
            'role'        => 'required|exists:roles,name',
            'photo'       => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'nik'         => $request->nik,
            'password'    => Hash::make($request->password),
            'division_id' => $request->division_id,
        ]);

        $user->assignRole($request->role);

        // Handle photo upload — saved as user_{id}.png
        if ($request->hasFile('photo')) {
            $request->file('photo')->move(public_path('assets/Images'), 'user_' . $user->id . '.png');
        }

        return redirect()->route('users.index')->with('success', "Karyawan \"{$user->name}\" berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        $divisions = Division::all();
        $roles = Role::all();
        $user->load(['roles', 'division']);
        return view('users.edit', compact('user', 'divisions', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email,' . $user->id,
            'nik'         => 'required|string|max:50|unique:users,nik,' . $user->id,
            'division_id' => 'required|exists:divisions,id',
            'role'        => 'required|exists:roles,name',
            'photo'       => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'password'    => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = [
            'name'        => $request->name,
            'email'       => $request->email,
            'nik'         => $request->nik,
            'division_id' => $request->division_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Handle photo upload — saved as user_{id}.png
        if ($request->hasFile('photo')) {
            $filename = 'user_' . $user->id . '.png';
            $destination = public_path('assets/Images');
            $request->file('photo')->move($destination, $filename);
        }

        // Reset & assign new role
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Delete uploaded photo if exists
        $photoPath = public_path('assets/Images/user_' . $user->id . '.png');
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', "Karyawan \"{$name}\" berhasil dihapus.");
    }
}
