<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Http\Models\User;
use App\Http\Models\Jabatan;
use App\Http\Models\JabatanLevel;
use App\Http\Models\Pegawai;
use App\Http\Models\SysConfig;
use App\Http\Models\TandaTangan;

class BridgeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function collectJabatan(Request $request)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];
        $responseNote = [];

        $config_esdm = SysConfig::get_data('url_esdm');
        $url_esdm = (!empty($config_esdm->value))? $config_esdm->value : false;

        $config_level = SysConfig::get_data('api_level');
        $url_level = (!empty($config_level->value))? $config_level->value : false;

        $config_jabatan = SysConfig::get_data('api_jabatan');
        $url_jabatan = (!empty($config_jabatan->value))? $config_jabatan->value : false;

        $pwd_config = SysConfig::get_data('default_password');
        $default_password = bcrypt($pwd_config->value);

        if(!$url_esdm || !$url_level || !$url_jabatan){
            $responseCode = 400;
            $responseMessage = 'URL target wajib diisi!';
        }elseif(empty($pwd_config)){
            $responseCode = 400;
            $responseMessage = 'Default Password wajib diisi!';
        }else{
            $errorCounter=0;
            $current_time = date('Y-m-d H:i:s');

            /* start import level */
            $list_level = file_get_contents($url_esdm.$url_level);

            if(!$list_level){
                $errorCounter++;
            }else{
                $decoded = json_decode($list_level);
                $data_level = $decoded->data;
                $arr_id_level = [];

                DB::table('jabatan_level')->whereNull('deleted_at')->update(['deleted_by' => 0, 'deleted_at' => $current_time]);

                $no=0;
                foreach ($data_level as $key => $value) {
                    $no++;
                    $arr_id_level[] = $value->id_level;

                    $level = (empty($value->level))? 'NULL' : $value->level;
                    $nama = (empty($value->nama))? 'NULL' : $value->nama;

                    $query = 'INSERT INTO "jabatan_level" (jbl_id, jbl_nama, jbl_level, created_by, created_at) VALUES ('.$value->id_level.', \''.$nama.'\', '.$level.', 0, \''.$current_time.'\') ON CONFLICT (jbl_id) DO UPDATE SET jbl_nama = \''.$nama.'\',  jbl_level = '.$level.', updated_by = 0, updated_at = \''.$current_time.'\';';

                    DB::connection()->getPdo()->exec($query);

                    DB::table('jabatan_level')->where('jbl_id', $value->id_level)->update(['deleted_by' => NULL, 'deleted_at' => NULL]);
                }

                /*DB::table('jabatan_level')->whereNotIn('jbl_id', $arr_id_level)->whereNull('deleted_at')->update(['deleted_by' => 0, 'deleted_at' => $current_time]);

                DB::table('jabatan_level')->whereIn('jbl_id', $arr_id_level)->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);*/
            }
            /* end import level */

            /* start import jabatan */
            $jabatan = file_get_contents($url_esdm.$url_jabatan);

            if(!$jabatan){
                $errorCounter++;
            }else{
                $decoded = json_decode($jabatan);
                $data_jabatan = $decoded->data;
                $arr_id_jabatan = [];
                $arr_id_user = [];

                DB::table('jabatan')->whereNull('deleted_at')->update(['deleted_by' => 0, 'deleted_at' => $current_time]);

                $no=0;
                foreach ($data_jabatan as $key => $value) {
                    $no++;
                    $arr_id_jabatan[] = $value->id_jabatan;

                    $induk = (empty($value->induk))? 'NULL' : $value->induk;
                    $urutan = (empty($value->urutan))? 'NULL' : $value->urutan;
                    $level = (empty($value->level))? 'NULL' : $value->level;
                    $slug = $value->slug;

                    $query = 'INSERT INTO "jabatan" (jbt_id, jbt_nama, jbt_induk, jbt_urutan, jbt_jbl_id, jbt_slug, created_by, created_at) VALUES ('.$value->id_jabatan.', \''.trim($value->jabatan).'\', '.$induk.', '.$urutan.', '.$level.', \''.$slug.'\',  0, \''.$current_time.'\') ON CONFLICT (jbt_id) DO UPDATE SET jbt_nama = \''.trim($value->jabatan).'\',  jbt_induk = '.$induk.',  jbt_urutan = '.$urutan.', jbt_jbl_id = '.$level.', jbt_slug = \''.$slug.'\', updated_by = 0, updated_at = \''.$current_time.'\';';

                    DB::connection()->getPdo()->exec($query);

                    DB::table('jabatan')->where('jbt_id', $value->id_jabatan)->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);

                    DB::table('user')->where('usr_jbt_id', $value->id_jabatan)->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);

                    /* start : user */
                    $username = trim($slug);
                    $id_jabatan = $value->id_jabatan;
                    if($this->cek_userjabatan($username, $id_jabatan) == true){
                        $get_user = User::where('usr_jbt_id', $id_jabatan)->first();
                        $get_level = JabatanLevel::get_data(encText($level.'level', true));
                        // $get_jabatan = Jabatan::get_data(encText($id_jabatan.'jabatan', true));

                        $grade = null;
                        if(!empty($get_level)){
                            $grade = $get_level->level;
                        }

                        if($grade == 1){
                            $role = 2;
                        }elseif($grade == 2){
                            $role = 3;
                        }else{
                            $role = 4;
                        }

                        if(empty($get_user)){
                            $m_user = new User();

                            $m_user->usr_username = $username;
                            $m_user->usr_password = $default_password;
                            $m_user->usr_jbt_id = $id_jabatan;
                            $m_user->usr_role = $role;
                            $m_user->created_by = $this->userdata('id_user');
                            $m_user->usr_name = $value->jabatan;
                            $m_user->usr_token_limits = $value->limit;
                            $m_user->usr_grade = $grade;
                            $m_user->save();
                        }else{
                            $m_user = User::find($get_user->usr_id);
                            $m_user->usr_username = $username;
                            $m_user->usr_role = $role;
                            $m_user->updated_by = $this->userdata('id_user');
                            $m_user->usr_name = $value->jabatan;
                            $m_user->usr_token_limits = $value->limit;
                            $m_user->usr_grade = $grade;
                            $m_user->save();
                        }

                        $arr_id_user[] = $m_user->usr_id;

                        if($grade == 1){
                            TandaTangan::where('ttd_id', 1)->update(['ttd_jbt_id' => $id_jabatan]);
                        }elseif($grade == 2){
                            TandaTangan::where('ttd_id', 2)->update(['ttd_jbt_id' => $id_jabatan]);
                        }
                    }
                    /* end : user */
                }

                // DB::table('jabatan')->whereNotIn('jbt_id', $arr_id_jabatan)->whereNull('deleted_at')->update(['deleted_by' => 0, 'deleted_at' => $current_time]);

                // DB::table('jabatan')->whereIn('jbt_id', $arr_id_jabatan)->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);

                DB::table('user')->whereNotIn('usr_id', $arr_id_user)->whereNotNull('usr_jbt_id')->whereNull('deleted_at')->update(['deleted_by' => 0, 'deleted_at' => $current_time]);

                // DB::table('user')->whereIn('usr_id', $arr_id_user)->whereNotNull('usr_jbt_id')->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);
            }
            /* end import jabatan */

            if($errorCounter > 0){
                $responseCode = 400;
                $responseMessage = 'Error Connection!';
            }else{
                $responseCode = 200;
                $responseData['completed'] = $no;
            }
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus, $responseNote);
        return response()->json($response, $responseCode);
    }

    public function updateJabatan(Request $request)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];
        $responseNote = [];

        $rules['ID'] = 'required';
        $rules['JABATAN'] = 'required';
        $rules['LEVEL'] = 'required';
        $rules['URUTAN'] = 'required';
        $rules['LIMIT'] = 'required';
        $rules['SLUG'] = 'required';

        $validator = Validator::make($request->all(), $rules, $this->validationMessage());

        if($validator->fails()){
            $responseCode = 400;
            $responseStatus = 'Missing Param';
            $responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
            $responseData['error_log'] = $validator->errors();
        }else{
            $pwd_config = SysConfig::get_data('default_password');
            $default_password = bcrypt($pwd_config->value);

            $errorCounter=0;
            $current_time = date('Y-m-d H:i:s');

            /* start import jabatan */
            $id_jabatan = $request->input('ID');
            $jabatan = $request->input('JABATAN');

            $induk = (empty($request->input('INDUK')))? 'NULL' : $request->input('INDUK');
            $urutan = (empty($request->input('URUTAN')))? 'NULL' : $request->input('URUTAN');
            $level = (empty($request->input('LEVEL')))? 'NULL' : $request->input('LEVEL');
            $slug = $request->input('SLUG');
            $limit = $request->input('LIMIT');

            $uptd = ($request->input('UPTD') == 1)? 't' : 'f';
            $aktif = ($request->input('STATUS') == 1)? 't' : 'f';

            $query = 'INSERT INTO "jabatan" (jbt_id, jbt_nama, jbt_induk, jbt_urutan, jbt_jbl_id, jbt_slug, jbt_aktif, jbt_uptd, created_by, created_at) VALUES ('.$id_jabatan.', \''.trim($jabatan).'\', '.$induk.', '.$urutan.', '.$level.', \''.$slug.'\', \''.$aktif.'\', \''.$uptd.'\', 0, \''.$current_time.'\') ON CONFLICT (jbt_id) DO UPDATE SET jbt_nama = \''.trim($jabatan).'\',  jbt_induk = '.$induk.',  jbt_urutan = '.$urutan.', jbt_jbl_id = '.$level.', jbt_slug = \''.$slug.'\', updated_by = 0, jbt_aktif = \''.$aktif.'\', jbt_uptd = \''.$uptd.'\', updated_at = \''.$current_time.'\';';

            DB::connection()->getPdo()->exec($query);

            /* start : user */
            $username = trim($slug);
            if($this->cek_userjabatan($username, $id_jabatan) == true){
                $get_user = User::where('usr_jbt_id', $id_jabatan)->first();
                $get_level = JabatanLevel::get_data(encText($level.'level', true));
                // $get_jabatan = Jabatan::get_data(encText($id_jabatan.'jabatan', true));

                $grade = null;
                if(!empty($get_level)){
                    $grade = $get_level->level;
                }

                if($grade == 1){
                    $role = 2;
                }elseif($grade == 2){
                    $role = 3;
                }else{
                    $role = 4;
                }

                if(empty($get_user)){
                    $m_user = new User();

                    $m_user->usr_username = $username;
                    $m_user->usr_password = $default_password;
                    $m_user->usr_jbt_id = $id_jabatan;
                    $m_user->usr_role = $role;
                    $m_user->created_by = $this->userdata('id_user');
                    $m_user->usr_name = $jabatan;
                    $m_user->usr_token_limits = $limit;
                    $m_user->usr_grade = $grade;
                    $m_user->usr_aktif = $aktif;
                    $m_user->save();
                }else{
                    $m_user = User::find($get_user->usr_id);
                    $m_user->usr_username = $username;
                    $m_user->usr_role = $role;
                    $m_user->updated_by = $this->userdata('id_user');
                    $m_user->usr_name = $jabatan;
                    $m_user->usr_token_limits = $limit;
                    $m_user->usr_grade = $grade;
                    $m_user->usr_aktif = $aktif;
                    $m_user->save();
                }

                $id_user = $m_user->usr_id;

                if($uptd == true){
                    if($grade == 1){
                        TandaTangan::where('ttd_id', 1)->update(['ttd_jbt_id' => $id_jabatan]);
                    }elseif($grade == 2){
                        TandaTangan::where('ttd_id', 2)->update(['ttd_jbt_id' => $id_jabatan]);
                    }
                }
            }
            /* end : user */

            DB::table('jabatan')->where('jbt_id', $id_jabatan)->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);

            DB::table('user')->where('usr_id', $id_user)->whereNotNull('usr_jbt_id')->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);
            /* end import jabatan */

            if($errorCounter > 0){
                $responseCode = 400;
                $responseMessage = 'Error Connection!';
            }else{
                $responseCode = 200;
            }
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus, $responseNote);
        return response()->json($response, $responseCode);
    }

    public function collectPegawai(Request $request)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];
        $responseNote = [];

        $config_esdm = SysConfig::get_data('url_esdm');
        $url_esdm = (!empty($config_esdm->value))? $config_esdm->value : false;

        $config = SysConfig::get_data('api_pegawai');
        $url_target = (!empty($config->value))? $config->value : false;

        if(!$url_esdm || !$url_target){
            $responseCode = 400;
            $responseMessage = 'URL target wajib diisi';
        }else{
            $pwd_config = SysConfig::get_data('default_password');
            $default_password = bcrypt($pwd_config->value);

            $pegawai = file_get_contents($url_esdm.$url_target);
            $decoded = json_decode($pegawai);
            $data_pegawai = $decoded->data->pegawai;
            // dd($data_pegawai);

            $current_time = date('Y-m-d H:i:s');

            $arr_data = [];
            $arr_id_pegawai = [];

            $no=0;
            foreach ($data_pegawai as $key => $value) {
                $no++;
                $id_pegawai = $value->id;
                $arr_id_pegawai[] = $id_pegawai;

                $arr_data[] = $id_pegawai;

                $id_jabatan = (empty($value->id_jabatan))? 'NULL' : '\''.$value->id_jabatan.'\'';
                $id_bidang = (empty($value->id_bidang))? 'NULL' : '\''.$value->id_bidang.'\'';
                $id_unit = (empty($value->unit_kerja))? 'NULL' : '\''.$value->unit_kerja.'\'';
                // dd($id_unit);
                $nip = (empty($value->nip))? 'NULL' : '\''.$value->nip.'\'';
                $nik = (empty($value->nik))? 'NULL' : '\''.protectInsertQuote($value->nik, true).'\'';
                $email = (empty($value->email))? 'NULL' : '\''.$value->email.'\'';
                $telp = (empty($value->telp))? 'NULL' : '\''.$value->telp.'\'';
                $tgl_pns = (empty($value->tgl_pns))? 'NULL' : '\''.$value->tgl_pns.'\'';
                $foto = (empty($value->foto))? 'NULL' : '\''.$value->foto.'\'';

                $query = 'INSERT INTO "pegawai" (pgw_id, pgw_unit_id, pgw_bidang_id, pgw_jbt_id, pgw_nama, pgw_nik, pgw_nip, pgw_email, pgw_gender, pgw_telp, pgw_foto, pgw_tanggal_pns, created_by, created_at) VALUES ('.$id_pegawai.', '.$id_unit.', '.$id_bidang.', '.$id_jabatan.', \''.protectInsertQuote($value->nama).'\', '.$nik.', '.$nip.', '.$email.', '.$value->jenis_kelamin.', '.$telp.', '.$foto.', '.$tgl_pns.', 0, \''.$current_time.'\') ON CONFLICT (pgw_id) DO UPDATE SET pgw_bidang_id = '.$id_bidang.', pgw_unit_id = '.$id_unit.', pgw_jbt_id = '.$id_jabatan.', pgw_nama = \''.protectInsertQuote($value->nama).'\', pgw_nik = '.$nik.', pgw_nip = '.$nip.', pgw_email = '.$email.', pgw_gender = '.$value->jenis_kelamin.', pgw_telp = '.$telp.', pgw_foto = '.$foto.', pgw_tanggal_pns = '.$tgl_pns.', updated_by = 0, updated_at = \''.$current_time.'\';';

                DB::connection()->getPdo()->exec($query);
            }

            DB::table('pegawai')->whereNotIn('pgw_id', $arr_id_pegawai)->whereNotNull('pgw_tanggal_pns')->whereNull('deleted_at')->update(['deleted_by' => 0, 'deleted_at' => $current_time]);

            DB::table('pegawai')->whereIn('pgw_id', $arr_id_pegawai)->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);

            $responseCode = 200;
            $responseData['completed'] = $no;
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus, $responseNote);
        return response()->json($response, $responseCode);
    }

    public function updatePegawai(Request $request)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];
        $responseNote = [];

        $rules['id'] = 'required';
        $rules['nama'] = 'required';
        $rules['telp'] = 'required';
        $rules['tgl_pns'] = 'required';

        $validator = Validator::make($request->all(), $rules, $this->validationMessage());

        if($validator->fails()){
            $responseCode = 400;
            $responseStatus = 'Missing Param';
            $responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
            $responseData['error_log'] = $validator->errors();
        }else{
            $pwd_config = SysConfig::get_data('default_password');
            $default_password = bcrypt($pwd_config->value);

            $errorCounter=0;
            $current_time = date('Y-m-d H:i:s');

            /* start import pegawai */
            $id_pegawai = $request->input('id');
            $nama = $request->input('nama');
            $id_jabatan = (empty($request->input('id_jabatan')))? 'NULL' : $request->input('id_jabatan');
            $id_unit = (empty($request->input('unit_kerja')))? 'NULL' : $request->input('unit_kerja');
            $id_bidang = (empty($request->input('id_bidang')))? 'NULL' : $request->input('id_bidang');
            $nip = '\''.protectInsertQuote(helpEmpty($request->input('nip'), 'NULL'), true).'\'';
            $nik = '\''.protectInsertQuote(helpEmpty($request->input('nik'), 'NULL'), true).'\'';
            $email = (empty($request->input('email')))? 'NULL' : '\''.$request->input('email').'\'';
            $email = str_replace('%40', '@', $email);
            $telp = (empty($request->input('telp')))? 'NULL' : '\''.$request->input('telp').'\'';
            $tgl_pns = (empty($request->input('tgl_pns')))? 'NULL' : '\''.$request->input('tgl_pns').'\'';
            $jenis_kelamin = $request->input('jenis_kelamin');
            $foto = (empty($request->input('foto')))? 'NULL' : '\''.$request->input('foto').'\'';

            $query = 'INSERT INTO "pegawai" (pgw_id, pgw_unit_id, pgw_bidang_id, pgw_jbt_id, pgw_nama, pgw_nik, pgw_nip, pgw_email, pgw_gender, pgw_telp, pgw_foto, pgw_tanggal_pns, created_by, created_at) VALUES ('.$id_pegawai.', '.$id_unit.', '.$id_bidang.', '.$id_jabatan.', \''.$nama.'\', '.$nik.', '.$nip.', '.$email.', '.$jenis_kelamin.', '.$telp.', '.$foto.', '.$tgl_pns.', 0, \''.$current_time.'\') ON CONFLICT (pgw_id) DO UPDATE SET pgw_bidang_id = '.$id_bidang.', pgw_unit_id = '.$id_unit.', pgw_jbt_id = '.$id_jabatan.', pgw_nama = \''.$nama.'\', pgw_nik = '.$nik.', pgw_nip = '.$nip.', pgw_email = '.$email.', pgw_gender = '.$jenis_kelamin.', pgw_telp = '.$telp.', pgw_foto = '.$foto.', pgw_tanggal_pns = '.$tgl_pns.', updated_by = 0, updated_at = \''.$current_time.'\';';

            DB::connection()->getPdo()->exec($query);

            DB::table('pegawai')->where('pgw_id', $id_pegawai)->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);
            /* end import pegawai */

            if($errorCounter > 0){
                $responseCode = 400;
                $responseMessage = 'Error Connection!';
            }else{
                $responseCode = 200;
            }
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus, $responseNote);
        return response()->json($response, $responseCode);
    }

    public function collectPegawaiKontrak(Request $request)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];
        $responseNote = [];

        $config_esdm = SysConfig::get_data('url_esdm');
        $url_esdm = (!empty($config_esdm->value))? $config_esdm->value : false;

        $config = SysConfig::get_data('api_pegawai_kontrak');
        $url_target = (!empty($config->value))? $config->value : false;

        if(!$url_esdm || !$url_target){
            $responseCode = 400;
            $responseMessage = 'URL target wajib diisi';
        }else{
            $pwd_config = SysConfig::get_data('default_password');
            $default_password = bcrypt($pwd_config->value);

            $pegawai = file_get_contents($url_esdm.$url_target);

            $decoded = json_decode($pegawai);
            $data_pegawai = $decoded->data->pegawai;
            // dd($data_pegawai);

            // echo '<pre>'; var_dump($data_pegawai); echo "</pre>"; die;

            $current_time = date('Y-m-d H:i:s');

            $arr_data = [];
            $arr_id_pegawai = [];

            $no=0;
            foreach ($data_pegawai as $key => $value) {
                $no++;
                $id_pegawai = $value->id;
                $arr_id_pegawai[] = $id_pegawai;

                $arr_data[] = $id_pegawai;

                $id_jabatan = (empty($value->id_jabatan))? 'NULL' : '\''.$value->id_jabatan.'\'';
                $id_bidang = (empty($value->ID_BIDANG))? 'NULL' : '\''.$value->ID_BIDANG.'\'';
                $id_unit = (empty($value->unit_kerja))? 'NULL' : '\''.$value->unit_kerja.'\'';
                $nip = (empty($value->nip))? 'NULL' : '\''.$value->nip.'\'';
                $nik = (empty($value->nik))? 'NULL' : '\''.protectInsertQuote($value->nik, true).'\'';
                $email = (empty($value->email))? 'NULL' : '\''.$value->email.'\'';
                $telp = (empty($value->telp))? 'NULL' : '\''.$value->telp.'\'';
                $tgl_pns = (empty($value->tgl_pns))? 'NULL' : '\''.$value->tgl_pns.'\'';
                $foto = (empty($value->foto))? 'NULL' : '\''.$value->foto.'\'';

                $query = 'INSERT INTO "pegawai" (pgw_id, pgw_unit_id, pgw_bidang_id, pgw_jbt_id, pgw_nama, pgw_nik, pgw_nip, pgw_email, pgw_gender, pgw_telp, pgw_foto, pgw_tanggal_pns, created_by, created_at) VALUES ('.$id_pegawai.', '.$id_unit.', '.$id_bidang.', '.$id_jabatan.', \''.protectInsertQuote($value->nama).'\', '.$nik.', '.$nip.', '.$email.', '.$value->JENIS_KELAMIN.', '.$telp.', '.$foto.', '.$tgl_pns.', 0, \''.$current_time.'\') ON CONFLICT (pgw_id) DO UPDATE SET pgw_bidang_id = '.$id_bidang.', pgw_unit_id = '.$id_unit.', pgw_jbt_id = '.$id_jabatan.', pgw_nama = \''.protectInsertQuote($value->nama).'\', pgw_nik = '.$nik.', pgw_nip = '.$nip.', pgw_email = '.$email.', pgw_gender = '.$value->JENIS_KELAMIN.', pgw_telp = '.$telp.', pgw_foto = '.$foto.', pgw_tanggal_pns = '.$tgl_pns.', updated_by = 0, updated_at = \''.$current_time.'\';';

                DB::connection()->getPdo()->exec($query);
            }

            DB::table('pegawai')->whereNotIn('pgw_id', $arr_id_pegawai)->whereNull('pgw_tanggal_pns')->whereNull('deleted_at')->update(['deleted_by' => 0, 'deleted_at' => $current_time]);

            DB::table('pegawai')->whereIn('pgw_id', $arr_id_pegawai)->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);

            $responseCode = 200;
            $responseData['completed'] = $no;
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus, $responseNote);
        return response()->json($response, $responseCode);
    }

    public function updatePegawaiKontrak(Request $request)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];
        $responseNote = [];

        $rules['ID_PEGAWAI'] = 'required';
        $rules['NAMA'] = 'required';

        $validator = Validator::make($request->all(), $rules, $this->validationMessage());

        if($validator->fails()){
            $responseCode = 400;
            $responseStatus = 'Missing Param';
            $responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
            $responseData['error_log'] = $validator->errors();
        }else{
            $pwd_config = SysConfig::get_data('default_password');
            $default_password = bcrypt($pwd_config->value);

            $errorCounter=0;
            $current_time = date('Y-m-d H:i:s');

            /* start import pegawai */
            $id_pegawai = $request->input('ID_PEGAWAI');
            $nama = trim($request->input('NAMA'));
            $id_jabatan = $request->input('ID_JABATAN');
            $nip = 'NULL';
            $id_unit =  $request->input('UNIT_KERJA');
            $id_bidang = $request->input('ID_BIDANG');
            // $nip = '\''.protectInsertQuote(helpEmpty($request->input('NIP'), 'NULL'), true).'\'';
            $nik = '\''.protectInsertQuote(helpEmpty($request->input('NIK'), 'NULL'), true).'\'';
            $email = (empty($request->input('EMAIL')))? 'NULL' : '\''.$request->input('EMAIL').'\'';
            $email = str_replace('%40', '@', $email);
            $telp = (empty($request->input('TELP')))? 'NULL' : '\''.$request->input('TELP').'\'';
            $tgl_pns = (empty($request->input('TGL_PNS')))? 'NULL' : '\''.$request->input('TGL_PNS').'\'';
            $jenis_kelamin = $request->input('JENIS_KELAMIN');
            $foto = (empty($request->input('FOTO_IDENTITAS')))? 'NULL' : '\''.$request->input('FOTO_IDENTITAS').'\'';

            $query = 'INSERT INTO "pegawai" (pgw_id, pgw_unit_id, pgw_bidang_id, pgw_jbt_id, pgw_nama, pgw_nik, pgw_nip, pgw_email, pgw_gender, pgw_telp, pgw_foto, pgw_tanggal_pns, created_by, created_at) VALUES ('.$id_pegawai.', '.$id_unit.', '.$id_bidang.', '.$id_jabatan.', \''.$nama.'\', '.$nik.', '.$nip.', '.$email.', '.$jenis_kelamin.', '.$telp.', '.$foto.', '.$tgl_pns.', 0, \''.$current_time.'\') ON CONFLICT (pgw_id) DO UPDATE SET pgw_bidang_id = '.$id_bidang.', pgw_unit_id = '.$id_unit.', pgw_jbt_id = '.$id_jabatan.', pgw_nama = \''.$nama.'\', pgw_nik = '.$nik.', pgw_nip = '.$nip.', pgw_email = '.$email.', pgw_gender = '.$jenis_kelamin.', pgw_telp = '.$telp.', pgw_foto = '.$foto.', pgw_tanggal_pns = '.$tgl_pns.', updated_by = 0, updated_at = \''.$current_time.'\';';

            DB::connection()->getPdo()->exec($query);

            DB::table('pegawai')->where('pgw_id', $id_pegawai)->whereNotNull('deleted_at')->update(['deleted_by' => NULL, 'deleted_at' => NULL]);
            /* end import pegawai */

            if($errorCounter > 0){
                $responseCode = 400;
                $responseMessage = 'Error Connection!';
            }else{
                $responseCode = 200;
            }
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus, $responseNote);
        return response()->json($response, $responseCode);
    }

    private function cek_userjabatan($username, $id_jabatan=null)
    {
        $m_user = new User();
        $result = false;

        $cek = $m_user::where('usr_username', $username)->first();

        if(!empty($id_jabatan)){
            if(!empty($cek) and $id_jabatan != $cek['usr_jbt_id']){
                $result = false;
            }else{
                $result = true;
            }
        }else{
            if(!empty($cek)){
                $result = false;
            }else{
                $result = true;
            }
        }

        return $result;
    }

    private function cek_userpegawai($username, $id_pegawai=null)
    {
        $m_user = new User();
        $result = false;

        $cek = $m_user::where('usr_username', $username)->first();

        if(!empty($id_pegawai)){
            if(!empty($cek) and $id_pegawai != $cek['usr_pgw_id']){
                $result = false;
            }else{
                $result = true;
            }
        }else{
            if(!empty($cek)){
                $result = false;
            }else{
                $result = true;
            }
        }

        return $result;
    }
}
