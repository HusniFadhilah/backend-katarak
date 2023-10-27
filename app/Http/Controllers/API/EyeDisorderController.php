<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\EyeDisorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class EyeDisorderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $isAll = $request->is_all ?? true;
            $pluckKey = $request->pluck_key ?? 'id';
            $id = $request->id;
            $name = $request->name;
            $eyeDisorders = EyeDisorder::orderBy('name', 'asc');
            if ($name)
                $eyeDisorders->where('name', 'LIKE', '%' . $name . '%');
            if ($id)
                $eyeDisorders->whereId($id);

            if ($isAll)
                $eyeDisorders = $eyeDisorders->get();
            else
                $eyeDisorders = $eyeDisorders->pluck($pluckKey, 'name');

            return response()->json($eyeDisorders);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }
}
