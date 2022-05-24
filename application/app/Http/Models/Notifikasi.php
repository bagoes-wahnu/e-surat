<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Notifikasi extends E_Model
{
    use SoftDeletes;
    protected $table = 'notifikasi';
    protected $primaryKey = 'ntf_id';
    public $incrementing = true;

    protected $fillable = [
        'ntf_sender_id',
        'ntf_receiver_id',
        'ntf_action',
        'ntf_message',
        'ntf_category',
        'ntf_unique_id',
        'ntf_sent',
        'ntf_sent_at',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];
}