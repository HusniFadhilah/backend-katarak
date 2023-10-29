<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\EyeExamination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class EyeExaminationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $isAll = $request->is_all ?? true;
            $pluckKey = $request->pluck_key ?? 'id';
            $id = $request->id;
            $name = $request->name;
            $eyeExaminations = EyeExamination::orderBy('examination_date_time', 'desc');
            if ($name)
                $eyeExaminations->where('name', 'LIKE', '%' . $name . '%');
            if ($id)
                $eyeExaminations->whereId($id);

            if ($isAll)
                $eyeExaminations = $eyeExaminations->get();
            else
                $eyeExaminations = $eyeExaminations->pluck($pluckKey, 'name');

            return response()->json($eyeExaminations);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }
}
