<?php

namespace App\Http\Controllers;

use Exception;
use App\Libraries\{Date, Fungsi};
use Illuminate\Http\Request;
use App\Models\{EyeDisorderExamination, EyeExamination, EyeImage, PastMedicalExamination};
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Stevebauman\Location\Facades\Location;

class EyeExaminationController extends Controller
{
    // public function test(Request $request)
    // {
    //     return EyeExamination::with(['eyeDisorders'])->get();
    // }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                return $this->filterDataTable($request);
            }
            return view('eye-examinations.index');
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            if ($request->ajax())
                return ResponseFormatter::error($error, 'Terjadi kegagalan, silahkan coba beberapa saat lagi', 500);
            else
                return abort(500, $error);
        }
    }

    private function filterDataTable(Request $request)
    {
        $columnsMember = array(
            0 => '#',
            1 => 'id',
            2 => 'name',
        );
        $data = array();
        $limit = $request->input('length');
        $start = $request->input('start', 0);
        $search = $request->input('search.value');
        $column = $request->input('order.0.column');
        $dir = $request->input('order.0.dir', 'asc');

        $eyeExaminations = EyeExamination::query();
        if ($request->s_name)
            $eyeExaminations->where('name', 'LIKE', '%' . $request->s_name . '%');
        if ($search) {
            $eyeExaminations->orWhere('name', 'LIKE', '%' . $search . '%');
        }

        if ($column != null && $column != 0) {
            $eyeExaminations = $eyeExaminations->orderBy($columnsMember[$column], $dir);
        }

        $totalData = $eyeExaminations->count();
        $totalFiltered = $totalData;
        $eyeExaminations = $eyeExaminations->offset($start)->limit($limit)->latest()->get();

        $no = $start;
        foreach ($eyeExaminations as $eyeExamination) {
            $no++;
            $row = array();
            $row['#'] = '<input type="checkbox" name="checked[]" class="check mr-2" value="' . $eyeExamination->id . '">';
            $row['DT_RowIndex'] =  $no;
            $row['name'] = $eyeExamination->patient->name;
            $row['history'] = $this->getColumnHistory($eyeExamination);
            $row['result'] = $this->getColumnResult($eyeExamination);
            $row['status'] = $this->getColumnStatus($eyeExamination);
            $row['recommendation'] = $this->getColumnRecommendation($eyeExamination);
            $row['action'] = $this->getColumnAction($eyeExamination);

            $data[] = $row;
        }

        $output = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        //output dalam format JSON
        return json_encode($output);
    }

    private function getColumnAction($eyeExamination)
    {
        $creator = 'Dibuat oleh: ' . $eyeExamination->kader->name . '<br>Dibuat pada: ' . Date::tglDefault($eyeExamination->created_at) . ' pukul ' . Date::pukul($eyeExamination->created_at);
        $text = '<a type="button" class="btn btn-dark btn-sm m-1 px-3" data-toggle="popover" tabindex="0" data-trigger="focus" title="Tentang pemeriksaan mata ini" data-bs-html="true" data-html="true" data-bs-content="' . $creator . '" data-content="' . $creator . '"><span class="fa fa-info-circle"></span></a>';
        $text .= '<a href="' . route('eye-examination.edit', $eyeExamination->id) . '"><button type="button" class="btn btn-warning btn-sm m-1 px-3" title="Edit data pemeriksaanta"><span class="fa fa-edit"></span></button></a>';
        $text .= '<a onclick="confirmDelete(\'/eye-examination/' . $eyeExamination->id . '/destroy\',\'Pemeriksaan mata\')"><button type="button" class="btn btn-danger btn-sm m-1 px-3" title="Hapus data pemeriksaan mata"><span class="fa fa-trash"></span></button></a>';
        return $text;
    }

    private function getColumnStatus($eyeExamination)
    {
        $badges = array(
            'wait' => 'Menunggu verifikasi dokter',
            'abnormal' => 'Terdapat kelainan di mata',
            'normal' => 'Tidak terdapat kelainan di mata',
        );
        $text = '<span class="badge badge-sm badge-dark">' . (@$badges[$eyeExamination->status] ?? "Menunggu verifikasi dokter") . '</span>';
        return $text;
    }

    private function getColumnHistory($eyeExamination)
    {
        $text = 'Riwayat keluhan mata: ' . implode(', ', $eyeExamination->eyeDisorders()->pluck('name')->toArray());
        $text .= '<br><br>Riwayat penyakit dahulu: ' . implode(', ', $eyeExamination->pastMedicals()->pluck('name')->toArray());
        return $text;
    }

    private function getColumnResult($eyeExamination)
    {
        $text = 'Tanggal pemeriksaan: ' . Date::tglIndo($eyeExamination->examination_date_time);
        $text .= '<br>Diperiksa oleh: ' . $eyeExamination->kader->name;
        $text .= '<br>Mata kanan: ' . $eyeExamination->right_eye_vision;
        $text .= '<br>Mata kiri: ' . $eyeExamination->left_eye_vision;
        return $text;
    }

    private function getColumnRecommendation($eyeExamination)
    {
        $text = 'Hasil Pemeriksaan Dokter: ' . $eyeExamination->evaluation_description;
        return $text;
    }

    public function create()
    {
        return view('eye-examinations.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'patient_id' => ['required'],
            'kader_id' => ['required'],
            'doctor_id' => ['required'],
            'examination_date_time' => ['required'],
        ];
        $request->validate($rules);

        $currentUserInfo = Location::get($request->ip());
        $attr = $request->all();
        $attr['latitude'] = $currentUserInfo ? $currentUserInfo->latitude : '';
        $attr['longitude'] = $currentUserInfo ? $currentUserInfo->longitude : '';
        $attr['formatted_location'] = $this->getFormattedLocation($attr['latitude'], $attr['longitude']);
        $eyeExamination = EyeExamination::create($attr);
        $imagePath = Fungsi::compressImage($request->file('image'), 'eye-examination/');
        if (!$imagePath) {
            $eyeExamination->delete();
            Fungsi::sweetalert('Silahkan perbaiki file upload Anda', 'error', 'Gagal!');
            return back()->withInput();
        }
        EyeImage::create(['eye_examination_id' => $eyeExamination->id, 'patient_id' => $eyeExamination->patient_id, 'kader_id' => $eyeExamination->kader_id, 'image_path' => $imagePath[1]]);
        $this->insertData($request->eye_disorders_id, new EyeDisorderExamination, $eyeExamination, 'eye_disorder_id', 'eyeDisorderExaminations');
        $this->insertData($request->past_medicals_id, new PastMedicalExamination, $eyeExamination, 'past_medical_id', 'pastMedicalExaminations');
        Fungsi::sweetalert('Data pemeriksaan mata berhasil ditambahkan', 'success', 'Berhasil!');
        return redirect(route('eye-examination'));
    }

    private function getFormattedLocation($latitude, $longitude)
    {
        $apiKey = '9e304c7d13924b718d56a78553dd1b01';
        $apiUrl = 'https://api.opencagedata.com/geocode/v1/json';

        $url = "$apiUrl?key=$apiKey&q=$latitude,$longitude&no_annotations=1";
        $response = file_get_contents($url);

        if ($response !== false) {
            $data = json_decode($response, true);

            $results = $data['results'];
            if (!empty($results)) {
                $formattedLocation = $results[0]['formatted'];
            }
        }

        return $formattedLocation ?? null;
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

    public function show(EyeExamination $eyeExamination)
    {
        return view('eye-examinations.show', compact('eyeExamination'));
    }

    public function edit(EyeExamination $eyeExamination)
    {
        $eyeDisorderIds = $eyeExamination->eyeDisorderExaminations->pluck('eye_disorder_id')->toArray();
        $pastMedicalIds = $eyeExamination->pastMedicalExaminations->pluck('past_medical_id')->toArray();
        $eyeImagesPath = $eyeExamination->eyeImages->pluck('image_path')->toArray();
        return view('eye-examinations.edit', compact('eyeExamination', 'eyeDisorderIds', 'pastMedicalIds', 'eyeImagesPath'));
    }

    public function update(Request $request, EyeExamination $eyeExamination)
    {
        $rules = [
            'patient_id' => ['required'],
            'kader_id' => ['required'],
            'doctor_id' => ['required'],
            'examination_date_time' => ['required'],
        ];
        $request->validate($rules);
        $attr = $request->all();
        $eyeExamination->update($attr);

        Fungsi::sweetalert('Data pemeriksaan mata berhasil diupdate', 'success', 'Berhasil!');
        return redirect(route('eye-examination'));
    }

    public function destroy(EyeExamination $eyeExamination)
    {
        $eyeExamination->delete();
        Fungsi::sweetalert('Data pemeriksaan mata berhasil dihapus', 'success', 'Berhasil!');
        return back();
    }

    public function bulkDestroy(Request $request)
    {
        $checks = $request->checked;
        if (!isset($checks)) {
            Fungsi::sweetalert('Tidak ada data pemeriksaanta yang dipilih', 'warning', 'Perhatian!');
        } else {
            $eyeExaminations = EyeExamination::whereIn('id', $checks)->get();
            $notEligibleDeleteEyeExamination = [];
            $status = true;
            foreach ($eyeExaminations as $eyeExamination) {
                // $status = false;
                array_push($notEligibleDeleteEyeExamination, $eyeExamination->name);
            }
            if ($status) {
                foreach ($eyeExaminations as $eyeExamination) {
                    $eyeExamination->delete();
                }
                Fungsi::sweetalert('Data pemeriksaan mata yang dipilih berhasil dihapus', 'success', 'Berhasil!');
            } else {
                Fungsi::sweetalert('Beberapa data pemeriksaanta tidak dapat dihapus, yaitu: ' . (implode(', ', $notEligibleDeleteEyeExamination)), 'error', 'Gagal!');
            }
        }
        return back();
    }
}
