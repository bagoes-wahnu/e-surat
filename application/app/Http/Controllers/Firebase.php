<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Surat;
use App\Http\Models\User;
use App\Http\Models\UserToken;
use App\Http\Models\Notifikasi;

class Firebase extends Controller
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

    public static function send($sender, $receiver, $message, $category=false, $unique_id=false, $action='notif')
    {
        $arr_receiver = is_array($receiver)? $receiver : array($receiver);
        $response   = [];
        $temp       = [];
        $title      = app_info('name');
        $time       = date('Y-m-d H:i:s');

        foreach ($arr_receiver as $key => $value) {
            if(!empty($value) && !in_array($value, $temp)){
                $temp[] = $value;

                $m_notif = new Notifikasi();

                $m_notif->ntf_sender_id = $sender;
                $m_notif->ntf_receiver_id = $value;
                $m_notif->ntf_action = $action;
                $m_notif->ntf_message = $message;
                $m_notif->ntf_category = $category;
                $m_notif->ntf_unique_id = $unique_id;
                $m_notif->created_by = $sender;
                $m_notif->save();

                $id = $m_notif->ntf_id;

                $get_token = UserToken::get_data(false, $value, true);
                foreach ($get_token as $key => $data) {
                    $device_token = array($data->device_token);

                    $firebase_data =  array(
                        "title"     => $title,
                        "message"   => $message,
                        "time"      => $time,
                        "id"        => encText($id.'notif', true),
                        "action"    => $action,
                        "category"  => $category,
                        "unique_id"  => $unique_id,
                    );

                    $proses = send_firebase($device_token, $firebase_data);
                    $proses = json_decode($proses);

                    if(!empty($proses) && $proses->success > 0){
                        $m_notif->ntf_sent = true;
                        $m_notif->ntf_sent_at = $time;
                        $m_notif->save();
                    }

                    $response[] = ['data' => $firebase_data, 'proses' => $proses];
                }
            }
        }

        return $response;        
    }
}
