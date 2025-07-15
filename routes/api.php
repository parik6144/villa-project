<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\AdditionalLicenceType;
use App\Models\PropertyLicence;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/property-types/{class}', function ($class) {
    return \App\Models\PropertyType::where('property_class', $class)->get();
});

// New API route for licence types
Route::post('/licence-types', function (Request $request) {
    $dealTypes = $request->input('deal_types', []);
    
    if (empty($dealTypes)) {
        return [];
    }
    
    $query = AdditionalLicenceType::where('required', true);
    
    $query->where(function ($q) use ($dealTypes) {
        $mapping = [
            'deal_type_sale'          => 'sale',
            'deal_type_rent'          => 'short_rent',
            'deal_type_monthly_rent'  => 'monthly_rent',
        ];
        $hasCondition = false;
        foreach ($mapping as $dealKey => $column) {
            if (in_array($dealKey, $dealTypes, true)) {
                $q->orWhere($column, true);
                $hasCondition = true;
            }
        }
        if (!$hasCondition) {
            $q->whereRaw('0 = 1');
        }
    });
    
    $licenceTypes = $query->get();
    
    return $licenceTypes->map(function ($licenceType) {
        return [
            'additional_licence_type_id'   => $licenceType->id,
            'licence_type'      => $licenceType->name,
            'licence_number'    => null,
            'licence_file_name' => null,
        ];
    })->toArray();
});

