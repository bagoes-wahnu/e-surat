<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

use App\Http\Models\Jabatan;
use App\Http\Models\JabatanLevel;
use App\Http\Models\Pegawai;
use App\Http\Models\Surat;
use App\Http\Models\SuratHistory;
use App\Http\Models\SuratMasuk;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        date_default_timezone_set("Asia/Jakarta");
    }

    public $title = '';
    public $menu = '';
    public $submenu = '';

    public function pageNotFound()
    {
        return response()->view('errors.404', [], 404);
    }

    public function accessForbidden()
    {
        return response()->view('errors.403', [], 403);
    }

    public function userdata($param = '')
    {
        $result = false;
        $sesi = Auth::user();

        if ($sesi) {
            $sesi = json_encode($sesi);
            $sesi = json_decode($sesi);

            $id_atasan = '';
            $uptd = false;
            if (!empty($sesi->usr_jbt_id)) {
                $get_jabatan = Jabatan::get_data(encText($sesi->usr_jbt_id . 'jabatan', true), false);

                if (!empty($get_jabatan)) {
                    $uptd = $get_jabatan->uptd;

                    if (!empty($get_jabatan->id_atasan)) {
                        $id_atasan = $get_jabatan->id_atasan;
                    }
                }
            }

            $user = [
                'id_user' => $sesi->usr_id,
                'nama' => $sesi->usr_name,
                'username' => $sesi->usr_username,
                'email' => $sesi->usr_email,
                'role' => $sesi->usr_role,
                'aktif' => $sesi->usr_aktif,
                'grade' => $sesi->usr_grade,
                'id_jabatan' => $sesi->usr_jbt_id,
                'id_atasan' => $id_atasan,
                'uptd' => $uptd
            ];

            if (!empty($param)) {
                $key = array($param);

                for ($i = 0; $i < count($key); $i++) {
                    if (array_key_exists($key[$i], $user)) {
                        $temp_key = $key[$i];
                        $result = $user[$temp_key];
                    }
                }
            } else {
                $result = $user;
            }

            $data = ['user' => $user, 'param' => $param, 'result' => $result];
        }

        return $result;
    }

    public function lastGrade()
    {
        $get_data = JabatanLevel::get_data(false, false, true, 'DESC', 'jbl_level');

        return $get_data->level;
    }

    public function validationMessage()
    {
        $error_message = [
            'required' => 'Form :attribute wajib diisi!',
            'numeric' => 'Nilai :attribute harus berupa angka!',
            'min' => 'Nilai minimal :attribute adalah :min!',
        ];

        return $error_message;
    }

    public function reverseId($category = '', $input = '')
    {
        $result = false;

        if ($input === null) {
            return NULL;
        }

        switch ($category) {
            case 'surat':
                $get_data = Surat::get_data($input, false);
                $result = ($get_data) ? $get_data->id_surat : NULL;
                break;

            case 'surat_masuk':
                $get_data = SuratMasuk::get_data($input, false);
                $result = ($get_data) ? $get_data->id_surat_masuk : NULL;
                break;

            case 'surat_history':
                $get_data = SuratHistory::get_data($input, false);
                $result = ($get_data) ? $get_data->id_surat_history : NULL;
                break;

            default:
                $result = false;
                break;
        }
        return $result;
    }
}
