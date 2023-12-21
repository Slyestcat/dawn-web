<?php
use Illuminate\Database\Eloquent\Model as Model;

class UsersCart extends Model {

    public $timestamps    = false;
    public $incrementing  = false;
    public $table         = "users_cart";

    protected $fillable = [
        'username',
        'product_id',
        'quantity'
    ];

}