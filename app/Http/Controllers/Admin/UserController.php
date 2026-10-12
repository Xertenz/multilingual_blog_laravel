<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // عرض قائمة المستخدمين مع رتبهم
    public function index()
    {
        // جلب المستخدمين مع رتبهم (مع التصفح Pagination لترتيب المنصة)
        $users = User::with('roles')->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    // عرض صفحة تعديل رتبة مستخدم محدد
    public function edit(User $user)
    {
        $roles = Role::all(); // جلب كافة الرتب المتاحة (super-admin, editor, writer)
        return view('admin.users.edit', compact('user', 'roles'));
    }

    // تحديث رتبة المستخدم
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        // مزامنة الرتبة الجديدة (تتم إزالة الرتب القديمة وتعيين الرتبة الجديدة المختار)
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User role updated successfully.');
    }
}
