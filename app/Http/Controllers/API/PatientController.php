<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
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

            return ResponseFormatter::success(
                $patients,
                'Data pasien berhasil diambil'
            );
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Fetch patient failed', 500);
        }
    }
}
