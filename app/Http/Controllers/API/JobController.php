<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Job;
use Illuminate\Http\Request;
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

            return response()->json($jobs);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }
}
