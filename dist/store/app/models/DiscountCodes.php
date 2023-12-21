<?php
use Illuminate\Database\Eloquent\Model as Model;

class DiscountCodes extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'percentage',
        'expires'
    ];

}