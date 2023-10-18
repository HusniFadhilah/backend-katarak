<?php

namespace App\Http\Controllers;

use Exception;
use App\Libraries\Fungsi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Auth, Hash};
use App\Actions\Fortify\PasswordValidationRules;
use App\Models\{Role, User};

class UserController extends Controller
{
    use PasswordValidationRules;

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                return $this->filterDataTable($request);
            }
            return view('users.index');
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
            3 => 'email',
            4 => 'role_id',
        );
        $data = array();
        $limit = $request->input('length');
        $start = $request->input('start', 0);
        $search = $request->input('search.value');
        $column = $request->input('order.0.column');
        $dir = $request->input('order.0.dir', 'asc');

        $users = User::with(['role:id,alias']);
        if ($request->s_name)
            $users->where('name', 'LIKE', '%' . $request->s_name . '%');
        if ($request->s_email)
            $users->where('email', 'LIKE', '%' . $request->s_email . '%');
        if ($request->s_roles)
            $users->whereIn('role_id', $request->s_roles);
        if ($search) {
            $users->orWhere('name', 'LIKE', '%' . $search . '%');
            $users->orWhere('email', 'LIKE', '%' . $search . '%');
            $users->orWhereHas('role', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($column != null && $column != 0) {
            $users = $users->orderBy($columnsMember[$column], $dir);
        }

        $totalData = $users->count();
        $totalFiltered = $totalData;
        $users = $users->offset($start)->limit($limit)->latest()->get();

        $no = $start;
        foreach ($users as $user) {
            $no++;
            $row = array();
            $row['#'] = '<input type="checkbox" name="checked[]" class="check mr-2" value="' . $user->id . '">';
            $row['DT_RowIndex'] =  $no;
            $row['name'] = $user->name;
            $row['email'] = $user->email;
            $row['role'] = '<span class="badge badge-sm badge-dark">' . $user->role->alias . '</span>';
            $row['action'] = $this->getColumnAction($user);

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

    private function getColumnAction($user)
    {
        $btn = '<a href="' . route('user.edit', $user->id) . '"><button type="button" class="btn btn-warning btn-sm m-1 px-3" title="Edit user"><span class="fa fa-edit"></span></button></a>';
        $btn .= '<a onclick="confirmDelete(\'/user/' . $user->id . '/destroy\',\'user\')"><button type="button" class="btn btn-danger btn-sm m-1 px-3" title="Hapus user"><span class="fa fa-trash"></span></button></a>';
        return $btn;
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $rules = [
            'role_id' => 'required',
            'name' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ];
        $request->validate($rules);

        $attr = $request->all();
        $attr['password'] = Hash::make(request('password'));

        $user = User::create($attr);

        Fungsi::sweetalert('User berhasil ditambahkan', 'success', 'Berhasil!');
        return redirect(route('user'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];
        if (isset($request->password)) {
            $rules['password'] = $this->passwordRules(false);
        }
        $request->validate($rules);
        $attr = $request->all();
        $attr['password'] =  isset($request->password) ? Hash::make(request('password')) : $user->password;

        $user->update($attr);

        Fungsi::sweetalert('User berhasil diupdate', 'success', 'Berhasil!');
        return redirect(route('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        Fungsi::sweetalert('User berhasil dihapus', 'success', 'Berhasil!');
        return back();
    }

    public function bulkDestroy(Request $request)
    {
        $checks = $request->checked;
        if (!isset($checks)) {
            Fungsi::sweetalert('Tidak ada user yang dipilih', 'warning', 'Perhatian!');
        } else {
            $users = User::whereIn('id', $checks)->get();
            $notEligibleDeleteUser = [];
            $status = true;
            foreach ($users as $user) {
                // $status = false;
                array_push($notEligibleDeleteUser, $user->name);
            }
            if ($status) {
                foreach ($users as $user) {
                    $user->delete();
                }
                Fungsi::sweetalert('User yang dipilih berhasil dihapus', 'success', 'Berhasil!');
            } else {
                Fungsi::sweetalert('Beberapa user tidak dapat dihapus, yaitu: ' . (implode(', ', $notEligibleDeleteUser)), 'error', 'Gagal!');
            }
        }
        return back();
    }
}
