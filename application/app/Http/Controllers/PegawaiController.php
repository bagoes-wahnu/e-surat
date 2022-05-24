<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Models\Jabatan;
use App\Http\Models\Pegawai;
use App\Http\Models\SysConfig;
use App\Http\Models\SysRoleUser;

class PegawaiController extends Controller
{

    public function __construct(){
        $this->title = 'Pegawai';
        $this->menu = 'master';
        $this->submenu = 'pegawai';
    }    

    public function load_suggest(Request $request)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        if(!$request->ajax()){
            return $this->accessForbidden();
        }else{
            $search = $request->input('term');
            $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
            $search = preg_replace($pattern, '', $search);

            $id_jabatan = (empty($this->userdata('id_jabatan')))? '' : encText($this->userdata('id_jabatan').'jabatan', true);

            $total = Pegawai::json_grid(0, 1, $search, true, 'asc', 'pgw_nama', $id_jabatan);
            $get_pegawai = Pegawai::json_grid(0, $total, $search, false, 'asc', 'pgw_nama', $id_jabatan);

            if(!empty($get_jalan)){
                $responseCode = 200;
                $responseMessage = 'Data tersedia.';
                $responseData['pegawai'] = $get_pegawai;
            }else{
                $responseData['pegawai'] = [];
                $responseStatus = 'No Data Available';
                $responseMessage = 'Data Jalan tidak tersedia';
            }

            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
            return response()->json($response, $responseCode);
        }
    }
}