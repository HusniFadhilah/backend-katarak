<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Libraries\Fungsi;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\{EyeExamination, EyeImage};
use Illuminate\Support\Facades\Validator;

class EyeImageController extends Controller
{
    public function uploadMultipleImage(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file.*' => ['required', 'mimetypes:image/*', 'max:2048'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'error' => $validator->errors(),
                    'message' => 'Harap isi form dengan benar'
                ], 'Upload multiple image failed', 422);
            }

            $eyeExamination = EyeExamination::get()->find($id);
            if (!$eyeExamination) {
                return ResponseFormatter::error([
                    'message' => 'Data pemeriksaan mata tidak ditemukan',
                ], 'Upload multiple image failed', 404);
            }

            $images = EyeImage::where('eye_examination_id', $eyeExamination->id)->get();
            if ($request->hasFile('image')) {
                if (count($request->file('image')) > 3) {
                    return ResponseFormatter::error([
                        'error' => $validator->errors(),
                        'message' => 'Hanya boleh mengupload maksimal 3 file'
                    ], 'Upload multiple image failed', 401);
                }
                if (count($images) >= 3) {
                    return ResponseFormatter::error([
                        'message' => 'Batas maksimal foto per pemeriksaan telah tercapai',
                    ], 'Upload multiple image failed', 403);
                }
                if (count($images) + count($request->file('image')) > 3) {
                    return ResponseFormatter::error([
                        'message' => 'Slot hanya tersedia untuk ' . abs(3 - count($images)) . ' foto lagi',
                    ], 'Upload multiple image failed', 403);
                }

                $data =  array();
                $images =  array();
                foreach ($request->file('image') as $key => $file) {
                    $name = Fungsi::compressImage($file, 'eye-examination/');
                    $data[$key] = $name;
                    $image = new EyeImage();
                    $image->eye_examination_id = $id;
                    $image->patient_id = $eyeExamination->patient_id;
                    $image->kader_id = $eyeExamination->kader_id;
                    $image->image_path = $data[$key];
                    $image->save();
                    $images[] = $image;
                }

                return ResponseFormatter::success([
                    'data' => $images
                ], 'File successfully uploaded');
            } else {
                return ResponseFormatter::error([
                    'message' => 'Tidak ada file gambar',
                ], 'Upload multiple image failed', 404);
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Terjadi kegagalan, silahkan coba lagi',
                'error' => $error,
            ], 'Upload multiple image failed', 500);
        }
    }
}
