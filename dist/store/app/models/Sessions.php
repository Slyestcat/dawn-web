<?php
use Fox\Request;
use Illuminate\Database\Eloquent\Model as Model;

class Sessions extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'username',
        'sess_id',
        'ip_address',
        'created',
        'expires'
    ];

}