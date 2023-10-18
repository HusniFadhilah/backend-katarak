<?php

namespace App\Http\Controllers;

use Exception;
use App\Libraries\Fungsi;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\{EyeDisorder};

class EyeDisorderController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                return $this->filterDataTable($request);
            }
            return view('eye-disorders.index');
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

        $eyeDisorders = EyeDisorder::query();
        if ($request->s_name)
            $eyeDisorders->where('name', 'LIKE', '%' . $request->s_name . '%');
        if ($search) {
            $eyeDisorders->orWhere('name', 'LIKE', '%' . $search . '%');
        }

        if ($column != null && $column != 0) {
            $eyeDisorders = $eyeDisorders->orderBy($columnsMember[$column], $dir);
        }

        $totalData = $eyeDisorders->count();
        $totalFiltered = $totalData;
        $eyeDisorders = $eyeDisorders->offset($start)->limit($limit)->latest()->get();

        $no = $start;
        foreach ($eyeDisorders as $eyeDisorder) {
            $no++;
            $row = array();
            $row['#'] = '<input type="checkbox" name="checked[]" class="check mr-2" value="' . $eyeDisorder->id . '">';
            $row['DT_RowIndex'] =  $no;
            $row['name'] = $eyeDisorder->name;
            $row['action'] = $this->getColumnAction($eyeDisorder);

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

    private function getColumnAction($eyeDisorder)
    {
        $btn = '<a href="' . route('eye-disorder.edit', $eyeDisorder->id) . '"><button type="button" class="btn btn-warning btn-sm m-1 px-3" title="Edit data keluhan mata"><span class="fa fa-edit"></span></button></a>';
        $btn .= '<a onclick="confirmDelete(\'/eye-disorder/' . $eyeDisorder->id . '/destroy\',\'Keluhan di mata\')"><button type="button" class="btn btn-danger btn-sm m-1 px-3" title="Hapus data keluhan mata"><span class="fa fa-trash"></span></button></a>';
        return $btn;
    }

    public function create()
    {
        return view('eye-disorders.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'max:255'],
        ];
        $request->validate($rules);

        $attr = $request->all();
        $eyeDisorder = EyeDisorder::create($attr);

        Fungsi::sweetalert('Data keluhan di mata berhasil ditambahkan', 'success', 'Berhasil!');
        return redirect(route('eye-disorder'));
    }

    public function show(EyeDisorder $eyeDisorder)
    {
        return view('eye-disorders.show', compact('eyeDisorder'));
    }

    public function edit(EyeDisorder $eyeDisorder)
    {
        return view('eye-disorders.edit', compact('eyeDisorder'));
    }

    public function update(Request $request, EyeDisorder $eyeDisorder)
    {
        $rules = [
            'name' => ['required', 'max:255'],
        ];
        if (isset($request->password)) {
            $rules['password'] = $this->passwordRules(false);
        }
        $request->validate($rules);
        $attr = $request->all();
        $eyeDisorder->update($attr);

        Fungsi::sweetalert('Data keluhan di mata berhasil diupdate', 'success', 'Berhasil!');
        return redirect(route('eye-disorder'));
    }

    public function destroy(EyeDisorder $eyeDisorder)
    {
        $eyeDisorder->delete();
        Fungsi::sweetalert('Data keluhan di mata berhasil dihapus', 'success', 'Berhasil!');
        return back();
    }

    public function bulkDestroy(Request $request)
    {
        $checks = $request->checked;
        if (!isset($checks)) {
            Fungsi::sweetalert('Tidak ada data keluhan mata yang dipilih', 'warning', 'Perhatian!');
        } else {
            $eyeDisorders = EyeDisorder::whereIn('id', $checks)->get();
            $notEligibleDeleteEyeDisorder = [];
            $status = true;
            foreach ($eyeDisorders as $eyeDisorder) {
                // $status = false;
                array_push($notEligibleDeleteEyeDisorder, $eyeDisorder->name);
            }
            if ($status) {
                foreach ($eyeDisorders as $eyeDisorder) {
                    $eyeDisorder->delete();
                }
                Fungsi::sweetalert('Data keluhan di mata yang dipilih berhasil dihapus', 'success', 'Berhasil!');
            } else {
                Fungsi::sweetalert('Beberapa data keluhan mata tidak dapat dihapus, yaitu: ' . (implode(', ', $notEligibleDeleteEyeDisorder)), 'error', 'Gagal!');
            }
        }
        return back();
    }
}
