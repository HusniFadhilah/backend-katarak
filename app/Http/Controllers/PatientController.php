<?php

namespace App\Http\Controllers;

use Exception;
use App\Libraries\Fungsi;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\{Role, Patient};

class PatientController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                return $this->filterDataTable($request);
            }
            return view('patients.index');
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

        $patients = Patient::with(['job:id,name']);
        if ($request->s_name)
            $patients->where('name', 'LIKE', '%' . $request->s_name . '%');
        if ($search) {
            $patients->orWhere('name', 'LIKE', '%' . $search . '%');
        }

        if ($column != null && $column != 0) {
            $patients = $patients->orderBy($columnsMember[$column], $dir);
        }

        $totalData = $patients->count();
        $totalFiltered = $totalData;
        $patients = $patients->offset($start)->limit($limit)->latest()->get();

        $no = $start;
        foreach ($patients as $patient) {
            $no++;
            $row = array();
            $row['#'] = '<input type="checkbox" name="checked[]" class="check mr-2" value="' . $patient->id . '">';
            $row['DT_RowIndex'] =  $no;
            $row['name'] = $patient->name;
            $row['action'] = $this->getColumnAction($patient);

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

    private function getColumnAction($patient)
    {
        $btn = '<a href="' . route('pasien.edit', $patient->id) . '"><button type="button" class="btn btn-warning btn-sm m-1 px-3" title="Edit pasien"><span class="fa fa-edit"></span></button></a>';
        $btn .= '<a onclick="confirmDelete(\'/pasien/' . $patient->id . '/destroy\',\'pasien\')"><button type="button" class="btn btn-danger btn-sm m-1 px-3" title="Hapus pasien"><span class="fa fa-trash"></span></button></a>';
        return $btn;
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'max:255'],
        ];
        $request->validate($rules);

        $attr = $request->all();
        $patient = Patient::create($attr);

        Fungsi::sweetalert('Pasien berhasil ditambahkan', 'success', 'Berhasil!');
        return redirect(route('patient'));
    }

    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $rules = [
            'name' => ['required', 'max:255']
        ];
        if (isset($request->password)) {
            $rules['password'] = $this->passwordRules(false);
        }
        $request->validate($rules);
        $attr = $request->all();
        $patient->update($attr);

        Fungsi::sweetalert('Pasien berhasil diupdate', 'success', 'Berhasil!');
        return redirect(route('patient'));
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        Fungsi::sweetalert('Pasien berhasil dihapus', 'success', 'Berhasil!');
        return back();
    }

    public function bulkDestroy(Request $request)
    {
        $checks = $request->checked;
        if (!isset($checks)) {
            Fungsi::sweetalert('Tidak ada pasien yang dipilih', 'warning', 'Perhatian!');
        } else {
            $patients = Patient::whereIn('id', $checks)->get();
            $notEligibleDeletePatient = [];
            $status = true;
            foreach ($patients as $patient) {
                // $status = false;
                array_push($notEligibleDeletePatient, $patient->name);
            }
            if ($status) {
                foreach ($patients as $patient) {
                    $patient->delete();
                }
                Fungsi::sweetalert('Pasien yang dipilih berhasil dihapus', 'success', 'Berhasil!');
            } else {
                Fungsi::sweetalert('Beberapa pasien tidak dapat dihapus, yaitu: ' . (implode(', ', $notEligibleDeletePatient)), 'error', 'Gagal!');
            }
        }
        return back();
    }
}
