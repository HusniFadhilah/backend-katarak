<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;


class PatientController extends Controller
{
    public function index(Request $request)
    {
        try {
            $isAll = $request->is_all ?? true;
            $pluckKey = $request->pluck_key ?? 'id';
            $id = $request->id;
            $name = $request->name;
            $patients = Patient::with(['job'])->orderBy('name', 'asc');
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

    public function store(Request $request)
    {
        try {
            $niceNames = array(
                'job_id' => 'jenis pekerjaan',
                'name' => 'nama pasien',
                'ktp' => 'no ktp',
                'gender' => 'jenis kelamin',
                'birth_place' => 'tempat lahir',
                'birth_date' => 'tanggal lahir',
                'address' => 'alamat',
            );

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'max:255'],
                'ktp' => ['required', 'unique:patients'],
                'gender' => ['required'],
                'birth_place' => ['required', 'max:255'],
                'birth_date' => ['required', 'max:255'],
                'job_id' => ['required'],
                'address' => ['required'],
            ], [], $niceNames);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Add patient failed', 422);
            }

            $attr = $request->all();
            $attr['created_by'] = Auth::id();
            $attr['ktp'] = encrypt($request->ktp);
            $patient = Patient::create($attr);

            return ResponseFormatter::success([
                'patient' => $patient
            ], 'Patient have been added');
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Add patient failed', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $patient = Patient::find($id);
            if (!isset($patient)) {
                return ResponseFormatter::error([
                    'message' => 'Data pasien tidak ditemukan',
                ], 'Patient not found', 404);
            }

            $patient->delete();
            return ResponseFormatter::success(null, 'Patient Have Been Deleted');
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Delete patient failed', 500);
        }
    }
}
