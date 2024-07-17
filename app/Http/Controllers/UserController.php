<?php

namespace App\Http\Controllers;

use Exception;
use App\Libraries\Date;
use App\Libraries\Fungsi;
use App\Models\{Role, User};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Auth, Hash};
use App\Actions\Fortify\PasswordValidationRules;

class UserController extends Controller
{
    use PasswordValidationRules;

    public function test()
    {
        // $response = \Illuminate\Support\Facades\Http::withHeaders([
        //     'Content-Type' => 'application/json',
        // ])->post('https://qpix.doltinuku.id/api/register', ['name' => 'test'])->json();
        $users = User::whereRoleId(2)->get();
        // $users = User::where('id', '<>', 0)->get();
        $data = [
            'route' => '/eye-examination/show',
            'arguments' => [
                'id' => 20,
            ],
        ];
        $response = Fungsi::sendNotification(true, $users, 'Data pemeriksaan pasien berhasil ditambahkan', 'Silahkan lakukan verifikasi dan berikan catatan tentang hasil pemeriksaan pasien', $data);
        return $response;
        // $token = [];
        // foreach (User::all() as $user) {
        //     array_push($token, $user->tokens()->get());
        // }
        // dd($token);
    }

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
            $row['role'] = Fungsi::getRoleTextUser($user);
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
        $creator = 'Dibuat pada: ' . Date::tglDefault($user->created_at) . ' pukul ' . Date::pukul($user->created_at);
        $btn = '<a type="button" class="btn btn-dark btn-sm m-1 px-3" data-toggle="popover" tabindex="0" data-trigger="focus" title="Tentang user ini" data-bs-html="true" data-html="true" data-bs-content="' . $creator . '" data-content="' . $creator . '"><span class="fa fa-info-circle"></span></a>';
        if ($user->is_verified)
            $btn .= '<a onclick="confirmAction(\'/user/' . $user->id . '/reject\',\'Akun ini akan ditolak/dibatalkan verifikasinya\')"><button type="button" class="btn btn-secondary btn-sm m-1 px-3" title="Tolak user"><span class="fa fa-times"></span></button></a>';
        if (!$user->is_verified)
            $btn .= '<a onclick="confirmAction(\'/user/' . $user->id . '/verify\',\'Akun ini akan disetujui/diverifikasi\')"><button type="button" class="btn btn-success btn-sm m-1 px-3" title="Verifikasi user"><span class="fa fa-check"></span></button></a>';
        $btn .= '<a href="' . route('user.edit', $user->id) . '"><button type="button" class="btn btn-warning btn-sm m-1 px-3" title="Edit user"><span class="fa fa-edit"></span></button></a>';
        $btn .= '<a onclick="confirmDelete(\'/user/' . $user->id . '/destroy\',\'user\')"><button type="button" class="btn btn-danger btn-sm m-1 px-3" title="Hapus user"><span class="fa fa-trash"></span></button></a>';
        return $btn;
    }

    public function create()
    {
        return view('users.create');
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
        return view('users.edit', compact('user'));
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

    public function reject(User $user)
    {
        $user->update(['is_verified' => 0]);
        // Show success message using Fungsi::sweetalert
        Fungsi::sweetalert('User berhasil ditolak', 'success', 'Berhasil!');
        // Redirect back
        return back();
    }

    public function verify(User $user)
    {
        $user->update(['is_verified' => 1]);
        // Show success message using Fungsi::sweetalert
        Fungsi::sweetalert('User berhasil diverifikasi', 'success', 'Berhasil!');
        // Redirect back
        return back();
    }

    /**
     * Bulk update users to passive status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkPassive(Request $request)
    {
        // Get the checked users
        $checks = $request->checked;

        // Check if any users are selected
        if (!isset($checks)) {
            Fungsi::sweetalert('Tidak ada user yang dipilih', 'warning', 'Perhatian!');
        } else {
            // Get the users based on the checked IDs
            $users = User::whereIn('id', $checks)->get();

            // Update users to passive
            foreach ($users as $participant) {
                $participant->update(['is_active' => 0]);
            }

            Fungsi::sweetalert('User yang dipilih berhasil dinonaktifkan', 'success', 'Berhasil!');
        }

        // Redirect back
        return back();
    }

    /**
     * Bulk update users to active status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkActive(Request $request)
    {
        // Get the checked users
        $checks = $request->checked;

        // Check if any users are selected
        if (!isset($checks)) {
            Fungsi::sweetalert('Tidak ada user yang dipilih', 'warning', 'Perhatian!');
        } else {
            // Get the users based on the checked IDs
            $users = User::whereIn('id', $checks)->get();

            // Update users to active
            foreach ($users as $participant) {
                $participant->update(['is_active' => 1]);
            }

            Fungsi::sweetalert('User yang dipilih berhasil diaktifkan', 'success', 'Berhasil!');
        }

        // Redirect back
        return back();
    }

    /**
     * Bulk cancel verification for users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkCancel(Request $request)
    {
        // Get the checked users
        $checks = $request->checked;

        // Check if any users are selected
        if (!isset($checks)) {
            Fungsi::sweetalert('Tidak ada user yang dipilih', 'warning', 'Perhatian!');
        } else {
            // Get the users based on the checked IDs
            $users = User::whereIn('id', $checks)->get();

            // Update users to inactive and unverified
            foreach ($users as $participant) {
                $participant->update(['is_active' => 0, 'is_verified' => 0]);
            }

            Fungsi::sweetalert('User yang dipilih berhasil dibatalkan verifikasinya', 'success', 'Berhasil!');
        }

        // Redirect back
        return back();
    }

    /**
     * Bulk verify users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkVerify(Request $request)
    {
        // Get the checked users
        $checks = $request->checked;

        // Check if any users are selected
        if (!isset($checks)) {
            Fungsi::sweetalert('Tidak ada user yang dipilih', 'warning', 'Perhatian!');
        } else {
            // Get the users based on the checked IDs
            $users = User::whereIn('id', $checks)->get();

            // Update users to active and verified
            foreach ($users as $participant) {
                $participant->update(['is_active' => 1, 'is_verified' => 1]);
            }

            Fungsi::sweetalert('User yang dipilih berhasil diverifikasi', 'success', 'Berhasil!');
        }

        // Redirect back
        return back();
    }
}
