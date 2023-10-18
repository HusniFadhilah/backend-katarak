<?php

namespace App\Http\Controllers;

use Exception;
use App\Libraries\Fungsi;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\{PastMedical};

class PastMedicalController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                return $this->filterDataTable($request);
            }
            return view('past-medicals.index');
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

        $pastMedicals = PastMedical::query();
        if ($request->s_name)
            $pastMedicals->where('name', 'LIKE', '%' . $request->s_name . '%');
        if ($search) {
            $pastMedicals->orWhere('name', 'LIKE', '%' . $search . '%');
        }

        if ($column != null && $column != 0) {
            $pastMedicals = $pastMedicals->orderBy($columnsMember[$column], $dir);
        }

        $totalData = $pastMedicals->count();
        $totalFiltered = $totalData;
        $pastMedicals = $pastMedicals->offset($start)->limit($limit)->latest()->get();

        $no = $start;
        foreach ($pastMedicals as $pastMedical) {
            $no++;
            $row = array();
            $row['#'] = '<input type="checkbox" name="checked[]" class="check mr-2" value="' . $pastMedical->id . '">';
            $row['DT_RowIndex'] =  $no;
            $row['name'] = $pastMedical->name;
            $row['action'] = $this->getColumnAction($pastMedical);

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

    private function getColumnAction($pastMedical)
    {
        $btn = '<a href="' . route('past-medical.edit', $pastMedical->id) . '"><button type="button" class="btn btn-warning btn-sm m-1 px-3" title="Edit riwayat penyakit"><span class="fa fa-edit"></span></button></a>';
        $btn .= '<a onclick="confirmDelete(\'/past-medical/' . $pastMedical->id . '/destroy\',\'riwayat penyakit\')"><button type="button" class="btn btn-danger btn-sm m-1 px-3" title="Hapus riwayat penyakit"><span class="fa fa-trash"></span></button></a>';
        return $btn;
    }

    public function create()
    {
        return view('past-medicals.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'max:255'],
        ];
        $request->validate($rules);

        $attr = $request->all();
        $pastMedical = PastMedical::create($attr);

        Fungsi::sweetalert('Riwayat penyakit berhasil ditambahkan', 'success', 'Berhasil!');
        return redirect(route('past-medical'));
    }

    public function show(PastMedical $pastMedical)
    {
        return view('past-medicals.show', compact('pastMedical'));
    }

    public function edit(PastMedical $pastMedical)
    {
        return view('past-medicals.edit', compact('pastMedical'));
    }

    public function update(Request $request, PastMedical $pastMedical)
    {
        $rules = [
            'name' => ['required', 'max:255'],
        ];
        if (isset($request->password)) {
            $rules['password'] = $this->passwordRules(false);
        }
        $request->validate($rules);
        $attr = $request->all();
        $pastMedical->update($attr);

        Fungsi::sweetalert('Riwayat penyakit berhasil diupdate', 'success', 'Berhasil!');
        return redirect(route('past-medical'));
    }

    public function destroy(PastMedical $pastMedical)
    {
        $pastMedical->delete();
        Fungsi::sweetalert('Riwayat penyakit berhasil dihapus', 'success', 'Berhasil!');
        return back();
    }

    public function bulkDestroy(Request $request)
    {
        $checks = $request->checked;
        if (!isset($checks)) {
            Fungsi::sweetalert('Tidak ada riwayat penyakit yang dipilih', 'warning', 'Perhatian!');
        } else {
            $pastMedicals = PastMedical::whereIn('id', $checks)->get();
            $notEligibleDeletePastMedical = [];
            $status = true;
            foreach ($pastMedicals as $pastMedical) {
                // $status = false;
                array_push($notEligibleDeletePastMedical, $pastMedical->name);
            }
            if ($status) {
                foreach ($pastMedicals as $pastMedical) {
                    $pastMedical->delete();
                }
                Fungsi::sweetalert('Riwayat penyakit yang dipilih berhasil dihapus', 'success', 'Berhasil!');
            } else {
                Fungsi::sweetalert('Beberapa riwayat penyakit tidak dapat dihapus, yaitu: ' . (implode(', ', $notEligibleDeletePastMedical)), 'error', 'Gagal!');
            }
        }
        return back();
    }
}
