<?php
use Fox\Request;
use Illuminate\Database\Eloquent\Model as Model;

class Categories extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'title'
    ];
    
}