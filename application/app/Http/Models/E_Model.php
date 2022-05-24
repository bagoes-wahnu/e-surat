<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class E_Model extends Model
{
    // protected $sequences = '';

    public function save_data($data, $id='')
    {
        if(empty($id)){
            if($this->timestamps == true){
                $data['created_at'] = date('Y-m-d H:i:s');
            }
            // $current_id = $current_id->$this->primaryKey;
            if($this->incrementing == false or is_array($this->primaryKey)){
                DB::table($this->table)->insert($data);
                $current_id = true;
            }else{
                $current_id = DB::table($this->table)->insertGetId($data, $this->primaryKey);
            }
            // $current_id = DB::getPdo()->lastInsertId();
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

    public function delete_data($where)
    {
        if(is_array($where)){
            $result = DB::table($this->table)->where(function($condition) use($where){
                foreach ($where as $key => $value) {
                    $condition->where($key, $value);
                }
            })->delete();
        }else{
            $result = DB::table($this->table)->where($this->primaryKey, '=', $where)->delete();
        }

        return $result;
    }
}