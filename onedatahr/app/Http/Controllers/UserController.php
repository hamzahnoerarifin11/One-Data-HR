<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Holding;
use App\Models\Company;
use App\Models\Division;
use App\Models\Department;
use App\Models\Unit;
use App\Models\UserScopeAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with(['roles', 'holding', 'company', 'division', 'department', 'unit', 'karyawan'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pages.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $holdings = Holding::orderBy('name')->get();
        $companies = Company::with('holding')->orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        
        return view('pages.users.create', compact(
            'roles', 'holdings', 'companies', 'divisions', 'departments', 'units'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'nik'      => 'required|string|max:50|unique:users,nik',
            'jabatan'  => 'required|string|max:200',
            'roles'    => 'required|array',
            'roles.*'  => 'exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
            'org_scope' => 'required|in:all,holding,company,division,department,unit',
            'holding_id' => 'nullable|exists:holdings,id',
            'company_id' => 'nullable|exists:companies,id',
            'division_id' => 'nullable|exists:divisions,id',
            'department_id' => 'nullable|exists:departments,id',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'nik'      => $request->nik,
            'jabatan'  => $request->jabatan,
            'password' => Hash::make($request->password),
            'org_scope' => $request->org_scope,
            'holding_id' => $request->org_scope !== 'all' ? $request->holding_id : null,
            'company_id' => in_array($request->org_scope, ['company', 'division', 'department', 'unit']) ? $request->company_id : null,
            'division_id' => in_array($request->org_scope, ['division', 'department', 'unit']) ? $request->division_id : null,
            'department_id' => in_array($request->org_scope, ['department', 'unit']) ? $request->department_id : null,
            'unit_id' => $request->org_scope === 'unit' ? $request->unit_id : null,
        ]);

        // Attach roles
        $user->roles()->sync($request->roles);

        // Create audit log
        UserScopeAuditLog::create([
            'user_id' => $user->id,
            'changed_by' => Auth::id(),
            'new_org_scope' => $user->org_scope,
            'new_holding_id' => $user->holding_id,
            'new_company_id' => $user->company_id,
            'new_division_id' => $user->division_id,
            'new_department_id' => $user->department_id,
            'new_unit_id' => $user->unit_id,
            'action' => 'create',
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('pages.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $holdings = Holding::orderBy('name')->get();
        $companies = Company::with('holding')->orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        
        return view('pages.users.edit', compact(
            'user', 'roles', 'holdings', 'companies', 'divisions', 'departments', 'units'
        ));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'nik'     => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'nik')->ignore($user->id),
            ],
            'jabatan' => 'required|string|max:200',
            'roles'   => 'required|array',
            'roles.*' => 'exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
            'org_scope' => 'required|in:all,holding,company,division,department,unit',
            'holding_id' => 'nullable|exists:holdings,id',
            'company_id' => 'nullable|exists:companies,id',
            'division_id' => 'nullable|exists:divisions,id',
            'department_id' => 'nullable|exists:departments,id',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        // Store old values for audit log
        $oldOrgScope = $user->org_scope;
        $oldHoldingId = $user->holding_id;
        $oldCompanyId = $user->company_id;
        $oldDivisionId = $user->division_id;
        $oldDepartmentId = $user->department_id;
        $oldUnitId = $user->unit_id;

        // Clear irrelevant org fields based on scope
        $newHoldingId = $request->org_scope !== 'all' ? $request->holding_id : null;
        $newCompanyId = in_array($request->org_scope, ['company', 'division', 'department', 'unit']) ? $request->company_id : null;
        $newDivisionId = in_array($request->org_scope, ['division', 'department', 'unit']) ? $request->division_id : null;
        $newDepartmentId = in_array($request->org_scope, ['department', 'unit']) ? $request->department_id : null;
        $newUnitId = $request->org_scope === 'unit' ? $request->unit_id : null;

        // Update data user
        $user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'nik'     => $request->nik,
            'jabatan' => $request->jabatan,
            'org_scope' => $request->org_scope,
            'holding_id' => $newHoldingId,
            'company_id' => $newCompanyId,
            'division_id' => $newDivisionId,
            'department_id' => $newDepartmentId,
            'unit_id' => $newUnitId,
        ]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // 🔥 INI KUNCI MULTI ROLE
        $user->roles()->sync($request->roles);

        // Create audit log if org scope changed
        if ($oldOrgScope !== $user->org_scope || 
            $oldHoldingId !== $user->holding_id || 
            $oldCompanyId !== $user->company_id ||
            $oldDivisionId !== $user->division_id ||
            $oldDepartmentId !== $user->department_id ||
            $oldUnitId !== $user->unit_id) {
            
            UserScopeAuditLog::create([
                'user_id' => $user->id,
                'changed_by' => Auth::id(),
                'old_org_scope' => $oldOrgScope,
                'new_org_scope' => $user->org_scope,
                'old_holding_id' => $oldHoldingId,
                'new_holding_id' => $user->holding_id,
                'old_company_id' => $oldCompanyId,
                'new_company_id' => $user->company_id,
                'old_division_id' => $oldDivisionId,
                'new_division_id' => $user->division_id,
                'old_department_id' => $oldDepartmentId,
                'new_department_id' => $user->department_id,
                'old_unit_id' => $oldUnitId,
                'new_unit_id' => $user->unit_id,
                'action' => 'update',
            ]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Batch delete users
     */
    public function batchDelete(Request $request)
    {
        $ids = array_filter(explode(',', $request->ids));

        if (count($ids) > 0) {
            User::whereIn('id', $ids)->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'User terpilih berhasil dihapus.');
        }

        return redirect()
            ->route('users.index')
            ->with('error', 'Tidak ada user yang dipilih.');
    }
}
