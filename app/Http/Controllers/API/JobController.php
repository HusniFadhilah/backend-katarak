<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Job;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class JobController extends Controller
{
    public function index(Request $request)
    {
        try {
            $isAll = $request->is_all ?? true;
            $pluckKey = $request->pluck_key ?? 'id';
            $id = $request->id;
            $name = $request->name;
            $jobs = Job::orderBy('name', 'asc');
            if ($name)
                $jobs->where('name', 'LIKE', '%' . $name . '%');
            if ($id)
                $jobs->whereId($id);

            if ($isAll)
                $jobs = $jobs->get();
            else
                $jobs = $jobs->pluck($pluckKey, 'name');

            return ResponseFormatter::success(
                $jobs,
                'Data pekerjaan berhasil diambil'
            );
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Fetch job failed', 500);
        }
    }
}
