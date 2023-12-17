<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\PastMedical;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
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

            return ResponseFormatter::success(
                $pastMedicals,
                'Data riwayat penyakit terdahulu berhasil diambil'
            );
            return response()->json($pastMedicals);
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Fetch past medical failed', 500);
        }
    }
}
