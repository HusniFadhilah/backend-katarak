<?php

namespace App\Http\Controllers;

use Exception;
use App\Libraries\Fungsi;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\{Job};

class JobController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                return $this->filterDataTable($request);
            }
            return view('jobs.index');
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

        $jobs = Job::query();
        if ($request->s_name)
            $jobs->where('name', 'LIKE', '%' . $request->s_name . '%');
        if ($search) {
            $jobs->orWhere('name', 'LIKE', '%' . $search . '%');
        }

        if ($column != null && $column != 0) {
            $jobs = $jobs->orderBy($columnsMember[$column], $dir);
        }

        $totalData = $jobs->count();
        $totalFiltered = $totalData;
        $jobs = $jobs->offset($start)->limit($limit)->latest()->get();

        $no = $start;
        foreach ($jobs as $job) {
            $no++;
            $row = array();
            $row['#'] = '<input type="checkbox" name="checked[]" class="check mr-2" value="' . $job->id . '">';
            $row['DT_RowIndex'] =  $no;
            $row['name'] = $job->name;
            $row['action'] = $this->getColumnAction($job);

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

    private function getColumnAction($job)
    {
        $btn = '<a href="' . route('job.edit', $job->id) . '"><button type="button" class="btn btn-warning btn-sm m-1 px-3" title="Edit pekerjaan"><span class="fa fa-edit"></span></button></a>';
        $btn .= '<a onclick="confirmDelete(\'/job/' . $job->id . '/destroy\',\'pekerjaan\')"><button type="button" class="btn btn-danger btn-sm m-1 px-3" title="Hapus pekerjaan"><span class="fa fa-trash"></span></button></a>';
        return $btn;
    }

    public function create()
    {
        return view('jobs.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'max:255'],
        ];
        $request->validate($rules);

        $attr = $request->all();
        $job = Job::create($attr);

        Fungsi::sweetalert('Pekerjaan berhasil ditambahkan', 'success', 'Berhasil!');
        return redirect(route('job'));
    }

    public function show(Job $job)
    {
        return view('jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        return view('jobs.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $rules = [
            'name' => ['required', 'max:255'],
        ];
        if (isset($request->password)) {
            $rules['password'] = $this->passwordRules(false);
        }
        $request->validate($rules);
        $attr = $request->all();
        $job->update($attr);

        Fungsi::sweetalert('Pekerjaan berhasil diupdate', 'success', 'Berhasil!');
        return redirect(route('job'));
    }

    public function destroy(Job $job)
    {
        $job->delete();
        Fungsi::sweetalert('Pekerjaan berhasil dihapus', 'success', 'Berhasil!');
        return back();
    }

    public function bulkDestroy(Request $request)
    {
        $checks = $request->checked;
        if (!isset($checks)) {
            Fungsi::sweetalert('Tidak ada pekerjaan yang dipilih', 'warning', 'Perhatian!');
        } else {
            $jobs = Job::whereIn('id', $checks)->get();
            $notEligibleDeleteJob = [];
            $status = true;
            foreach ($jobs as $job) {
                // $status = false;
                array_push($notEligibleDeleteJob, $job->name);
            }
            if ($status) {
                foreach ($jobs as $job) {
                    $job->delete();
                }
                Fungsi::sweetalert('Pekerjaan yang dipilih berhasil dihapus', 'success', 'Berhasil!');
            } else {
                Fungsi::sweetalert('Beberapa pekerjaan tidak dapat dihapus, yaitu: ' . (implode(', ', $notEligibleDeleteJob)), 'error', 'Gagal!');
            }
        }
        return back();
    }
}
