<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\{AreaInfrastructure, BaseWeightConstant, DisasterCategory, FacilityInfrastructure, SubAreaInfrastructure, WasteCategory, WasteCollection};

class DependentDropdownController extends Controller
{
    public function getRoles(Request $request)
    {
        try {
            $name = $request->name;
            $roles = Role::query();
            if ($name)
                $roles->where('alias', 'LIKE', '%' . $name . '%');

            $roles = $roles->pluck('id', 'alias');

            return response()->json($roles);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }
}
