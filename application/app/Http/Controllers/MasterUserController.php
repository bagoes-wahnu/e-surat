<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Models\User;
use App\Http\Models\Pegawai;
use App\Http\Models\SysConfig;
use App\Http\Models\SysRoleUser;

class MasterUserController extends Controller
{

    public function __construct(){
        $this->title = 'Master User';
        $this->menu = 'master';
        $this->submenu = 'user';
    }
    
    public function index()
    {
        $data['title']  = $this->title;
        $data['menu_active']   = $this->menu;
        $data['sub_menu_active']    = $this->submenu;
        $default_password = SysConfig::get_data('default_password');
        $data['default_password'] = $default_password->value;
        $role_user = SysRoleUser::get_data();
        
        $arr_role_user = [];
        foreach ($role_user as $key => $value) {
            $arr_role_user[$value->role] = $value->keterangan;
        }

        $data['role_user'] = $arr_role_user;
        return view("master/user/grid", $data);
    }

    public function saveSetting(Request $request, User $m_user)
    {
        $code = 403;
        $status = '';
        $message = '';
        $data = [];

        $rules['id_user'] = 'required';

        $limit_access = $request->input('limit_access');

        if(empty($limit_access)){
            $rules['total_limit_access'] = 'required|min:1';
        }else{
            $rules['limit_access'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $code = 400;
            $status = 'Missing Param';
            $message = 'Silahkan isi form dengan benar terlebih dahulu';
            $data['error_log'] = $validator->errors();
        }else{
            $id_user = $request->input('id_user');

            $usr_token_permission = (!empty($request->input('allow_access'))) ? 't' : 'f';
            $usr_token_limits = (!empty($limit_access))? 0 : $request->input('total_limit_access');

            $get_user = User::get_data($id_user, false);

            if(!empty($get_user)){
                $m_user = User::find($get_user->id_user);
                $m_user->updated_by = $this->userdata('id_user');

                $m_user->usr_token_permission = $usr_token_permission;
                $m_user->usr_token_limits = $usr_token_limits;

                $m_user->save();

                $code = 200;
                $message = 'Data berhasil disimpan';
            }else{
                $code = 400;
                $status = 'No Data Available';
                $message = 'Data tidak tersedia';
            }
        }

        $response = helpResponse($code, $data, $message, $status);
        return response()->json($response, $code);
    }

    public function show($id_user)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $get_user = User::get_data($id_user);

        if(!empty($get_user)){
            $responseData['user'] = $get_user;
            $responseCode = 200;
            $responseMessage = 'Data tersedia';
        }else{
            $responseData['user'] = [];
            $responseCode = 400;
            $responseStatus = 'No Data Available';
            $responseMessage = 'Data tidak tersedia';
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        return response()->json($response, $responseCode);
    }

    // public function delete(request $request)
    // {
    //     $code = 403;
    //     $status = '';
    //     $message = '';
    //     $data = [];

    //     $validator = Validator::make($request->all(), ['id_user' => 'required']);

    //     if($validator->fails()){
    //         $code = 400;
    //         $status = 'Missing Param';
    //         $message = 'Silahkan isi form dengan benar terlebih dahulu';
    //         $data['error_log'] = $validator->errors();
    //     }else{

    //         $id_user = $request->input('id_user');

    //         $m_user = User::find($id_user);

    //         if(!empty($m_user) && $id_user != 1){
    //             $code = 200;
    //             $message = 'Data berhasil dihapus';
    //             $m_user->deleted_by = $this->userdata()['usr_id'];
    //             $m_user->save();
    //             $m_user->delete();

    //             $m_pegawai = Pegawai::find($m_user->usr_pgw_id);
    //             $m_pegawai->deleted_by = $this->userdata()['usr_id'];
    //             $m_pegawai->save();
    //             $m_pegawai->delete();
    //         }else{
    //             $code = 400;
    //             $status = 'Error';
    //             $message = 'Data tidak berhasil dihapus';
    //         }
    //     }

    //     $response = helpResponse($code, $data, $message, $status);
    //     return response()->json($response, $code);
    // }

    public function set_aktif(Request $request, User $m_user)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        
        $rules['id_user'] = 'required';

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $responseCode = 400;
            $responseStatus = 'Missing Param';
            $responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
            $responseData['error_log'] = $validator->errors();
        }elseif (!$request->ajax()) {
            return $this->accessForbidden();
        }else{
            $id_user = $request->input('id_user');
            $usr_aktif = $request->input('aktif');

            $m_user = User::find($id_user);

            if(!empty($m_user)){
                $m_user->usr_aktif = $usr_aktif;

                $m_user->save();

                if($m_user->usr_role == 2){
                    Pegawai::where('kcm_id', $m_user->usr_kcm_id)->update(['kcm_aktif_hukum' => $usr_aktif, 'updated_by' => session('userdata')['id_user']]);
                }


                $responseCode = 200;
                $responseMessage = 'Status berhasil '. (($usr_aktif == 't')? 'diaktifkan' : 'di-nonaktifkan');
            }else{
                $responseCode = 400;
                $responseMessage = 'Data tidak tersedia';
            }
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        return response()->json($response, $responseCode);
    }

    public function json(Request $request)
    {
        $responseCode = 200;
        $responseStatus = 'OK';
        $responseMessage = 'Data tersedia';
        $responseData = [];

        if(!$request->ajax()){
            return $this->accessForbidden();
        }else{

            $m_user = new User();

            $numbcol = $request->get('order');
            $columns = $request->get('columns');

            $echo = $request->get('draw');
            $start = $request->get('start');
            $perpage = $request->get('length');

            $search = $request->get('search');
            $search = $search['value'];
            $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
            $search = preg_replace($pattern, '', $search);

            $sort = $numbcol[0]['dir'];
            $field = $columns[$numbcol[0]['column']]['data'];
            
            $condition = ($request->get('aktif')? $request->get('aktif') : false);

            $page = ($start / $perpage) + 1;

            if($page >= 0){
                $result = $m_user->json_grid($start, $perpage, $search, false, $sort, $field, $condition);
                $total = $m_user->json_grid($start, $perpage, $search, true, $sort, $field, $condition);
            }else{
                $result = $m_user::orderBy($field, $sort)->get();
                $total = $m_user::all()->count();
            }

            $responseData = array("sEcho"=>$echo,"iTotalRecords"=>$total,"iTotalDisplayRecords"=>$total,"aaData"=>$result);

            return response()->json($responseData, $responseCode);
        }
    }

    // private function cek_username($username, $id_user=null)
    // {
    //     $m_user = new User();
    //     $result = false;            

    //     $cek = $m_user::where('usr_username', $username)->first();

    //     if(!empty($id_user)){
    //         if(!empty($cek) and $id_user != $cek['usr_id']){
    //             $result = false;
    //         }else{
    //             $result = true;
    //         }
    //     }else{
    //         if(!empty($cek)){
    //             $result = false;
    //         }else{
    //             $result = true;
    //         }
    //     }


    //     return $result;
    // }

    public function updatePassword(Request $request)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $rules['password_lama'] = 'required';
        $rules['password_baru'] = 'required';
        $rules['conf_password_baru'] = 'required';
        $rules['id_user'] = 'required';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $responseCode = 400;
            $responseStatus = 'Missing Param';
            $responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
            $responseData['error_log'] = $validator->errors();
        } else if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $responseCode = 400;

            $id_user = $request->input('id_user');
            $password_lama = $request->input('password_lama');
            $password_baru = $request->input('password_baru');
            $conf_password_baru = $request->input('conf_password_baru');

            $m_user = User::find($id_user);

            if (!empty($m_user)) {
                if (Hash::check($password_lama, $m_user->usr_password)) {
                    if ($password_baru == $conf_password_baru) {
                        $m_user->usr_password = bcrypt($password_baru);
                        $m_user->updated_by = session('userdata')['id_user'];
                        $m_user->save();

                        $responseCode = 200;
                        $responseMessage = 'Password berhasil di-update';
                    } else {
                        $responseMessage = 'Konfirmasi password baru tidak sama';
                    }
                } else {
                    $responseMessage = 'Password Anda salah';
                }
            } else {
                $responseMessage = 'Data tidak tersedia';
            }
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        return response()->json($response, $responseCode);
    }

    public function resetPassword($id_user)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $get_user = User::get_data($id_user, false);

        if(!empty($get_user)){
            $responseCode = 200;
            $responseStatus = 'OK';
            $responseMessage = 'Password berhasil di reset ke default';

            $m_user = new User;
            $get_default_password = SysConfig::get_data('default_password');
            $default_password = $get_default_password->value;
            $default_password = bcrypt($default_password);

            $m_user->where('usr_id', $get_user->id_user)->update(['usr_password' => $default_password, 'updated_by' => $this->userdata('id_user')]);
        }else{
            $responseData['user'] = [];
            $responseCode = 400;
            $responseStatus = 'No Data Available';
            $responseMessage = 'Data tidak tersedia';
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        return response()->json($response, $responseCode);   
    }
}