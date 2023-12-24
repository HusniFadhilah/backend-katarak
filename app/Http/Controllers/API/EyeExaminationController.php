<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\EyeExamination;
use App\Helpers\ResponseFormatter;
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
            $search = $request->search;
            $kaderId = $request->kader_id;
            $doctorId = $request->doctor_id;
            $eyeExaminations = EyeExamination::orderBy('examination_date_time', 'desc');
            if ($name)
                $eyeExaminations->whereHas('patient', function ($q) use ($name) {
                    $q->where('name', 'LIKE', '%' . $name . '%');
                });
            if ($search)
                $eyeExaminations->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')->orWhere('ktp', 'LIKE', '%' . $search . '%');
                });
            if ($id)
                $eyeExaminations->whereId($id);
            if ($kaderId)
                $eyeExaminations->whereKaderId($kaderId);
            if ($doctorId)
                $eyeExaminations->whereDoctorId($doctorId);
            if ($isAll)
                $eyeExaminations = $eyeExaminations->with(['patient:id,ktp,job_id,name,gender,birth_date,birth_place,address', 'patient.job:id,name', 'kader:id,role_id,name,phone_number,email,is_active,email_verified_at', 'doctor:id,role_id,name,phone_number,email,is_active,email_verified_at', 'eyeDisorders', 'pastMedicals'])->get();
            else
                $eyeExaminations = $eyeExaminations->pluck($pluckKey, 'name');

            return ResponseFormatter::success(
                $eyeExaminations,
                'Data pemeriksaan mata berhasil diambil'
            );
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Fetch eye examination failed', 500);
        }
    }
}
