<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['bookings', 'services'])->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateUser($request);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.users')->with('success', 'تمت إضافة المستخدم بنجاح.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validateUser($request, $user);

        abort_if(
            $user->id === auth()->id() && $data['role'] !== 'admin',
            403,
            'لا يمكنك التنازل عن صلاحية الإدارة من حسابك الخاص.'
        );

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'تم تحديث بيانات المستخدم.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,provider,customer']);

        abort_if(
            $user->id === auth()->id() && $request->role !== 'admin',
            403,
            'لا يمكنك التنازل عن صلاحية الإدارة من حسابك الخاص.'
        );

        $user->update(['role' => $request->role]);
        return back()->with('success', 'تم تحديث الدور.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'لا يمكنك حذف حسابك الخاص.');
        $user->delete();
        return back()->with('success', 'تم حذف المستخدم.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $passwordRule = $user ? ['nullable', 'confirmed', Password::defaults()]
                               : ['required', 'confirmed', Password::defaults()];

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'national_id' => ['required', 'string', 'digits:9', Rule::unique('users')->ignore($user)],
            'phone'       => ['required', 'string', 'max:20'],
            'email'       => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'role'        => ['required', 'in:admin,provider,customer'],
            'bio'         => ['nullable', 'string', 'max:1000'],
            'password'    => $passwordRule,
        ]);

        $data['email'] = $data['email'] ?? null;

        return $data;
    }
}
