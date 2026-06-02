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
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'nik'           => 'required|string|max:50|unique:users',
            'password'      => 'required|string|min:6',
            'division_id'   => 'required|exists:divisions,id',
            'role'          => 'required|exists:roles,name',
            'photo'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'join_date'     => 'required|date',
            'phone'         => 'nullable|string|max:20',
            'birth_date'    => 'nullable|date',
            'employee_type' => 'required|string|in:Full Time,Part Time,Contract,Internship,Freelance',
            'gender'        => 'required|string|in:Laki-laki,Perempuan',
        ]);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'nik'           => $request->nik,
            'password'      => Hash::make($request->password),
            'division_id'   => $request->division_id,
            'join_date'     => $request->join_date,
            'phone'         => $request->phone,
            'birth_date'    => $request->birth_date,
            'employee_type' => $request->employee_type,
            'gender'        => $request->gender,
        ]);

        $user->assignRole($request->role);

        // Handle photo upload — saved as user_{id}.png
        if ($request->filled('cropped_photo')) {
            $data = $request->input('cropped_photo');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    $data = base64_decode($data);
                    if ($data !== false) {
                        file_put_contents(public_path('assets/Images/user_' . $user->id . '.png'), $data);
                    }
                }
            }
        } elseif ($request->hasFile('photo')) {
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
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'nik'           => 'required|string|max:50|unique:users,nik,' . $user->id,
            'division_id'   => 'required|exists:divisions,id',
            'role'          => 'required|exists:roles,name',
            'photo'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'password'      => 'nullable|string|min:6|confirmed',
            'join_date'     => 'required|date',
            'phone'         => 'nullable|string|max:20',
            'birth_date'    => 'nullable|date',
            'employee_type' => 'required|string|in:Full Time,Part Time,Contract,Internship,Freelance',
            'gender'        => 'required|string|in:Laki-laki,Perempuan',
        ]);

        $updateData = [
            'name'          => $request->name,
            'email'         => $request->email,
            'nik'           => $request->nik,
            'division_id'   => $request->division_id,
            'join_date'     => $request->join_date,
            'phone'         => $request->phone,
            'birth_date'    => $request->birth_date,
            'employee_type' => $request->employee_type,
            'gender'        => $request->gender,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Handle photo upload — saved as user_{id}.png
        if ($request->filled('cropped_photo')) {
            $data = $request->input('cropped_photo');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    $data = base64_decode($data);
                    if ($data !== false) {
                        file_put_contents(public_path('assets/Images/user_' . $user->id . '.png'), $data);
                    }
                }
            }
        } elseif ($request->hasFile('photo')) {
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

    public function profile()
    {
        $user = auth()->user();
        $divisions = Division::all();
        $roles = Role::all();
        $managers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['CEO', 'GM', 'Head']);
        })->where('id', '!=', $user->id)->orderBy('name')->get();

        return view('users.profile', compact('user', 'divisions', 'roles', 'managers'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender'     => 'nullable|string|in:Laki-laki,Perempuan',
            'photo'      => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $updateData = [
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'birth_date' => $request->birth_date,
            'gender'     => $request->gender,
        ];

        $user->update($updateData);

        // Handle photo upload
        if ($request->filled('cropped_photo')) {
            $data = $request->input('cropped_photo');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    $data = base64_decode($data);
                    if ($data !== false) {
                        file_put_contents(public_path('assets/Images/user_' . $user->id . '.png'), $data);
                    }
                }
            }
        } elseif ($request->hasFile('photo')) {
            $filename = 'user_' . $user->id . '.png';
            $destination = public_path('assets/Images');
            $request->file('photo')->move($destination, $filename);
        }

        return redirect()->back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password Anda berhasil diperbarui.');
    }

    public function show(User $user)
    {
        $divisions = Division::all();
        $roles = Role::all();
        $managers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['CEO', 'GM', 'Head']);
        })->where('id', '!=', $user->id)->orderBy('name')->get();

        return view('users.show', compact('user', 'divisions', 'roles', 'managers'));
    }

    public function editPermissions(Request $request)
    {
        $users = User::with(['roles', 'permissions', 'division'])->get();
        return view('users.permissions', compact('users'));
    }

    public function updatePermissions(Request $request)
    {
        $users = User::all();
        $availablePermissions = ['view_dashboard', 'weekly_report', 'leave_request', 'attendance_history', 'crud_users', 'crud_events', 'manage_calendar', 'rekap_absen', 'rekap_weekly', 'weekly_history', 'leave_approvals'];
        $inputPermissions = $request->input('permissions', []);

        foreach ($users as $user) {
            // Lockout safeguard: do not modify the active logged-in user's permissions
            if ($user->id === auth()->id()) {
                continue;
            }

            $userChecked = isset($inputPermissions[$user->id]) ? $inputPermissions[$user->id] : [];
            $permissionsToSync = [];

            foreach ($availablePermissions as $perm) {
                if (isset($userChecked[$perm]) && $userChecked[$perm] == '1') {
                    $permissionsToSync[] = $perm;
                }
            }

            $user->syncPermissions($permissionsToSync);
        }

        return redirect()->route('users.permissions');
    }
}
