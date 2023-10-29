<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\{EyeDisorder, Job, PastMedical, Patient, Role, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Crypt, Log};

class DependentDropdownController extends Controller
{
    public function getShowKTP(Request $request)
    {
        try {
            $id = $request->id;
            $patient = Patient::find($id);
            if (!$patient) {
                return response()->json(array('msg' => 'Pasien tidak ditemukan', 'status' => false, 'showKTP' => ''));
            }
            if ($patient->ktp)
                $showKTP = Crypt::decrypt($patient->ktp);
            return response()->json(array('msg' => 'Show KTP berhasil dilakukan', 'status' => true, 'showHiddenText' => $showKTP ?? ''));
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }

    public function getRoles(Request $request)
    {
        try {
            $name = $request->name;
            $roles = Role::query();
            if ($name)
                $roles->where('alias', 'LIKE', '%' . $name . '%');

            $roles = $roles->pluck('id', 'alias');

            return response()->json($roles);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }

    public function getJobs(Request $request)
    {
        try {
            $name = $request->name;
            $jobs = Job::query();
            if ($name)
                $jobs->where('name', 'LIKE', '%' . $name . '%');

            $jobs = $jobs->pluck('id', 'name');

            return response()->json($jobs);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }

    public function getEyeDisorders(Request $request)
    {
        try {
            $name = $request->name;
            $eyeDisorder = EyeDisorder::query();
            if ($name)
                $eyeDisorder->where('name', 'LIKE', '%' . $name . '%');

            $eyeDisorder = $eyeDisorder->pluck('id', 'name');

            return response()->json($eyeDisorder);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }

    public function getPastMedicals(Request $request)
    {
        try {
            $name = $request->name;
            $pastMedical = PastMedical::query();
            if ($name)
                $pastMedical->where('name', 'LIKE', '%' . $name . '%');

            $pastMedical = $pastMedical->pluck('id', 'name');

            return response()->json($pastMedical);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }

    public function getPatients(Request $request)
    {
        try {
            $name = $request->name;
            $patients = Patient::query();
            if ($name)
                $patients->where('name', 'LIKE', '%' . $name . '%');

            $patients = $patients->pluck('id', 'name');

            return response()->json($patients);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }

    public function getDoctors(Request $request)
    {
        try {
            $name = $request->name;
            $doctors = User::whereRoleId(2);
            if ($name)
                $doctors->where('name', 'LIKE', '%' . $name . '%');

            $doctors = $doctors->pluck('id', 'name');

            return response()->json($doctors);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }

    public function getKaders(Request $request)
    {
        try {
            $name = $request->name;
            $kaders = User::whereRoleId(3);
            if ($name)
                $kaders->where('name', 'LIKE', '%' . $name . '%');

            $kaders = $kaders->pluck('id', 'name');

            return response()->json($kaders);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            $arr = array('msg' => 'Terjadi kegagalan. Error: ' . $error->getMessage(), 'status' => false);
            return response()->json($arr);
        }
    }
}
