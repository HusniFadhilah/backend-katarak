<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\PastMedical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PastMedicalController extends Controller
{
    public function index(Request $request)
    {
        try {
            $isAll = $request->is_all ?? true;
            $pluckKey = $request->pluck_key ?? 'id';
            $id = $request->id;
            $name = $request->name;
            $pastMedicals = PastMedical::orderBy('name', 'asc');
            if ($name)
                $pastMedicals->where('name', 'LIKE', '%' . $name . '%');
            if ($id)
                $pastMedicals->whereId($id);

            if ($isAll)
                $pastMedicals = $pastMedicals->get();
            else
                $pastMedicals = $pastMedicals->pluck($pluckKey, 'name');

            return response()->json($pastMedicals);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }
}
