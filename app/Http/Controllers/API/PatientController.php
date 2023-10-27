<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class PatientController extends Controller
{
    public function index(Request $request)
    {
        try {
            $isAll = $request->is_all ?? true;
            $pluckKey = $request->pluck_key ?? 'id';
            $id = $request->id;
            $name = $request->name;
            $patients = Patient::orderBy('name', 'asc');
            if ($name)
                $patients->where('name', 'LIKE', '%' . $name . '%');
            if ($id)
                $patients->whereId($id);

            if ($isAll)
                $patients = $patients->get();
            else
                $patients = $patients->pluck($pluckKey, 'name');

            return response()->json($patients);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }
}
