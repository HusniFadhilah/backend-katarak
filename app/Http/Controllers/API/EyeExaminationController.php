<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Rules\InEnum;
use App\Libraries\Fungsi;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\{Auth, Validator};
use App\Models\{EyeExamination, EyeDisorderExamination, PastMedicalExamination, EyeImage};


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
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $patientId = $request->patient_id;
            $statuses = $request->statuses;
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
            if ($patientId)
                $eyeExaminations->wherePatientId($patientId);
            if ($doctorId)
                $eyeExaminations->whereDoctorId($doctorId);
            if ($statuses)
                $eyeExaminations->whereIn('status', explode(',', $statuses));
            if ($startDate)
                $eyeExaminations->whereDate('examination_date_time', '>=', $startDate);
            if ($endDate)
                $eyeExaminations->whereDate('examination_date_time', '<=', $endDate);
            if ($isAll)
                $eyeExaminations = $eyeExaminations->with(['patient:id,ktp,job_id,name,gender,birth_date,birth_place,address', 'patient.job:id,name', 'kader:id,role_id,name,phone_number,email,is_active,email_verified_at', 'doctor:id,role_id,name,phone_number,email,is_active,email_verified_at', 'eyeDisorders', 'pastMedicals', 'eyeImages'])->get();
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

    public function store(Request $request)
    {
        try {
            $niceNames = array(
                'patient_id' => 'pasien',
                'right_eye_vision' => 'hasil pemeriksaan mata kanan',
                'left_eye_vision' => 'hasil pemeriksaan mata kiri',
                'status' => 'status pemeriksaan',
            );

            $validator = Validator::make($request->all(), [
                'patient_id' => ['required'],
                'right_eye_vision' => ['required'],
                'left_eye_vision' => ['required'],
                'status' => ['required', new InEnum(['wait', 'abnormal', 'normal'])],
            ], [], $niceNames);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Submit examination failed', 422);
            }

            $users = User::whereRoleId(2)->get();
            $attr = $request->all();
            $attr['kader_id'] = Auth::id();
            $attr['examination_date_time'] = now();
            $currentUserInfo = Location::get($request->ip());
            $attr['latitude'] = $currentUserInfo ? $currentUserInfo->latitude : '';
            $attr['longitude'] = $currentUserInfo ? $currentUserInfo->longitude : '';
            if ($request->latitude)
                $attr['latitude'] = $request->latitude;
            if ($request->longitude)
                $attr['longitude'] = $request->longitude;
            if ($attr['latitude'] && $attr['longitude'])
                $attr['formatted_location'] = Fungsi::getFormattedLocation($attr['latitude'], $attr['longitude']);
            $eyeExamination = EyeExamination::create($attr);
            $this->insertData($request->eye_disorders_id, new EyeDisorderExamination, $eyeExamination, 'eye_disorder_id', 'eyeDisorderExaminations');
            $this->insertData($request->past_medicals_id, new PastMedicalExamination, $eyeExamination, 'past_medical_id', 'pastMedicalExaminations');
            Fungsi::sendNotification($users, 'Data pemeriksaan pasien berhasil ditambahkan', 'Silahkan lakukan verifikasi dan berikan catatan tentang hasil pemeriksaan pasien');
            return ResponseFormatter::success([
                'examination' => $eyeExamination
            ], 'Examination have been submitted');
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Submit examination failed', 500);
        }
    }

    private function insertData($ids, $model, $eyeExamination, $column, $relationName)
    {
        $eyeExamination->$relationName()->delete();
        $dataInsert = array_map(function ($id) use ($eyeExamination, $column) {
            return [
                'eye_examination_id' => $eyeExamination->id,
                $column => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $ids);
        $model::insert($dataInsert);
    }

    public function uploadImage(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|max:2048'
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Upload image failed', 422);
            }

            if ($request->file('image')) {
                $eyeExamination = EyeExamination::find($id);
                if (!isset($eyeExamination)) {
                    return ResponseFormatter::error([
                        'error' => null,
                        'message' => 'Data pemeriksaan mata tidak ada'
                    ], 'Eye examination not found', 404);
                }
                $imagePath = Fungsi::compressImage($request->file('image'), 'eye-examination/');
                if (!$imagePath) {
                    $eyeExamination->delete();
                    return ResponseFormatter::error([
                        'error' => null,
                        'message' => 'Silahkan perbaiki file upload Anda'
                    ], 'Upload image failed', 422);
                }
                $eyeImage = EyeImage::create(['eye_examination_id' => $eyeExamination->id, 'patient_id' => $eyeExamination->patient_id, 'kader_id' => $eyeExamination->kader_id, 'image_path' => $imagePath[1]]);

                return ResponseFormatter::success([$eyeImage], 'File successfully uploaded');
            }
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Upload eye examination\'s image failed', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $eyeExamination = EyeExamination::find($id);
            if (!isset($eyeExamination)) {
                return ResponseFormatter::error([
                    'message' => 'Data pemeriksaan mata tidak ditemukan',
                ], 'Eye examination not found', 404);
            }

            $eyeExamination->delete();
            return ResponseFormatter::success(null, 'Eye examination Have Been Deleted');
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Delete eye examination failed', 500);
        }
    }

    public function confirm(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required',
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Change examination status failed', 422);
            }

            $user = Auth::user();
            $examination = EyeExamination::find($id);
            if (!$examination) {
                return ResponseFormatter::error([
                    'error' => null,
                    'message' => 'Data pemeriksaan mata tidak ada'
                ], 'Change examination status failed', 404);
            }
            if ($examination->doctor_id != null)
                if ($user->id != $examination->doctor_id) {
                    return ResponseFormatter::error([
                        'error' => null,
                        'message' => 'Maaf, terdapat perbedaan dokter yang melakukan pemeriksaan'
                    ], 'Change examination status failed', 403);
                }

            $examination->update($request->all());
            return ResponseFormatter::success($examination, 'Change examination status successfull');
        } catch (Exception $error) {
            Log::channel('api')->info($error);
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Change examination status failed', 500);
        }
    }
}
