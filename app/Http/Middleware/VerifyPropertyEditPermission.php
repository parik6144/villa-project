<?php

namespace App\Http\Middleware;

use App\Models\Property;
use App\Models\CompanyEmployee;
use App\Models\CompanyMeta;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyPropertyEditPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $recordId = $request->route('record');
        $record = Property::find($recordId);

        if (!$record) {
            abort(404);
        }

        if (Auth::user()->hasRole('admin')) {
            return $next($request);
        }

        if ($record->user_id == Auth::id() && Auth::user()->hasRole('property_owner')) {
            return $next($request);
        }

        if (Auth::user()->hasRole('manager')) {
            $companyId = CompanyEmployee::where('employee_user_id', Auth::id())
                ->value('company_user_id');
            
            $hasPropertyManagementCompany = CompanyMeta::where('user_id', $companyId)
                ->where('type', 'Property Management Company')
                ->exists();
            
            if ($companyId && $hasPropertyManagementCompany && $record->user_id == $companyId) {
                return $next($request);
            }

            if ($record->user_id == Auth::id()){
                return $next($request);
            }
        }

        abort(404);
    }

}
