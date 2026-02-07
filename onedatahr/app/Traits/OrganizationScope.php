<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Division;
use App\Models\Department;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

/**
 * Trait OrganizationScope
 * 
 * Provides methods to filter data based on user's organization scope.
 * Used for role-based data access filtering.
 * 
 * org_scope values:
 * - 'all': No filter (superadmin/admin)
 * - 'holding': Filter by holding_id
 * - 'company': Filter by company_id
 * - 'division': Filter by division_id
 * - 'department': Filter by department_id
 * - 'unit': Filter by unit_id
 */
trait OrganizationScope
{
    /**
     * Check if current user has unrestricted access (no organization filter)
     */
    public function hasUnrestrictedAccess(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        
        // Superadmin and Admin have unrestricted access
        if ($user->hasRole(['superadmin', 'admin'])) {
            return true;
        }
        
        // Users with 'all' org_scope also have unrestricted access
        return ($user->org_scope ?? 'all') === 'all';
    }

    /**
     * Get accessible Holding IDs based on user's organization scope
     */
    public function getAccessibleHoldingIds(): array
    {
        $user = Auth::user();
        if (!$user || $this->hasUnrestrictedAccess()) {
            return []; // Empty = no filter (all accessible)
        }
        
        $scope = $user->org_scope ?? 'all';
        
        if ($scope === 'holding' && $user->holding_id) {
            return [$user->holding_id];
        }
        
        // For company/division/department/unit scope, get the parent holding
        if (in_array($scope, ['company', 'division', 'department', 'unit'])) {
            $holdingId = $this->getHoldingIdFromUserScope($user);
            return $holdingId ? [$holdingId] : [];
        }
        
        return [];
    }

    /**
     * Get accessible Company IDs based on user's organization scope
     */
    public function getAccessibleCompanyIds(): array
    {
        $user = Auth::user();
        if (!$user || $this->hasUnrestrictedAccess()) {
            return []; // Empty = no filter (all accessible)
        }
        
        $scope = $user->org_scope ?? 'all';
        
        if ($scope === 'holding' && $user->holding_id) {
            // Get all companies under this holding
            return Company::where('holding_id', $user->holding_id)->pluck('id')->toArray();
        }
        
        if ($scope === 'company' && $user->company_id) {
            return [$user->company_id];
        }
        
        // For division/department/unit, get company from the entity
        if (in_array($scope, ['division', 'department', 'unit'])) {
            $companyId = $this->getCompanyIdFromUserScope($user);
            return $companyId ? [$companyId] : [];
        }
        
        return [];
    }

    /**
     * Get accessible Division IDs based on user's organization scope
     */
    public function getAccessibleDivisionIds(): array
    {
        $user = Auth::user();
        if (!$user || $this->hasUnrestrictedAccess()) {
            return []; // Empty = no filter (all accessible)
        }
        
        $scope = $user->org_scope ?? 'all';
        
        if ($scope === 'holding' && $user->holding_id) {
            // Get all divisions (holding-based + company-based under companies in this holding)
            $holdingDivisions = Division::where('based_on', 'holding')
                ->where('holding_id', $user->holding_id)
                ->pluck('id')
                ->toArray();
            
            $companyIds = Company::where('holding_id', $user->holding_id)->pluck('id')->toArray();
            $companyDivisions = Division::where('based_on', 'company')
                ->whereIn('company_id', $companyIds)
                ->pluck('id')
                ->toArray();
            
            return array_merge($holdingDivisions, $companyDivisions);
        }
        
        if ($scope === 'company' && $user->company_id) {
            // Get all divisions for this company (including parent divisions from holding)
            $company = Company::find($user->company_id);
            $divisionIds = Division::where('company_id', $user->company_id)->pluck('id')->toArray();
            
            // Also include holding-based divisions if company has a holding
            if ($company && $company->holding_id) {
                $holdingDivisions = Division::where('based_on', 'holding')
                    ->where('holding_id', $company->holding_id)
                    ->pluck('id')
                    ->toArray();
                $divisionIds = array_merge($divisionIds, $holdingDivisions);
            }
            
            return array_unique($divisionIds);
        }
        
        if ($scope === 'division' && $user->division_id) {
            // Get this division and all child divisions
            return $this->getDivisionAndChildren($user->division_id);
        }
        
        // For department/unit scope, get division from the entity
        if (in_array($scope, ['department', 'unit'])) {
            $divisionId = $this->getDivisionIdFromUserScope($user);
            return $divisionId ? [$divisionId] : [];
        }
        
        return [];
    }

    /**
     * Get accessible Department IDs based on user's organization scope
     */
    public function getAccessibleDepartmentIds(): array
    {
        $user = Auth::user();
        if (!$user || $this->hasUnrestrictedAccess()) {
            return [];
        }
        
        $scope = $user->org_scope ?? 'all';
        
        if ($scope === 'holding' && $user->holding_id) {
            // Get all departments under accessible divisions
            $divisionIds = $this->getAccessibleDivisionIds();
            return Department::whereIn('division_id', $divisionIds)->pluck('id')->toArray();
        }
        
        if ($scope === 'company' && $user->company_id) {
            $divisionIds = $this->getAccessibleDivisionIds();
            return Department::whereIn('division_id', $divisionIds)->pluck('id')->toArray();
        }
        
        if ($scope === 'division' && $user->division_id) {
            $divisionIds = $this->getAccessibleDivisionIds();
            return Department::whereIn('division_id', $divisionIds)->pluck('id')->toArray();
        }
        
        if ($scope === 'department' && $user->department_id) {
            return $this->getDepartmentAndChildren($user->department_id);
        }
        
        if ($scope === 'unit' && $user->unit_id) {
            $unit = Unit::find($user->unit_id);
            return $unit && $unit->department_id ? [$unit->department_id] : [];
        }
        
        return [];
    }

    /**
     * Get accessible Unit IDs based on user's organization scope
     */
    public function getAccessibleUnitIds(): array
    {
        $user = Auth::user();
        if (!$user || $this->hasUnrestrictedAccess()) {
            return [];
        }
        
        $scope = $user->org_scope ?? 'all';
        
        if (in_array($scope, ['holding', 'company', 'division', 'department'])) {
            $departmentIds = $this->getAccessibleDepartmentIds();
            return Unit::whereIn('department_id', $departmentIds)->pluck('id')->toArray();
        }
        
        if ($scope === 'unit' && $user->unit_id) {
            return $this->getUnitAndChildren($user->unit_id);
        }
        
        return [];
    }

    /**
     * Get division ID and all child division IDs recursively
     */
    protected function getDivisionAndChildren($divisionId): array
    {
        $ids = [$divisionId];
        $children = Division::where('parent_id', $divisionId)->pluck('id')->toArray();
        
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getDivisionAndChildren($childId));
        }
        
        return array_unique($ids);
    }

    /**
     * Get department ID and all child department IDs recursively
     */
    protected function getDepartmentAndChildren($departmentId): array
    {
        $ids = [$departmentId];
        $children = Department::where('parent_id', $departmentId)->pluck('id')->toArray();
        
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getDepartmentAndChildren($childId));
        }
        
        return array_unique($ids);
    }

    /**
     * Get unit ID and all child unit IDs recursively
     */
    protected function getUnitAndChildren($unitId): array
    {
        $ids = [$unitId];
        $children = Unit::where('parent_id', $unitId)->pluck('id')->toArray();
        
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getUnitAndChildren($childId));
        }
        
        return array_unique($ids);
    }

    /**
     * Get holding ID from user's scope entity
     */
    protected function getHoldingIdFromUserScope($user): ?int
    {
        if ($user->holding_id) return $user->holding_id;
        
        if ($user->company_id) {
            $company = Company::find($user->company_id);
            return $company?->holding_id;
        }
        
        if ($user->division_id) {
            $division = Division::with('company.holding')->find($user->division_id);
            if ($division?->based_on === 'holding') {
                return $division?->holding_id;
            }
            return $division?->company?->holding_id;
        }
        
        if ($user->department_id) {
            $dept = Department::with('division.company.holding')->find($user->department_id);
            return $dept?->division?->company?->holding_id;
        }
        
        if ($user->unit_id) {
            $unit = Unit::with('department.division.company.holding')->find($user->unit_id);
            return $unit?->department?->division?->company?->holding_id;
        }
        
        return null;
    }

    /**
     * Get company ID from user's scope entity
     */
    protected function getCompanyIdFromUserScope($user): ?int
    {
        if ($user->company_id) return $user->company_id;
        
        if ($user->division_id) {
            $division = Division::find($user->division_id);
            return $division?->company_id;
        }
        
        if ($user->department_id) {
            $dept = Department::with('division')->find($user->department_id);
            return $dept?->division?->company_id;
        }
        
        if ($user->unit_id) {
            $unit = Unit::with('department.division')->find($user->unit_id);
            return $unit?->department?->division?->company_id;
        }
        
        return null;
    }

    /**
     * Get division ID from user's scope entity
     */
    protected function getDivisionIdFromUserScope($user): ?int
    {
        if ($user->division_id) return $user->division_id;
        
        if ($user->department_id) {
            $dept = Department::find($user->department_id);
            return $dept?->division_id;
        }
        
        if ($user->unit_id) {
            $unit = Unit::with('department')->find($user->unit_id);
            return $unit?->department?->division_id;
        }
        
        return null;
    }
}
