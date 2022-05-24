<?php

namespace App\Http\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'user';
    protected $primaryKey = 'usr_id';
    protected $connection = 'pgsql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usr_username',
        'usr_name',
        'usr_email',
        'usr_password',
        'usr_role',
        'usr_aktif',
        'usr_jbt_id',
        'usr_token_permission',
        'usr_token_limits',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'usr_password', 'remember_token', 'created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    public function getAuthPassword()
    {
        return $this->usr_password;
    }

    public static function get_auth($username)
    {
        return User::select(DB::raw('usr_id, usr_name, usr_username, usr_password, usr_email, usr_role, usr_password, usr_jbt_id, jbt_induk, COALESCE(jbt_uptd, \'f\') AS jbt_uptd'))
        ->leftJoin(DB::raw('jabatan jbt'), 'jbt_id', '=', 'usr_jbt_id')
        ->where('usr_username', $username)
        ->where('usr_aktif', true)
        ->where(function($where)
        {
            $where->where('usr_role', 1)
            ->orWhereNotNull('usr_jbt_id');
        })->first();
    }

    public function save_data($data, $id='')
    {
        if(empty($id)){
            if($this->timestamps == true){
                $data['created_at'] = date('Y-m-d H:i:s');
            }
            
            if($this->incrementing == false or is_array($this->primaryKey)){
                DB::table($this->table)->insert($data);
                $current_id = true;
            }else{
                $current_id = DB::table($this->table)->insertGetId($data, $this->primaryKey);
            }
        }else{
            $current_id = $id;

            if($this->timestamps == true){
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            if(is_array($id)){
                DB::table($this->table)->where(function($condition) use($id){
                    foreach ($id as $key => $value) {
                        $condition->where($key, $value);
                    }
                })->update($data);
            }else{
                DB::table($this->table)->where($this->primaryKey, $id)->update($data);
            }
        }

        return $current_id;
    }

    public static function get_data($id_user = false, $md5=true, $field=false, $sort=false)
    {
        $encryptPrimary = encText('user');

        $result = DB::table(DB::raw('"user" usr'))
        ->select(DB::raw( (($md5 == true)? "MD5(CONCAT(usr_id, '".$encryptPrimary."'))" : "usr_id" )." AS id_user, usr_name AS nama, usr_username AS username,  usr_email AS email, usr_role AS \"role\", role_keterangan AS nama_role, usr_token_permission AS token_permission, usr_token_limits AS token_limits, usr_aktif AS aktif, jbt_nama AS nama_jabatan, COALESCE(jbt_uptd, 'f') AS uptd"))
        ->leftJoin(DB::raw('sys_role_user'), DB::raw('CAST(role_code AS INTEGER)'), '=', 'usr_role')
        ->leftJoin(DB::raw('jabatan jbt'), 'jbt_id', '=', 'usr_jbt_id')
        ->whereNull(DB::raw('usr.deleted_at'));

        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_user == true){
            $result = $result->where(DB::raw("MD5(CONCAT(usr_id, '".$encryptPrimary."'))"), $id_user)->first();
        }else{
            $result  = $result->get();
        }
        
        return $result;
    }

    public static function json_grid($start, $length, $search='', $count=false, $sort, $field, $condition=false)
    {
        $encryptPrimary = encText('user');
        $query_tanggal = helpDateQuery('usr_tanggal', 'mi', 'pgsql');
        
        $result = DB::table(DB::raw('"user" usr'))
        ->select(DB::raw("MD5(CONCAT(usr_id, '".$encryptPrimary."')) AS id_user, usr_name AS nama, usr_username AS username,  usr_email AS email, usr_role AS \"role\", role_keterangan AS nama_role, usr_token_permission AS token_permission, usr_token_limits AS token_limits, usr_aktif AS aktif, jbt_nama AS nama_jabatan, COALESCE(jbt_uptd, 'f') AS uptd"))
        ->leftJoin(DB::raw('sys_role_user'), DB::raw('CAST(role_code AS INTEGER)'), '=', 'usr_role')
        ->leftJoin(DB::raw('jabatan jbt'), 'jbt_id', '=', 'usr_jbt_id')
        ->whereNull(DB::raw('usr.deleted_at'));

        if(!empty($search)){
            $result = $result->where(function($where) use($search, $query_tanggal){
                $where->where('usr_name', 'ILIKE', '%'.$search.'%')
                ->orWhere('usr_username', 'ILIKE', '%'.$search.'%')
                ->orWhere('jbt_nama', 'ILIKE', '%'.$search.'%')
                ->orWhere('role_keterangan', 'ILIKE', '%'.$search.'%')
                ;
            });
        }

        $result = $result->where(function($where){
            $where->where('usr_role', 1)
            ->orWhereNotNull('usr_jbt_id');
        });

        if($condition == true){
            $result = $result->where('usr_aktif', $condition);
        }

        if($count == true){
            $result = $result->count();
        }else{
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
