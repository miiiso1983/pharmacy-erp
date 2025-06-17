<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['roles', 'orders', 'invoices'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:admin,employee,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'phone' => $request->phone,
            'address' => $request->address,
            'company_name' => $request->company_name,
            'tax_number' => $request->tax_number,
            'status' => 'active',
        ]);

        // تعيين الأدوار
        if ($request->roles) {
            $user->assignRole($request->roles);
        }

        return redirect()->route('users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'user_type' => 'required|in:admin,employee,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'user_type' => $request->user_type,
            'phone' => $request->phone,
            'address' => $request->address,
            'company_name' => $request->company_name,
            'tax_number' => $request->tax_number,
            'status' => $request->status,
        ];

        // تحديث كلمة المرور إذا تم إدخالها
        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // تحديث الأدوار
        $user->syncRoles($request->roles ?? []);

        return redirect()->route('users.show', $user->id)
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // منع حذف المستخدم الحالي
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'لا يمكنك حذف حسابك الخاص']);
        }

        // التحقق من عدم وجود طلبات مرتبطة
        if ($user->orders()->count() > 0 || $user->invoices()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف المستخدم لوجود بيانات مرتبطة به']);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('company_name', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}
